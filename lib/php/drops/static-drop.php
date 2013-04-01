<?php
class Static_Drop extends Drop_It_Drop {
	function __construct( $id = 'static', $label = 'Static', $template = 'static', $options = array() ) {
		parent::__construct( $id, $label, $template, $options );
	}
}