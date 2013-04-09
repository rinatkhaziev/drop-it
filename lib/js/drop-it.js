/**
 * Admin UI
 */
var DI_Drops = { Views: {}};


( function( $, window, undefined ) {
	var document = window.document;

	var DropIt = function() {
		// Reference to the object
		var SELF = this;
		// Cached DOM elements
		var UI = {};

		var _init = function() {
			UI.droppable_meta_box = $('#drop_it_layout_droppable');
			//$('#normal-sortables').prepend( UI.droppable_meta_box );
			UI.droppable_area = $('.di-droppable-area', UI.droppable_meta_box );
			// Display settings 
			UI.droppable_area.sortable( { grid: [4, 3], connectWith: '.meta-box-sortables', containment: 'parent' } ).disableSelection();
			UI.droppable_area.append("<div class='di-drop'>b</div>");
		};
		// Init the stuff
		_init();
	};
	window.DropIt = new DropIt();

	// Prototype backbone
    DI_Drops.Model = Backbone.Model.extend({
        defaults : {
            'correct' : false
        },
        url : ajaxurl+'?action=di_drop_save',
        toJSON : function() {
            var attrs = _.clone( this.attributes );
            attrs.post_id = 40;
            return attrs;
        },
        initialize : function() {
            if ( false === true ) {
                this.set( 'correct', true );
            }
        },
    });

    DI_Drops.Collection = Backbone.Collection.extend({
        model: DI_Drops.Model
    });

    DI_Drops.Views.Inputs = Backbone.View.extend({
    initialize:function () {
        this.collection.each( this.addInput, this );
    },
    addInput : function( model, index ) {
        var input = new DI_Drops.Views.Input({ model:model });
        this.$el.append( input.render().el );
    }
    });
    DI_Drops.Views.Input = Backbone.View.extend({
        tagName: 'p',
        // Get the template from the DOM
        template :_.template( $('#testTemplate').html() ),
 
        // When a model is saved, return the button to the disabled state
        initialize:function () {
            var _this = this;
            this.model.on( 'sync', function() {
                _this.$('button').text( 'Save' ).attr( 'disabled', true );
            });
        },
 
        // Attach events
        events : {
            'keyup input' : 'blur',
            'blur input' : 'blur',
            'click button' : 'save'
        },
 
        // Perform the Save
        save : function( e ) {
            e.preventDefault();
            $(e.target).text( 'wait' );
            this.model.save();
        },
 
        // Update the model attributes with data from the input field
        blur : function() {
            var input = this.$('input').val();
            if ( input !== this.model.get( 'answer' ) ) {
                this.model.set('answer', input);
                this.$('button').attr( 'disabled', false );
            }
        },

        // Render the single input - include an index.
        render:function () {
            this.model.set( 'index', this.model.collection.indexOf( this.model ) + 1 );
            this.$el.html( this.template( this.model.toJSON() ) );
            return this;
        }
    });

    DI_Drops.Views.Select = Backbone.View.extend({
        initialize:function () {
            this.collection.each( this.addOption, this );
        },
        addOption:function ( model ) {
            var option = new DI_Drops.Views.Option({ model:model });
            this.$el.append( option.render().el );
        }
    });

    DI_Drops.Views.Option = Backbone.View.extend({
        tagName:'option',

        // returning a hash allows us to set attributes dynamically
        attributes:function () {
            return {
                'value':this.model.get( 'answer_id' ),
                'selected':this.model.get( 'correct' )
            };
        },

        // Watch for changes to each model (that happen in the input fields and re-render when there is a change
        initialize:function () {
            this.model.on( 'change:answer', this.render, this );
        },
        render:function () {
            this.$el.text( this.model.get( 'answer' ) );
            return this;
        }
    });

	var answers = new DI_Drops.Collection( [ { answer: 'test', answer_id: 555 }, { answer: 'tedst', answer_id: 4356 }  ] );
    var selectElem = new DI_Drops.Views.Select({ collection:answers, el : $('#answerSelect select') });
    var inputs = new DI_Drops.Views.Inputs({ collection:answers, el:$('#answerInputs') });


})( jQuery, window );

