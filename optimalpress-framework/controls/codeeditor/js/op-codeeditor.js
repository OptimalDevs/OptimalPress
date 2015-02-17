
(function($){

	$(window).load(function() {

		init_ace_editor = function($elements){
		
			if( window.ace !== 'undefined' ) {
			
				if( $elements.length <= 0 )
					return;

				$elements.each(function() {

					var editor   = ace.edit(jQuery(this).get(0));
					var textarea = jQuery(this).prev();

					
					if( jQuery(this).data('mode') != '' ){
						editor.getSession().setMode("ace/mode/" + jQuery(this).data('mode') );
					}
										
					editor.getSession().setValue(textarea.val());
					editor.getSession().on('change', function(){
					  textarea.val(editor.getSession().getValue());
					  editor.resize();
					});

				});
			}

		}
		
		init_ace_editor( jQuery( '.op-js-codeeditor' ) );
		jQuery( '.op-js-codeeditor' ).css('font-size', '13px');  //Fix for Codeeditor in shortcodes.
		
		jQuery( 'body' ).on( 'op-addnew-group-element', function(){
			init_ace_editor( jQuery( '.op-js-codeeditor' ) );
		});
	
	});
	
})(jQuery);