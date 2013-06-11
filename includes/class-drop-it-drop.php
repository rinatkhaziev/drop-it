<?php
/**
 * Base class that should be extended on a per drop basis
 *
 * Since it's abstract you have to make sure that all the abstract methods are implemented. Otherwise it'll produce Fatal.
 *
 * @since 0.1
 */
abstract class Drop_It_Drop {
	public static $_id = 'drop_it_drop';
	public $id,
	$label,
	$template,
	$options;
	/**
	 * Constructor
	 *
	 * @todo may be get rid of constructor at all
	 *
	 * @param [type]  $id       [description]
	 * @param [type]  $label    [description]
	 * @param [type]  $template [description]
	 * @param array   $options  [description]
	 */
	function __construct( $id, $label, $template, $options = array() ) {
		$this->label = $label;
		$this->template = apply_filters( $id .'_drop_template', $template );
		$this->options = $options;
		$this->id = $id;
	}

	function preview() {
	}

	function save() {
	}

	/**
	 * Prepare data for template logic
	 *
	 * Override this method in a child class to customize your data
	 *
	 * @param array   $drop a single drop
	 * @return array prepared data, ready for templating
	 *
	 */
	function prepare_data( $drop = array() ) {
		return $drop;
	}
}
