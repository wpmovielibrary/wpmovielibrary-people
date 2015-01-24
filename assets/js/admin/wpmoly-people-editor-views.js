
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	editor = wpmoly.editor || {};

	var person = function() {

		editor.views.status = new wpmoly.editor.View.Status();
		editor.views.panel = new wpmoly.editor.View.Panel();
		editor.views.person = new wpmoly.editor.View.Person();
		editor.views.search = new wpmoly.editor.View.Search({
			model: editor.models.search,
			target: editor.models.person
		});
	};

	/**
	 * WPMOLY Backbone Movie Model
	 * 
	 * View for metabox movie metadata fields
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.View.Person = Backbone.View.extend({

		el: '#wpmoly-people-meta',

		model: editor.models.person,

		events: {
			"change .meta-data-field": "update"
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			var template = $( this.el ).html();
			if ( undefined === template )
				return false;

			this.template = _.template( template );
			this.render();

			_.bindAll( this, 'render' );

			this.model.on( 'change', this.changed, this );
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Model
		 * 
		 * @return   void
		 */
		render: function( model ) {

			this.$el.html( this.template() );
			return this;
		},

		/**
		 * Update the View to match the Model's changes
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Model
		 * 
		 * @return   void
		 */
		changed: function( model ) {

			_.each( model.changed, function( meta, key ) {
				$( '#meta_data_' + key ).val( meta );
			} );
		},

		/**
		 * Update the Model whenever an input value is changed
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		update: function( event ) {

			var meta = event.currentTarget.id.replace( 'meta_data_', '' ),
			   value = event.currentTarget.value;

			this.model.set( meta, value );
		}
	});

	person();

})(jQuery);