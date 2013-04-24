var app = window.app || {};

app.DropsView = Backbone.View.extend({
    el: '#drops',

    initialize: function( initialDrops ) {
        this.collection = new app.Drops( initialDrops );
        this.render();
    },

    // render library by rendering each book in its collection
    render: function() {
        this.collection.each(function( item ) {
            this.renderDrop( item );
        }, this );
    },

    // render a book by creating a BookView and appending the
    // element it renders to the library's element
    renderDrop: function( item ) {
        var dropView = new app.DropView({
            model: item
        });
        this.$el.append( dropView.render().el );
    }
});

app.DropProtosView = Backbone.View.extend({
    el: '#create-drop',

    initialize: function( initialDrops ) {
        this.collection = new app.DropProtos( initialDrops );
        this.render();
    },

    // render library by rendering each book in its collection
    render: function() {
        this.collection.each(function( item ) {
            this.renderDrop( item );
        }, this );
    },

    // render a book by creating a BookView and appending the
    // element it renders to the library's element
    renderDrop: function( item ) {
        var dropView = new app.DropProtoView({
            model: item
        });
        this.$el.append( dropView.render().el );
    }
});