DropIt.Drops = Backbone.Collection.extend({
	model: DropIt.Drop,
	url: ajaxurl + '?action=drop_it_ajax_route&mode=update_collection&drop_it_nonce=' + DropIt.Admin.nonce,
	initialize: function(models, options) {
	}
});