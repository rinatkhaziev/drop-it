var app = window.app || {};

app.DropView = Backbone.View.extend({
    tagName: 'div',
    className: 'drop-item',
    template: _.template( jQuery( '#dropTemplate' ).html() ),

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );

        return this;
    }
});

app.DropProtoView = Backbone.View.extend({
    tagName: 'div',
    className: 'drop-create',
    template: _.template( jQuery( '#dropProtoTemplate' ).html() ),

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );

        return this;
    }
});