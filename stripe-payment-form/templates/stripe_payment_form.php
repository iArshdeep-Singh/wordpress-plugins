<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="stripe-payment-wrap" style="display: none">
    <label for="currency" style="display: none;">Currency</label>

    <select id="currency">
        <option value="usd" data-symbol="$" <?= $currency == "usd" ? "selected" : "" ?>>USD ($)</option>
        <option value="eur" data-symbol="€" <?= $currency == "eur" ? "selected" : "" ?>>EUR (€)</option>
        <option value="gbp" data-symbol="£" <?= $currency == "gbp" ? "selected" : "" ?>>GBP (£)</option>
        <option value="inr" data-symbol="₹" <?= $currency == "inr" ? "selected" : "" ?>>INR (₹)</option>
        <option value="cad" data-symbol="$" <?= $currency == "cad" ? "selected" : "" ?>>CAD ($)</option>
    </select>

    <input type="text" id="amount" placeholder="Enter Amount" value="<?= $amount ?>" />
    <button id="load-payment">Proceed</button>

    <div id="submitted-amount"></div>

    <div id="payment-element"></div>

    <button id="payment-button" style="display: none;">Pay Now</button>
    <button id="cancel-button" style="display: none;">Cancel</button>
    <div id="success-message"></div>
    <div id="error-message"></div>
</div>