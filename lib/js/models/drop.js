var DropItApp = window.DropItApp || {};

DropItApp.DropProto = Backbone.Model.extend( {

	defaults: {
		// Default types (should be extendable in the future)
		types: { static_html: 'Static HTML', single: 'Single Post',  query: 'Custom Query' },
		// ajax action
		action: 'create_drop',
		// Empty content
		content: '',
		// Colspan
		width: 1,
		// Drop It Zone id
		post_id: 0
	},

	// Payload should be read from php://input, so we use a single endpoint that will route request
	// to perform CRUD actions
	url: ajaxurl + '?action=drop_it_ajax_route',

	validate: function( attrs, options ) {
		if ( attrs.type == 'static_html' && typeof attrs.content === 'string' && attrs.content === '' )
			return 'Content is empty';
		if ( attrs.type == 'single' && typeof attrs.content === 'string' && attrs.content === '' )
			return 'Invalid post id';
	},

	initialize: function() {
			this.on( "invalid", function( model, error ) {
					alert( error );
			});
	}

});

// Define a model for drop
DropItApp.Drop  = Backbone.Model.extend({

	defaults: {
		type: 'static_html',
		// @todo extend default types
		types: { static_html: 'Static HTML', single: 'Single Post',  query: 'Custom Query' },
		tpl: 'static_html',
		content: '',
		width: 1,
		post_id: 0,
		drop_id: 0

	},

	// Custom ID
	idAttribute: 'drop_id',

	// Ajax Endpoint
	url: ajaxurl + '?action=drop_it_ajax_route',

	validate: function( attrs, options ) {
	}

});