/**
 * Admin UI
 */
window.DropItApp = window.DropItApp || {};
var DropItApp = window.DropItApp;
(function( $, window ) {
	// Just temporary dev mocks
    var drops = [
        { drop_id: '1', type: 'static', tpl: 'static' },
        { drop_id: '2', type: 'loop', tpl: 'loop' }
    ];
   // Display admin UI
   new DropItApp.DropProtoView();

   // Populate existing drops
   new DropItApp.DropsView( drops );
})(jQuery, window);