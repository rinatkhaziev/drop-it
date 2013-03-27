<?php
/*
Plugin Name: Drop It
Plugin URI: http://digitallyconscious.com
Description: Easy drag and drop layout management for WordPress
Author: Rinat Khaziev
Version: 0.1
Author URI: http://doejo.com

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

define( 'DROP_IT_VERSION', '0.1' );
define( 'DROP_IT_ROOT' , dirname( __FILE__ ) );
define( 'DROP_IT_FILE_PATH' , DROP_IT_ROOT . '/' . basename( __FILE__ ) );
define( 'DROP_IT_URL' , plugins_url( '/', __FILE__ ) );

// Bootstrap
require_once DROP_IT_ROOT . '/lib/php/class-drop-it-drop.php';

class DropIt {

	public $drops;

	function __construct( $drops = array() ) {
		add_action( 'after_setup_theme', $this->_a( 'action_init' ) );
		add_action( 'admin_enqueue_scripts', $this->_a( 'admin_enqueue_scripts' ) );
		register_activation_hook( __FILE__, $this->_a( 'activation' ) );
	}

	function register_drops() {
	}

	function action_init() {
		register_post_type( 'di-drop', array(
				'labels' => array( name => _x( 'Drops', 'post type general name', 'drop-it' ) ),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => _x( 'di-drop', 'Drop slug', 'drop-it' ) ),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			) );
		// Must register drops after we register our post type
		$this->register_drops( apply_filters( 'di_available_drops', $drops ) );
	}

	function save() {
	}

	/**
	 * Do activation specific stuff
	 * @return [type] [description]
	 */
	function activation() {
		// Make sure our post type rewrite is registered
		flush_rewrite_rules();
	}

	/**
	 * Clean after ourselves
	 * @return [type] [description]
	 */
	function deactivation() {
		flush_rewrite_rules();
	}

	/**
	 * Preview a drop
	 * @return [type] [description]
	 */
	function preview() {
	}

	/**
	 * Register Admin scripts and styles
	 * @return [type] [description]
	 */
	function admin_enqueue_scripts() {
		wp_enqueue_script( 'drop-it-ui', DROP_IT_ROOT . '/lib/js/drop-it.js', array( 'jquery' ) );
	}

	/**
	 * Just a convenience wrapper that returns array of reference to the instance and a method
	 * Used for registering hooks
	 *
	 * @param  [type] $method [description]
	 * @return [type]         [description]
	 */
	private function _a( $method ) {
		return array( $this, $method );

	}
}

$dropit = new DropIt;