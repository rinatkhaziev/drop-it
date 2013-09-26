<?php
/*
 * @todo unfuckup the mess:
 *
 * Move all drop templates to their classes
 * Unhardcode
 *
 */
?>
<script type="text/template" id="common_drop_template">
	<div class="di-drop di-drop-collapsed widget">
		<input type="hidden" name="drop_id" value="<%= drop_id %>" />
		<input type="hidden" name="column" value="<%= column %>" />
		<input type="hidden" name="row" value="<%= row %>" />
		<div class="widget-top">
			<div class="widget-title">
				<h4>
            <% _.each( DropIt.Admin.drop_types, function( item ) { %> 
                <%= type === item.id ? item.label : '' %>
            <% }); %>
				</h4>
			</div>
		</div>
			<%
			// Pass model to a subtemplate 
			var html = _.template( jQuery('#' + template + '_drop_template').html(), this.model.toJSON() );
			%>
			<%= html %>
		<button class="button button-primary drop-save">Save</button>
		<button class="button button-primary drop-expand">Edit</button>
		<button class="button button-secondary right drop-delete">Delete</button>
	</div>
</script>

<script type="text/template" id="dropProtoTemplate">
<div class="drop-input-wrapper">
    <label for="dropSelectTemplate">Drop type:</label>
    <select id="dropSelectTemplate" name="type">
	<% _.each( types, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %>
    </select>
</div>
<div id="varyOptionsForProto"></div>

	<?php if ( isset( $_GET['post'] ) ): ?>
	<input type="hidden" id="drop_it_post_id" name="post_id" value="<?php echo (int) $_GET['post'] ?>" />
	<?php endif; ?>

		<button class="button button-primary drop-add">Add It</button>
</script>

<?php
// Instead of hardcoding create drop templates, register the action
// This should provide necessary flexibility
do_action( 'di_create_drop_templates' );
do_action( 'di_edit_drop_templates' );
?>

<script type="text/template" id="autocompleteDropTemplate">
<div class="drop-input-wrapper">
	<a><%= post_title %></a>
	<input type="hidden" value="<%= post_id %>" name="data" />
</div>
</script>

<div id="create-drop"></div>
<div id="drops" class="gridster"></div>
