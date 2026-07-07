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
            'pk' => ''
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


function stripe_form()
{
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

