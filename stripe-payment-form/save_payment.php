<?php

// require dirname(__DIR__, 3) . '/wp-load.php';
global $wpdb;
$table_name = $wpdb->prefix . "stripe_settings";
$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1", []), ARRAY_A);

$sk = $result['sk'];


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["payment_intent"]) && !empty($data["payment_intent"])) {

    $id = $data["payment_intent"];

    $response = stripe_payment_intent_and_charges($sk, $id);

    echo json_encode($response);
    // header("Location: " . admin_url('admin-ajax.php') . "?action=redirect&payment_intent=" . $id . "&payment_intent_client_secret=" . $data['payment_intent_client_secret'] . "&redirect_status=" . $data['redirect_status']);
    exit;

} else if (isset($_GET["payment_intent"])) {

    $id = $_GET["payment_intent"];

    $response = stripe_payment_intent_and_charges($sk, $id);

    echo json_encode($response);
    header("Location: " . admin_url('admin-ajax.php') . "?action=redirect&payment_intent=" . $id . "&payment_intent_client_secret=" . $data['payment_intent_client_secret'] . "&redirect_status=" . $data['redirect_status']);
    exit;
    // echo "<script>history.go(-2)</script>"; // redirect
}


function stripe_payment_intent_and_charges($sk, $id)
{

    $url = "https://api.stripe.com/v1/payment_intents/{$id}";

    $response = wp_remote_request($url, [
        "method" => "GET",
        "headers" => [
            "content-type" => "application/json",
            "Authorization" => "Bearer " . $sk
        ]
    ]);


    $data = wp_remote_retrieve_body($response);
    $decoded_data = json_decode($data, true);

    $latest_charge = $decoded_data['latest_charge'];

    if (is_wp_error($response)) {
        file_put_contents("stripe.json", $response->get_error_message());
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
