<?php

namespace AbleCore\Install\Helpers;

class Menu {

	/**
	 * The menu definition.
	 * @var array|null
	 */
	public $definition = null;

	protected $name = '';
	protected $machine_name = '';

	public function __construct($machine_name, $name, $definition = array())
	{
		$this->definition = $definition;
		$this->name = $name;
		$this->machine_name = $machine_name;

		$this->definition['menu_name'] = $this->machine_name;
		$this->definition['title'] = $this->name;
	}

	/**
	 * Creates a new menu.
	 *
	 * @param string $machine_name The machine name of the menu to create.
	 * @param string $name The title of the menu.
	 *
	 * @return Menu|static
	 */
	public static function create($machine_name, $name)
	{
		$menu = menu_load($machine_name);
		if ($menu) {
			$instance = static::load($machine_name);
			$instance->definition['title'] = $name;
			$instance->definition['menu_name'] = $machine_name;
			return $instance;
		} else {
			return new static($machine_name, $name);
		}
	}

	/**
	 * Loads an existing menu.
	 *
	 * @param string $machine_name The machine name of the menu to load.
	 *
	 * @return static
	 */
	public static function load($machine_name)
	{
		$menu = menu_load($machine_name);
		return new static($machine_name, $menu['title'], $menu);
	}

	/**
	 * Sets the description of the menu.
	 *
	 * @param string $description The description for the menu.
	 *
	 * @return $this
	 */
	public function setDescription($description)
	{
		$this->definition['description'] = $description;
		return $this;
	}

	/**
	 * Provides some default items for the menu. These items will only be added
	 * if the menu is already empty.
	 *
	 * @param array $items The items to add to the menu. The key is the title for
	 *                     the item, and the value can either be an array of the
	 *                     menu item settings, or a string representing the path
	 *                     of the menu item.
	 *
	 * @return $this
	 */
	public function seed(array $items)
	{
		// Clear the menu cache so that menu items added from the parent
		// module will appear.
		menu_cache_clear();

		$existing_menu = menu_load($this->machine_name);
		if (!empty($existing_menu)) {
			if (!static::menuHasLinks($this->machine_name)) {

				// Clear the existing menu.
				foreach (menu_load_links($this->machine_name) as $link) {
					menu_link_delete($link['mlid']);
				}

				$weight = 0;
				foreach ($items as $title => $item) {

					// Create the base definition from the item if it's an array.
					$definition = array();
					if (is_array($item)) {
						$definition = $item;
					} else {
						$definition['link_path'] = $item;
					}

					// Update the link path if we don't have one already.
					if (!array_key_exists('link_title', $definition)) {
						$definition['link_title'] = $title;
					}

					// Then update the menu name.
					if (!array_key_exists('menu_name', $definition)) {
						$definition['menu_name'] = $this->machine_name;
					}

					// Then update the weight.
					if (!array_key_exists('weight', $definition)) {
						$definition['weight'] = $weight;
					}

					// Save the menu link.
					menu_link_save($definition);

					$weight++;

				}
			}
		}

		return $this;
	}

	/**
	 * Saves the menu.
	 *
	 * @return $this
	 */
	public function save()
	{
		menu_save($this->definition);
		return $this;
	}

	/**
	 * Checks to see whether the specified menu has links.
	 *
	 * @param string $menu_name The machine name of the menu.
	 *
	 * @return bool Whether or not it has links.
	 */
	public static function menuHasLinks($menu_name)
	{
		$links = menu_load_links($menu_name);

		// If we only have one link in the main menu...
		if ($menu_name == 'main-menu' && count($links) === 1) {
			$first_link = reset($links);

			// And that link is the homepage, say the menu is empty.
			if ($first_link['link_path'] == '<front>') {
				return false;
			}
		}

		return (is_array($links) && count($links) > 0);
	}

} 
