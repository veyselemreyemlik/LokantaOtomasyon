<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fırın Siparişleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    .card {
        margin-bottom: 1rem;
        /* Kartlar arasındaki dikey boşluğu kontrol eder */
    }

    .card-body {
        padding: 1rem;
        /* Kart içindeki elemanların çevresindeki boşluğu kontrol eder */
        height: auto;
        /* Kart yüksekliğini otomatik yapar */
    }
    </style>
</head>

<body style="padding-top: 20px;">
<h1 style ="text-align:center">FIRIN SİPARİŞLERI</h1>
    <div class="container">
        <div class="row">
            <?php
            session_start();
            include '../connection.php';

            if (!isset($_SESSION['user_id'])) {
                header("Location: ../login.php");
                exit();
            }

            // SQL sorgusunu güncelle
            $sql = "SELECT o.order_id, t.table_name, o.status_number
            FROM orders o
            JOIN tables t ON o.table_id = t.table_id
            WHERE o.status_number = 0
            AND EXISTS (
                SELECT 1
                FROM order_details od
                JOIN menu_items mi ON od.menu_id = mi.menu_id
                WHERE od.order_id = o.order_id
                AND mi.place_id = 3
                AND od.status_number = 0
            )
            ORDER BY o.order_id DESC
            ";

            $result = $conn->query($sql);

            if ($result === false) {
                echo 'Hata: Veritabanı sorgusu başarısız: ' . $conn->error;
                exit;
            }

            if ($result->num_rows > 0) {
                while ($order = $result->fetch_assoc()) {
                    $card_color = 'bg-secondary';
                    $status_text = 'Sipariş verildi';

                    if ($order['status_number'] == 0) {
                        $card_color = 'bg-success';
                        $status_text = 'Sipariş verildi';
                    }
                    ?>
                    
            <div class="col-md-4 mb-3">
                <div class="card <?php echo $card_color; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title">Sipariş Numarası: <?php echo htmlspecialchars($order['order_id']); ?>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Masa:
                            <?php echo htmlspecialchars($order['table_name']); ?></h6>
                        <p class="card-text"><b><?php echo $status_text; ?></b></p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modal<?php echo $order['order_id']; ?>">
                            Detaylar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal<?php echo $order['order_id']; ?>" tabindex="-1"
                aria-labelledby="modalLabel<?php echo $order['order_id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalLabel<?php echo $order['order_id']; ?>">
                                Sipariş Detayları - Sipariş Numarası: <?php echo $order['order_id']; ?>
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalBody<?php echo $order['order_id']; ?>">
                            <!-- Sipariş detayları burada AJAX ile yüklenecek -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success"
                                onclick="markAsReady(<?php echo $order['order_id']; ?>)">
                                Hazırlandı
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "Detay bulunamadı.";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
    function markAsReady(orderId) {
        if (confirm('Siparişi hazır olarak işaretlemek istediğinizden emin misiniz?')) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId,
                    status_number: 1
                },
                success: function(response) {
                    if (response === 'success') {
                        alert('Sipariş başarıyla hazırlandı.');
                        location.reload(); // Sayfayı yenileyerek güncel durumu göster
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

    $(document).ready(function() {
        $('button[data-bs-toggle="modal"]').click(function() {
            var orderId = $(this).data('bs-target').replace('#modal', '');
            $.ajax({
                url: 'get_order_details.php',
                type: 'GET',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    $('#modalBody' + orderId).html(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Hatası:', status, error);
                }
            });
        });
    });
    </script>
</body>

</html>
