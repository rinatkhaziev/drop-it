var DropItApp = window.DropItApp || {};

DropItApp.DropsView = Backbone.View.extend({
    el: '#drops',

    initialize: function( initialDrops ) {
        this.collection = new DropItApp.Drops( initialDrops );
        this.render();
    },

    // render library by rendering each book in its collection
    render: function() {
        this.collection.each(function( item ) {
            this.renderDrop( item );
        }, this );
        this.afterRenderDrops();
    },
    afterRenderDrops: function() {
      DropItApp.gridster = jQuery("#drops").gridster({
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
      }).gridster().data('gridster');
    },
    // render a book by creating a BookView and DropItAppending the
    // element it renders to the library's element
    renderDrop: function( item ) {
        var dropView = new DropItApp.DropView({
            model: item
        });
        this.$el.append( dropView.render().el );
    }
});

