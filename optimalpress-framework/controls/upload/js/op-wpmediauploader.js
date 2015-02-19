"use strict";

jQuery( window ).load(function() {
				
	jQuery( document ).on('click', '.op-upload-media', function( event ){
	
		var file_frame;
		
		var wp_media_post_id = wp.media.model.settings.post.id;
	
		event.preventDefault();
	
		var opMediaInput 	= jQuery(this).prev();
		var multiple		= jQuery(this).data( 'multiple' );

		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data( 'uploader_title' ),
			button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
			},
			multiple:	multiple,
		});

		file_frame.on( 'select', function(){
			
			var selection = file_frame.state().get('selection');
			var opImageList = new Array();
			
			if ( ! multiple ) {
				opMediaInput.parent().next().empty();
			}
			selection.map( function( attachment ) {
	 
				var attachment = attachment.toJSON();
				opMediaInput.parent().next().append( '<div class="image"><img src="' + attachment.url + '" alt="" style="max-width:200px;" data-id="' + attachment.id + '"/><input class="op-remove-media op-button button" type="button" value="x" /></div>' );
				opImageList.push( attachment.id );
				
			});
			console.log( opMediaInput.val() );
			
			if ( ! multiple ) {
				opMediaInput.val( opImageList );
			}
			else if( multiple) {
				if ( opMediaInput.val() != '' ){
					opMediaInput.val(  opMediaInput.val() + ',' + opImageList );
				}else{
					opMediaInput.val( opImageList );
				}
			}
			
			opMediaInput.trigger('change');
			
			wp.media.model.settings.post.id = wp_media_post_id;
			
		});

		file_frame.open();
		
	});

	jQuery( 'a.add_media' ).on( 'click', function(){

		wp.media.model.settings.post.id = wp_media_post_id;

	});

	jQuery( document ).on( 'click', '.op-remove-media', function(e){

		e.preventDefault();
		console.log("asd");
		if( jQuery(this).parents( '.input' ).find( '.op-upload-media' ).data( 'multiple' ) ) {
		
			var text = jQuery(this).parents( '.input' ).find( '.op-input' ).val().replace( jQuery(this).prev().data( 'id' ), '' );
			text = text.replace(/,\s*$/, "");
			text = text.replace(/^,/, "");
			text = text.replace( ',,', ',');
			jQuery(this).parents( '.input' ).find( '.op-input' ).val( text );

		}else{
			jQuery(this).parents( '.input' ).find( '.op-input' ).val('');
		}
		
		jQuery(this).parents( '.input' ).find( '.op-input' ).trigger('change');
		
		jQuery(this).parent().remove();
		
		

	});

});