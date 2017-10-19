# Pick Settings
An easy option and settigns management framwork for WordPress, Developer friendly and elegant look build with native WordPress UI,  

### How to use
`
include "class-pick-settings.php";

$args = array(
	'add_in_menu'     => true,
	'menu_type'       => 'main',
	'menu_title'      => __( 'My Settings', 'text-domain' ),
	'page_title'      => __( 'My Settings', 'text-domain' ),
	'menu_page_title' => __( 'My Settings Page', 'text-domain' ),
	'capability'      => "manage_options",
	'menu_slug'       => "my-settings",
	'menu_icon'       => "dashicons-hammer",
);

$Pick_settings = new Pick_settings( $args );

`
