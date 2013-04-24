/**
 * Admin UI
 */
window.app = window.app || {};
var app = window.app;
(function( $, window ) {
	// Just temporary dev mocks
    var drops = [
        { index: '1', type: 'static' },
        { index: '2', type: 'loop' }
    ];
   // Display admin UI
   new app.DropProtoView();

   // Populate existing drops
   new app.DropsView( drops );
})(jQuery, window);