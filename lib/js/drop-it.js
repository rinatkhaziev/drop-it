/**
 * Admin UI
 */
window.DropItApp = window.DropItApp || {};
var DropItApp = window.DropItApp;
DropItApp.gridster = {};
(function( $, window ) {
  // Display admin UI
  new DropItApp.DropProtoView();

  // Populate existing drops
  new DropItApp.DropsView( window.drops );

  DropItApp.gridster = $("#drops").gridster({
    avoid_overlapped_widgets: true,
    autogenerate_stylesheet: true,
    widget_selector: '.drop-item',
    widget_margins: [10, 10],
    widget_base_dimensions: [140, 140],
    shift_larger_widgets_down: false,
    min_cols: 3,
    min_rows: 3,
    max_cols: 3,
    max_rows: 3
  }).gridster().data('gridster').add_faux_cols(3).add_faux_rows(3);

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