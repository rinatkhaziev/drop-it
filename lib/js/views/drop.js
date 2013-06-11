var DropItApp = window.DropItApp || {};

DropItApp.DropView = Backbone.View.extend({

    initialize: function ( args ) {
        this.template = _.template( jQuery( '#'  + this.model.get( 'tpl' ) + '-dropTemplate' ).html() );
        this.listenTo( this.model, 'destroy', this.removeDrop );
    },
    tagName: 'div',
    className: 'drop-item gs_w',
    events: {
        'click .drop-expand': 'expandDrop',
        'click .drop-delete': 'deleteDrop'
    },

    expandDrop: function( e ) {
        e.preventDefault();
    },

    deleteDrop: function( e ) {
        e.preventDefault();
        this.model.destroy( {
            wait: false,
            success: function(model, response, options ) {

            },
            error: function(model, xhr, options) {

            },
            data: JSON.stringify( { action: 'delete_drop', 'drop_id': this.model.id, post_id: drop_it_layout_id } ),
            dataType: 'json'
        } );
    },

    removeDrop: function( e ) {
        DropItApp.gridster.remove_widget(this.$el, true);
        this.remove();
        DropItApp.gridster.recalculate_faux_grid();

    },

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        var html = this.template( this.model.toJSON() );
        //console.log( this.model.attributes );
        this.$el.html( html );
        DropItApp.gridster.add_widget(
            this.$el,
            this.model.attributes.width,
            1,
            this.model.attributes.column,
            this.model.attributes.row  );
        return this;
    }
});

/**
 * View for DropProto:
 * Handles creation of a new drop
 * @type {[type]}
 */
DropItApp.DropProtoView = Backbone.View.extend({
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

    initialize: function() {
        // Set a model and render
        this.model = new DropItApp.DropProto();
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
        new DropItApp.DropProtoSubView({ model: this.model, view: opt });
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
                }
                // Cast id to string as tmp workaround
                item.drop_id = String( response.meta_id );
                item.post_title = response.post_title;
                inst.collection.create(item);
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
DropItApp.DropProtoSubView = Backbone.View.extend({
    tagName: 'div',
    className: 'drop-create',
    template: _.template( jQuery( '#dropProtoTemplate' ).html() ),
    el: '#varyOptionsForProto',

    initialize: function( args ) {
        this.template = _.template( jQuery( '#'  + args.view + '-createDropTemplate' ).html() );
        this.render();
    },

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );
        return this;
    }
});