<?php
/*
Plugin Name: SW WP Track User Referer
Plugin URI: https://www.shariqueweb.com
Description: Capture User Referer URL when a user Registers on your wordpress website. Display referer info on user listing page on backend. Works with Woocommerce too.
Version: 1.0
Author: Sharique Anwer
Author URI: http://www.shariqueweb.com/
License: GPLv2 or later
*/




/* Set Cookie for New Users - Cookie Validity: 30 Days */
add_action( 'init', 'swwp_set_new_user_cookie');
function swwp_set_new_user_cookie(){
	$cookie_value	=	isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
	
	if(!empty($cookie_value)){
	
		if ( !is_admin() && !isset($_COOKIE['swwp_new_user_referer'])){
			setcookie( 'swwp_new_user_referer', sanitize_text_field($cookie_value), time()+3600*24*30, COOKIEPATH, COOKIE_DOMAIN, false);
		}
	}
}






/* Update User Referer in usermeta table */
add_action( 'user_register', 'swwp_save_referer', 10, 1 );
function swwp_save_referer($user_id){
    if (isset( $_COOKIE['swwp_new_user_referer'])){
        update_user_meta($user_id, 'swwp_referal_url', sanitize_text_field($_COOKIE['swwp_new_user_referer']));
	}
}







/* Add Column on User Listing Page in Admin Area */
add_filter( 'manage_users_columns', 'swwp_modify_user_table_column_in_admin_area' );
function swwp_modify_user_table_column_in_admin_area( $column ) {
    $column['swwp_user_referer_url'] = 'Referer';
    return $column;
}






/* Add Column Value on User Listing Page in Admin Area */
add_filter( 'manage_users_custom_column', 'swwp_add_value_in_user_table_column_in_admin_area', 10, 3 );
function swwp_add_value_in_user_table_column_in_admin_area( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'swwp_user_referer_url' :
            return esc_url(get_user_meta($user_id, 'swwp_referal_url', true ));
        default:
    }
    return $val;
}
