<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>Sipariş Detayları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body style="padding-top: 20px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Sipariş Detayları - Sipariş Numarası: <?php echo htmlspecialchars($_GET['order_id']); ?>
                    </div>
                    <div class="card-body">
                        <?php
                        session_start();
                        include '../connection.php';

                        if (!isset($_SESSION['user_id'])) {
                            header("Location: ../login.php");
                            exit();
                        }

                        $order_id = $_GET['order_id'];
                        $sql = "SELECT od.detail_id, od.piece, mi.menu_name, od.statement
                        FROM order_details od
                        JOIN menu_items mi ON od.menu_id = mi.menu_id
                        WHERE od.order_id = ?
                        AND mi.place_id = 1
                        AND od.status_number = 0";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $order_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result === false) {
                            echo 'Hata: Veritabanı sorgusu başarısız: ' . $conn->error;
                            exit;
                        }

                        if ($result->num_rows > 0) {
                            echo '<ul class="list-group">';
                            while ($detail = $result->fetch_assoc()) {
                                echo '<li class="list-group-item">';
                                echo '<b>Miktar:</b> ' . htmlspecialchars($detail['piece']) . '<br>';
                                echo '<b>Menü Adı:</b> ' . htmlspecialchars($detail['menu_name']) . '<br>';
                                echo '<b>Açıklama:</b> ' . htmlspecialchars($detail['statement']) . '<br>';
                                echo '<button type="button" class="btn btn-success mt-2" onclick="markDetailAsReady(' . htmlspecialchars($detail['detail_id']) . ', ' . htmlspecialchars($order_id) . ')">Hazırlandı</button>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo "Sipariş detayı bulunamadı.";
                        }

                        $stmt->close();
                        $conn->close();
                        ?>
                    </div>
                    <div class="card-footer text-end">
                        <a href="izgara.php" class="btn btn-secondary">Geri</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
    function markDetailAsReady(detailId, orderId) {
        if (confirm('Bu sipariş detayını hazır olarak işaretlemek istediğinizden emin misiniz?')) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: {
                    detail_id: detailId,
                    order_id: orderId,
                    status_number: 1
                },
                success: function(response) {
                    if (response === 'success') {
                        alert('Sipariş detayı başarıyla hazırlandı.');
                        location.reload(); // Sayfayı yeniden yükle
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
</body>

</html>
