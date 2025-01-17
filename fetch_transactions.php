<?php
include 'conn.php';

$itemId = $_GET['item_id'] ?? null;

if ($itemId) {
    $sql = "SELECT transaction_id, transaction_type, quantity, date_time, description FROM inventory_transactions WHERE item_id = ? ORDER BY date_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$itemId]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($transactions)) {
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Transaction ID</th>';
        echo '<th>Type</th>';
        echo '<th>Quantity</th>';
        echo '<th>Date & Time</th>';
        echo '<th>Description</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($transactions as $transaction) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($transaction['transaction_id']) . '</td>';
            echo '<td>' . htmlspecialchars(ucfirst($transaction['transaction_type'])) . '</td>';
            echo '<td>' . htmlspecialchars($transaction['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($transaction['date_time']) . '</td>';
            echo '<td>' . htmlspecialchars($transaction['description']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No transactions found for this item.</p>';
    }
} else {
    echo '<p>Invalid item ID.</p>';
}
?>
