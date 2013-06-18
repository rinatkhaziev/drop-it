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
                drop_id: this.model.id,
                // Zone ID (post id)
                post_id: drop_it_layout_id
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

/**
 * View for DropProto:
 * Handles creation of a new drop
 * @type {[type]}
 */
DropIt.DropProtoView = Backbone.View.extend({
    tagName: 'div',
    className: 'drop-create',
    template: _.template( jQuery( '#dropProtoTemplate' ).html() ),
    inst: this,
    el: '#create-drop',
    events: {
        'change #dropSelectTemplate': 'selectChanged',
        'click .drop-add': 'addDrop',
        'focus .drop-name-autocomplete': 'getAutocomplete',
        'keyup .drop-name-autocomplete': 'getAutocomplete'
    },

    /**
     * Constructor
     * @return {[type]} [description]
     */
    initialize: function() {
        // Set a model and render
        this.model = new DropIt.DropProto();
        this.render();
        this.$el.find('option').eq(0).prop('selected', 'selected');
        this.selectChanged();
    },

    // Set additional properties
    afterViewChanged: function() {
        this.autocompleteEl = this.$el.find( '.drop-name-autocomplete' );
        this.autocompleteCache = [];
        this.autocompleteAjax = {};
    },

    // Render drops list by rendering each drop in its collection
    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );
        return this;
    },

    // Triggered when select is changed
    selectChanged: function(event) {
        var opt = this.$el.find('option:selected').val() ;
        this.subview = new DropIt.DropProtoSubView({ model: this.model, view: opt });
        if ( opt === 'single' )
            this.afterViewChanged();
    },
    /**
     * Save drop and add it to the zone
     */
    addDrop: function( event ) {
        event.preventDefault();
        var item = this.$el.serializeAll( 'json' );
        this.model.save( item, {
            success: function( model, response ) {
                // Add post id to array of ids that should be excluded for autocomplete
                if ( item.type == 'single' ) {
                    window.drop_it_autocomplete_exclude.push( parseInt( item.data, 0 ) );
                    // @todo Tmp workaround for autocomplete not firing after adding a drop
                    new DropIt.DropProtoView();
                }
                // Cast id to string as tmp workaround
                item.drop_id = String( response.meta_id );
                item.post_title = response.post_title;
                inst.collection.create(item, { initLoad: false } );
            },
            error: function( model, xhr, options ) {
               alert( xhr.responseText );
            }
        } );

    },

    getAutocomplete: function() {
        // Save the reference
        var $diapp = this;
        this.autocompleteEl.autocomplete( {
            minLength: 3,
            source: function( request, response ) {
                var term = $diapp.autocompleteEl.value;

                if ( term in $diapp.autocompleteCache ) {
                    response( $diapp.autocompleteCache[ term ] );
                    return;
                }

                // Append more request vars
                request.action = 'drop_it_ajax_search';
                request.exclude = window.drop_it_autocomplete_exclude;

                $diapp.autocompleteAjax = jQuery.getJSON( ajaxurl, request, function( data, status, xhr ) {
                    $diapp.autocompleteCache[ term ] = data;
                    if ( xhr === $diapp.autocompleteAjax ) {
                       response( data );
                    }
                });
            },
            select: function( e, ui ) {
                $diapp.$el.find( '.di-found-post' ).html( ui.item.post_title );
                $diapp.$el.find( '.di-found-data' ).val( ui.item.post_id );
            }
        });

        var autocomplete = this.autocompleteEl.data( 'autocomplete' ) || this.autocompleteEl.data( 'ui-autocomplete' );
        autocomplete._renderItem = function( ul, item ) {
          return jQuery( "<li></li>" ).data( "item.autocomplete", item)
                .append( _.template( jQuery('#autocompleteDropTemplate' ).html(), item ) )
                .appendTo( ul );
        };
    }

});

// Rendering specific template for creation of a drop
DropIt.DropProtoSubView = Backbone.View.extend({
    tagName: 'div',
    className: 'drop-create',
    template: _.template( jQuery( '#dropProtoTemplate' ).html() ),
    el: '#varyOptionsForProto',

    initialize: function( args ) {
        this.template = _.template( jQuery( '#'  + args.view + '_create_drop_template' ).html() );
        this.render();
        this.listenTo( this.model, 'sync', this.render );
    },

    render: function() {
        this.$el.html( this.template( this.model.toJSON() ) );
        return this;
    }
});