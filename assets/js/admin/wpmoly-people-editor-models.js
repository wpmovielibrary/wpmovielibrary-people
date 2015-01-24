
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	editor = wpmoly.editor || {};

	var person = function() {

		editor.models.person = new wpmoly.editor.Model.Person();
		editor.models.search = new wpmoly.editor.Model.Search();
		editor.models.cast = new wpmoly.editor.Model.Movies();
	};

	/**
	 * Override WPMOLY Backbone Search Model
	 * 
	 * @since    1.0
	 */
	wpmoly.editor.Model.Search = Backbone.Model.extend({

		defaults: {
			lang: $( '#wpmoly-search-lang' ).val(),
			query: ''
		}
	});

	/**
	 * WPMOLY Backbone Person Model
	 * 
	 * Model for the metabox movie metadata fields. Holy Grail! That model
	 * is linked to a view containing all the inputs and handles the sync
	 * with the server to search for movies.
	 * 
	 * @since    1.0
	 */
	wpmoly.editor.Model.Person = Backbone.Model.extend({

		// The Holy Grail: person metadata.
		defaults: {
			adult: '',
			alias: '',
			biography: '',
			birthday: '',
			deathday: '',
			homepage: '',
			tmdb_id: '',
			imdb_id: '',
			name: '',
			birthplace: '',
			credit: {
				cast: [],
				crew: []
			},
			profile_path: ''
		},

		/**
		 * Initialize Model. Set the AJAX url and current Post ID.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.url = ajaxurl;
			this.post_id = $( '#post_ID' ).val();
		},

		/**
		 * Overload Backbone sync method to fetch our own data and save
		 * them to the server.
		 * 
		 * @since    2.2
		 * 
		 * @param    string    method Are we searching or is it a regular sync?
		 * @param    object    model Current model
		 * @param    object    options Query options
		 * 
		 * @return   mixed
		 */
		sync: function( method, model, options ) {

			// Let know we've started queryring
			this.trigger( 'sync:start', this );

			// Not search means regular Backbone sync, not our concern
			if ( 'search' == method ) {

				editor.models.status.trigger( 'loading:start' );
				editor.models.status.trigger( 'status:say', wpmoly_lang.search_people );

				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'wpmoly_search_people',
					nonce: wpmoly.get_nonce( 'search-people' ),
					post_id: this.post_id,
					lang: editor.models.search.get( 'lang' ),
					query: editor.models.search.get( 'query' )
				});

				// Let know we're done queryring
				options.complete = function() {
					this.trigger( 'sync:end', this );
					editor.models.status.trigger( 'loading:end' );
				};

				// Let's go!
				options.success = function( response ) {

					// Response has meta, that's a single people
					if ( undefined != response.meta ) {

						this.set_meta( response.meta );

						if ( undefined != response.credits.cast ) {
							editor.models.cast.reset();
							editor.models.cast.add( response.credits.cast );
						}

						editor.models.status.trigger( 'status:say', wpmoly_lang.done );

						return true;
					}

					// If not, means multiple people, show a choice
					/*_.each( response, function( result ) {
						var result = new editor.Model.Result( result );
						editor.models.results.add( result );
					} );*/
					editor.models.status.trigger( 'status:say', wpmoly_lang.multiple_results );
				};

				return wp.ajax.send( options );
			}
			// Fallback to Backbone sync
			else {
				return Backbone.Model.prototype.sync.apply( this, options );
			}
		},

		/**
		 * Update the Model's attributes with the fetched movie's metadata
		 * 
		 * @since    2.2
		 * 
		 * @param    object    data Movie metadata
		 * 
		 * @return   void
		 */
		set_meta: function( data ) {

			var data = _.pick( data, _.keys( this.defaults ) );
			this.set( data );

			this.trigger( 'sync:done', this, data );
			editor.models.status.trigger( 'status:say', wpmoly_lang.metadata_saved );
		},

		/**
		 * Save the movie. Our job is done!
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		save: function() {

			/*var params = {
				emulateJSON: true,
				data: { 
					action: 'wpmoly_save_meta',
					nonce: wpmoly.get_nonce( 'save-movie-meta' ),
					post_id: this.post_id,
					data: this.parse( this.toJSON() )
				} 
			};

			return Backbone.sync( 'create', this, params );*/
		},

		/**
		 * Simple parser to prepare attributes: we don't want to feed
		 * subarrays to the server.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    data Movie metadata
		 * 
		 * @return   mixed
		 */
		parse: function( data ) {

			_.map( data, function( meta, key ) {
				if ( _.isArray( meta ) )
					data[ key ] = meta.toString();
			} );

			return data;
		}
	});

	wpmoly.editor.Model.Movie = Backbone.Model.extend({

		defaults: {
			id: '',
			title: '',
			original_title: '',
			character: '',
			job: '',
			poster_path: '',
			release_date: ''
		},
	});

	wpmoly.editor.Model.Movies = Backbone.Collection.extend({

		model: wpmoly.editor.Model.Movie
	});

	/**
	 * WPMOLY Backbone Preview Model
	 * 
	 * Model for the Metabox Preview Panel.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.Model.Preview = Backbone.Model.extend({

		defaults: {
			
		},

		/**
		 * Initialize Model.
		 * 
		 * Bind the Model update on the Movie Model sync:done event to
		 * update the preview when the Movie attributes are changed.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			editor.models.movie.on( 'sync:done', this.update, this );
		},

		/**
		 * Update Model to match the Movie Model changes
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Movie Model
		 * 
		 * @return   mixed
		 */
		update: function( model, data ) {

			var meta = {};
			_.each( this.defaults, function( value, attr ) {
				meta[ attr ] = data.meta[ attr ];
			}, this );
			meta.poster = data.poster;

			this.set( meta );
		}
	});

	person();

})(jQuery);
