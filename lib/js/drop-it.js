/**
 * Admin UI
 */
var DropIt = window.DropIt || {};

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
  DropIt.gridster = $("#drops").gridster({
    avoid_overlapped_widgets: false,
    autogenerate_stylesheet: true,
    widget_selector: '.drop-item',
    widget_margins: [5, 5],
    widget_base_dimensions: [300, 250],
    shift_larger_widgets_down: true,
    min_cols: 3,
    min_rows: 3,
    max_cols: 3,
    max_rows: 6,

    serialize_params: function( $w, wgd ) {
      var return_item = { column: wgd.col, row: wgd.row, width: wgd.size_x };
      // may be account for other values in the future
      var inputs =  $w.find('input[name="drop_id"]');
      $( inputs ).each( function( index ) {
        var key = this.name;
        return_item[key] = this.value;
      });
      return_item['post_id'] = DropIt.Admin.layout_id;
      return return_item;
    }
  }).gridster().data('gridster').generate_faux_grid(6,3);
  // Display admin UI
  new DropIt.DropProtoView();

  // Populate existing drops
  new DropIt.DropsView( DropIt.Admin.drops, { initLoad: true } );

  $.fn.serializeAll = function(type) {
    var els;
      if(type == 'uri') {
        var toReturn    = [];
        els         = $(this).find(':input').get();
        $.each(els, function() {
            if (this.name && !this.disabled && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) {
                var val = $(this).val();
                toReturn.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( val ) );
            }
        });
        return toReturn.join("&").replace(/%20/g, "+");
      }

      if(type == 'json') {
        els = $(this).find(':input').get();
        var json = {};

        $.map($(els).serializeArray(), function(n, i) {
            json[n['name']] = n['value'];
        });
        return json;
      }
 };

})(jQuery, window);