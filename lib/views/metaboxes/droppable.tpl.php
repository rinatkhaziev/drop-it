<script type="text/template" id="testTemplate">
    <label for="<%= answer_id %>"><%= index %>:</label>
    <input id="<%= answer_id %>" class="answers" size="30" type="text" name="<%= answer_id %>" value="<%= answer %>" placeholder="Answer for Question <%= index %> Here">
    <button disabled="true">Save</button>
</script>

<p>Enter the Answers below</p>
<div id="answerInputs"></div>
<div id="answerSelect">
    <span>Correct Answer:</span>
    <select></select>
</div>
<p>
    <input name="save" type="submit" class="button button-primary button-small" value="Save all">
</p>


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