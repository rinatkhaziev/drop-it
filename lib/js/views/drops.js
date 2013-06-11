var DropItApp = window.DropItApp || {};

DropItApp.DropsView = Backbone.View.extend({
    el: '#drops',

    inst: {},

    initialize: function( initialDrops ) {
        this.collection = new DropItApp.Drops( initialDrops );
        DropItApp.gridster.options.draggable.stop = this.savePositions;
        _(this).bindAll('renderDrop');
        this.collection.bind( 'add', this.renderDrop );
        this.render();
        // Save the reference to DropsView object
        inst = this;
    },

    /**
     * Gridster callback, this refers to gridster instance
     * @param  {[type]} e  [description]
     * @param  {[type]} ui [description]
     * @return {[type]}    [description]
     */
    savePositions: function( e, ui ) {
        inst.grids = this.serialize();
        inst.dropsMovedCallback();
    },

    /**
     * Sync collection when drops are moved
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    dropsMovedCallback: function ( e ) {
        _.each( this.grids, function( item ) {
            var model = _.findWhere( inst.collection.models, { id: item.drop_id } );
            if (typeof model != 'undefined' )
                model.set(item);
        } );
        this.collection.sync( 'update', this.collection );
    },

    // Render collection (iterate through collection of drops and render each one of them)
    render: function() {
        this.collection.each(function( item ) {
            this.renderDrop( item );
        }, this );
    },

    // Render a single drop
    renderDrop: function( item ) {
        var dropView = new DropItApp.DropView({
            model: item
        });
        this.$el.append( dropView.render() );
    }
});

