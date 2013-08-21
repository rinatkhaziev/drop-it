<?php
/**
 * Single Post Drop
 */
class Ad_Drop_It_Drop extends Drop_It_Drop {
	static $_id = 'ad';
	function __construct( $label = 'Advertisement', $template = 'ad', $options = array() ) {
		parent::__construct( self::$_id, $label, $template, $options );
	}

	/**
	 * Add post itself for rendering (Front End)
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
