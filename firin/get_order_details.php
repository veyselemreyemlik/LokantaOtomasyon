<?php
include '../connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    $sql = "SELECT o.order_id, od.menu_id, mi.menu_name, od.piece, od.statement, mi.price, (od.piece * mi.price) as total_price
            FROM orders o
            JOIN order_details od ON o.order_id = od.order_id
            JOIN menu_items mi ON od.menu_id = mi.menu_id
            WHERE o.order_id = $order_id AND mi.place_id = 3 AND od.status_number = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Adet</th><th>Menü İsmi</th><th>Not</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['piece']) . '</td>';
            echo '<td>' . htmlspecialchars($row['menu_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['statement']) . '</td>';
           
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'Detay bulunamadı.';
    }
} else {
    echo 'Sipariş ID belirtilmedi.';
}

$conn->close();
?>