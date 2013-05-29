<script type="text/template" id="static_html-dropTemplate">
 <div class="di-drop di-drop-collapsed">
 	<input type="hidden" name="drop_id" value="<%= drop_id %>" />
 	<input type="hidden" name="column" value="<%= column %>" />
 	<input type="hidden" name="row" value="<%= row %>" />
 	<ul>
	 	<li><strong>Drop type</strong>:
		<% switch( type ) {
			case 'static_html':
			%> Static HTML <%
			break;
			case 'single':
			%> Single Post <%
			break;
		}
		%>

	 	</li>
	 	<li><strong>Columns</strong>: <%= width %></li>
	 	<li></li>
	 	<li></li>
	 	<li><strong>Parameters</strong>:<br/>
	 	<% switch( type ) {
			case 'static_html':
			%> <%= content %> <%
			break;
			case 'single':
			%> Post title: <%= post_title %> <br/> <%
			%> Post ID: <%= content %> <%
			break;
		}
		%>
		</li>

	</ul>
 </div>
 <button class="button button-primary drop-expand">Edit</button>
 <button class="button button-secondary right drop-delete">Delete</button>
</script>
<script type="text/template" id="query-dropTemplate">
	<strong>Awesome2</strong>
</script>

<script type="text/template" id="dropProtoTemplate">
    <label for="dropSelectTemplate">Drop type:</label>
    <select id="dropSelectTemplate" name="type">
	<% _.each( types, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %>
    </select>

    <label for="dropWidthTemplate">Columns Span</label>
    <select id="dropWidthTemplate" name="width">
	<% _.each( {1:1, 2:2, 3:3}, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %>
    </select>

    <label for="dropColumnTemplate">Column</label>
    <select id="dropColumnTemplate" name="column">
	<% _.each( {1:1, 2:2, 3:3}, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %>
    </select>

    <label for="dropRowTemplate">Row</label>
    <select id="dropRowTemplate" name="row">
	<% _.each( {1:1, 2:2, 3:3}, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %>
    </select>

	<?php if ( isset( $_GET['post'] ) ): ?>
	<input type="hidden" id="drop_it_post_id" name="post_id" value="<?php echo (int) $_GET['post'] ?>" />
	<?php endif; ?>

    <div id="varyOptionsForProto"></div>
</script>

<script type="text/template" id="static_html-createDropTemplate">
	<textarea name="content" id="content" placeholder="Enter Your HTML content"></textarea>
	<button class="button button-primary drop-add">Add It</button>
</script>

<script type="text/template" id="single-createDropTemplate">
	<input type="text" name="post_search" class="drop-name-autocomplete" placeholder="Find a post by title" />

	<label class="di-found-post"></label>
	<input type="hidden" name="content" class="di-found-content" />

	<button class="button button-primary drop-add">Add It</button>
</script>

<script type="text/template" id="query-createDropTemplate">
	Im loop
</script>

<script type="text/template" id="autocompleteDropTemplate">
	<a><%= post_title %></a>
	<input type="hidden" value="<%= post_id %>" name="content" />
</script>



<div id="create-drop"></div>

<div id="drops" class="gridster">
</div>
<div class="clear"></div>