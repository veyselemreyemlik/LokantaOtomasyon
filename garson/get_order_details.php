<?php
include '../connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Sipariş durumunu, kullanıcı ve masa bilgisini al
    $status_sql = "SELECT o.status_number, u.username, t.table_name 
                   FROM orders o
                   LEFT JOIN users u ON o.user_id = u.user_id
                   LEFT JOIN tables t ON o.table_id = t.table_id
                   WHERE o.order_id = ?";
    $stmt = $conn->prepare($status_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $status_result = $stmt->get_result();

    if ($status_result->num_rows > 0) {
        $status_row = $status_result->fetch_assoc();
        $status_number = $status_row['status_number'];
        $username = $status_row['username'];
        $table_name = $status_row['table_name'];

        // Eğer status_number 3 ise modal açılmasın
        if ($status_number == 3) {
            echo '<p>Sipariş durumu nedeniyle detaylar gösterilemiyor.</p>';
            exit;
        }

        // Sipariş detaylarını al
        $sql = "SELECT od.menu_id, mi.menu_name, od.piece, od.statement, mi.price, (od.piece * mi.price) as total_price
                FROM order_details od
                JOIN menu_items mi ON od.menu_id = mi.menu_id
                WHERE od.order_id = ? AND od.status_number = 1";  // od.status_number = 1 filtresi eklendi
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Siparişi Veren</th>';
            echo '<th>Masa Adı</th>';
            echo '<th>Ürün Adı</th>';
            echo '<th>Miktar</th>';
            echo '<th>Not</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $total = 0;
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($username) . '</td>';
                echo '<td>' . htmlspecialchars($table_name) . '</td>';
                echo '<td>' . htmlspecialchars($row['menu_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['piece']) . '</td>';
                echo '<td>' . htmlspecialchars($row['statement']) . '</td>';
                echo '</tr>';
                $total += $row['total_price'];
            }

            echo '</tbody>';
            echo '</table>';
            
            // Sipariş durumu
            $status_text = '';
            switch ($status_number) {
                case 0:
                    $status_text = 'Sipariş verildi, şu anda hazırlanıyor.';
                    break;
                case 1:
                    $status_text = 'Sipariş hazırlandı, teslim edilmesi gerekiyor.';
                    break;
                case 2:
                    $status_text = 'Sipariş teslim edildi, ödeme bekliyor.';
                    break;
                default:
                    $status_text = 'Durum bilinmiyor.';
            }
            echo '<h6>' . $status_text . '</h6>';
        } else {
            echo '<p>Detay bulunamadı.</p>';
        }
    } else {
        echo '<p>Sipariş durumu ve kullanıcı bilgisi bulunamadı.</p>';
    }
} else {
    echo '<p>Sipariş ID belirtilmedi.</p>';
}

$conn->close();
?>
