<?php
/**
 * Single Post Drop
 */
class Single_Drop_It_Drop extends Drop_It_Drop {
	static $_id = 'single';
	function __construct( $label = 'Single Post', $template = 'single', $options = array() ) {
		parent::__construct( self::$_id, $label, $template, $options );
	}

	/**
	 * Add post itself for rendering
	 *
	 * @param [type]  $drop [description]
	 * @return [type]       [description]
	 */
	function prepare_data( $drop ) {
		$post = get_post( $drop['data'] );
		$drop['post'] = $post;
		return $drop;
	}
}
