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
						case 'ad':
						%> Advertisement <%
						break;
						case 'search_box':
						%> Search Box <%
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
					%> Title: <strong> <%= title %> </strong><br /><br />
					 <%= data %> <br />
					 <button class="button button-primary drop-expand">Edit</button>
					 <button class="button button-secondary right drop-delete">Delete</button>
					<%
					break;
					case 'single':
					%> Post title: <strong> <%= post_title %> </strong><br />
					<button class="button button-secondary right drop-delete">Delete</button>
					<%
					break;
					case 'ad':
					%><strong> Advertisement </strong><br />
					<button class="button button-secondary right drop-delete">Delete</button>
					<%
					break;
					case 'search_box':
					%><strong> Search Box </strong><br />
					<button class="button button-secondary right drop-delete">Delete</button>
					<%
					break;
				}
				%>
			</p>

			</ul>
		</div>
		<div class="widget-inside-edit">
			<p>
			 	<% if( type=="static_html" ) {
					%>
					<p>Title:</p>
					<input type="text" class="drop-single-title" name="title" value="<%= title %>" />
					<p>Text:</p>
					<textarea name="data" class="drop-single-data"><%= data %></textarea>
					<button class="button button-primary drop-save">Save</button>
					<button class="button button-secondary right drop-delete">Delete</button>
					 <%
					}
					%>
			</p>
			</ul>
		</div>
	</div>
</script>

<script type="text/template" id="ad_drop_template">
	<strong>2</strong>
</script>

<script type="text/template" id="search_box_drop_template">
	<strong>3</strong>
</script>

<script type="text/template" id="query_drop_template">
	<strong>4</strong>
</script>

<script type="text/template" id="single_drop_template">
	<strong>5</strong>
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
?>

<script type="text/template" id="autocompleteDropTemplate">
<div class="drop-input-wrapper">
	<a><%= post_title %></a>
	<input type="hidden" value="<%= post_id %>" name="data" />
</div>
</script>

<div id="create-drop"></div>
<div id="drops" class="gridster"></div>
