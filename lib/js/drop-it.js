/**
 * Admin UI
 */
window.DropItApp = window.DropItApp || {};



(function( $, window ) {

// Get drop types _.js mixin
  _.mixin({
    get_drop_types: function( list ) {
      var types = {};
      _.each( list, function( value, key, list ) {
        types[value.id] = value.label;
      } );
      return types;
    }
  });

  // Init gridster before instantiating drops
  DropItApp.gridster = $("#drops").gridster({
    avoid_overlapped_widgets: true,
    autogenerate_stylesheet: true,
    widget_selector: '.drop-item',
    widget_margins: [20, 20],
    widget_base_dimensions: [220, 220],
    shift_larger_widgets_down: true,
    min_cols: 3,
    min_rows: 3,
    max_cols: 3,
    // @todo probably move it to backbone
    serialize_params: function( $w, wgd ) {
      var return_item = { column: wgd.col, row: wgd.row, width: wgd.size_x };
      // may be account for other values in the future
      var inputs =  $w.find('input[name="drop_id"]');
      $( inputs ).each( function( index ) {
        var key = this.name;
        return_item[key] = this.value;
      });
      return_item['post_id'] = window.drop_it_layout_id;
      return return_item;
    }
  }).gridster().data('gridster').generate_faux_grid(3,3);
  // Display admin UI
  new DropItApp.DropProtoView();

  // Populate existing drops
  new DropItApp.DropsView( window.drops );




})(jQuery, window);

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