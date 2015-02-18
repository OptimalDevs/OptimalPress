/*
 * Adds the Shortcode Generator button to tinyMce
 *
 * This script adds the button shortcode generator to the WordPress text editor.
 */
(function($){
	function create(sg)	{
		tinymce.create( 'tinymce.plugins.' + sg.name, {
		
			init: function( ed, url ) {
			
				var cmd_cb = function(name) {
					return function() {
						$( '#' + name + '_modal' ).css( { visibility: 'visible' } );
						$( '.op-modal-overlay' ) .show();
						$( '#' + name + '_modal' ).unbind( 'op_insert_shortcode.op_tinymce' );
						$( '#' + name + '_modal' ).bind( 'op_insert_shortcode.op_tinymce', function( event, code ) {
							ed.selection.setContent( code );
							$( ed.getElement() ).insertAtCaret( code );
						});
					}
				}
				ed.addCommand( sg.name + '_cmd', cmd_cb( sg.name ) );
				ed.addButton( sg.name, { title: sg.button_title, cmd: sg.name + '_cmd', image: sg.main_image } );
			},
			getInfo: function() {
				return {
					longname: 'Optimalpress Framework',
					author  : 'Optimaldevs'
				};
			}
			
		});
	}
	
	for( var i = 0; i < op_sg.length; i++ ){
		create( op_sg[i] );
	}

})(jQuery);


for( var i = 0; i < op_sg.length; i++ ){
	tinymce.PluginManager.add( op_sg[i].name, tinymce.plugins[op_sg[i].name] );
}
