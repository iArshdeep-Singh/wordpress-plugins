<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="stripe-payment-wrap">

    <input type="text" id="amount" placeholder="Enter Amount" />
    <button id="load-payment">Proceed</button>

    <div id="payment-element"></div>

    <button id="payment-button" style="display: none;">Pay Now</button>
    <div id="message"></div>
</div>