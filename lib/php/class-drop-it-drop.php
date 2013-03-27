<?php
/**
 * Base class that should be extended on a per drop basis
 */
abstract class DropIt_Drop {
	public $id,
		   $label,
		   $template,
		   $options;

	function __construct( $id, $label, $template, $options = array() ) {
		$this->id = $id;
		$this->label = $label;
		$this->template = $template;
		$this->options = $options;
	}

	function render() {
	}

	abstract function datasource();
}