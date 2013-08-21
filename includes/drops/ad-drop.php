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

	function action_di_create_drop_templates() {
?>
	<script type="text/template" id="ad_create_drop_template">
	<div class="drop-input-wrapper">
		<p>Add an advertisement module</p>
	</div>
	</script>
<?php
	}
}
