/**
 * Admin UI
 */

( function( $, window, undefined ) {
	var document = window.document;

	var DropIt = function() {
		// Reference to the object
		var SELF = this;
		// Cached DOM elements
		var UI = {};

		var _init = function() {
			UI.droppable_meta_box = $('#drop_it_layout_droppable');
			//$('#normal-sortables').prepend( UI.droppable_meta_box );
			UI.droppable_area = $('.di-droppable-area', UI.droppable_meta_box );
			// Display settings 
			UI.droppable_area.sortable( { grid: [4, 3], connectWith: '.meta-box-sortables', containment: 'parent' } ).disableSelection();
			UI.droppable_area.append("<div class='di-drop'>b</div>");
		};
		// Init the stuff
		_init();
	};
	window.DropIt = new DropIt();

})( jQuery, window );