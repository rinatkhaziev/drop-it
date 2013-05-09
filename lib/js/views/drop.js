var DropItApp = window.DropItApp || {};

DropItApp.DropView = Backbone.View.extend({

    initialize: function ( args ) {
        this.template = _.template( jQuery( '#'  + this.model.get( 'tpl' ) + '-dropTemplate' ).html() );
        this.listenTo( this.model, 'destroy', this.remove );
    },

    tagName: 'div',
    className: 'drop-item',
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
            wait: true,
            success: function(model, response, options ) {

            },
            error: function(model, xhr, options) {

            },
            data: JSON.stringify( { action: 'delete_drop', 'drop_id': this.model.id, post_id: drop_it_layout_id } ),
            dataType: 'json'
        } );
    },

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );

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
    el: '#create-drop',
    events: {
        'change select': 'selectChanged',
        'click .drop-add': 'addDrop',
        'focus .drop-name-autocomplete': 'getAutocomplete'
    },

    initialize: function() {
        // Set a model and render
        this.model = new DropItApp.DropProto();
        this.render();
        this.$el.find('option').eq(0).attr('selected', 'selected');
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
                    window.drop_it_autocomplete_exclude.push( parseInt( item.content ) );
                }
                item.drop_id = response;
                new DropItApp.DropsView( item );
            },
            error: function( model, xhr, options ) {
               alert( xhr.responseText );
            }
        } );
    },

    findPost: function ( e ) {

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
                $diapp.$el.find( '.di-found-content' ).val( ui.item.post_id );
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