<?php
if (!defined('ABSPATH')) {
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

global $wpdb;

$table = $wpdb->prefix . "stripe_payment_logs";

$type = "card";
$order_by = "created_at";
$order = "ASC";
// $limit = 20;
$limit = $data["limit"] ?? 20;
$offset = 0;
// $card = true;
$card = $data['card'] ?? false;

$sql = "";
$fields_sql = "";

if ($card) {
    $sql = "SELECT * FROM $table WHERE type = %s ORDER BY %s $order LIMIT %d OFFSET %d";
    $fields_sql = "SELECT * FROM $table WHERE type = %s LIMIT 1";
} else {
    $sql = "SELECT * FROM $table WHERE type != %s ORDER BY %s $order LIMIT %d OFFSET %d";
    $fields_sql = "SELECT * FROM $table WHERE type != %s LIMIT 1";
}



$row = $wpdb->get_row($wpdb->prepare($fields_sql, $type), ARRAY_A);

$column_names = array_keys(array_filter($row, fn($value) => $value !== null));


// print_r($column_names);


$columns = array_combine(
    array_values($column_names),
    array_map(function ($column) {
        return ucwords(str_replace('_', ' ', $column));
    }, $column_names)
);


$results = $wpdb->get_results(
    $wpdb->prepare(
        $sql,
        array($type, $order_by, $limit, $offset)
    ),
    ARRAY_A
);

$final_result = array_map(fn($result) => array_filter($result, fn($value) => $value !== null), $results);


$response = ["columns" => $columns, "data" => $final_result];

// wp_send_json($response);

echo json_encode($response, JSON_PRETTY_PRINT);
?>

<h1>Logs</h1>

<div>
    <table>
        <tr>
            <?php foreach ($columns as $column) {
                echo "<th>" . $column . "</th>";
            } ?>
        </tr>
    </table>
    <button id="send">Click</button>
</div>
<script>
    (async () => {

        document.getElementById("send").addEventListener('click', async () => {
            await fetch("", {
                headers: { "content-type": "application/json" },
                method: 'POST',
                body: JSON.stringify({ card: true, limit: 5 })
            })
        })

    })()
</script>