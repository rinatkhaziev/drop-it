var DropIt = window.DropIt || {};

// A model for to-be-created drop
DropIt.DropProto = Backbone.Model.extend( {

	// Defaults
	defaults: {
		// Default types (should be extendable in the future)
		types: {},
		// Ajax action
		action: 'create_drop',
		title: '',
		data: '',
		// Colspan
		width: 1,
		// Drop It Zone id
		post_id: 0,
		row: 1,
		column: 1
	},

	// Constructor
	initialize: function() {
		this.attributes.types = _( DropIt.Admin.drop_types ).get_drop_types();
			this.on( "invalid", function( model, error ) {
					alert( error );
			});
	},

	// Payload should be read from php://input, so we use a single endpoint that will route request
	// to perform CRUD actions
	// @todo probably use rewrites instead of admin-ajax.php callback
	url: ajaxurl + '?action=drop_it_ajax_route',

	// Validate model about to be saved
	validate: function( attrs, options ) {
		// @todo add a validation callback per drop
		if ( attrs.type == 'static_html' && typeof attrs.data === 'string' && attrs.data === '' )
			return 'Content is empty';
		if ( attrs.type == 'single' && typeof attrs.data === 'string' && attrs.data === '' )
			return 'Invalid post id';
	}

});