var app = app || {};

var DropList = Backbone.Collection.extend({
	model: app.drop,
	refresh: function() {}
});

app.Drops = new DropList();