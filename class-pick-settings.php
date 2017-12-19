<?php
/*
* @Author : PickPlugins
* @Copyright : 2015 PickPlugins.com
* @Version : 1.0.8
* @URL : https://github.com/jaedm97/Pick-Settings
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


if( ! class_exists( 'Pick_settings' ) ) {
	
class Pick_settings {
	
	public $data = array();
	
    public function __construct( $args ){
		
		$this->data = &$args;
	
		if( $this->add_in_menu() ) {
			add_action( 'admin_menu', array( $this, 'add_menu_in_admin_menu' ), 12 );
		}
		
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 10 );
		add_action( 'wp_dashboard_setup', array( $this, 'update_app_data' ), 10 );
		
		add_action( 'admin_init', array( $this, 'pick_settings_display_fields' ), 12 );
		add_filter( 'whitelist_options', array( $this, 'pick_settings_whitelist_options' ), 99, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'pick_enqueue_color_picker' ) );
	}
	
	public function add_menu_in_admin_menu() {
		
		if( "main" == $this->get_menu_type() ) {
			add_menu_page( $this->get_menu_name(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), array( $this, 'pick_settings_display_function' ), $this->get_menu_icon() );
		}
		
		if( "submenu" == $this->get_menu_type() ) {
			add_submenu_page( $this->get_parent_slug(), $this->get_page_title(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), array( $this, 'pick_settings_display_function' ) );
		}
	}
	
	public function pick_enqueue_color_picker(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}
	
	public function pick_settings_display_fields() { 
		
 		foreach( $this->get_settings_fields() as $key => $setting ):
		
			add_settings_section(
				$key,
				isset( $setting['title'] ) ? $setting['title'] : "",
				array( $this, 'pick_settings_section_callback' ), 
				$this->get_current_page()
			);
			
			foreach( $setting['options'] as $option ) :
			add_settings_field( $option['id'], $option['title'], array($this,'pick_settings_field_generator'), $this->get_current_page(), $key, $option );
			endforeach;
		
		endforeach;
	}
	
	public function pick_settings_field_generator( $option ) {
			
		$id 		= isset( $option['id'] ) ? $option['id'] : "";
		$details 	= isset( $option['details'] ) ? $option['details'] : "";
		
		if( empty( $id ) ) return;
		
		try{
			if( isset($option['type']) && $option['type'] === 'select' ) 		$this->pick_settings_generate_select( $option );
			elseif( isset($option['type']) && $option['type'] === 'checkbox')	$this->pick_settings_generate_checkbox( $option );
			elseif( isset($option['type']) && $option['type'] === 'radio')		$this->pick_settings_generate_radio( $option );
			elseif( isset($option['type']) && $option['type'] === 'textarea')	$this->pick_settings_generate_textarea( $option );
			elseif( isset($option['type']) && $option['type'] === 'number' ) 	$this->pick_settings_generate_number( $option );
			elseif( isset($option['type']) && $option['type'] === 'text' ) 		$this->pick_settings_generate_text( $option );
			elseif( isset($option['type']) && $option['type'] === 'colorpicker')$this->pick_settings_generate_colorpicker( $option );
			elseif( isset($option['type']) && $option['type'] === 'datepicker')	$this->pick_settings_generate_datepicker( $option );
			elseif( isset($option['type']) && $option['type'] === 'select2')	$this->pick_settings_generate_select2( $option );
			elseif( isset($option['type']) && $option['type'] === 'range')		$this->pick_settings_generate_range( $option );
			elseif( isset($option['type']) && $option['type'] === 'media')		$this->pick_settings_generate_media( $option );

			elseif( isset($option['type']) && $option['type'] === 'custom' ) 	do_action( "pick_settings_action_custom_field_$id", $option );

			if( !empty( $details ) ) echo "<p class='description'>$details</p>";
		
		}
		catch(Pick_error $e) {
			echo $e->get_error_message();
		}
	}

	public function pick_settings_generate_media( $option ){

		$id			= isset( $option['id'] ) ? $option['id'] : "";
		$value		= get_option( $id );
		$media_url	= wp_get_attachment_url( $value );
		
		wp_enqueue_media();
		
		echo "<div class='media_preview' style='width: 150px;margin-bottom: 10px;'>";
		echo "<img id='media_preview_$id' src='$media_url' style='width:100%'/>";
		echo "</div>";
		echo "<input type='hidden' name='$id' id='media_input_$id' value='$value' />";
		echo "<div class='button' id='media_upload_$id'>Upload</div>";
		
		echo "<script>jQuery(document).ready(function($){
		$('#media_upload_$id').click(function() {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment) {
				$('#media_preview_$id').attr('src', attachment.url);
				$('#media_input_$id').val(attachment.id);
				wp.media.editor.send.attachment = send_attachment_bkp;
			}
			wp.media.editor.open($(this));
			return false;
		});
		});	</script>";
	}
	
	public function pick_settings_generate_range( $option ){

		$id 		= isset( $option['id'] ) ? $option['id'] : "";
		$min 		= isset( $option['min'] ) ? $option['min'] : 1;
		$max 		= isset( $option['max'] ) ? $option['max'] : 100;
		$value		= get_option( $id );
		$value		= empty( $value ) ? 0 : $value;
		
		echo "<input type='range' min='$min' max='max' name='$id' value='$value' class='pick_range' id='$id'>";
		echo "<span id='{$id}_show_value' class='show_value'>$value</span>";
		
		echo "<style>
		.pick_range {
			-webkit-appearance: none;
			width: 280px;
			height: 20px;
			border-radius: 3px;
			background: #9a9a9a;
			outline: none;
			opacity: 0.7;
			-webkit-transition: .2s;
			transition: opacity .2s;
		}
		.pick_range:hover { opacity: 1; }
		.show_value {
			font-size: 25px;
			margin-left: 8px;
		}
		.pick_range::-webkit-slider-thumb {
			-webkit-appearance: none;
			appearance: none;
			width: 25px;
			height: 25px;
			border-radius: 50%;
			background: #138E77;
			cursor: pointer;
		}
		.pick_range::-moz-range-thumb {
			width: 25px;
			height: 25px;
			border-radius: 50%;
			background: #138E77;
			cursor: pointer;
		}
		</style>
		<script>jQuery(document).ready(function($) { 
			$('#$id').on('input', function(e) { $('#{$id}_show_value').html( $('#$id').val() ); });
		})
		</script>";
	}
	
	public function pick_settings_generate_select2( $option ){

		$id 		= isset( $option['id'] ) ? $option['id'] : "";
		$args 		= isset( $option['args'] ) ? $option['args'] : array();	
		$args		= is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
		$value		= get_option( $id );
		$multiple 	= isset( $option['multiple'] ) ? $option['multiple'] : '';	
		
		wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array('jquery') );
		
		echo $multiple ? "<select name='{$id}[]' id='$id' multiple>" : "<select name='{$id}' id='$id'>";
		foreach( $args as $key => $name ):
			
			if( $multiple ) $selected = in_array( $key, $value ) ? "selected" : "";
			else $selected = $value == $key ? "selected" : "";
			echo "<option $selected value='$key'>$name</option>";
			
		endforeach;
		echo "</select>";
		
		echo "<script>jQuery(document).ready(function($) { $('#$id').select2({
			width: '320px',
			allowClear: true
		});});</script>";
	}
	
	public function pick_settings_generate_datepicker( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 			= get_option( $id );
		
		wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui' );
		
		echo "<input type='text' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
		echo "<script>jQuery(document).ready(function($) { $('#$id').datepicker();});</script>";
	}
	
	public function pick_settings_generate_colorpicker( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		
		echo "<input type='text' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
		
		echo "<script>jQuery(document).ready(function($) { $('#$id').wpColorPicker();});</script>";
	}
	
	public function pick_settings_generate_text( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		
		echo "<input type='text' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
	}
	
	public function pick_settings_generate_number( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		
		echo "<input type='number' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
	}
	
	public function pick_settings_generate_textarea( $option ){
		
		$id = isset( $option['id'] ) ? $option['id'] : "";
		$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		
		$value 	 = get_option( $id );
		
		echo "<textarea name='$id' id='$id' cols='40' rows='5' placeholder='$placeholder'>$value</textarea>";
	}
	
	public function pick_settings_generate_select( $option ){
		
		$id 	= isset( $option['id'] ) ? $option['id'] : "";
		$args 	= isset( $option['args'] ) ? $option['args'] : array();	
		$args	= is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
		$value	= get_option( $id );
		
		echo "<select name='$id' id='$id'>";
		foreach( $args as $key => $name ):
			$selected = $value == $key ? "selected" : "";
			echo "<option $selected value='$key'>$name</option>";
		endforeach;
		echo "</select>";
	}
		
	public function pick_settings_generate_checkbox( $option ){
		
		$id				= isset( $option['id'] ) ? $option['id'] : "";
		$args			= isset( $option['args'] ) ? $option['args'] : array();
		$args			= is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
		$option_value	= get_option( $id );
		
		echo "<fieldset>";
		foreach( $args as $key => $value ):

			$checked = is_array( $option_value ) && in_array( $key, $option_value ) ? "checked" : "";
			echo "<label for='$id-$key'><input name='{$id}[]' type='checkbox' id='$id-$key' value='$key' $checked>$value</label><br>";
			
		endforeach;
		echo "</fieldset>";
	}
		
	public function pick_settings_generate_radio( $option ){

		$id				= isset( $option['id'] ) ? $option['id'] : "";
		$args			= isset( $option['args'] ) ? $option['args'] : array();
		$args			= is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
		$option_value	= get_option( $id );

		echo "<fieldset>";
		foreach( $args as $key => $value ):

			$checked = is_array( $option_value ) && in_array( $key, $option_value ) ? "checked" : "";
			echo "<label for='$id-$key'><input name='{$id}[]' type='radio' id='$id-$key' value='$key' $checked>$value</label><br>";
				
		endforeach;
		echo "</fieldset>";
	}
	
	public function pick_settings_section_callback( $section ) { 
		
		$data = isset( $section['callback'][0]->data ) ? $section['callback'][0]->data : array();
		$description = isset( $data['pages'][$this->get_current_page()]['page_settings'][$section['id']]['description'] ) ? $data['pages'][$this->get_current_page()]['page_settings'][$section['id']]['description'] : "";
		
		echo $description;
	}
	
	public function pick_settings_whitelist_options( $whitelist_options ){
		
		foreach( $this->get_pages() as $page_id => $page ): foreach( $page['page_settings'] as $section ):
			foreach( $section['options'] as $option ):
				$whitelist_options[$page_id][] = $option['id'];
			endforeach; endforeach;
		endforeach;
		
		return $whitelist_options;
	}
	
	public function pick_settings_display_function(){

		echo "<div class='wrap'>";
		echo "<h2>{$this->get_menu_page_title()}</h2><br>";
		
		parse_str( $_SERVER['QUERY_STRING'], $nav_menu_url_args );
		global $pagenow;
		
		
		settings_errors();
		
		$tab_count 	 = 0;
		echo "<nav class='nav-tab-wrapper'>";
		foreach( $this->get_pages() as $page_id => $page ): $tab_count++;
			
			$active = $this->get_current_page() == $page_id ? 'nav-tab-active' : '';
			$nav_menu_url_args['tab'] = $page_id;
			$nav_menu_url = http_build_query( $nav_menu_url_args );
			
			echo "<a href='$pagenow?$nav_menu_url' class='nav-tab $active'>{$page['page_nav']}</a>";

		endforeach;
        echo "</nav>";

		echo "<form class='pick_settings_form' action='options.php' method='post'>";
		
		settings_fields( $this->get_current_page() );
		do_settings_sections( $this->get_current_page() );
		do_action( $this->get_current_page() );
		
		$get_settings_fields = $this->get_settings_fields();
		if( ! empty( $get_settings_fields ) ) submit_button();
		
		echo "</form>";
		
		/* echo "<style>
nav.nav-tab-wrapper {
    border-bottom: 1px solid #DC4840;
}
.nav-tab, .nav-tab:hover, .nav-tab:focus, .nav-tab:active, .nav-tab:visited {
    float: left;
    border-bottom: none;
    padding: 5px 10px;
    font-size: 14px;
    line-height: 24px;
    color: #555;
    background: none;
    border: none;
    outline-color: none;
    outline-style: none;
    outline-width: none;
}
a.nav-tab.nav-tab-active {
    background: #ef726b;
    color: #fffefe;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    border-right: none;
    margin-bottom: -1px;
    border-bottom: 1px solid #DC4840;
}
form.pick_settings_form {
    background: #fff;
    padding: 10px;
    box-shadow: 0 2px 4px 3px rgba(0,0,0,.04);
}
		</style>"; */
		
		echo "</div>";		
	}
	
	
	// Default Functions
	
	public function generate_args_from_string( $string, $option ){
		
		if( strpos( $string, 'PICK_PAGES_ARRAY' ) !== false ) return $this->get_pages_array();
		if( strpos( $string, 'PICK_TAX_' ) !== false ) return $this->get_taxonomies_array( $string, $option );
		if( strpos( $string, 'PICK_POSTS_' ) !== false ) return $this->get_posts_array( $string, $option );
		
		
		return array();
	}
	
	public function get_posts_array( $string, $option ){
		
		$arr_posts = array();
		
		preg_match_all( "/\%([^\]]*)\%/", $string, $matches );
		
		if( isset( $matches[1][0] ) ) $post_type = $matches[1][0];
		else throw new Pick_error('Invalid post type declaration!');
		
		if( ! post_type_exists( $post_type ) ) throw new Pick_error("Post type <strong>$post_type</strong> doesn't exists!");
		
		$wp_query 	= isset( $option['wp_query'] ) ? $option['wp_query'] : array();
		$ppp 		= isset( $wp_query['posts_per_page'] ) ? $option['posts_per_page'] : -1;
		$wp_query 	= array_merge( $wp_query, array( 'post_type' => $post_type, 'posts_per_page' => $ppp ) );
		$posts 		= get_posts( $wp_query );
		
		foreach( $posts as $post ) $arr_posts[ $post->ID ] = $post->post_title;
				
		return $arr_posts;		
	}
	
	public function get_taxonomies_array( $string, $option ){
		
		$taxonomies = array();
		
		preg_match_all( "/\%([^\]]*)\%/", $string, $matches );
		
		if( isset( $matches[1][0] ) ) $taxonomy = $matches[1][0];
		else throw new Pick_error('Invalid taxonomy declaration !');
		
		if( ! taxonomy_exists( $taxonomy ) ) throw new Pick_error("Taxonomy <strong>$taxonomy</strong> doesn't exists !");
		
		$terms = get_terms( $taxonomy, array(
			'hide_empty' => false,
		) );
		
		foreach( $terms as $term ) $taxonomies[ $term->term_id ] = $term->name;
				
		return $taxonomies;		
	}
	
	public function get_pages_array(){
		
		$pages_array = array();
		foreach( get_pages() as $page ) $pages_array[ $page->ID ] = $page->post_title;
		
		return apply_filters( 'FILTER_PICK_PAGES_ARRAY', $pages_array );
	}
	
	
	// Get Data from Dataset //
	
	public function get_option_ids(){
		
		$option_ids = array();
		foreach( $this->get_pages() as $page ):
			$setting_sections = isset( $page['page_settings'] ) ? $page['page_settings'] : array();
			foreach( $setting_sections as $setting_section ):
		
				$options = isset( $setting_section['options'] ) ? $setting_section['options'] : array();
				foreach( $options as $option ) $option_ids[] = isset( $option['id'] ) ? $option['id'] : '';
				
			endforeach;
		endforeach;
		return $option_ids; 
	}
	
	public function get_current_page(){
		
		$all_pages 		= $this->get_pages();
		$page_keys 		= array_keys($all_pages);
		$default_tab 	= ! empty( $all_pages ) ? reset( $page_keys ) : "";
		
		return isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $default_tab;
	}
	private function get_menu_type(){
		if( isset( $this->data['menu_type'] ) ) return $this->data['menu_type'];
		else return "main";
	}
	private function get_pages(){
		if( isset( $this->data['pages'] ) ) return $this->data['pages'];
		else return array();
	}
	private function get_settings_fields(){
		if( isset( $this->get_pages()[$this->get_current_page()]['page_settings'] ) ) return $this->get_pages()[$this->get_current_page()]['page_settings'];
		else return array();
	}
	private function get_settings_name(){
		if( isset( $this->data['settings_name'] ) ) return $this->data['settings_name'];
		else return "my_custom_settings";
	}
	private function get_menu_icon(){
		if( isset( $this->data['menu_icon'] ) ) return $this->data['menu_icon'];
		else return "";
	}
	private function get_menu_slug(){
		if( isset( $this->data['menu_slug'] ) ) return $this->data['menu_slug'];
		else return "my-custom-settings";
	}
	private function get_capability(){
		if( isset( $this->data['capability'] ) ) return $this->data['capability'];
		else return "manage_options";
	}
	private function get_menu_page_title(){
		if( isset( $this->data['menu_page_title'] ) ) return $this->data['menu_page_title'];
		else return "My Custom Menu";
	}
	private function get_menu_name(){
		if( isset( $this->data['menu_name'] ) ) return $this->data['menu_name'];
		else return "Menu Name";
	}
	private function get_menu_title(){
		if( isset( $this->data['menu_title'] ) ) return $this->data['menu_title'];
		else return "Menu Title";
	}
	private function get_page_title(){
		if( isset( $this->data['page_title'] ) ) return $this->data['page_title'];
		else return "Page Title";
	}
	private function add_in_menu(){
		if( isset( $this->data['add_in_menu'] ) && $this->data['add_in_menu'] ) return true;
		else return false;
	}
	private function get_parent_slug(){
		if( isset( $this->data['parent_slug'] ) && $this->data['parent_slug'] ) return $this->data['parent_slug'];
		else return "";
	}
	
	public function update_app_data(){
		
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL 			=> "https://api.github.com/repos/jaedm97/Pick-Settings/contents/version",
			CURLOPT_HTTPHEADER 		=> [
				"Accept: application/vnd.github.v3+json",
				"Content-Type: text/plain",
				"User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 YaBrowser/16.3.0.7146 Yowser/2.5 Safari/537.36"
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
		]);

		$data 			= curl_exec($curl);
		curl_close($curl);
		$array 			= json_decode($data, true);
		$latest_version	= base64_decode( $array['content'] );
		$latest_version = empty( $latest_version ) ? "1.0.0" : $latest_version;

		update_option( 'pick_settings_latest_version', $latest_version );
		
		
		$docComments 	= array_filter( token_get_all( file_get_contents( __FILE__ ) ), function( $entry ) { return $entry[0] == T_COMMENT; } );
		$fileDocComment = array_shift( $docComments );
		$regexp 		= "/\@.*\:\s.*\r/";

		preg_match_all( $regexp, $fileDocComment[1], $matches );

		foreach( $matches[0] as $line ){

			$line 		= str_ireplace( array( "@", " : " ), array( "", "~" ), $line );
			$arr_item 	= explode( "~", $line );
			$line_key	= isset( $arr_item[0] ) ? trim( $arr_item[0] ) : "";
			$line_key	= strtolower( $line_key );
			$line_value	= isset( $arr_item[1] ) ? trim( $arr_item[1] ) : "";
				
			update_option( "pick_settings_$line_key", $line_value );
		}			
	}
	
	public function admin_notices(){
		
		$PICK_SETTINGS_DEBUG = ! defined( "PICK_SETTINGS_DEBUG" ) ? true : PICK_SETTINGS_DEBUG;
		if( ! $PICK_SETTINGS_DEBUG ) return;
		
		$latest_version 	= get_option( 'pick_settings_latest_version' );
		$latest_version 	= empty( $latest_version ) ? "1.0.0" : $latest_version;
		$current_version 	= get_option( 'pick_settings_version' );
		$pick_settings_url	= get_option( 'pick_settings_url' );
		
		if( empty( $current_version ) ) return;
		
		$version_difference	= version_compare( $latest_version,  $current_version );
		$notice_message		= sprintf("<strong>Pick Settings</strong> has a new version (%s) <a href='%s'>Update</a> now", $latest_version, $pick_settings_url );
		$notice_message_2	= sprintf("<i>Download the latest version and replace with your version(%s) here <b>%s</b></i>", $current_version, __FILE__ );
		
		$message = __( 'Irks! An error has occurred.', 'sample-text-domain' );

		// printf( '<div class="%1$s"><p>%2$s</p><p>%3$s</p></div>', esc_attr( "notice notice-warning is-dismissible" ), $notice_message, $notice_message_2 ); 
		
		
		printf( '<div class="%1$s"><p>%2$s</p><p>%3$s</p></div>', esc_attr( "notice notice-warning is-dismissible" ), $notice_message, $notice_message_2 ); 
	}
	
}

}


if( ! class_exists( 'Pick_error' ) ) {
	class Pick_error extends Exception { 

		public function __construct($message, $code = 0, Exception $previous = null) {
			parent::__construct($message, $code, $previous);
		}
		
		public function get_error_message(){
			
			return "<p class='notice notice-error' style='padding: 10px;'>{$this->getMessage()}</p>";
		}
	}
}