var DropItApp = window.DropItApp || {};

DropItApp.DropsView = Backbone.View.extend({
    el: '#drops',

    inst: {},

    initialize: function( initialDrops ) {
        this.collection = new DropItApp.Drops( initialDrops );
        DropItApp.gridster.options.draggable.stop = this.savePositions;
        inst = this;
        this.render();
    },

    savePositions: function( e, ui ) {
        inst.grids = this.serialize();
        console.log( inst.collection );
    },

    gridsterDragStop: function ( e ) {
        console.log(this.grids);
    },

    // render library by rendering each book in its collection
    render: function() {
        this.collection.each(function( item ) {
            this.renderDrop( item );
        }, this );
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

