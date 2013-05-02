var DropItApp = window.DropItApp || {};

DropItApp.DropView = Backbone.View.extend({

    initialize: function ( args ) {
        this.template = _.template( jQuery( '#'  + this.model.get( 'tpl' ) + '-dropTemplate' ).html() );
    },

    tagName: 'div',
    className: 'drop-item',
    events: {
        'click .drop-expand': 'expandDrop'
    },

    expandDrop: function( e ) {
        e.preventDefault();
        alert('1');
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
        'click .drop-add': 'addDrop'
    },

    initialize: function() {
        // Set a model and render
        this.model = new DropItApp.DropProto();
        this.render();
        this.$el.find('option').eq(0).attr('selected', 'selected');
        this.selectChanged();
    },

    // render library by rendering each book in its collection
    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );
        return this;
    },

    // Triggered when select is changed
    selectChanged: function(event) {
        var opt = this.$el.find('option:selected').val() ;
        new DropItApp.DropProtoSubView({ model: this.model, view: opt });
    },
    /**
     * Save drop and add it to the zone
     */
    addDrop: function( event ) {
        event.preventDefault();
        var item = this.$el.serializeAll( 'json' );
        this.model.save( item );
        new DropItApp.DropsView( item );
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