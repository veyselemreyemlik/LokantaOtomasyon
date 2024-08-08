<?php

include '../connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$place_id = $_SESSION['place_id'];

// Kullanıcının place_id'sini kontrol et
if ($place_id != 5) {
    if ($place_id != 4) {
        header("Location: ../index.php");
        exit();
    }
}

$order_id = intval($_GET['order_id']);

// Sipariş sorgusu
$order_sql = "SELECT o.order_id, o.table_id, o.status_number, t.table_name
              FROM orders o
              JOIN tables t ON o.table_id = t.table_id
              WHERE o.order_id = ?";
$stmt = $conn->prepare($order_sql);
if (!$stmt) {
    echo "Sipariş sorgusu hazırlama hatası: " . $conn->error;
    exit;
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    echo "Belirtilen sipariş bulunamadı.";
    exit;
}

$order = $order_result->fetch_assoc();
$table_name = $order['table_name'];

// Sipariş detayları sorgusu
$details_sql = "SELECT od.detail_id, od.piece, mi.menu_name, od.statement, od.status_number
                FROM order_details od
                JOIN menu_items mi ON od.menu_id = mi.menu_id
                WHERE od.order_id = ?";
$stmt = $conn->prepare($details_sql);
if (!$stmt) {
    echo "Sipariş detayları sorgusu hazırlama hatası: " . $conn->error;
    exit;
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$details_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>Sipariş Detayları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .container {
            margin-top: 100px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #343a40;
        }
        .table {
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #218838;
        }
        .back-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl7/4yHf5r5/5E3f5a5e5r5zF5r3f5d5sE4zF5r+F2s5K9s5t5E2Q=="
        crossorigin="anonymous"></script>
    <div class="container">
        <h3 class="modal-title text-center">Sipariş Detayları</h3>
        <p><strong>Sipariş Numarası:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
        <p><strong>Masa Adı:</strong> <?php echo htmlspecialchars($table_name); ?></p>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Adet</th>
                    <th>Menü Adı</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th>Teslim Edildi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($detail = $details_result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($detail['piece']) . "</td>
                            <td>" . htmlspecialchars($detail['menu_name']) . "</td>
                            <td>" . htmlspecialchars($detail['statement']) . "</td>
                            <td>" . ($detail['status_number'] == 2 ? 'Teslim Edildi' : 'Hazırlanıyor') . "</td>
                            <td>";
                    if ($detail['status_number'] != 2) {
                        echo "<button class='btn btn-success' onclick='confirmDetailDelivery(" . $detail['detail_id'] . ")'>Teslim Edildi</button>";
                    }
                    echo "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="text-center back-btn">
            <a href="garson_order.php" class="btn btn-primary">Geri Dön</a>
        </div>
    </div>

    <script>
    function confirmDetailDelivery(detailId) {
        if (confirm('Bu sipariş detayının teslim edildiğinden emin misiniz?')) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: {
                    detail_id: detailId,
                    action: 'detail_delivery'
                },
                success: function(response) {
                    if (response === 'success') {
                        window.location.reload(); // Sayfayı yenileyerek güncel durumu göster
                    } else {
                        alert('Hata: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Hatası:', status, error);
                }
            });
        }
    }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>
</html>
