
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	editor = wpmoly.editor || {};

	editor.views.init = function() {

		editor.views.status = new wpmoly.editor.View.Status();
		editor.views.panel = new wpmoly.editor.View.Panel( { el: '#wpmoly-people-metabox' } );
	};

	editor.views.init();

})(jQuery);