<?php
/*
 * @todo unfuckup the mess:
 *
 * Move all drop templates to their classes
 * Unhardcode
 *
 */
?>

<script type="text/template" id="static_html_drop_template">
	<div class="di-drop di-drop-collapsed widget">
		<input type="hidden" name="drop_id" value="<%= drop_id %>" />
		<input type="hidden" name="column" value="<%= column %>" />
		<input type="hidden" name="row" value="<%= row %>" />
		<div class="widget-top">
			<div class="widget-title">
				<h4>
					<% switch( type ) {
						case 'static_html':
						%> Static HTML <%
						break;
						case 'single':
						%> Single Post <%
						break;
					}
					%>

				</h4>
			</div>
		</div>
		<div class="widget-inside">
			<p>
			 	<% switch( type ) {
					case 'static_html':
					%> Title: <strong> <%= title %> </strong> <br /> 
					<%= data %> <%
					break;
					case 'single':
					%> Post title: <strong> <%= post_title %> </strong> <%
					break;
				}
				%>
			</p>

			</ul>
			<button class="button button-primary drop-expand">Edit</button>
			<button class="button button-secondary right drop-delete">Delete</button>
		</div>
	</div>
</script>
<script type="text/template" id="query_drop_template">
	<strong></strong>
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

<script type="text/template" id="static_html_create_drop_template">
<div class="drop-input-wrapper">
	<label>HTML/Shortcodes</label>
	<input type="text" name="title" id="title" placeholder="Title"></textarea>
	<textarea name="data" id="data" placeholder="Enter Your HTML data"></textarea>
</div>
</script>

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

<script type="text/template" id="query_create_drop_template">

</script>

<script type="text/template" id="autocompleteDropTemplate">
<div class="drop-input-wrapper">
	<a><%= post_title %></a>
	<input type="hidden" value="<%= post_id %>" name="data" />
</div>
</script>

<div id="create-drop"></div>
<div id="drops" class="gridster"></div>
