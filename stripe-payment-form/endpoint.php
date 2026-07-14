<?php

$data = json_decode(file_get_contents("php://input"), true);

$amount = $data['amount'];
$currency = $data['currency'];

$env = parse_ini_file(__DIR__ . '/.env');
$sk = $env['SK'];
$url = "https://api.stripe.com/v1/payment_intents";

$body = [
    'amount' => $amount,
    'currency' => $currency,
    'automatic_payment_methods[enabled]' => 'true',
    // 'payment_method_types' => ['card'], // show only card
    // 'receipt_email' => "iarshdeephans@gmail.com"// show only card
    "excluded_payment_method_types" => [
        "cashapp"
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/x-www-form-urlencoded",
    "Authorization: Bearer " . $sk
]);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));


$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    echo $response;
}

