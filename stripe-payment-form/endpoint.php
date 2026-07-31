<?php

$data = json_decode(file_get_contents("php://input"), true);

$sk = '';
$card_or_link = '';

global $wpdb;

$table_name = $wpdb->prefix . "stripe_settings";

$stripe_config_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1", []), ARRAY_A);

if ($stripe_config_data !== null) {
    $sk = $stripe_config_data['sk'];
    $card_or_link = $stripe_config_data['card_or_link'];
}

$amount = $data['amount'];
$currency = $data['currency'];

$env = parse_ini_file(__DIR__ . '/.env');
// $sk = $env['SK'];
$url = "https://api.stripe.com/v1/payment_intents";

$body = [
    'amount' => $amount,
    'currency' => $currency,
    'automatic_payment_methods[enabled]' => $stripe_config_data['card_or_link'] == "1" ? "true" : 'false'
];


$body = $stripe_config_data['card_or_link'] == "0" ? [...$body, ...['payment_method_types' => ['card']]] : $body;


$response = wp_remote_post($url, [
    "headers" => [
        "Content-Type" => "application/x-www-form-urlencoded",
        "Authorization" => "Bearer " . $sk
    ],
    "body" => http_build_query($body)
]);

if (is_wp_error($response)) {
    echo $response->get_error_message();
} else {
    echo wp_remote_retrieve_body($response);
}

exit;



// $ch = curl_init();

// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     "Content-Type: application/x-www-form-urlencoded",
//     "Authorization: Bearer " . $sk
// ]);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));


// $response = curl_exec($ch);

// file_put_contents('data.txt', $response);

// if (curl_errno($ch)) {
//     echo curl_error($ch);
// } else {
//     echo $response;
// }