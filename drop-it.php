<?php
/*
Plugin Name: Drop It
Plugin URI: http://digitallyconscious.com
Description: Easy drag and drop layout management for WordPress
Author: Rinat Khaziev
Version: 0.1
Author URI: http://digitallyconscious.com

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

// Bootstrap the stuff
require_once DROP_IT_ROOT . '/includes/class-drop-it-drop.php';

// Do not init Twig until it passes VIP Review
// require_once DROP_IT_ROOT . '/includes/class-wp-twig.php';

class Drop_It {

	public $drops;
	public $key = 'drop-it';
	public $manage_cap;
	// public $twig;

	/**
	 * Instantiate the plugin, hook the filters and actions
	 */
	function __construct() {
		// Create custom post types
		add_action( 'init', array( $this, 'action_init' ) );

		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add some JS vars for admin UI
		add_action( 'admin_head', array( $this, 'action_admin_head' ) );

		// Add meta boxes
		add_action( 'add_meta_boxes', array( $this, 'action_add_meta_boxes' ) );

		// @todo Implement
		add_action( 'edit_form_advanced', array( $this, 'action_enable_tiny' ) );

		// Initial setup
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		// Route AJAX actions
		add_action( 'wp_ajax_drop_it_ajax_route', array( $this, '_route_ajax_actions' ) );
		add_action( 'wp_ajax_drop_it_ajax_search', array( $this, '_ajax_search' ) );

		// Shortcode and template tag
		add_shortcode( 'drop-it-zone', array( $this, '_render_shortcode' ) );
		add_action( 'drop-it-zone', array( $this, '_do_render_action' ) );

		/**
		 * Init Twig
		 *
		 * Configuration filter: 'di_drop_templates_paths':
		 * Array of folders with Twig templates
		 *
		 * Disabled until Twig review
		 *
		 * @param array   list of folders to look for drop templates
		 *
		 */
/*		if ( !is_admin() )
			$this->twig = new WP_Twig( apply_filters( 'di_drop_templates_paths', array(
						DROP_IT_ROOT . '/lib/views/templates/',
						get_stylesheet_directory() . '/drops/templates/'
				),
			false ) );*/
	}

	/**
	 * Registering available drops
	 *
	 * @param array   $drops [description]
	 * @return [type]        [description]
	 */
	function register_drops() {
		$drops_path =  DROP_IT_ROOT . '/includes/drops/';

		// Scan drops folder for bundled drops
		$class_files = array_diff( scandir( $drops_path ), array( '..', '.' ) );

		foreach ( $class_files as $drop ) {
			// Full path to class file
			$class_file = $drops_path . $drop;
			include_once $class_file;
			// Get rid of .php part
			$class_name = str_replace( '.php' , '', $drop );

			// Follow class naming convention
			// E.g. class name for static-html-drop.php should be Static_Html_Drop_It_Drop
			$class_name_arr = array_map( 'ucfirst', explode( '-', $class_name ) );
			$class_name = implode( '_', $class_name_arr ) . '_It_Drop';
			if ( is_subclass_of( $class_name, 'Drop_It_Drop' ) )
				$this->drops[ $class_name::$_id ] = new $class_name;
		}

		$this->drops = apply_filters( 'di_registered_drop_types', $this->drops );
	}


	/**
	 * Init TinyMCE for textareas
	 *
	 * @todo Implement
	 * @return [type] [description]
	 */
	function action_enable_tiny() {
		//wp_editor( '', 'staticcontent' );
	}

	/**
	 * Register drop and layout post types
	 *
	 * @return [type] [description]
	 */
	function action_init() {
		// i18n
		load_plugin_textdomain( 'drop-it', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Drop It Zone custom post type
		register_post_type( 'di-zone', array(
				'labels' => array( 'name' => _x( 'Drop It Zones', 'Drop layout post type plural name', 'drop-it' ) ),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => _x( 'di-zone', 'Drop layout slug', 'drop-it' ) ),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'thumbnail' ),
			) );

		// Capabilities needed to be able to manage Drop It
		$this->manage_cap = apply_filters( 'di_manage_cap', 'edit_others_posts' );

		// Must register drops after we register our post type
		$this->register_drops();
	}

	/**
	 * Add meta boxes for drop it zones
	 */
	function action_add_meta_boxes() {
		$suffix = !isset( $_GET['post'] ) ? '_new_post' : '';
		add_meta_box(
			"drop_it_layout_droppable{$suffix}",
			__( 'Drop It Here!', 'drop-it' ),
			array( $this, '_metabox' ),
			'di-zone',
			'normal',
			'default',
			array( 'view' => "droppable{$suffix}" )
		);
	}

	/**
	 * Metabox callback
	 * @param  [type] $post_id [description]
	 * @param  [type] $metabox [description]
	 * @return [type]          [description]
	 */
	function _metabox( $post_id, $metabox ) {
		extract( $metabox['args'] );
		$this->_render( 'metaboxes/' . $view );
	}

	/**
	 * Some global JS vars
	 * @return [type] [description]
	 */
	function action_admin_head() {
		$screen = get_current_screen();
		if ( !isset( $_GET['post'] ) || $screen->base != 'post' ||  $screen->post_type != 'di-zone' )
			return;

		// Sanitize post id
		$zone_id = absint( $_GET['post'] );

		// Get teh drops
		$drops = $this->get_drops_for_layout( $zone_id );

		// Array of post IDs to exclude from autocomplete search
		$exclude = array();

		foreach ( $drops as $drop ) {
			// Add the post id to array of posts that should be excluded in autocomplete search
			if ( $drop['type'] == 'single' ) {
				$exclude[] = (int) $drop['data'];
			}
		}

		$exclude = json_encode( $exclude );
		?>

		<script type="text/javascript">
			var DropIt = window.DropIt || {};
			DropIt.Admin = {};
			// All the drops for this layout
			DropIt.Admin.drops = <?php echo json_encode( $drops ); ?>;
			// Layout ID
			DropIt.Admin.layout_id = '<?php echo esc_js( $zone_id ) ?>';
			// Array of post IDs excluded from autocomplete search
			DropIt.Admin.autocomplete_exclude = <?php echo $exclude ?>;
			// Array of registered drop types
			DropIt.Admin.drop_types = <?php echo json_encode( $this->drops ) ?>;
			DropIt.Admin.nonce = '<?php echo wp_create_nonce( DROP_IT_FILE_PATH ) ?>';
		</script>
		<?php
	}

	/**
	 * Route AJAX actions to CRUD methods
	 *
	 * @return [type] [description]
	 */
	function _route_ajax_actions() {
		// Read and decode JSON payload fro
		$payload = json_decode( file_get_contents( 'php://input' ) );

		if ( !empty( $payload ) && isset( $payload->action ) ) {

			$payload->title = sanitize_title($payload->title);
			$payload->data = wp_filter_post_kses($payload->data);

			switch ( $payload->action ) {
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
			switch ( $_REQUEST['mode'] ) {
			case 'update_collection':
				$this->update_collection( $payload );
				break;
			}
			exit;
		}
	}

	/**
	 * AJAX Autocomplete callback
	 *
	 * @return json encoded array of found posts
	 */
	function _ajax_search() {

		// Bail if search term is empty
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
		foreach ( $posts as $post ) {
			$return[] = (object) array( 'post_id' => $post->ID, 'post_title' => $post->post_title, 'post_date' => $post->post_date );
		}
		echo json_encode( $return );
		exit;
	}

	/**
	 * Handles security checks
	 *
	 * @param string nonce
	 * @return bool
	 *
	 */
	function _check_perms_and_nonce( $nonce = '') {
		return current_user_can( $this->manage_cap ) && wp_verify_nonce( $nonce, DROP_IT_FILE_PATH );
	}

	/**
	 * Construct and return array of drops as expected by Backbone.js model
	 *
	 * @param [type]  $post_id [description]
	 * @return [type]          [description]
	 */
	function get_drops_for_layout( $post_id ) {
		global $wpdb;

		// We need meta id, so wpdb query it is
		// Should be fine, the function gets called only in admin
		$drops = $wpdb->get_results(
			$wpdb->prepare( "select * from $wpdb->postmeta where post_id=%s and meta_key='_drop'", $post_id )
		);

		$prepared = $extra = array();

		// Prepare each drop for rendering
		foreach ( (array) $drops as $drop ) {
			// Reset extra
			$extra = array();
			$meta  = (array) unserialize( $drop->meta_value );

			// Add any extra data for UI
			if ( is_callable( array( $this->drops[ $meta['type'] ], 'add_extra_info_for_ui' ) ) )
				$extra = (array) $this->drops[ $meta['type'] ]->add_extra_info_for_ui( $meta );

			$prepared[] = array_merge( array( 'drop_id' => $drop->meta_id ), $meta, $extra );
		}

		return $prepared;
	}

	/**
	 * Sort drops according to their grid coords
	 *
	 * @param [type]  $drops [description]
	 * @return [type]        [description]
	 */
	function sort_drops( $drops = array() ) {

		// Bail if we don't have any drops
		if ( empty($drops ) )
			return $drops;

		$prepared = array();

		// Sort drops by rows
		foreach ( $drops as $drop ) {
			$prepared[ $drop['row'] ][] = $drop;
		}

		// Sort by column
		foreach ( $prepared as $index => $prep ) {
			usort( $prepared[ $index ], function( $a, $b ) {  return $a['column'] - $b['column']  ;} );
		}

		// Flatten it
		$prepared = call_user_func_array( 'array_merge', $prepared );

		return $prepared;
	}

	/**
	 * Create a new drop
	 *
	 * @param object  $payload Decoded JSON payload
	 * @return mixed  int of freshly created drop on success or false on failure
	 */
	function create_drop( $payload ) {
		global $wpdb;
		// Array to hold additional per drop properties
		$extra = array();

		if ( (int) $payload->post_id != 0 ) {
			$drop = array(
				'type' => $payload->type,
				'title' => $payload->title,
				'data' => $payload->data,
				'width' => (int) $payload->width,
				'column' => (int) $payload->column,
				'row' => (int) $payload->row
			);

			switch ( $payload->type ) {
			case 'static_html':
			case 'single':
			case 'ad':
				add_post_meta( (int) $payload->post_id, '_drop', $drop );
				$meta_id = $wpdb->get_var(
					$wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id=%s AND meta_key='_drop' ORDER BY meta_id DESC LIMIT 1", $payload->post_id ) );

				if ( $payload->type == 'single' ) {
					$post = get_post( $payload->data, 'ARRAY_A' );
					$extra['post_title'] = $post['post_title'];
				}

				// Add any extra data for UI
				if ( is_callable( array( $this->drops[ $payload->type ], 'add_extra_info_for_ui' ) ) )
					$extra = (array) $this->drops[ $payload->type ]->add_extra_info_for_ui( $payload );

				return json_encode( array( 'meta_id' => (int) $meta_id ) + $extra );
				break;
			default:
				return false;
			}
		}
		return false;
	}

	function get_drop( $payload ) {
		// @todo Implement
	}

	/**
	 * Update a drop
	 *
	 * @todo delegate to Drop classes
	 * @param [type]  $payload [description]
	 * @return [type]          [description]
	 */
	function update_drop( $payload ) {
		$drop = array(
			'type' => $payload->type,
			'title' => $payload->title,
			'data' => $payload->data,
			'width' => (int) $payload->width,
			'column' => (int) $payload->column,
			'row' => (int) $payload->row
		);
		update_metadata_by_mid( 'post', $payload->drop_id, $drop, $meta_key = false );
	}

	/**
	 * Update a collection of drops
	 * @param  array  $drops [description]
	 * @return [type]        [description]
	 */
	function update_collection( $drops = array() ) {
		foreach ( $drops as $drop )
			$this->update_drop( $drop );
	}

	/**
	 * Remove the drop and clear the cache
	 *
	 * @param int     $drop_id meta_id
	 * @param int     $post_id [description]
	 * @return bool result
	 */
	function delete_drop( $drop_id, $post_id ) {
		$result = (bool) delete_metadata_by_mid( 'post', $drop_id );

		return $result;
	}

	/**
	 * Do activation
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
	 * Render backend view
	 *
	 * @param string  $view_slug
	 * @return [type]            [description]
	 */
	function _render( $view_slug = '', $pre = '<div class="wrap">', $after = '</div>' ) {
		ob_start();
		$file = DROP_IT_ROOT .'/lib/views/' . $view_slug . '.php';
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
		if ( $screen->base != 'post' || $screen->post_type != 'di-zone' )
			return;

		// @todo Test $wp_version < 3.6
		if ( version_compare( floatval( $wp_version ), '3.6' ) == -1  ) {
			wp_deregister_script( 'backbone' );
			wp_register_script( 'backbone', DROP_IT_URL . 'lib/vendor/backbone.js', array( 'jquery', 'underscore' ), false, true );
		}

		// Models
		wp_enqueue_script( 'di-drop-drop-model', DROP_IT_URL . 'lib/js/models/drop.js', array( 'jquery', 'backbone' ), false, true );
		wp_enqueue_script( 'di-drop-dropproto-model', DROP_IT_URL . 'lib/js/models/dropproto.js', array( 'jquery', 'backbone' ), false, true );

		// Collection
		wp_enqueue_script( 'di-drop-collection', DROP_IT_URL . 'lib/js/collections/drops.js', array( 'jquery',  'backbone' ), false, true );

		// Views
		wp_enqueue_script( 'di-drop-view', DROP_IT_URL . 'lib/js/views/drop.js', array( 'jquery',  'backbone' ), false, true );
		wp_enqueue_script( 'di-dropproto-view', DROP_IT_URL . 'lib/js/views/dropproto.js', array( 'jquery',  'backbone' ), false, true );
		wp_enqueue_script( 'di-drops-view', DROP_IT_URL . 'lib/js/views/drops.js', array( 'jquery',  'backbone' ), false, true );

		// Gridster
		wp_enqueue_script( 'di-gridster', DROP_IT_URL . 'lib/js/vendor/gridster/jquery.gridster.with-extras.min.js', array( 'jquery', 'backbone', 'jquery-ui-autocomplete' ), false, true );

		// Init
		wp_enqueue_script( 'di-ui', DROP_IT_URL . 'lib/js/drop-it.js', array( 'jquery',  'backbone', 'jquery-ui-autocomplete' ), false, true );
		wp_enqueue_style( 'drop-it', DROP_IT_URL . 'lib/css/drop-it.css' );
		//wp_enqueue_style( 'di-gridster-style', DROP_IT_URL . 'lib/js/vendor/gridster/jquery.gridster.min.css' );
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
	 *
	 * @param int     $zone_id Drop It Zone post_id
	 * @return array
	 */
	function get_drops_for_zone( $zone_id ) {
		// Bail if $zone_id is malformed
		if ( (int) $zone_id === 0 )
			return false;

		$drops = get_post_meta( $zone_id, '_drop' );

		return $drops;
	}

	/**
	 * Get zone id by slug
	 *
	 * @param  string $slug zone slug
	 * @return (bool|int)    zone id
	 */
	function get_zone_id_by_slug( $slug = '' ) {

		$zone = get_posts( array(
				'name' => $slug,
				'post_type' => 'di-zone',
				'posts_per_page' => 1,
				'post_status' => 'any'
			) );

		if ( !isset( $zone[0] ) )
			return false;

		return $zone[0]->ID;
	}

	/**
	 * do_action callback
	 *
	 * @param [type]  $atts [description]
	 * @return [type]       [description]
	 */
	function _do_render_action( $atts ) {
		$atts = apply_filters( 'drop_it_zone_action_atts', $atts );
		echo $this->_render_shortcode(  $atts ) ;
	}

	/**
	 * Shortcode callback
	 *
	 * @param  array  $atts shortcode attributes
	 * @return string rendered shortcode
	 */
	function _render_shortcode( $atts ) {

		extract( shortcode_atts( array(
					// Zone slug
					'zone' => '',
				), $atts ) );

		// Bail if no zone is set
		if ( empty( $zone ) )
			return;

		$zone_id = $this->get_zone_id_by_slug( $zone );

		// Bail if there's no zone with this slug
		if ( ! $zone_id )
			return;

		// Get dem drops
		$zone_drops = $this->get_drops_for_zone( $zone_id );

		// And sort dem drops
		$zone_drops = $this->sort_drops( $zone_drops );

		// Bail if there's no drops for the zone
		if ( empty( $zone_drops ) )
			return;

		// At last render
		return $this->_render_drops( $zone_drops );
	}

	/**
	 * Parse and return template for each drop
	 *
	 * @param array   $drops Drops to render
	 * @return string Processed HTML
	 */
	function _render_drops( $drops = array() ) {
		ob_start();
		foreach ( $drops as $drop ) {

			// Skip to the next drop if this one doesn't have matching Drop_It_Drop or malformed
			if ( !isset( $drop['type'] ) || !isset( $this->drops[ $drop['type'] ] ) )
				continue;

			// Convenenience var
			$di = $this->drops[ $drop['type'] ];

			// Pass prepared data to render the template.
			// prepare_data should be defined in a child of Drop_It_Drop class
			// Twig is disabled for v0.1
			//$this->twig->render( $di->template, $di->prepare_data( $drop ) );

			// Instead using temporary rendering function (or maybe provide it as alternative for people who don't want to mess with Twig)
			$this->render( $di->template, $di->prepare_data( $drop ) );

		}
		return ob_get_clean();
	}

	/**
	 * v0.1 version of rendering method
	 * @param  string $template_name [description]
	 * @param  array  $drop          [description]
	 * @return [type]                [description]
	 */
	function render( $template_name = '', $drop_data = array() ) {
		// Declare global $drop to use in templates
		global $drop;
		$drop = $drop_data;

		// Try to include template located in theme first
		$theme_tpl = locate_template( "drops/templates/{$template_name}.php" );
		if ( isset( $drop['post'] ) ) {
			// Setup global $post if it's a single
			global $post;
			$post = $drop['post'];
			setup_postdata( $post );
		}

		if ( !empty( $theme_tpl ) ) {
			load_template( $theme_tpl, false );
		// Then try to include the one bundled with plugin
		} else {
			$plugin_tpl = DROP_IT_ROOT . "/lib/views/templates/{$template_name}.php";
			if ( file_exists( $plugin_tpl ) )
				load_template( $plugin_tpl, false );
		}
		wp_reset_postdata();
		return;
	}
}

/**
 * Just a convenience wrapper
 *
 * @param [type]  $zone_id [description]
 * @return [type]          [description]
 */
function di_get_drops_for_zone( $zone_id ) {
	global $drop_it;
	$drops = $drop_it->get_drops_for_zone( $zone_id );
}

// Here we go and add some overhead
global $drop_it;
$drop_it = new Drop_It;