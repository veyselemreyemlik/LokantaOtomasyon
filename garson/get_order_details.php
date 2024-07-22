<?php
include '../connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Önce sipariş durumunu ve kullanıcı bilgisini alıyoruz
    $status_sql = "SELECT o.status_number, u.username 
                   FROM orders o
                   LEFT JOIN users u ON o.user_id = u.user_id
                   WHERE o.order_id = $order_id";
    $status_result = $conn->query($status_sql);

    if ($status_result->num_rows > 0) {
        $status_row = $status_result->fetch_assoc();
        $status_number = $status_row['status_number'];
        $username = $status_row['username'];

        // Eğer status_number 3 ise modal açılmasın
        if ($status_number == 3) {
            echo 'Sipariş durumu nedeniyle detaylar gösterilemiyor.';
            exit;
        }

        // Sipariş detaylarını alıyoruz
        $sql = "SELECT o.order_id, od.menu_id, mi.menu_name, od.piece, od.statement, mi.price, (od.piece * mi.price) as total_price
        FROM orders o
        JOIN order_details od ON o.order_id = od.order_id
        JOIN menu_items mi ON od.menu_id = mi.menu_id
        WHERE od.status_number=0 AND o.order_id = $order_id";


        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<ul class="list-group">';
            echo '<li class="list-group-item"><strong>Siparişi Veren:</strong> ' . htmlspecialchars($username) . '</li>';
            echo '<li class="list-group-item"></li>';
            $total = 0;
            while($row = $result->fetch_assoc()) {
                echo '<li class="list-group-item">';
                echo htmlspecialchars($row['piece']) . ' Adet ' . htmlspecialchars($row['menu_name']) . ' Not: <b>' . htmlspecialchars($row['statement']) . '</b>';

                echo '</li>';
                $total += $row['total_price'];
            }
            echo '<li class="list-group-item"></li>';
            echo '<li class="list-group-item"><strong>Toplam: </strong>' . number_format($total, 2) . ' TL</li>';
            echo '<li class="list-group-item"></li>';
            // Sipariş durumu ve kullanıcı bilgisi
            $status_text = '';
            switch ($status_number) {
                case 0:
                    $status_text = 'Sipariş verildi şuanda hazırlanıyor';
                    break;
                case 1:
                    $status_text = 'Sipariş hazırlandı teslim edilmesi gerekiyor';
                    break;
                case 2:
                    $status_text = 'Sipariş teslim edildi ödeme bekliyor';
                    break;
                default:
                    $status_text = 'Durum bilinmiyor';
            }
            echo '<li class="list-group-item"><strong>Durum:</strong> ' . $status_text . '</li>';
        } else {
            echo 'Detay bulunamadı.';
        }
    } else {
        echo 'Sipariş durumu ve kullanıcı bilgisi bulunamadı.';
    }
} else {
    echo 'Sipariş ID belirtilmedi.';
}

$conn->close();
?>