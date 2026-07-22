<?php
if (!defined('ABSPATH')) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    global $wpdb;

    $table = $wpdb->prefix . "stripe_payment_logs";

    $type = "card";
    $order_by = $data["sort_by"] ?? "created_at";
    $order = $data["order"] ?? "ASC";
    // $limit = 20;
    $limit = $data["limit"] ?? 5;
    $offset = $data['offset'] ?? 0;
    // $card = true;
    $card = $data['card'] ?? true;

    $sql = "";
    $fields_sql = "";

    if ($card) {
        $sql = "SELECT * FROM $table WHERE type = %s ORDER BY $order_by $order LIMIT %d OFFSET %d";
        $fields_sql = "SELECT * FROM $table WHERE type = %s LIMIT 1";
        $count_sql = "SELECT COUNT(*) FROM $table WHERE type = %s";
    } else {
        $sql = "SELECT * FROM $table WHERE type != %s ORDER BY $order_by $order LIMIT %d OFFSET %d";
        $fields_sql = "SELECT * FROM $table WHERE type != %s LIMIT 1";
        $count_sql = "SELECT COUNT(*) FROM $table WHERE type != %s";
    }



    $row = $wpdb->get_row($wpdb->prepare($fields_sql, $type), ARRAY_A);

    $column_names = array_keys(array_filter($row, fn($value) => $value !== null));



    $columns = array_combine(
        array_values($column_names),
        array_map(function ($column) {
            return ucwords(str_replace('_', ' ', $column));
        }, $column_names)
    );


    $results = $wpdb->get_results(
        $wpdb->prepare(
            $sql,
            array($type, $limit, $offset)
        ),
        ARRAY_A
    );

    $final_result = array_map(fn($result) => array_filter($result, fn($value) => $value !== null), $results);


    // count
    $count = $wpdb->get_var($wpdb->prepare($count_sql, $type));

    $response = ["columns" => $columns, "rows" => $final_result, "count" => $count];

    wp_send_json($response);

    // echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}
?>
<div id="logs-wrap">
    <h1>Logs</h1>

    <div id="logs">
        <button id="previous" disabled>Previous</button>
        <button id="next">Next</button>
        <label for="name">Per Page</label>
        <select name="limit" class="limit-type-sort-order">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
        </select>
        <label for="type">Type</label>
        <select name="type" class="limit-type-sort-order">
            <option value="true" selected>Card Payment</option>
            <option value="false">Express Checkout</option>
        </select>
        <label for="sort">Sort By</label>
        <select name="sort_by" class="limit-type-sort-order"></select>
        <label for="order">Order</label>
        <select name="order" class="limit-type-sort-order">
            <option value="ASC" selected>Ascending</option>
            <option value="DESC">Descending</option>
        </select>
        <table id="payment-logs"></table>
    </div>
</div>