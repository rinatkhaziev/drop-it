var app = window.app || {};


app.Drops = Backbone.Collection.extend({
	model: app.Drop
});

app.DropProtos = Backbone.Collection.extend({
	model: app.DropProto
});