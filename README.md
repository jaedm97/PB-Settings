# Pick Settings
An easy option and settigns management framwork for WordPress, Developer friendly and elegant look build with native WordPress UI,  

### How to use

#### Adding Pick_Settings class file
```php
include "class-pick-settings.php";
```

#### Add your menu to WordPress Admin Menu
```php
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
```

#### Write your options in the format below
```php
$setting_page_1 = array(

	'page_nav' 	=> __( 'Page 1', 'text-domain' ),
	'page_settings' => array(
		
		'section_1' => array(
			'title' 	=> 	__('Section 1','text-domain'),
			'description' 	=> __('Description of section 1','text-domain'),
			'options' 	=> array(
				array(
					'id'		=> 'select_field',
					'title'		=> __('Select field','text-domain'),
					'details'	=> __('Description of select field','text-domain'),
					'type'		=> 'select',
					'args'		=> array(
						'option_1'	=> __('Option 1','text-domain'),
						'option_2'	=> __('Option 2','text-domain'),
					),
				),
				array(
					'id'		=> 'text_field',
					'title'		=> __('Text field','text-domain'),
					'details'	=> __('Description of text field','text-domain'),
					'type'		=> 'text',
					'placeholder' => __('My text field','text-domain'),
				),
				array(
					'id'		=> 'colorpicker_field',
					'title'		=> __('Color picker field','text-domain'),
					'details'	=> __('Description of colorpicker field','text-domain'),
					'type'		=> 'colorpicker',
				),
				array(
					'id'		=> 'radio_field',
					'title'		=> __('Radio field','text-domain'),
					'details'	=> __('Description of radio field','text-domain'),
					'type'		=> 'radio',
					'args'		=> array(
						'item_1'	=> __('Item 1','text-domain'),
						'item_2'	=> __('Item 2','text-domain'),
						'item_3'	=> __('Item 3','text-domain'),
						'item_4'	=> __('Item 4','text-domain'),
					),
				),
				
			)
		),
		
		'section_2' => array(
			'title' 	=> 	__('Section 2','text-domain'),
			'description' 	=> __('Description of section 2','text-domain'),
			'options' 	=> array(
				array(
					'id'		=> 'number_field',
					'title'		=> __('Number field','text-domain'),
					'details'	=> __('Description of number field','text-domain'),
					'type'		=> 'number',
					'placeholder' => 10,
				),
				array(
					'id'		=> 'checkbox_field',
					'title'		=> __('Checkbox field','text-domain'),
					'details'	=> __('Description of checkbox field','text-domain'),
					'type'		=> 'checkbox',
					'args'		=> array(
						'item_1'	=> __('Item 1','text-domain'),
						'item_2'	=> __('Item 2','text-domain'),
						'item_3'	=> __('Item 3','text-domain'),
					),
				),
			)
		),
	),
);
```

#### Add your options `$setting_page_1` to the Menu
```php
$args = array(
	'add_in_menu'     => true,
	'menu_type'       => 'main',
	'menu_title'      => __( 'My Settings', 'text-domain' ),
	'page_title'      => __( 'My Settings', 'text-domain' ),
	'menu_page_title' => __( 'My Settings Page', 'text-domain' ),
	'capability'      => "manage_options",
	'menu_slug'       => "my-settings",
	'menu_icon'       => "dashicons-hammer",
	'pages' 	  => array(
		'setting_page_1' => $setting_page_1,
	),
);

$Pick_settings = new Pick_settings( $args );
```
