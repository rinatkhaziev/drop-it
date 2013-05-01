/**
 * Admin UI
 */
window.DropItApp = window.DropItApp || {};
var DropItApp = window.DropItApp;
(function( $, window ) {
	// Just temporary dev mocks

   // Display admin UI
   new DropItApp.DropProtoView();

   // Populate existing drops
   new DropItApp.DropsView( window.drops );
})(jQuery, window);

/*@projectDescription jQuery Serialize All
*     Serialize All (and not just forms!)
* @author [for serialize to uri] from serializeAnything plugin
*     by Bramus! (Bram Van Damme))
* @website: http://www.bram.us/  license: BSD
* @author [serialize to json array]
*    by jpalala
* @modified 2012-08-08
*/

(function($) {
    $.fn.serializeAll = function(type) {
        if(type == 'uri') {
            var toReturn    = [];
            var els         = $(this).find(':input').get();
            $.each(els, function() {
                if (this.name && !this.disabled && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) {
                    var val = $(this).val();
                    toReturn.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( val ) );
                }
            });
            return toReturn.join("&").replace(/%20/g, "+");
        }

        if(type == 'json') {
            var els = $(this).find(':input').get();
            var json = {};

            $.map($(els).serializeArray(), function(n, i) {
                json[n['name']] = n['value'];
            });
         return json;
        };
   };
})(jQuery);