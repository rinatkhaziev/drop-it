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
	public $settings;

	/**
	 * Instantiate the plugin, hook the filters and actions
	 */
	function __construct() {
		add_action( 'init', $this->_a( 'action_init' ) );
		add_action( 'admin_enqueue_scripts', $this->_a( 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', $this->_a( 'action_admin_menu' ) );
		add_action( 'admin_head', $this->_a( 'action_admin_head' ) );
		add_action( 'add_meta_boxes', $this->_a( 'action_add_meta_boxes' ) );
		add_action( 'admin_init', $this->_a( '_route_ajax_actions' ) );
		register_activation_hook( __FILE__, $this->_a( 'activation' ) );
		$this->manage_cap = apply_filters( 'di_manage_cap', 'edit_others_posts' );
		$this->settings =  new Drop_It_Settings( $this->key, $this->manage_cap );


	}

	function _route_ajax_actions( $req ) {
		add_action( 'wp_ajax_save_drop', $this->_a( 'save_drop' ) );
	}


	/**
	 * Registering available drios
	 *
	 * @param array   $drops [description]
	 * @return [type]        [description]
	 */
	function register_drops() {
		$path = DROP_IT_ROOT . '/lib/php/drops/';
		$class_files = $class_names = array();

		// Scan drops folder for bundled drops
		// Use this filter to add custom drops in
		$class_files = apply_filters( 'di_drops_to_register', scandir( $path ) );
		foreach ( $class_files as $drop ) {
			$class_file = $path . $drop;
			if ( in_array( $drop, array( '.', '..' ) ) || !file_exists( $class_file ) )
				continue;
			// @todo try to prevent file inclusion if class does not comply to interface
			require_once $class_file;
			$class_names = array_merge( $class_names, $this->file_get_php_classes( $class_file ) );
		}

		$this->if_initialize_classes( $class_names );
	}

	/**
	 * Check if available class definitions subclasses of Drop_It_Drop
	 * @param  array  $class_names [description]
	 * @return [type]              [description]
	 */
	function if_initialize_classes( $class_names = array() ) {
		foreach( $class_names as $class_name ) {
			$reflection = new ReflectionClass( $class_name );
			if ( $reflection->isSubclassOf( 'Drop_It_Drop' ) )
				$this->drops[ sanitize_title_with_dashes( $class_name ) ] = new $class_name;
		}
	}

	function file_get_php_classes( $filepath ) {
		$php_code = file_get_contents( $filepath );
		$classes = $this->get_php_classes( $php_code );
		return $classes;
	}

	function get_php_classes( $php_code ) {
		$classes = array();
		$tokens = token_get_all( $php_code );
		$count = count( $tokens );
		for ( $i = 2; $i < $count; $i++ ) {
			if ( $tokens[$i - 2][0] == T_CLASS
				&& $tokens[$i - 1][0] == T_WHITESPACE
				&& $tokens[$i][0] == T_STRING ) {

				$class_name = $tokens[$i][1];
				$classes[] = $class_name;
			}
		}
		return $classes;
	}

	/**
	 * Register drop and layout post types
	 *
	 * @return [type] [description]
	 */
	function action_init() {
		load_plugin_textdomain( 'drop-it', false, dirname( plugin_basename( __FILE__ ) ) . '/lib/languages/' );
		register_post_type( 'di-drop', array(
				'labels' => array( 'name' => _x( 'Drop It Drops', 'Drop post type plural name', 'drop-it' ) ),
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
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' )
			) );
		register_post_type( 'di-layout', array(
				'labels' => array( 'name' => _x( 'Drop It Layouts', 'Drop layout post type plural name', 'drop-it' ) ),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => _x( 'di-layout', 'Drop layout slug', 'drop-it' ) ),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
			) );
		// Must register drops after we register our post type
		$this->register_drops();
	}

	function action_add_meta_boxes() {
		// The post is not saved, so display a note that the post should be saved
		if ( !isset( $_GET['post'] ) ) {
			add_meta_box(
				'drop_it_layout_droppable_new_post',
				__( 'Drop It Here!', 'drop-it' ),
				$this->_a( '_metabox' ),
				'di-layout',
				'normal',
				'default',
				array( 'view' => 'droppable_new_post' )
			);
			return;
		}
		add_meta_box(
			'drop_it_layout_droppable',
			__( 'Drop It Here!', 'drop-it' ),
			$this->_a( '_metabox' ),
			'di-layout',
			'normal',
			'default',
			array( 'view' => 'droppable' )
		);

	}

	function _metabox( $post_id, $metabox ) {
		extract( $metabox['args'] );
		$this->_render( 'metaboxes/' . $view );
	}
	/**
	 * Add menu items
	 *
	 * @return [type] [description]
	 */
	function action_admin_menu() {
		add_menu_page( __( 'Drop It!', 'drop-it' ), __( 'Drop It!', 'drop-it' ), $this->manage_cap , $this->key, $this->_a( 'admin_page' ), 'div', 11 );
	//	add_submenu_page( $this->key, __( 'Drops', 'drop-it' ), __( 'Drops', 'drop-it' ), $this->manage_cap, $this->key . '-drops', $this->_a( 'admin_page_drops' ) );
	//	add_submenu_page( $this->key, __( 'Layouts', 'drop-it' ), __( 'Layouts', 'drop-it' ), $this->manage_cap, $this->key . '-layouts', $this->_a( 'admin_page_layouts' ) );
	}

	function action_admin_head() {
	}

	function save_drop() {
		// Retrieving json payload fro m php input stream
		$payload = json_decode( file_get_contents('php://input') );
		var_dump( $payload );
		exit;
	}

	/**
	 * Do activation specific stuff
	 *
	 * @return [type] [description]
	 */
	function activation() {
		// Make sure our post type rewrite is registered
		flush_rewrite_rules();
	}

	/**
	 * Clean after ourselves
	 *
	 * @return [type] [description]
	 */
	function deactivation() {
		flush_rewrite_rules();
	}

	/**
	 * Preview a drop
	 *
	 * @return [type] [description]
	 */
	function preview() {
	}

	/**
	 * View for index admin page
	 *
	 * @return [type] [description]
	 */
	function admin_page() {
		$this->_render( 'index' );
	}

	/**
	 * View for drops management page
	 *
	 * @return [type] [description]
	 */
	function admin_page_drops() {
		$this->_render( 'drops' );
	}

	/**
	 * View for layouts management page
	 *
	 * @return [type] [description]
	 */
	function admin_page_layouts() {
		$this->_render( 'layouts' );
	}

	/**
	 * Render a view
	 *
	 * @param string  $view_slug
	 * @return [type]            [description]
	 */
	function _render( $view_slug = '', $pre = '<div class="wrap">', $after = '</div>' ) {
		ob_start();
		$file = DROP_IT_ROOT .'/lib/views/' . $view_slug .'.tpl.php';
		if ( file_exists( $file ) )
			require $file;
		echo $pre  . ob_get_clean() . $after;
	}

	/**
	 * Register Admin scripts and styles
	 *
	 * @return [type] [description]
	 */
	function admin_enqueue_scripts() {
		// Bust cache for dev
		$rnd = mt_rand( 100, 10000 );
		wp_enqueue_script( 'di-bb-drop-model', DROP_IT_URL . 'lib/js/models/drop.js', array( 'jquery', 'jquery-ui-sortable', 'backbone' ), $rnd, true );
		wp_enqueue_script( 'di-bb-drop-collection', DROP_IT_URL . 'lib/js/collections/drops.js', array( 'jquery', 'jquery-ui-sortable', 'backbone' ), $rnd, true );
		wp_enqueue_script( 'di-bb-drop-view', DROP_IT_URL . 'lib/js/views/drop.js', array( 'jquery', 'jquery-ui-sortable', 'backbone' ), $rnd, true );
		wp_enqueue_script( 'di-bb-drops-view', DROP_IT_URL . 'lib/js/views/drops.js', array( 'jquery', 'jquery-ui-sortable', 'backbone' ), $rnd, true );
		wp_enqueue_script( 'drop-it-ui', DROP_IT_URL . 'lib/js/drop-it.js', array( 'jquery', 'jquery-ui-sortable', 'backbone' ), $rnd, true );
		wp_enqueue_style( 'drop-it', DROP_IT_URL . 'lib/css/drop-it.css' );
	}

	/**
	 * Just a convenience wrapper that returns array of reference to the instance and a method
	 * Used for registering hooks
	 *
	 * @param [type]  $method [description]
	 * @return [type]         [description]
	 */
	private function _a( $method ) {
		return array( $this, $method );

	}
}

global $drop_it;
$drop_it = new Drop_It;
