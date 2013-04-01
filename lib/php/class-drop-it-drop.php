<?php
/**
 * Base class that should be extended on a per drop basis
 */
class Drop_It_Drop implements Drop_It_Droppable {
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

	function datasource() {

	}

	function preview() {

	}

	function save() {

	}
}

interface Drop_It_Droppable {
	function datasource();

	function render();

	function preview();

	function save();
}