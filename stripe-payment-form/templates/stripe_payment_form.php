<?php

if (!defined('ABSPATH')) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'])



$sk = "";
$url = "";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPhEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer "
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));


$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    echo $response;
}

?>

<div>

</div>