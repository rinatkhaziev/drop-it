/**
 * Admin UI
 */
var DI_Drops = { Views: {}};
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



	// var answers = new DI_Drops.Collection( [ { answer: 'test', drop_id: 555 }, { answer: 'tedst', drop_id: 4356 }  ] );
 //    var selectElem = new DI_Drops.Views.Select({ collection:answers, el : $('#answerSelect select') });
 //    var inputs = new DI_Drops.Views.Inputs({ collection:answers, el:$('#answerInputs') });


})( jQuery, window );

