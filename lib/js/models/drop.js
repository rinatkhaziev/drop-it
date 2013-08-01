var DropIt = window.DropIt || {};

// Define a model for drop
DropIt.Drop  = Backbone.Model.extend({
	// Defaults
	defaults: {
		type: 'static_html',
		types: {},
		tpl: 'static_html',
		data: '',
		width: 1,
		post_id: 0,
		// Drop ID (meta id)
		drop_id: 0,
		row: 1,
		column: 1

	},

	// Constructor
	initialize: function() {
		this.attributes.types =  _( DropIt.Admin.drop_types ).get_drop_types();
	},

	// Custom ID
	idAttribute: 'drop_id',

	// Ajax Endpoint
	url: ajaxurl + '?action=drop_it_ajax_route',

	validate: function( attrs, options ) {
	}

});