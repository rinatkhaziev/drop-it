var app = window.app || {};

// Define a model for drop
app.Drop  = Backbone.Model.extend({
	defaults: {
		drop_id: '0',
		type: 'static_html',
		types: { static_html: 'Static HTML', single: 'Single Post',  loop: 'Custom Query' },
		tpl: 'static'
	}
});

app.DropProto = Backbone.Model.extend( {
	defaults: {
		types: { static_html: 'Static HTML', single: 'Single Post',  loop: 'Custom Query' }
	}
});