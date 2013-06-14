var DropIt = window.DropIt || {};

DropIt.DropProto = Backbone.Model.extend( {

	defaults: {
		// Default types (should be extendable in the future)
		types: {},
		// ajax action
		action: 'create_drop',
		// Empty data
		data: '',
		// Colspan
		width: 1,
		// Drop It Zone id
		post_id: 0,
		row: 1,
		column: 1
	},

	// Payload should be read from php://input, so we use a single endpoint that will route request
	// to perform CRUD actions
	// @todo probably use rewrites instead of admin-ajax.php callback
	url: ajaxurl + '?action=drop_it_ajax_route',

	validate: function( attrs, options ) {
		// @todo add a validation callback per drop
		if ( attrs.type == 'static_html' && typeof attrs.data === 'string' && attrs.data === '' )
			return 'Content is empty';
		if ( attrs.type == 'single' && typeof attrs.data === 'string' && attrs.data === '' )
			return 'Invalid post id';
	},

	initialize: function() {
		this.attributes.types = _( window.drop_it_drop_types ).get_drop_types();
			this.on( "invalid", function( model, error ) {
					alert( error );
			});
	}

});

// Define a model for drop
DropIt.Drop  = Backbone.Model.extend({
	defaults: {
		type: 'static_html',
		types: {},
		tpl: 'static_html',
		data: '',
		width: 1,
		post_id: 0,
		drop_id: 0,
		row: 1,
		column: 1

	},

	initialize: function() {
		this.attributes.types =  _( window.drop_it_drop_types ).get_drop_types();
	},

	// Custom ID
	idAttribute: 'drop_id',

	// Ajax Endpoint
	url: ajaxurl + '?action=drop_it_ajax_route',

	validate: function( attrs, options ) {
	}

});