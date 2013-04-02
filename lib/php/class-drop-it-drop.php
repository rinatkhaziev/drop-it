<?php
/**
 * Base class that should be extended on a per drop basis
 *
 * @since 0.1
 * @uses Drop_It_Droppable
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

	function datasource( $args = array() ) {

	}

	function preview() {

	}

	function save() {

	}
}

/**
 * All Drops have to implement methods of this interface
 *
 * @since 0.1
 */
interface Drop_It_Droppable {
	function datasource( $args = array() );

	function render();

	function preview();

	function save();
}