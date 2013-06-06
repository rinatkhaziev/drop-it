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
require_once DROP_IT_ROOT . '/lib/php/class-wp-twig.php';

class Drop_It {

	public $drops;
	public $key = 'drop-it';
	public $manage_cap;
	public $settings;
	public $twig;

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
		add_action( 'edit_form_advanced', $this->_a( 'action_enable_tiny' ) );
		register_activation_hook( __FILE__, $this->_a( 'activation' ) );
		$this->manage_cap = apply_filters( 'di_manage_cap', 'edit_others_posts' );
		$this->settings =  new Drop_It_Settings( $this->key, $this->manage_cap );
		add_action( 'wp_ajax_drop_it_ajax_route', $this->_a( '_route_ajax_actions' ) );
		add_action( 'wp_ajax_drop_it_ajax_search', $this->_a( '_ajax_search' ) );
		add_shortcode( 'drop-it-zone', $this->_a( '_render_shortcode' ) );

		$this->twig = new WP_Twig( DROP_IT_ROOT . '/lib/views/twig-templates/', false );
	}

	function _ajax_search() {
		if ( !isset( $_GET['term'] ) || empty( $_GET['term'] ) )
			exit;

		// Sanitize term and make sure that exclude is array
		$term = sanitize_text_field( $_GET['term'] );
		$exclude = isset( $_GET['exclude'] ) && is_array( $_GET['exclude'] ) ? $_GET['exclude'] : array();
		$posts = get_posts( array(
			's' => $term,
			'posts_per_page' => 10,
			'exclude' => $exclude
		) );

		$return = array();
		foreach( $posts as $post ) {
			$return[] = (object) array( 'post_id' => $post->ID, 'post_title' => $post->post_title, 'post_date' => $post->post_date );
		}
		echo json_encode( $return );
		exit;
	}
	/**
	 * Route AJAX actions to CRUD methods`
	 * @return [type] [description]
	 */
	function _route_ajax_actions() {
		// Read and decode JSON payload fro
		$payload = json_decode( file_get_contents( 'php://input' ) );
		if ( !empty( $payload ) && isset( $payload->action ) ) {
			switch( $payload->action ) {
				case 'create_drop':
						$result = $this->create_drop( $payload );
						if ( ! $result ) {
							status_header( 701 );
							$result = "The drop you're trying to save is invalid";
						}
						echo $result;
					break;
				case 'get_drop':
						echo $this->get_drop( $payload );
					break;
				case 'update_drop':
						echo $this->update_drop( $payload );
					break;
				case 'delete_drop':
						echo $this->delete_drop( $payload->drop_id, $payload->post_id );
					break;
			}
		exit;
		}

		/**
		 * Prototype of handling CRUD actions for collections
		 */
		if ( isset( $_REQUEST['mode'] ) && !empty( $payload ) ) {
			switch( $_REQUEST['mode'] ) {
				case 'update_collection':
					$this->update_collection( $payload );
				break;
			}
			exit;
		}
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
		$class_files = apply_filters( 'di_drops_to_register', array_diff( scandir( $path ), array( '..', '.' ) ) );
		foreach ( $class_files as $drop ) {
			$class_file = $path . $drop;

			// Just a safety check for a filter
			if ( !file_exists( $class_file ) )
				continue;

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

	/**
	 * Parse PHP tokens to grab see what classes are defined in the file
	 * Normally, there should be only one, paranoid check
	 * @param  string $php_code PHP Code of the file
	 * @return array            [description]
	 */
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

	function action_enable_tiny() {
		//wp_editor( '', 'staticcontent' );
	}
	/**
	 * Register drop and layout post types
	 *
	 * @return [type] [description]
	 */
	function action_init() {
		load_plugin_textdomain( 'drop-it', false, dirname( plugin_basename( __FILE__ ) ) . '/lib/languages/' );
/*		register_post_type( 'di-drop', array(
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
			) );*/
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
	//	add_menu_page( __( 'Drop It!', 'drop-it' ), __( 'Drop It!', 'drop-it' ), $this->manage_cap , $this->key, $this->_a( 'admin_page' ), 'div', 11 );
	//	add_submenu_page( $this->key, __( 'Drops', 'drop-it' ), __( 'Drops', 'drop-it' ), $this->manage_cap, $this->key . '-drops', $this->_a( 'admin_page_drops' ) );
	//	add_submenu_page( $this->key, __( 'Layouts', 'drop-it' ), __( 'Layouts', 'drop-it' ), $this->manage_cap, $this->key . '-layouts', $this->_a( 'admin_page_layouts' ) );
	}

	function action_admin_head() {
		$screen = get_current_screen();
		if ( !isset( $_GET['post'] ) || $screen->base != 'post' ||  $screen->post_type != 'di-layout' )
			return;
		$drops = $this->get_drops_for_layout( $_GET['post'] );
		$exclude = array();
		$meta = json_encode( $drops );
		foreach( $drops as $drop ) {
			if ( $drop['type'] == 'single' ) {
				$exclude[] = (int) $drop['content'];
			}
		}
		$exclude = json_encode( $exclude ); ?>

<script type="text/javascript">
	window.drops = <?php echo $meta ?>;
	window.drop_it_layout_id = '<?php echo esc_js( $_GET['post'] ) ?>';
	window.drop_it_autocomplete_exclude = <?php echo $exclude ?>
</script>
<?php
	}

	/**
	 * Construct and return array of drops as expected by Backbone.js model
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	function get_drops_for_layout( $post_id ) {
		global $wpdb;
		//@todo need to sort out the sorting
		$drops = $wpdb->get_results( $wpdb->prepare( "select * from $wpdb->postmeta where post_id=%s and meta_key='_drop'", $post_id ) );
		$prepared = array();

		foreach( (array) $drops as $drop ) {
			$meta  = (array) unserialize( $drop->meta_value );
			// Just for the sake of UI friendliness adding post_title and post_excerpt to returned data;
			if ( $meta['type'] == 'single' ) {
				$post = (array) get_post( $meta['content'], 'ARRAY_A' );

				if ( !empty($post ) )
					$meta = array_merge( $meta,
						array(
							'post_title' =>  $post['post_title'],
							'post_excerpt' => $post['post_excerpt'],
						) );
			}
			$prepared[] = array_merge( array( 'drop_id' => $drop->meta_id ), $meta );
		}

		return $prepared;
	}

	/**
	 * Create a new drop
	 * @param  object $payload Decoded JSON payload
	 * @return mixed  int of freshly created drop on success or false on failure
	 */
	function create_drop( $payload ) {
		global $wpdb;
		// Array to hold additional per drop properties
		$extra = array();
		if ( (int) $payload->post_id != 0 ) {
			$drop = array(
				'type' => $payload->type,
				'content' => wp_filter_post_kses( $payload->content ),
				'width' => (int) $payload->width,
				'column' => (int) $payload->column,
				'row' => (int) $payload->row
			);
			switch ( $payload->type ) {
				case 'static_html':
				case 'single':
					add_post_meta( (int) $payload->post_id, '_drop', $drop );
					$meta_id = $wpdb->get_var(
						$wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id=%s AND meta_key='_drop' ORDER BY meta_id DESC LIMIT 1", $payload->post_id ) );

					if ( $payload->type == 'single' ) {
						$post = get_post( $payload->post_id, 'ARRAY_A' );
						$extra['post_title'] = $post['post_title'];
					}

					return json_encode( array( 'meta_id' => (int) $meta_id ) + $extra );
				break;
				default:
					return false;
			}
		}
		return false;
	}

	function get_drop( $payload ) {

	}

	/**
	 * Prototype of updating a drop
	 *
	 * @todo Add all the kinds of checks
	 * @param  [type] $payload [description]
	 * @return [type]          [description]
	 */
	function update_drop( $payload ) {
		$drop = array(
			'type' => $payload->type,
			'content' => wp_filter_post_kses( $payload->content ),
			'width' => (int) $payload->width,
			'column' => (int) $payload->column,
			'row' => (int) $payload->row
		);
		update_metadata_by_mid( 'post', $payload->drop_id, $drop, $meta_key = false );
	}

	function update_collection( $drops = array() ) {
		foreach( $drops as $drop ) {
			$this->update_drop( $drop );
		}
	}

	/**
	 * Remove the drop and clear the cache
	 * @param  int $drop_id meta_id
	 * @param  int $post_id [description]
	 * @return bool result
	 */
	function delete_drop( $drop_id, $post_id ) {
		global $wpdb;
		$result = (bool) $wpdb->delete( "$wpdb->postmeta", array( 'meta_id' => (int) $drop_id ) );
		if ( $result ) {
			wp_cache_delete( $post_id, 'post_meta' );
		}
		return $result;
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
		global $wp_version;
		// Bust cache for dev
		$screen = get_current_screen();
		// Bail if we're somewhere else besides layout editor
		if ( $screen->base != 'post' || $screen->post_type != 'di-layout' )
			return;

		$rnd = mt_rand( 100, 10000 );

		// @todo Test $wp_version < 3.6
		if ( version_compare( floatval( $wp_version ), '3.6' ) == -1  ) {
			wp_deregister_script( 'backbone' );
			wp_register_script( 'backbone', DROP_IT_URL . 'lib/vendor/backbone.js', array( 'jquery', 'underscore' ), $rnd, true );
		}
		wp_enqueue_script( 'di-bb-drop-model', DROP_IT_URL . 'lib/js/models/drop.js', array( 'jquery', 'backbone' ), $rnd, true );
		wp_enqueue_script( 'di-bb-drop-collection', DROP_IT_URL . 'lib/js/collections/drops.js', array( 'jquery',  'backbone' ), $rnd, true );
		wp_enqueue_script( 'di-bb-drop-view', DROP_IT_URL . 'lib/js/views/drop.js', array( 'jquery',  'backbone' ), $rnd, true );
		wp_enqueue_script( 'di-bb-drops-view', DROP_IT_URL . 'lib/js/views/drops.js', array( 'jquery',  'backbone' ), $rnd, true );
		wp_enqueue_script( 'drop-gridster', DROP_IT_URL . 'lib/vendor/gridster/jquery.gridster.with-extras.min.js', array( 'jquery', 'backbone', 'jquery-ui-autocomplete' ), $rnd, true );
		wp_enqueue_script( 'drop-it-ui', DROP_IT_URL . 'lib/js/drop-it.js', array( 'jquery',  'backbone', 'jquery-ui-autocomplete' ), $rnd, true );
		wp_enqueue_style( 'drop-it', DROP_IT_URL . 'lib/css/drop-it.css' );
		wp_enqueue_style( 'drop-it-gridster-style', DROP_IT_URL . 'lib/vendor/gridster/jquery.gridster.min.css' );
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

	/**
	 * Get drops meta data, format it, and return
	 * @param  int $zone_id Drop It Zone post_id
	 * @return array
	 */
	function get_drops_for_zone( $zone_id ) {
		// Bail if $zone_id is malformed
		if ( (int) $zone_id === 0 )
			return false;

		$drops = get_post_meta( $zone_id, '_drop' );

		return $drops;
	}

	function get_zone_id_by_slug( $slug ) {
		$zone = get_posts( array(
			'post_name' => $slug,
			'post_type' => 'di-layout',
			'posts_per_page' => 1,
			'post_status' => 'any,'
		) );

		if ( !isset( $zone[0] ) )
			return false;

		return $zone[0]->ID;
	}

	/**
	 * @param zone
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	function _render_shortcode( $atts ) {
		extract( shortcode_atts( array(
			// zone slug
			'zone' => '',
		), $atts ) );

		// Bail if no zone is set
		if ( empty( $zone ) )
			return;

		$zone_id = $this->get_zone_id_by_slug( $zone );

		// Bail if there's no drop it zone
		if ( ! $zone_id )
			return;

		$zone_drops = $this->get_drops_for_zone( $zone_id );

		// Bail if there's no drops for the zone
		if ( empty( $zone_drops ) )
			return;

		return $this->_render_drops( $zone_drops );
	}


	/**
	 * Parse and return template for each drop
	 * @param  array  $drops Drops to render
	 * @return string Processed HTML
	 */
	function _render_drops( $drops = array() ) {
		ob_start();
		foreach( $drops as $drop ) {
			if ( !isset( $this->drops[ $drop['type'] ] ) )
				continue;

			$drop_instance = $this->drops[ $drop['type'] ];

			$this->twig->render( $drop_instance->template, $drop );
		}
		return ob_get_clean();
	}
}

/**
 * Just a convinience wrapper
 * @param  [type] $zone_id [description]
 * @return [type]          [description]
 */
function di_get_drops_for_zone( $zone_id ) {
	global $drop_it;
	$drops = $drop_it->get_drops_for_zone( $zone_id );
}

global $drop_it;
$drop_it = new Drop_It;