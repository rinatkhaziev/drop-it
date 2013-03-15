<?php
class DropIt_Drop {
	public $id,
		   $label, 
		   $template,
		   $options;

	function __construct( $id, $label, $template, $options = array() ) {
		$this->id = $id;
		$this->label = $label;
		$this->template = $template;
	}

	function render() {
	}
}