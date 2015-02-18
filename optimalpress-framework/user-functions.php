<?php

/**
 * List of useful functions for developer.
 *
 * There are functions to retrieve values from the database and functions that help fill a control in the correct format.
 */
 
/**
 * Get metabox value from database.
 *
 * @param int 		$post_id		The ID of the post from which you want the data.
 * @param string	$key 			A string containing the name of the meta value you want.
 * @param string	$name 			The option name.
 * @param mixed		$default		The default value to return if no value is returned.
 * @param bool		$allow_empty	Decide whether the result to return may be empty.
 *									If "false" and the value to return is empty, the result to return will be the value assigned to $default variable.
 *									If "true" it'll return the result of the database even if it's empty.
 *
 * @return mixed Returns the value retrieved from database.
 */

if( ! function_exists( 'op_get_metabox_field' ) ) {

	function op_get_metabox_field( $post_id, $key, $name, $default = null, $allow_empty = true ) {
				
		$value	= get_post_meta( $post_id, $key, true );
		
		if ( isset( $value[ $name ] ) ) {
		
			if ( is_null( $value[ $name ] ) || ( ! $allow_empty && empty( $value[ $name ] ) ) ) {
			
				return $default;
				
			}
				
			return $value[ $name ];
			
		}
		
		return $default;
		
	}
	
}

/**
 * Get values for a named option from the options database table.
 *
 * @param string	$key 			Name of the option set.
 * @param string	$name 			Name of the option to retrieve.
 * @param mixed		$default		The default value to return if no value is returned.
 *
 * @return mixed Returns the value retrieved from database.
 */
 
if( ! function_exists( 'op_get_option_field' ) ) {

	function op_get_option_field( $key, $name, $default = null ) {
	
		$op_options = get_option( $key );

		if( empty( $op_options ) || ! isset( $op_options[ $name ] ) ) {
		
			return $default;
			
		}
		
		return $op_options[ $name ];
		
	}
}

/**
 * Functions for Backend
 *
 */
if( is_admin() ) {
		  
	if( ! function_exists( 'op_get_tags' ) ) {
		/**
		 * Get all the post tags ready to be used in the Optimalframework control format.
		 *
		 * @return array Retrieve an array as 'value' and 'label' ready for use it in the Framework controls.
		 */
		function op_get_tags() {

			$wp_tags	= get_tags( array( 'hide_empty' => 0 ) );
			$result 	= array();
			
			foreach( $wp_tags as $tag ) {
				$result[] = array( 'value' => $tag->term_id, 'label' => $tag->name );
			}
			
			return $result;
			
		}
		
	}

	if( ! function_exists( 'op_get_categories' ) ) {
		/**
		 * Get all the post categories ready to be used in the Optimalframework control format.
		 *
		 * @return array Retrieve an array as 'value' and 'label' ready for use it in the Framework controls.
		 */
		function op_get_categories() {
			
			$wp_cat	= get_categories( array( 'hide_empty' => 0 ) );
			$result = array();
			
			foreach ( $wp_cat as $cat ) {
				$result[] = array( 'value' => $cat->cat_ID, 'label' => $cat->name );
			}
			
			return $result;
			
		}
		
	}

	if( ! function_exists( 'op_get_posts' ) ) {
		/**
		 * Get all the posts ready to be used in the Optimalframework control format.
		 *
		 * @return array Retrieve an array as 'value' and 'label' ready for use it in the Framework controls.
		 */
		function op_get_posts() {
		
			$posts_ids = get_posts( array(
				'post_type'  => 'post',
				'post_status' => array( 'publish', 'private' ),
				'numberposts' => -1,
				'fields'  => 'ids'
			) );


			$result = array();
			
			foreach( $posts_ids as $id ) {
			
				$result[] = array( 'value' => $id, 'label' => get_the_title( $id ) );
			
			}
			
			return $result;
			
		}

	}

	if( ! function_exists( 'op_get_pages' ) ) {
		
		/**
		 * Get all the pages ready to be used in the Optimalframework control format.
		 *
		 * @return array Retrieve an array as 'value' and 'label' ready for use it in the Framework controls.
		 */
		 
		function op_get_pages() {

			$pages_ids = get_posts( array(
				'post_type'  => 'page',
				'post_status' => array( 'publish', 'private' ),
				'numberposts' => -1,
				'fields'  => 'ids'
			) );


			$result = array();
			
			foreach( $pages_ids as $id ) {
			
				$result[] = array( 'value' => $id, 'label' => get_the_title( $id ) );
			
			}
			
			return $result;
			
		}

	}
}


?>