<?php
include '../connection.php';

if (isset($_GET['table_id'])) {
    $table_id = intval($_GET['table_id']);
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;

    if ($order_id) {
        $sql_order_details = "SELECT o.id AS order_id, mi.name AS menu_item_name, od.quantity, od.status, od.statement, o.created_at, u.username AS waiter_name
                              FROM orders o
                              JOIN order_details od ON o.id = od.order_id
                              JOIN menu_items mi ON od.menu_item_id = mi.id
                              JOIN users u ON o.user_id = u.id
                              WHERE o.id = $order_id";
        $result_order_details = $conn->query($sql_order_details);

        if ($result_order_details && $result_order_details->num_rows > 0) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>Sipariş ID</th><th>Menü Adı</th><th>Adet</th><th>Durum</th><th>Not</th><th>Sipariş Saati</th><th>Garson</th></tr></thead><tbody>';
            while ($row = $result_order_details->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['order_id'] . '</td>';
                echo '<td>' . $row['menu_item_name'] . '</td>';
                echo '<td>' . $row['quantity'] . '</td>';
                echo '<td>' . $row['status'] . '</td>';
                echo '<td>' . $row['statement'] . '</td>';
                echo '<td>' . date('d.m.Y H:i', strtotime($row['created_at'])) . '</td>';
                echo '<td>' . $row['waiter_name'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '<button class="btn btn-danger" onclick="closeTable(' . $order_id . ')">Masayı Kapat</button>';
        } else {
            echo '<div class="alert alert-info">Bu masaya ait sipariş bulunamadı.</div>';
        }
    } else {
        echo '<div class="alert alert-info">Bu masa şu anda boş.</div>';
    }
}
?>

<script>
    function closeTable(orderId) {
        if (confirm('Masayı kapatmak istediğinize emin misiniz?')) {
            fetch('close_table.php?order_id=' + orderId)
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Masa başarıyla kapatıldı.');
                        $('#tableDetailsModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Masa kapatılırken bir hata oluştu: ' + data);
                    }
                });
        }
    }
</script>