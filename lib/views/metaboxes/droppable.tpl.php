<script type="text/template" id="dropTemplate">
    <label for="<%= drop_id %>">Widget type:</label>
    <select name="type">
	<% _.each( types, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %> 
    </select>
</script>

<script type="text/template" id="static_html-dropTemplate">
 <div class="di-drop di-drop-collapsed">
	<%= content %>
 </div>
 <button class="button button-primary drop-expand">Expand</button>
</script>
<script type="text/template" id="loop-dropTemplate">
	<strong>Awesome2</strong>
</script>

<script type="text/template" id="dropProtoTemplate">
    <label for="id">Drop type:</label>
    <select id="dropSelectTemplate" name="type">
	<% _.each( types, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %> 
    </select>
	<input type="hidden" name="action" value="save_drop" />
	<?php if ( isset( $_GET['post'] ) ): ?>
	<input type="hidden" name="post_id" value="<?php echo (int) $_GET['post'] ?>" />
	<?php endif; ?>
    <div id="varyOptionsForProto"></div>
</script>

<script type="text/template" id="static_html-createDropTemplate">
	<textarea name="content" id="staticcontent" placeholder="Enter Your HTML content"></textarea>
	<button class="button button-primary drop-add">Add It</button>
</script>

<script type="text/template" id="single-createDropTemplate">
	<input type="text" name="post_id" placeholder="Enter post id" />
	<button class="button button-primary drop-add">Add It</button>
</script>

<script type="text/template" id="loop-createDropTemplate">
	Im loop
</script>

<div id="create-drop"></div>

<div id="drops">

<div class="clear"></div>
</div>
<div class="clear"></div>