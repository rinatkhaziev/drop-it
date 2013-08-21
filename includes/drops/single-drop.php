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
	 * Add post itself for rendering (Front End)
	 *
	 * @param [type]  $drop [description]
	 * @return [type]       [description]
	 */
	function prepare_data( $drop = array() ) {
		$post = get_post( $drop['data'] );
		$drop['post'] = $post;
		return $drop;
	}

	// Just for the sake of UI friendliness adding post_title and post_excerpt to returned data;
	function add_extra_info_for_ui( $meta ) {
		// Cast to array if it's an object
		$meta = (array) $meta;
		$post = (array) get_post( $meta['data'], 'ARRAY_A' );

		if ( !empty( $post ) )
			$meta = array_merge( $meta,
				array(
					'post_title' =>  $post['post_title'],
					'post_excerpt' => $post['post_excerpt'],
				) );

		return $meta;
	}

	function action_di_create_drop_templates() {
?>
		<script type="text/template" id="single_create_drop_template">
		<div class="drop-input-wrapper">
			<label>Post Title</label>
			<input type="text" name="post_search" class="drop-name-autocomplete" placeholder="Find a post by title" />
		</div>

		<div class="drop-input-wrapper">
			<label class="di-found-post"></label>
			<input type="hidden" name="data" class="di-found-data" />
		</div>
		</script>
<?php
	}
}
