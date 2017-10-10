<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


$pickplugins_wl_settings_options = array(
	'page_nav' => __( 'Options', 'woo-wishlist' ) . " <span class='dashicons dashicons-admin-tools'></span>",
	'page_settings' => array(
		
		'pick_section_options'	=> array(
			'title' 			=> 	__('Pages','woo-wishlist'),
			'description' 		=> __('Please select all the pages here','woo-wishlist'),
			'options' => array(
				array(
					'id'		=> 'pickplugins_wl_wishlist_page',
					'title'		=> __('Wishlist Page','woo-wishlist'),
					'details'	=> __('Users will able to view their wishlists','woo-wishlist')." Use shortcode [pickplugins_wl_wishlist] on that page",
					'type'		=> 'select',
					'args'		=> pickplugins_wl_get_wishlist_pages(),
				),
			)
		),
		
		'pick_section_pagination' => array(
			'title' 			=> 	__('Pagination','woo-wishlist'),
			'description' 		=> __('Update your pagination settings','woo-wishlist'),
			'options' => array(
				array(
					'id'		=> 'pickplugins_wl_list_per_page',
					'title'		=> __('Wishlist per Page','woo-wishlist'),
					'details'	=> __('How many wishlists will show per page?','woo-wishlist'),
					'type'		=> 'number',
					'placeholder' => __('10','woo-wishlist'),
				),
				array(
					'id'		=> 'pickplugins_wl_list_items_per_page',
					'title'		=> __('Wishlist items per Page','woo-wishlist'),
					'details'	=> __('How many items (products) will show per page?','woo-wishlist'),
					'type'		=> 'number',
					'placeholder' => __('10','woo-wishlist'),
				),
			)
		),
	),
	
);


$args = array(
	'add_in_menu' => true, 															// true, false
	'menu_type' => 'submenu', 														// main, submenu
	'menu_title' => __( 'Settings', 'woo-wishlist' ), 								// Menu Title
	'page_title' => __( 'Settings', 'woo-wishlist' ), 								// Page Title
	'menu_page_title' => __( 'WooCommerce Wishlist - Settings', 'woo-wishlist' ),	// Menu Page Title
	'capability' => "manage_options",												// Capability
	'menu_slug' => "ww-settings",													// Menu Slug
	'parent_slug' => "edit.php?post_type=wishlist",									// Parent Slug for submenu
	'pages' => array(
		'pickplugins_wl_settings_options' => $pickplugins_wl_settings_options,
	),
);
		
$Pick_settings = new Pick_settings( $args );
		

		
function pickplugins_wl_get_wishlist_pages(){
	
	$pages_array = array( '' => __( 'Select Page', 'woo-wishlist' ) );
	
	foreach( get_pages() as $page ):
		$pages_array[ $page->ID ] = $page->post_title;
	endforeach;
	
	return $pages_array;
}

