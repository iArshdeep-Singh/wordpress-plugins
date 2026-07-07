<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="stripe-payment-wrap">
    <label for="currency">Currency</label>

    <select id="currency">
        <option value="usd" data-symbol="$">USD ($)</option>
        <option value="eur" data-symbol="€">EUR (€)</option>
        <option value="gbp" data-symbol="£">GBP (£)</option>
        <option value="inr" data-symbol="₹">INR (₹)</option>
        <option value="cad" data-symbol="$">CAD ($)</option>
    </select>

    <input type="text" id="amount" placeholder="Enter Amount" value="<?php echo esc_attr($amount); ?>" />
    <button id="load-payment">Proceed</button>

    <div id="submitted-amount"></div>

    <div id="payment-element"></div>

    <button id="payment-button" style="display: none;">Pay Now</button>
    <button id="cancel-button" style="display: none;">Cancel</button>
    <div id="success-message"></div>
    <div id="error-message"></div>
</div>