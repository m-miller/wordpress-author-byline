<?php
/*
Plugin Name: Custom Author Byline
Plugin URI: 
Version: 1.5
License: GPL2
Description: Adds an author name to the byline other than the user writing/editing the post.

Author: Martin Miller
Author URI: 
*/


	add_action( 'admin_menu', 'ab_create_meta_box' );
	add_action( 'save_post', 'ab_save_postdata' );
	
	add_filter( 'the_author','add_byline' );
	add_filter( 'the_author_posts_link', 'filter_the_author_posts_link');
	
	
	function filter_the_author_posts_link() {
		global $post, $authordata;
		$custom_author = get_post_meta( $post->ID, 'author', TRUE );
		if ( $custom_author ) {
	        $link = $custom_author;
		} else {
		        /*$link = sprintf(
		                '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		                esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		                esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
		                get_the_author()
		        );  */
				$link = get_the_author();   
		}
		return $link;
}
	
	
// Replaces the_author() output with the current author and the additional byline.
// filter the author_link as well 

function add_byline( $author ) {
	global $post;
	$custom_author = get_post_meta( $post->ID, 'author', TRUE );
	if( $custom_author ) {
		return $custom_author;
	}
	return $author;
}


$ab_new_meta_box =
	array(
		"author" => array(
			"name" => "author",
			"std" => "",
			"description" => "Add a custom author name (other than your own) to override giving yourself credit for this post."
		),
	);

function ab_new_meta_box() {
	global $post, $ab_new_meta_box;

	foreach($ab_new_meta_box as $meta_box) {
		$meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);

		if($meta_box_value == "") {
			$meta_box_value = $meta_box['std'];
		}
		//echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		echo'<p><input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" size="55" /><br />';
		echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label></p>';
		//Notice: Undefined index: author_noncename in /srv/www/granicus/wp-content/plugins/add-author/custom-author-byline.php on line 89
		
		//echo get_the_author();
	}
}

function ab_create_meta_box() {
	if ( function_exists('add_meta_box') ) {
		add_meta_box( 'ab-new-meta-box', 'Custom Author Byline', 'ab_new_meta_box', 'post', 'normal', 'high' );

	}
}

function ab_save_postdata( $post_id ) {
	global $post, $ab_new_meta_box;

	foreach($ab_new_meta_box as $meta_box) {
		//if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
		//	return $post_id;
		//}
	
		if ( !current_user_can( 'edit_post', $post_id ))
			return $post_id;
		}

		$data = $_POST[$meta_box['name']];

		if(get_post_meta($post_id, $meta_box['name']) == "")
			add_post_meta($post_id, $meta_box['name'], $data, true);
		elseif($data != get_post_meta($post_id, $meta_box['name'], true))
			update_post_meta($post_id, $meta_box['name'], $data);
		elseif($data == "")
		delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
	}