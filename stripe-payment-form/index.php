<?php
/*
Plugin Name: Stripe Payment Form
Description: A lightweight WordPress plugin that integrates Stripe Payments using the Stripe Payment Element for secure and seamless online payments.
Author: Arshdeep Singh
Version: 1.0
*/


if (!defined('ABSPATH')) {
    exit;
}

$pk = parse_ini_file(__DIR__ . '/.env')['PK'];
define('PK', $pk);
// Create table in wordpress database



function stripe_plugin_create_table()
{
    create_settings_table();
    create_logs_table();
}


function stripe_plugin_delete_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'stripe_settings';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    // $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS $table_name")); // this is also correct, but no need to use prepare() for table deletion
}

function create_settings_table()
{
    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); // dbDelta() is not loaded automatically it is defined in this file

    $table_name = $wpdb->prefix . "stripe_settings";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name(
    id INT NOT NULL AUTO_INCREMENT,
    pk VARCHAR(100),
    sk VARCHAR(100),
    card_or_link BOOLEAN NOT NULL DEFAULT TRUE,
    secure_link BOOLEAN NOT NULL DEFAULT TRUE,
    currency_amount_mode VARCHAR(50),
    amount DECIMAL(10,2),
    currency VARCHAR(50),
    PRIMARY KEY (id)
    ) $charset_collate";

    dbDelta($sql);
}


function create_logs_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'stripe_payment_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name(
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    amount DECIMAL(10,2),
    currency VARCHAR(50),
    status VARCHAR(50),
    type VARCHAR(100),
    brand VARCHAR(100),
    last4 VARCHAR(50),
    exp_year INT,
    exp_month INT,
    card_country VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
}


function plugin_frontend_scripts()
{
    wp_enqueue_script(
        'stripe-js',
        'https://js.stripe.com/v3/',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'my-script',
        plugins_url('assets/script.js', __FILE__),
        ['stripe-js'], // waits for 'stripe-js' loading
        '1.0',
        true
    );


    wp_localize_script(  // A WordPress function used to pass PHP data (such as URLs, API keys, AJAX URLs, or nonces) to a JavaScript file before it loads.
        'my-script',
        'stripe_data',
        [
            'endpoint' => plugins_url('endpoint.php', __FILE__),
            'save_payment' => plugins_url('save_payment.php', __FILE__),
            'pk' => PK
        ]
    );

    wp_enqueue_style(
        'my-style',
        plugins_url('assets/style.css', __FILE__),
        [],
        '1.0'
    );
}

function plugin_admin_scripts($hook)
{

    if ($hook == 'toplevel_page_stripe-payment-form') { // toplevel_page_<admin-menu-page-slug> (for main page)

        wp_enqueue_script(
            'settings-script',
            plugins_url('assets/settings.js', __FILE__),
            [],
            null,
            true
        );

        wp_localize_Script(
            'settings-script',
            'settings_data',
            [
                'settings_url' => admin_url('admin-ajax.php')
            ]
        );
    }


    if ($hook == 'payment-form_page_payment-logs') { // <admin-menu-page-slug(-first)>_page_<admin-submenu-page-slug> (for submenu page)
        wp_enqueue_script(
            'logs-script',
            plugins_url('assets/logs.js', __FILE__),
            [],
            null,
            true
        );

        wp_localize_script(
            'logs-script',
            'logs_data',
            [
                'ajax_url' => admin_url('admin-ajax.php')
            ]
        );

        wp_enqueue_style(
            'logs-style',
            plugins_url('assets/logs.css', __FILE__),
            [],
            '1.0'
        );
    }
}


function stripe_form($atts)
{
    // Default shortcode attributes
    $atts = shortcode_atts(['amount' => '', 'currency' => ''], $atts, 'stripe_payment_form');

    // Make $amount available in the template
    $amount = $atts['amount'];
    $currency = $atts['currency'];

    ob_start();

    require plugin_dir_path(__FILE__) . 'templates/stripe_payment_form.php';

    return ob_get_clean();

}
// function stripe_form()
// {
//     ob_start();

//     require plugin_dir_path(__FILE__) . 'templates/stripe_payment_form.php';

//     $form = ob_get_clean();
//     return $form;

// }



function stripe_plugin_admin_menu()
{
    add_menu_page(
        'Stripe Payment Form',      // Page title
        'Payment Form',             // Menu title
        'manage_options',           // Capability
        'stripe-payment-form',      // Menu slug
        'plugin_dashboard',         // callback function
        'dashicons-money-alt',      // Icon
        25                          // Position
    );

    add_submenu_page(
        'stripe-payment-form',      // Parent slug
        'Payment Logs',             // Page title
        'Payment Logs',             // Menu title
        'manage_options',
        'payment-logs',             // Menu slug
        'payment_logs'              // Callback function 

    );
}


function plugin_dashboard()
{
    require plugin_dir_path(__FILE__) . 'admin/settings.php';
}

function payment_logs()
{
    require plugin_dir_path(__FILE__) . 'admin/logs.php';
}

function get_payment_logs()
{
    require plugin_dir_path(__FILE__) . 'admin/logs.php';
}

function payment_settings()
{
    require plugin_dir_path(__FILE__) . 'admin/settings.php';
}


register_activation_hook(__FILE__, 'stripe_plugin_create_table');
register_deactivation_hook(__FILE__, 'stripe_plugin_delete_table');
register_uninstall_hook(__FILE__, 'stripe_plugin_delete_table');
add_action('wp_enqueue_scripts', 'plugin_frontend_scripts');  // Loads Scritps on the frontend
add_action('admin_enqueue_scripts', 'plugin_admin_scripts');  // Loads Scripts on the admin menu
add_action('admin_menu', 'stripe_plugin_admin_menu'); // add menu pages
add_action('wp_ajax_get_payment_logs', 'get_payment_logs'); // Registers the get_payment_logs() function to handle the AJAX request with the action name get_payment_logs for logged-in WordPress users.
add_action('wp_ajax_payment_settings', 'payment_settings');
add_shortcode('stripe_payment_form', 'stripe_form');