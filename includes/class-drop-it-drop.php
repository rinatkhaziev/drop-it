<?php
/**
 * Base class that should be extended on a per drop basis
 *
 * Since it's abstract you have to make sure that all the abstract methods are implemented. Otherwise it'll produce Fatal.
 *
 * @since 0.1
 */
abstract class Drop_It_Drop  {
	public $id,
		   $label,
		   $template,
		   $options,
		   $allowed_tokens;

	function __construct( $id, $label, $template, $options = array() ) {
		$this->id = $id;
		$this->label = $label;
		$this->template = $template;
		$this->options = $options;
		$this->allowed_tokens = apply_filters( "di_allowed_tokens", array(
			'the_title',
			'the_content',
			'the_excerpt',
			'the_date',
		) );
	}

	function preview() {
	}

	function save() {
	}

	// Each drop type should implement it's own render logic
	function render( $drop ) {
		return apply_filters( "{$this->id}_drop_template", '' );
	}
}

