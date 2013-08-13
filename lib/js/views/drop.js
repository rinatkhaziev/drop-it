var DropIt = window.DropIt || {};
/**
 * Backbone view representing a single drop
 */
DropIt.DropView = Backbone.View.extend({

    //Constructor
    initialize: function ( args ) {
        this.template = _.template( jQuery( '#'  + this.model.get( 'tpl' ) + '_drop_template' ).html() );
        this.listenTo( this.model, 'destroy', this.removeDrop );
        this.initOptions = args.options;
    },
    tagName: 'div',
    className: 'drop-item gs_w',
    events: {
        'click .drop-edit': 'editDrop',
        'click .drop-delete': 'deleteDrop'
    },

    /**
     * Edit drop
     *
     * @todo implement
     *
     * @param  {event} e [description]
     * @return {[type]}   [description]
     */
    editDrop: function( e ) {
        e.preventDefault();
    },


    /**
     * Delete drop
     * @param  {event} e [description]
     * @return {[type]}   [description]
     */
    deleteDrop: function( e ) {
        e.preventDefault();
        this.model.destroy( {
            // Remove the drop from UI right away
            wait: false,
            // Success callback
            success: function( model, response, options ) {},
            // Failure callback
            error: function( model, xhr, options ) {},
            // Data to be passed
            data: JSON.stringify( {
                // Pass action to wp_ajax
                action: 'delete_drop',
                // Drop ID (meta id)
                drop_id: this.model.id
            } ),
            dataType: 'json'
        } );
    },

    /**
     * Delete the drop and recalculate Gridster grid
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    removeDrop: function( e ) {
        DropIt.gridster.remove_widget(this.$el, true);
        this.remove();
        DropIt.gridster.recalculate_faux_grid();
    },


    /**
     * Render a drop within existing collection, or add a new one.
     * 
     * @return {[type]} [description]
     */
    render: function() {

        // Render template
        this.$el.html( this.template( this.model.toJSON() ) );

        // Due to the way Gridster populates grid with widgets
        // We need to determine if the widget should be added with specific coords
        // Or just to the end of the zone

        if ( this.initOptions.initLoad ) {
            DropIt.gridster.add_widget(
                this.$el,
                this.model.attributes.width,
                1,
                this.model.attributes.column,
                this.model.attributes.row
            );
        // Or add a new drop
        } else {
            var widget = DropIt.gridster.add_widget( this.$el ).data();
            this.model.attributes.column = widget.col;
            this.model.attributes.row = widget.row;
            this.model.save( { action: 'update_drop' } );
        }
        DropIt.gridster.recalculate_faux_grid();
        return this;
    }
});