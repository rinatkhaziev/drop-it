<?php
class Static_HTML extends Drop_It_Drop {
	function __construct( $id = 'static_html', $label = 'Static HTML', $template = 'static_html', $options = array() ) {
		parent::__construct( $id, $label, $template, $options );
	}

	function prepare_data( $drop ) {
		$drop['content'] = do_shortcode( $drop['content'] );
		return $drop;
	}
}