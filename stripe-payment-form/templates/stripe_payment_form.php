<?php

if (!defined('ABSPATH')) {
    exit;
}
$currencies = file_get_contents(plugins_url('../assets/currencies.json', __FILE__));
?>

<div class="stripe-payment-wrap" style="display: none">
    <label for="currency" style="display: none;">Currency</label>

    <select id="currency">
        <option value="usd" data-symbol="$" <?= $currency == "usd" ? "selected" : "" ?>>USD ($)</option>
        <option value="eur" data-symbol="€" <?= $currency == "eur" ? "selected" : "" ?>>EUR (€)</option>
        <option value="gbp" data-symbol="£" <?= $currency == "gbp" ? "selected" : "" ?>>GBP (£)</option>
        <option value="inr" data-symbol="₹" <?= $currency == "inr" ? "selected" : "" ?>>INR (₹)</option>
        <option value="cad" data-symbol="$" <?= $currency == "cad" ? "selected" : "" ?>>CAD ($)</option>
        <option value="aud" data-symbol="A$" <?= $currency == "aud" ? "selected" : "" ?>>AUD (A$)</option>
        <option value="nzd" data-symbol="NZ$" <?= $currency == "nzd" ? "selected" : "" ?>>NZD (NZ$)</option>
        <option value="jpy" data-symbol="¥" <?= $currency == "jpy" ? "selected" : "" ?>>JPY (¥)</option>
        <option value="cny" data-symbol="¥" <?= $currency == "cny" ? "selected" : "" ?>>CNY (¥)</option>
        <option value="krw" data-symbol="₩" <?= $currency == "krw" ? "selected" : "" ?>>KRW (₩)</option>
        <option value="aed" data-symbol="د.إ" <?= $currency == "aed" ? "selected" : "" ?>>AED (د.إ)</option>
        <option value="sar" data-symbol="﷼" <?= $currency == "sar" ? "selected" : "" ?>>SAR (﷼)</option>
        <option value="qar" data-symbol="﷼" <?= $currency == "qar" ? "selected" : "" ?>>QAR (﷼)</option>
        <option value="egp" data-symbol="E£" <?= $currency == "egp" ? "selected" : "" ?>>EGP (E£)</option>
        <option value="zar" data-symbol="R" <?= $currency == "zar" ? "selected" : "" ?>>ZAR (R)</option>
        <option value="chf" data-symbol="CHF" <?= $currency == "chf" ? "selected" : "" ?>>CHF (CHF)</option>
        <option value="sek" data-symbol="kr" <?= $currency == "sek" ? "selected" : "" ?>>SEK (kr)</option>
        <option value="nok" data-symbol="kr" <?= $currency == "nok" ? "selected" : "" ?>>NOK (kr)</option>
        <option value="dkk" data-symbol="kr" <?= $currency == "dkk" ? "selected" : "" ?>>DKK (kr)</option>
        <option value="sgd" data-symbol="S$" <?= $currency == "sgd" ? "selected" : "" ?>>SGD (S$)</option>
        <option value="hkd" data-symbol="HK$" <?= $currency == "hkd" ? "selected" : "" ?>>HKD (HK$)</option>
        <option value="mxn" data-symbol="$" <?= $currency == "mxn" ? "selected" : "" ?>>MXN ($)</option>
        <option value="brl" data-symbol="R$" <?= $currency == "brl" ? "selected" : "" ?>>BRL (R$)</option>
        <option value="try" data-symbol="₺" <?= $currency == "try" ? "selected" : "" ?>>TRY (₺)</option>
        <option value="pln" data-symbol="zł" <?= $currency == "pln" ? "selected" : "" ?>>PLN (zł)</option>
        <option value="czk" data-symbol="Kč" <?= $currency == "czk" ? "selected" : "" ?>>CZK (Kč)</option>
        <option value="huf" data-symbol="Ft" <?= $currency == "huf" ? "selected" : "" ?>>HUF (Ft)</option>
        <option value="ron" data-symbol="lei" <?= $currency == "ron" ? "selected" : "" ?>>RON (lei)</option>
        <!-- rub omitted: Stripe doesn't support Russia-based merchant accounts; verify against your Dashboard's supported currencies before re-adding -->
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

<script>
    let currencies = <?= $currencies ?>

    let document.getElementById("currency")

</script>