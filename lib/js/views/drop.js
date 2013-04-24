var DropItApp = window.DropItApp || {};

DropItApp.DropView = Backbone.View.extend({

    initialize: function ( args ) {
        this.template = _.template( jQuery( '#'  + this.model.get( 'tpl' ) + '-dropTemplate' ).html() );
    },

    tagName: 'div',
    className: 'drop-item',

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );

        return this;
    }
});

DropItApp.DropProtoView = Backbone.View.extend({
    tagName: 'div',
    className: 'drop-create',
    template: _.template( jQuery( '#dropProtoTemplate' ).html() ),
    el: '#create-drop',

    initialize: function() {
        // Set a model and render
        this.model = new DropItApp.DropProto();
        this.render();
    },

    // render library by rendering each book in its collection
    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );
        return this;
    }
});
