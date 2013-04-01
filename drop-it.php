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
require_once DROP_IT_ROOT . '/lib/php/wp-settings-api/class.settings-api.php';
require_once DROP_IT_ROOT . '/lib/php/drop-it-settings.php';

class Drop_It {

	public $drops;
	public $key = 'drop-it';
	public $manage_cap;
	private $settings;

	/**
	 * Instantiate the plugin, hook the filters and actions
	 */
	function __construct() {
		add_action( 'after_setup_theme', $this->_a( 'action_init' ) );
		add_action( 'admin_enqueue_scripts', $this->_a( 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', $this->_a( 'action_admin_menu' ) );
		add_action( 'admin_head', $this->_a( 'action_admin_head' ) );
		register_activation_hook( __FILE__, $this->_a( 'activation' ) );
		$this->manage_cap = apply_filters( 'di_manage_cap', 'edit_others_posts' );
		$this->settings =  new Drop_It_Settings( $this->key, $this->manage_cap );
	}

	/**
	 * Registering available drios
	 * @param  array  $drops [description]
	 * @return [type]        [description]
	 */
	function register_drops( $drops = array() ) {
		$path = DROP_IT_ROOT . '/lib/php/drops/';
		foreach ( (array) scandir( $path ) as $drop ) {
			if ( in_array( $drop, array( '.', '..' ) ) )
				continue;
			require_once $path . $drop;
		}
		foreach( get_declared_classes() as $class ) {
			if ( ! is_subclass_of( $class, 'Drop_It_Drop' ) )
				continue;
			$this->drops[ sanitize_title_with_dashes( $class ) ] = new $class;
		}
	}

	/**
	 * Register drop and layout post types
	 * @return [type] [description]
	 */
	function action_init() {
		register_post_type( 'di-drop', array(
				'labels' => array( 'name' => _x( 'Drops', 'post type general name', 'drop-it' ) ),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'rewrite' => array( 'slug' => _x( 'di-drop', 'Drop slug', 'drop-it' ) ),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			) );
		register_post_type( 'di-layout', array(
			) );
		// Must register drops after we register our post type
		$this->register_drops( apply_filters( 'di_available_drops', array() ) );
	}

	/**
	 * Add menu items
	 * @return [type] [description]
	 */
	function action_admin_menu() {
		add_menu_page( __( 'Drop It!', 'drop-it' ), __( 'Drop It!', 'drop-it' ), $this->manage_cap , $this->key, $this->_a( 'admin_page' ), 'div', 11 );
		add_submenu_page( $this->key, __( 'Drops', 'drop-it' ), __( 'Drops', 'drop-it' ), $this->manage_cap, $this->key . '-drops', $this->_a( 'admin_page_drops' ) );
		add_submenu_page( $this->key, __( 'Layouts', 'drop-it' ), __( 'Layouts', 'drop-it' ), $this->manage_cap, $this->key . '-layouts', $this->_a( 'admin_page_layouts' ) );
	}

	function action_admin_head() {
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
	 * View for index admin page
	 * @return [type] [description]
	 */
	function admin_page() {
		$this->_render( 'index' );
	}

	/**
	 * View for drops management page
	 * @return [type] [description]
	 */
	function admin_page_drops() {
		$this->_render( 'drops' );
	}

	/**
	 * View for layouts management page
	 * @return [type] [description]
	 */
	function admin_page_layouts() {
		$this->_render( 'layouts' );
	}

	/**
	 * Render a view
	 * @param  string $view_slug
	 * @return [type]            [description]
	 */
	function _render( $view_slug = '' ) {
		ob_start();
		$file = DROP_IT_ROOT .'/lib/views/' . $view_slug .'.tpl.php';
		if ( file_exists( $file ) )
			require $file;
		echo '<div class="wrap"> ' .ob_get_clean() . '</div>';
	}

	/**
	 * Register Admin scripts and styles
	 * @return [type] [description]
	 */
	function admin_enqueue_scripts() {
		wp_enqueue_script( 'drop-it-ui', DROP_IT_URL . 'lib/js/drop-it.js', array( 'jquery' ) );
		wp_enqueue_style( 'drop-it', DROP_IT_URL . 'lib/css/drop-it.css' );
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

$drop_it = new Drop_It;