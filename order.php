<?php
include 'connection.php';
include 'sidebar.php';


session_start();

// Masaları ve sipariş durumlarını veritabanından çekme
$sql_active_tables = "SELECT t.order_id AS table_id, t.table_name, u.username AS waiter_name
                      FROM tables t
                      LEFT JOIN orders o ON t.table_id = o.table_id
                      LEFT JOIN users u ON o.user_id = u.id
                      WHERE o.status_number IS NOT NULL
                      GROUP BY t.order_id, t.table_name, u.username";
$result_active_tables = $conn->query($sql_active_tables);

$sql_inactive_tables = "SELECT t.table_id AS table_id, t.table_name
                        FROM tables t
                        LEFT JOIN orders o ON t.table_id = o.table_id
                        WHERE o.status IS NULL";
$result_inactive_tables = $conn->query($sql_inactive_tables);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    <link rel="stylesheet" href="view/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 40vh;
        }

        body {
            background-color: #f0f0f0;
        }

        .row {
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            font-style: oblique;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-6">
        <div class="row">
            <!-- Main content -->
            <main>
                <!-- Masalar -->
                <div class="container mt-4" style="background-color: #f0f0f0;">
                    <h2 style="text-align: center;">Siparişler</h2>
                    <div class="row row-cols-1 row-cols-md-5 g-4">
                        <?php
                        // Tüm masaları sorgula
                        $sql = "SELECT t.*, COUNT(o.table_id) AS order_count
                            FROM tables t
                            LEFT JOIN order_id o ON t.table_id = o.table_id
                            GROUP BY t.table_id";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $table_id = $row['id'];
                                $order_count = $row['order_count'];

                                // Masanın arka plan rengini belirle
                                $bg_color_class = ($order_count > 0) ? 'bg-success' : 'bg-primary';

                                echo '<div class="col">';
                                echo '<div class="card ' . $bg_color_class . '" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#masaModal' . $table_id . '">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">Masa ' . $row['table_name'] . '</h5>';
                                if ($order_count > 0) {
                                    // Masada sipariş varsa, son alınan siparişi göster
                                    $last_order_sql = "SELECT o.*, u.username AS garson_name, GROUP_CONCAT(mi.name SEPARATOR ', ') AS menu_items
                                                   FROM orders o
                                                   LEFT JOIN users u ON o.user_id = u.id
                                                   LEFT JOIN order_details od ON o.id = od.order_id
                                                   LEFT JOIN menu_items mi ON od.menu_item_id = mi.id
                                                   WHERE o.table_id = $table_id
                                                   ORDER BY o.created_at DESC
                                                   LIMIT 1";
                                    $last_order_result = $conn->query($last_order_sql);

                                    if ($last_order_result && $last_order_result->num_rows > 0) {
                                        $last_order_row = $last_order_result->fetch_assoc();
                                        echo '<p class="card-text">Siparişi Veren Garson: ' . $last_order_row['garson_name'] . '</p>';
                                        echo '<p class="card-text">Tarihi ve Saati: ' . date('d.m.Y H:i', strtotime($last_order_row['created_at'])) . '</p>';
                                    }
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';

                                // Modal oluştur
                                echo '<div class="modal fade" id="masaModal' . $table_id . '" tabindex="-1" aria-labelledby="masaModalLabel' . $table_id . '" aria-hidden="true">';
                                echo '<div class="modal-dialog">';
                                echo '<div class="modal-content">';
                                echo '<div class="modal-header">';
                                echo '<h5 class="modal-title" id="masaModalLabel' . $table_id . '">Masa ' . $row['table_name'] . ' Detayları</h5>';
                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                echo '</div>';
                                echo '<div class="modal-body">';
                                if ($order_count > 0) {
                                    // Masada sipariş varsa, son alınan siparişi göster
                                    echo '<p>Siparişi Veren Garson :' . $last_order_row['garson_name'] . '</p>';
                                    echo '<p>Sipariş İçeriği: ' . $last_order_row['menu_items'] . '</p>';
                                    echo '<p>Tarihi ve Saati: ' . date('d.m.Y H:i', strtotime($last_order_row['created_at'])) . '</p>';
                                } else {
                                    echo '<p>Bu masada henüz sipariş yok.</p>';
                                }
                                echo '</div>';
                                echo '<div class="modal-footer">';
                                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-info">Hiç masa bulunamadı.</div>';
                        }
                        ?>
                    </div>
                </div>

            </main>
        </div>

    </div>



</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"></script>
<script>
    function showOrderDetails(tableId) {
        fetch('fetch_order_details.php?table_id=' + tableId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('orderDetailsContent').innerHTML = data;
                $('#orderDetailsModal').modal('show');
            });
    }
</script>
<?php include 'footer.php';
?>