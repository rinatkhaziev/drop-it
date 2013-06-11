var DropItApp = window.DropItApp || {};

DropItApp.Drops = Backbone.Collection.extend({
	model: DropItApp.Drop,
	url: ajaxurl + '?action=drop_it_ajax_route&mode=update_collection'
});

DropItApp.DropProtos = Backbone.Collection.extend({
	model: DropItApp.DropProto
});