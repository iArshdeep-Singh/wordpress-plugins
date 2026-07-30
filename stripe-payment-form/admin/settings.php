<?php

if (!defined('ABSPATH')) {
    exit;
}


$config_is_setup = true;
$message = "";

global $wpdb;

$table_name = $wpdb->prefix . "stripe_settings";

$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"), ARRAY_A);

if ($result !== null) {
    $config_is_setup = true;
} else {
    $config_is_setup = false;
    $message = "<h3 style='color:red;'>Stripe configuration is not set up yet.</h3>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $pk = $data['pk'] ?? "";
    $sk = $data['sk'] ?? "";
    $card_or_link = $data['card_or_link'];
    $secure_link = $data['secure_link'];

    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"), ARRAY_A);

    if ($row !== null) {

        $result = $wpdb->update($table_name, ["pk" => $pk, "sk" => $sk, "card_or_link" => $card_or_link, "secure_link" => $secure_link], ["id" => $row['id']], ['%s', '%s', '%d', '%d'], ['%d']);

        echo wp_send_json([$result, ["updated" => "yes"], ["message" => $wpdb->last_error]]);
    } else {

        $config_data = $wpdb->query($wpdb->prepare("INSERT INTO $table_name (pk, sk, card_or_link, secure_link) VALUES (%s, %s, %d, %d)", $pk, $sk, $card_or_link, $secure_link));

        if ($config_data === false) {

            echo wp_send_json([
                "message" => $wpdb->last_error,
                "query" => $wpdb->last_query
            ]);
        } else {
            // $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"), ARRAY_A); // returns array
            $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"), ARRAY_A);

            echo wp_send_json($result);
        }
    }

    exit;
}


?>

<div id="settings">
    <h1>Dashboard Page</h1>

    <?= $config_is_setup ? "" : $message ?>

    <div class="stripe-config">
        <label>Enter Publishable Key</label>
        <input type="text" name="pk" placeholder="pk" value=<?= isset($result['pk']) ? $result['pk'] : "" ?>>
    </div>

    <div class="stripe-config">
        <label>Enter Secret Key</label>
        <input type="text" name="sk" placeholder="sk" value=<?= isset($result['sk']) ? $result['sk'] : "" ?>>
    </div>

    <div class="stripe-config">
        <label>Would you like to accept only card payments, or do you also need support for payment
            links
            and third-party integrations?</label>
        <select name="card-or-link">
            <option value="true" <?= isset($result['card_or_link']) ? "selected" : "" ?>>Yes</option>
            <option value="false" <?= isset($result['card_or_link']) ? "selected" : "" ?>>No (Only Card Payments)
            </option>
        </select>

    </div>

    <div class="stripe-config">
        <label>Enable Stripe Secure Payment Link</label>
        <select name="secure-link">
            <option value="true" <?= isset($result['secure_link']) ? "selected" : "" ?>>Yes</option>
            <option value="false" <?= isset($result['secure_link']) ? "selected" : "" ?>>No</option>
        </select>
    </div>

    <div class="stripe-config">
        <label>Redirect Page Code (PHP)</label>
        <textarea name="redirect-page" rows="15" cols="100" placeholder="Paste your code here..."></textarea>
    </div>

    <button id="save-update-code"><?= $config_is_setup ? "Update" : "Save" ?></button>

</div>

<script>

    console.log(<?= json_encode($result) ?>, "result")

</script>