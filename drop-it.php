<?php
/*
Plugin Name: Drop It
Plugin URI: http://digitallyconscious.com
Description: Free drag and drop layout management for WordPress
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

	function __construct() {
		add_action( 'init', 'action_init' );
	}

	function action_init() {
		register_post_type( 'di-drop', array(
				'labels' => array( name => _x( 'Drops', 'post type general name', 'drop-it' ) ),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => _x( 'drop', 'Drop slug', 'drop-it' ) ),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			) );
	}

	function save() {

	}

	function preview() {

	}
}

$dropit = new DropIt;