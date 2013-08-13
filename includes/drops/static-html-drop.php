<?php
class Static_Html_Drop_It_Drop extends Drop_It_Drop {
	static $_id = 'static_html';
	function __construct( $label = 'Static HTML', $template = 'static_html', $options = array() ) {
		parent::__construct( self::$_id, $label, $template, $options );
	}

	/**
	 * Render inside shortcodes if any
	 *
	 * @param [type]  $drop [description]
	 * @return [type]       [description]
	 */
	function prepare_data( $drop = array() ) {
		$drop['title'] = do_shortcode( $drop['title'] );
		$drop['data'] = do_shortcode( $drop['data'] );
		return $drop;
	}
}
