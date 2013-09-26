<?php
class Static_Html_Drop_It_Drop extends Drop_It_Drop {
	static $_id = 'static_html';
	function __construct( $label = 'Static HTML', $template = 'static_html', $options = array() ) {
		parent::__construct( self::$_id, $label, $template, $options );
	}

	/**
	 * Render inside shortcodes if any
	 *
	 * @param [type]  $drop [description]
	 * @return [type]       [description]
	 */
	function prepare_data( $drop = array() ) {
		$drop['title'] = do_shortcode( $drop['title'] );
		$drop['data'] = do_shortcode( $drop['data'] );
		return $drop;
	}

	/**
	 * Callback to render admin JS template
	 */
	function action_di_create_drop_templates() {
?>
<script type="text/template" id="static_html_create_drop_template">
<div class="drop-input-wrapper">
	<label>HTML/Shortcodes</label>
	<input type="text" name="title" id="title" placeholder="Title">
	<textarea name="data" id="data" placeholder="Enter Your HTML data"></textarea>
</div>
</script>
<?php
	}

	function action_di_edit_drop_templates() {
?>
<script type="text/template" id="static_html_drop_template">
		<div class="widget-inside">
			<p>
			Title: <strong> <%= title %> </strong><br /><br />
			<%= data %> <br />
			</p>

		</div>
		<div class="widget-inside-edit">
		<p>Title:</p>
		<input type="text" class="drop-single-title" name="title" value="<%= title %>" />
		<p>Text:</p>
		<textarea name="data" class="drop-single-data"><%= data %></textarea>
		</p>

		</div>
</script>
<?php
	}
}
