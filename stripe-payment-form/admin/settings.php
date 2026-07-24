<?php

if (!defined('ABSPATH')) {
    exit;
}

$currencies = file_get_contents(plugins_url('../assets/currencies.json', __FILE__));

$config_message_is = true;
$message = "";

global $wpdb;

$table_name = $wpdb->prefix . "stripe_settings";

$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"), ARRAY_A);

if ($result !== null) {
    $config_message_is = false;
} else {
    $config_message_is = true;
    $message = "<h3 style='color:red;'>Stripe configuration is not set up yet.</h3>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $pk = $data['pk'] ?? "";
    $sk = $data['sk'] ?? "";
    $card_or_link = $data['card_or_link'] ?? true;
    $secure_link = $data['secure_link'] ?? true;
    $currency_amount_mode = $data['currency_amount_mode'] ?? "selection";
    $amount = $data['amount'] ?? 0;
    $currency = $data['currency'] ?? "usd";

    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"), ARRAY_A);

    if ($result !== null) {
        echo wp_send_json($result);
    } else {

        // $config_data = $wpdb->query($wpdb->prepare("INSERT INTO $table_name (pk, sk, card_or_link, secure_link, currency_amount_mode, amount, currency) VALUES (%s, %s, %d, %d, %s, %d, %s)", $pk, $sk, $card_or_link, $secure_link, $currency_amount_mode, $amount, $currency));
        $config_data = $wpdb->insert(
            $table_name,
            [
                "pk" => $pk,
                "sk" => $sk,
                "card_or_link" => $card_or_link,
                "secure_link" => $secure_link,
                "currency_amount_mode" => $currency_amount_mode,
                "amount" => $amount,
                "currency" => $currency
            ],
            [
                "%s",
                "%s",
                "%d",
                "%d",
                "%s",
                "%d",
                "%s"
            ]
        );

        echo wp_send_json($config_data);
    }

    exit;
}



?>

<div id="settings">
    <h1>Dashboard Page</h1>

    <?= $config_message_is ? $message : "" ?>

    <div class="stripe-config">
        <label>Enter Publishable Key</label>
        <input type="text" name="pk" placeholder="pk" />
    </div>

    <div class="stripe-config">
        <label>Enter Secret Key</label>
        <input type="text" name="sk" placeholder="sk" />
    </div>

    <div class="stripe-config">
        <label>Would you like to accept only card payments, or do you also need support for payment
            links
            and third-party integrations?</label>
        <select name="card-or-link">
            <option value="true" selected>Yes</option>
            <option value="false">No (Only Card Payments)</option>
        </select>

    </div>

    <div class="stripe-config">
        <label>Enable Stripe Secure Payment Link</label>
        <select name="secure-link">
            <option value="true" selected>Yes</option>
            <option value="false">No</option>
        </select>
    </div>

    <div class="stripe-config">
        <label>Currency & Amount Mode</label>
        <select name="currency-amount-mode">
            <option value="selection" selected>Let Customers Select Currency & Enter Amount</option>
            <option value="fixed">Use a Fixed Currency & Amount</option>
            <option value="custom">Enter Currency & Amount on the Checkout Page</option>
        </select>
    </div>

    <div class="stripe-config" style="display: none;">
        <label>Enter amount and choose currency</label>
        <select name="currency"></select>
        <input type=" text" name="amount" placeholder="Enter Amount" />
    </div>

    <div class="stripe-config">
        <label>Redirect Page Code (PHP)</label>
        <textarea name="redirect-page" rows="15" cols="100" placeholder="Paste your code here..."></textarea>
    </div>

    <button id="save-update-code">Save</button>

</div>

<script>

    let currencies = <?= $currencies ?>

</script>