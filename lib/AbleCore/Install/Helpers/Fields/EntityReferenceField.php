<?php

namespace AbleCore\Install\Helpers\Fields;

use AbleCore\Install\Helpers\Field;
use AbleCore\Install\Helpers\FieldTypes;

class EntityReferenceField extends Field {

	public function __construct($field_name, array $definition)
	{
		parent::__construct($field_name, $definition);

		$this->setSetting('target_type', 'node');
		$this->setSetting('handler', 'base');
		$this->setSetting('handler_settings', array(
			'target_bundles' => array(),
			'sort' => array('type' => 'none'),
		));
	}

	public function save()
	{
		if (empty($this->definition['settings']['handler_settings']['target_bundles'])) {
			throw new \Exception('The field ' . $this->getName() . ' does not reference any target bundles.');
		}

		return parent::save(); // TODO: Change the autogenerated stub
	}

	/**
	 * Defines the bundles this field references.
	 *
	 * @param array $bundles An array of bundles this entity reference references.
	 *
	 * @return $this
	 */
	public function references(array $bundles = array())
	{
		$this->definition['settings']['handler_settings']['target_bundles'] = drupal_map_assoc($bundles);
		return $this;
	}

	/**
	 * Sets the entity type this field references.
	 *
	 * @param string $entity_type The entity type.
	 *
	 * @return $this
	 */
	public function referencesType($entity_type = 'node')
	{
		return $this->setSetting('target_type', $entity_type);
	}

	/**
	 * Creates a new entity reference field.
	 *
	 * @param string $field_name  The name of the new field.
	 * @param array  $bundles     The bundles the field references (must not be empty).
	 * @param string $entity_type The type of entity the field references.
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public static function createEntityReference($field_name, array $bundles = array(), $entity_type = 'node')
	{
		$instance = static::create($field_name);
		$instance->setType(FieldTypes::ENTITY_REFERENCE);
		$instance->references($bundles);
		$instance->referencesType($entity_type);

		return $instance->save();
	}
}