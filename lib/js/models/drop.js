var app = app || {};

// Define a model for drop
app.Drop  = new Backbone.Model.extend({
	defaults: {
		type: 'static',
		types: { static_html: 'Static HTML', single: 'single',  loop: 'loop' }
	}
});