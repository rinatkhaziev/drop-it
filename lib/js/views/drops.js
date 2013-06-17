DropIt.DropsView = Backbone.View.extend({
    el: '#drops',

    inst: {},

    initialize: function( initialDrops, options ) {
        this.collection = new DropIt.Drops( initialDrops, options );
        DropIt.gridster.options.draggable.stop = this.savePositions;
        _(this).bindAll('renderDrop');
        this.collection.bind( 'add', this.renderDrop );
        this.render( options );
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
    render: function(  options ) {
        this.collection.each(function( item ) {
            this.renderDrop( item, options );
        }, this );
    },

    // Render a single drop
    renderDrop: function( item, options ) {
        var isInit = typeof options.initLoad !== 'undefined' && options.initLoad ? true : false;
        var dropView = new DropIt.DropView({
            model: item,
            options: { 'initLoad': isInit }
        });
        this.$el.append( dropView.render() );
    }
});

