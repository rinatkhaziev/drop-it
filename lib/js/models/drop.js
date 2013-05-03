var DropItApp = window.DropItApp || {};

DropItApp.DropProto = Backbone.Model.extend( {
	defaults: {
		// Default types (should be extendable in the future)
		types: { static_html: 'Static HTML', single: 'Single Post',  loop: 'Custom Query' },
		// ajax action
		action: 'save_drop',
		// Empty content
		content: '',
		// Colspan
		width: 1,
		// Drop It Zone id
		post_id: 0
	},
	// Payload should be read from php://input, so we use a single endpoint that will route request
	// to perform CRUD actions
	url: ajaxurl + '?action=drop_it_ajax_route'
});

// Define a model for drop
DropItApp.Drop  = Backbone.Model.extend({
	defaults: {
		type: 'static_html',
		types: { static_html: 'Static HTML', single: 'Single Post',  loop: 'Custom Query' },
		tpl: 'static_html',
		content: '',
		width: 1,
		post_id: 0,
		drop_id: 0

	},
	url: ajaxurl + '?action=drop_it_ajax_route'
});