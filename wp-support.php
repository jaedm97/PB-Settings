<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

// Useful function for generating input field for WordPress





function pick_settings_page_list(){

	$pages_array = array( '' => __( 'Select Page', 'woo-wishlist' ) );

	foreach( get_pages() as $page ):
		$pages_array[ $page->ID ] = $page->post_title;
	endforeach;

	return $pages_array;
}

