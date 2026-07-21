<?php

require dirname(__DIR__, 3) . '/wp-load.php';


$env = parse_ini_file(__DIR__ . '/.env');
$sk = $env['SK'];

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["payment_intent"]) && !empty($data["payment_intent"])) {

    $id = $data["payment_intent"];

    $response = stripe_payment_intent_and_charges($sk, $id);

    echo json_encode($response);

} else if (isset($_GET["payment_intent"])) {

    $id = $_GET["payment_intent"];

    $response = stripe_payment_intent_and_charges($sk, $id);

    echo json_encode($response);
    // echo "<script>history.go(-2)</script>"; // redirect
}


function stripe_payment_intent_and_charges($sk, $id)
{

    $url = "https://api.stripe.com/v1/payment_intents/{$id}";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer " . $sk
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    $response = curl_exec($ch);

    $decoded_data = json_decode($response, true);
    $latest_charge = $decoded_data['latest_charge'];

    if (!isset($decoded_data['latest_charge'])) {
        file_put_contents("stripe.json", $response);
        exit;
    }

    if (curl_errno($ch)) {
        file_put_contents("stripe.json", $response);
    } else {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.stripe.com/v1/charges/{$latest_charge}");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $sk
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        $res = curl_exec($curl);
        $decoded = json_decode($res);

        if (isset($decoded->status) && $decoded->status == "succeeded") {

            global $wpdb;
            $table = $wpdb->prefix . 'stripe_payment_logs';

            $amount_in_cents = $decoded->amount;
            $amount = $amount_in_cents / 100;

            $created = date("Y-m-d H:i:s", $decoded->created);

            if ($decoded->payment_method_details->type !== "card") {

                $response = [
                    "status" => $decoded->status,
                    "type" => $decoded->payment_method_details->type,
                    "amount" => $amount,
                    "currency" => $decoded->currency,
                    "created_at" => $created
                ];



                $result = $wpdb->insert(
                    $table,
                    $response,
                    [
                        '%s',
                        '%s',
                        '%f',
                        '%s',
                        '%s'
                    ]
                );

                if ($result === false) {
                    file_put_contents('db_error.txt', $wpdb->last_error . PHP_EOL, FILE_APPEND);
                }

                return $response;

            } else {

                $response = [
                    "status" => $decoded->status,
                    "type" => $decoded->payment_method_details->type,
                    "card" => [
                        "brand" => $decoded->payment_method_details->card->brand,
                        "last4" => $decoded->payment_method_details->card->last4,
                        "exp_year" => $decoded->payment_method_details->card->exp_year,
                        "exp_month" => $decoded->payment_method_details->card->exp_month,
                        "country" => $decoded->payment_method_details->card->country,
                    ],
                    "amount" => $amount,
                    "currency" => $decoded->currency,
                    "created" => $created
                ];


                global $wpdb;

                $result = $wpdb->insert(
                    $table,
                    [
                        "status" => $decoded->status,
                        "type" => $decoded->payment_method_details->type,
                        "brand" => $decoded->payment_method_details->card->brand,
                        "last4" => $decoded->payment_method_details->card->last4,
                        "exp_year" => $decoded->payment_method_details->card->exp_year,
                        "exp_month" => $decoded->payment_method_details->card->exp_month,
                        "card_country" => $decoded->payment_method_details->card->country,
                        "amount" => $amount,
                        "currency" => $decoded->currency,
                        "created_at" => $created
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%s',
                        '%f',
                        '%s',
                        '%s'
                    ]
                );

                if ($result === false) {
                    file_put_contents('db_error.txt', $wpdb->last_error . PHP_EOL, FILE_APPEND);
                }

                return $response;
            }

        }
    }
}
