var DropItApp = window.DropItApp || {};

DropItApp.DropsView = Backbone.View.extend({
    el: '#drops',

    inst: {},

    initialize: function( initialDrops ) {
        this.collection = new DropItApp.Drops( initialDrops );
        DropItApp.gridster.options.draggable.stop = this.savePositions;
        inst = this;
        _(this).bindAll('renderDrop');
        this.collection.bind( 'add', this.renderDrop );
        this.render();
    },

    savePositions: function( e, ui ) {
        inst.grids = this.serialize();
        inst.dropsMovedCallback();
    },

    dropsMovedCallback: function ( e ) {
        _.each( this.grids, function( item ) {
            var model = _.findWhere( inst.collection.models, { id: item.drop_id } );
            model.set(item);
        } );
        this.collection.sync( 'update', this.collection );
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
        this.$el.append( dropView.render() );
    }
});

