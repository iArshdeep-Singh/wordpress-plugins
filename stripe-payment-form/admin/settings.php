<?php

if (!defined('ABSPATH')) {
    exit;

}

$currencies = file_get_contents(plugins_url('../assets/currencies.json', __FILE__));

global $wpdb;

$table_name = $wpdb->prefix . "stripe_settings";

$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id=%d", 1), ARRAY_A);

if ($result !== null) {



} else {
    echo "<h3 style='color:red;'>Stripe configuration is not set up yet.</h3>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);


    exit;
}



?>

<div id="settings">
    <h1>Dashboard Page</h1>

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
        <select name="currency_amount_mode">
            <option value="selection" selected>Let Customers Select Currency & Enter Amount</option>
            <option value="fixed">Use a Fixed Currency & Amount</option>
            <option value="custom">Enter Currency & Amount on the Checkout Page</option>
        </select>
    </div>

    <div class="stripe-config" style="display: none;">
        <label>Enter amount and choose currency</label>
        <input type=" text" placeholder="Enter Amount">
        <select name="currency"></select>
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