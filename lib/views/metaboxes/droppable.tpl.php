<script type="text/template" id="dropTemplate">
    <label for="<%= drop_id %>">Widget type:</label><br/>
    <select>
	<% _.each( types, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %> 
    </select>
</script>

<script type="text/template" id="dropProtoTemplate">
	<form>
    <label for="">Widget type:</label><br/>
    <select>
	<% _.each( types, function( value, key, types ) { %> <option value="<%= key %>"><%= value %></option><% } ); %> 
    </select>
    <textarea>dsdasd</textarea>
    </form>
</script>

<div id="create-drop"></div>

<div id="drops"></div>

<div class="di-droppable-wrapper">
	<div class="di-droppable-area">
<!-- 		<div class="di-drop">2</div>
		<div class="di-drop">3</div>
		<div class="di-drop">4</div>

		<div class="di-drop"></div>
		<div class="di-drop"></div>
		<div class="di-drop"></div>

		<div class="di-drop"></div>
		<div class="di-drop"></div>
		<div class="di-drop"></div>

		<div class="di-drop"></div>
		<div class="di-drop"></div>
		<div class="di-drop"></div> -->
	</div>
	<div class="clear"></div>
</div>