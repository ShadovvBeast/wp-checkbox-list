<?php
/**
 * Plugin Name: WP Checkbox List
 * Plugin URI: https://github.com/ShadovvBeast/wp-checkbox-list
 * Description: Checkbox list plugin for WordPress
 * Version: 0.1
 * Text Domain: wp-checkbox-list
 * Author: ShadowBeast (Asaf Levy)
 * Author URI: https://github.com/ShadovvBeast
 */
function wp_checkbox_list_shortcode($atts) {
    global $wpdb;
    $item_table_name = $wpdb->prefix . 'cl_items';
    $check_table_name = $wpdb->prefix . 'cl_checks';
    $user_id = get_current_user_id();
    $items = $wpdb->get_results($wpdb->prepare("SELECT items.*, checks.check_id 
                                                FROM $item_table_name items
                                                LEFT JOIN $check_table_name checks ON (checks.item_id = items.item_id AND checks.user_id = $user_id) 
                                                WHERE list_id = %d
                                                ORDER BY items.item_id ASC", $atts['list_id']), 'ARRAY_A');
    $result ='<ol id="list-'.$atts['list_id'].'">';
    foreach  ($items as $item)
        $result .= "<li><input class='wp-checkbox-list-checkbox' id='{$item['item_id']}' name='{$item['item_id']}' type='checkbox' ".($item['check_id'] ? 'checked="checked" disabled="disabled"' : '')." }><label for='{$item['item_id']}'>{$item['item_text']}</label></li>";
    $result.= '</ol>';
    return $result;
}

function wp_list_checkbox_check() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cl_checks';
    $wpdb->insert($table_name, ['user_id'=> get_current_user_id(), 'item_id' => $_POST['item_id']]);
}
function wp_checkbox_list_enqueue() {
    wp_enqueue_script('wp-checkbox-list-js', plugins_url('/js/list.js', __FILE__));
    global $wpdb;
    $table_name = $wpdb->prefix . 'cl_checks';
    $user_id = get_current_user_id();
    wp_localize_script( 'wp-checkbox-list-js', 'list_ajax_object', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'checked_items' => $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY item_id ASC")]);
    wp_enqueue_style('wp-checkbox-list-style', plugins_url('wp-checkbox-list.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'wp_checkbox_list_enqueue');
add_action( 'wp_ajax_list_checkbox_check', 'wp_list_checkbox_check');
add_shortcode('wp-checkbox-list', 'wp_checkbox_list_shortcode');
