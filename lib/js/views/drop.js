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
    events: {
        'change select': 'selectChanged',
        'click .drop-add': 'addDrop'
    },

    initialize: function() {
        // Set a model and render
        this.model = new DropItApp.DropProto();
        this.on('apiEvent', this.callback);
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

    // Events
    selectChanged: function(event) {
        var opt = this.$el.find('option:selected').val() ;
        this.trigger('apiEvent', opt );
    },
    addDrop: function( event ) {
        event.preventDefault();
        //console.log( this.$el.serializeAll( 'json') );
        this.model.save( this.$el.serializeAll( 'json') );
    },
    callback: function(subView) {
        new DropItApp.DropProtoSubView({ model: this.model, view: subView });
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