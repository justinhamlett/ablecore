<?php

namespace AbleCore;

class HTML {

	public static function image($source, $alt = '', $title = '', array $attributes = array(), array $options = array())
	{
		$default_options = array(
			'type' => 'theme',
		);
		$options = array_replace_recursive($default_options, $options);

		$variables = array();
		switch($options['type']) {
			case 'theme':
				$variables['path'] = path_to_theme() . '/' . $source;
				break;
			default:
				$variables['path'] = $source;
		}
		if ($alt) {
			$variables['alt'] = $alt;
		}
		if ($title) {
			$variables['title'] = $title;
		}
		if (count($attributes) > 0) {
			if (array_key_exists('width', $attributes)) {
				$variables['width'] = $attributes['width'];
				unset($attributes['width']);
			}
			if (array_key_exists('height', $attributes)) {
				$variables['height'] = $attributes['height'];
				unset($attributes['height']);
			}
			$variables['attributes'] = $attributes;
		}

		return theme('image', $variables);
	}

} 
