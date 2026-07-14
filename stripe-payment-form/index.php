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

// Create table in wordpress database
register_activation_hook(__FILE__, 'stripe_plugin_create_table');

function stripe_plugin_create_table()
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



$pk = parse_ini_file(__DIR__ . '/.env')['PK'];
define('PK', $pk);

function plugin_scripts()
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

add_action('wp_enqueue_scripts', 'plugin_scripts');


function stripe_form($atts)
{
    // Default shortcode attributes
    $atts = shortcode_atts(['amount' => '', 'currency' => 'usd'], $atts, 'stripe_payment_form');

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

add_shortcode('stripe_payment_form', 'stripe_form');

