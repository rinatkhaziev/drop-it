DropIt.Drops = Backbone.Collection.extend({
	model: DropIt.Drop,
	url: ajaxurl + '?action=drop_it_ajax_route&mode=update_collection',
	initialize: function(models, options) {
	}
});

DropIt.DropProtos = Backbone.Collection.extend({
	model: DropIt.DropProto
});