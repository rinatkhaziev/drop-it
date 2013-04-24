var DropItApp = window.DropItApp || {};


DropItApp.Drops = Backbone.Collection.extend({
	model: DropItApp.Drop
});

DropItApp.DropProtos = Backbone.Collection.extend({
	model: DropItApp.DropProto
});