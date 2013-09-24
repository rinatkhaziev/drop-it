var DropIt = window.DropIt || {};
/**
 * Backbone view representing a single drop
 */
DropIt.DropView = Backbone.View.extend({

    //Constructor
    initialize: function ( args ) {
         _.bindAll(this, "render");
         console.log( args );
         //console.log( this.model );
        this.template = _.template( jQuery( '#'  + this.model.get( 'template' ) + '_drop_template' ).html() );
        this.listenTo( this.model, 'destroy', this.removeDrop );
        this.initOptions = args.options;
    },
    tagName: 'div',
    className: 'drop-item gs_w',
    events: {
        'click .drop-expand': 'editDrop',
        'click .drop-save': 'saveDrop',
        'click .drop-delete': 'deleteDrop'
    },

    /**
     * Edit and save updated drop
     *
     * @todo implement
     *
     * @param  {event} e [description]
     * @return {[type]}   [description]
     */
    editDrop: function( e ) {
        e.preventDefault();
        this.$('.widget-inside').hide();
        this.$('.widget-inside-edit').show();

        var item = this.model.toJSON();

        console.log(item);
    },

    saveDrop: function( e ) {
        e.preventDefault();
        this.$('.widget-inside').show();
        this.$('.widget-inside-edit').hide();

        var item = this.$el.serializeAll( 'json' );
        item.post_id = DropIt.Admin.layout_id;
        console.log( item );

        // Quick and dirty workaround for updating a single drop issues
        // @todo fix later
        var saveData = item;
        saveData.action = 'update_drop';

        this.model.save( item,
            {  
                // Success callback
                success: function( model, response, options ) {
                    console.log('success', model, response);
                },
                // Failure callback
                error: function( model, xhr, options ) {
                    console.log('error', model, xhr);
                },
                dataType: 'json'
            });

        this.render();
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

        // Get title and data

        this.$('.drop-single-title').text(this.model.get('title'));
        this.$('.drop-single-data').text(this.model.get('data'));

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