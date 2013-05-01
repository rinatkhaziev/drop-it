var DropItApp = window.DropItApp || {};

// Define a model for drop
DropItApp.Drop  = Backbone.Model.extend({
	defaults: {
		drop_id: '0',
		type: 'static_html',
		types: { static_html: 'Static HTML', single: 'Single Post',  loop: 'Custom Query' },
		tpl: 'static_html',
		content: ''
	}
});

DropItApp.DropProto = Backbone.Model.extend( {
	defaults: {
		types: { static_html: 'Static HTML', single: 'Single Post',  loop: 'Custom Query' },
		action: 'save_drop',
		content: '',
		post_id: 0
	},
	// tmp workaround
	url: ajaxurl + '?action=save_drop'
});