<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: whitesmoke;
        }

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

        .bg-bos {
            background-color: #91C8E4;
        }



        .btn-detail {
            background-color: #DFDFDE;
            color: #176B87;
        }

        .btn-detail:hover {
            background-color: #7eaa92;
            color: whitesmoke;
            border: 1.5px solid whitesmoke;
        }

        .container {
            background-color: #FBF9F1;
            padding-top: 20px;
            padding-left: 20px;
            padding-bottom: 20px;
            padding-right: 20px;
            border-radius: 5px;
        }

        .h5-style {
            color: #FBF9F1;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }

        #modalBody31 {
            color: black;
        }

        .modal-header {
            color: #176B87;
        }

        .bg-secondary {
            background-color: #7EAA92 !important;
        }

        .bg-success {
            background-color: #4F709C !important;
        }

        .text-order {
            color: black;
        }

        .btn-order {
            background-color: #DFDFDE;
            color: #176B87;
            border: 1.5px solid #91C8E4;
        }

        .btn-order:hover {
            background-color: #91C8E4;
            color: whitesmoke;
            border: 1.5px solid whitesmoke;
        }

        .heading {
            color: #606676;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }

        .btn-add {
            background-color: #DFDFDE;
            color: #176B87;

        }

        .btn-add:hover {
            background-color: #4f709c;
            color: whitesmoke;
            border: 1.5px solid whitesmoke;
        }
    </style>
</head>

<body style="padding-top: 50px;">

    <div class="container">
        <div class="heading">
            <h3>Sipariş Oluştur</h3>
        </div>
        <div class="row">
            <?php
            session_start();
            include '../connection.php';

            if (!isset($_SESSION['user_id'])) {
                header("Location: ../login.php");
                exit();
            }

            // Önce tüm masaları alıyoruz
            $sql = "SELECT t.table_id, t.table_name, o.order_id, o.status_number, o.payment, u.username
                    FROM tables t
                    LEFT JOIN orders o ON t.table_id = o.table_id
                    LEFT JOIN users u ON o.user_id = u.user_id
                    ORDER BY t.table_name, o.created_at DESC";

            $result = $conn->query($sql);

            if ($result === false) {
                echo 'Hata: Veritabanı sorgusu başarısız: ' . $conn->error;
                exit;
            }

            $tables = [];
            while ($row = $result->fetch_assoc()) {
                if (!isset($tables[$row['table_id']])) {
                    $tables[$row['table_id']] = $row;
                }
            }

            if (count($tables) > 0) {
                foreach ($tables as $table) {
                    $card_color = 'bg-bos';
                    $status_text = 'Masa boş';

                    if (isset($table['status_number'])) {
                        switch ($table['status_number']) {
                            case 0:
                                $card_color = 'bg-success';
                                $status_text = 'Sipariş verildi';
                                break;
                            case 1:
                                $card_color = 'bg-secondary';
                                $status_text = 'Sipariş hazırlandı';
                                break;
                            case 2:
                                $card_color = 'bg-info';
                                $status_text = 'Ödeme bekliyor..';
                                break;
                            case 3:
                                $card_color = 'bg-secondary';
                                $status_text = 'Masa boş';
                                break;
                            default:
                                $card_color = '';
                                $status_text = 'Durum bilinmiyor';
                        }
                    }
                    ?>

                    <div class="col-md-3 mb-3 card-style">
                        <div class="card <?php echo $card_color; ?>">
                            <div class="card-body" style="text-align: center;">
                                <h5 class="card-title h5-style">Masa <?php echo htmlspecialchars($table['table_name']); ?></h5>
                                <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
                                    <h6 class="card-subtitle mb-2 text-order">Sipariş Numarası:
                                        <?php echo $table['order_id']; ?>
                                    </h6>
                                <?php else: ?>
                                    <h6 class="card-subtitle mb-2 text-order">Sipariş Numarası: -</h6>
                                <?php endif; ?>
                                <p class="card-text"><b><?php echo $status_text; ?></b></p>

                                <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
                                    <?php if ($table['status_number'] == 1): ?>
                                        <button type="button" class="btn btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modal<?php echo $table['order_id']; ?>">
                                            Detaylar
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-add me-2"
                                        onclick="redirectToAddOrder(<?php echo $table['table_id']; ?>, <?php echo $table['order_id']; ?>)">Sipariş
                                        Ekle</button>
                                <?php elseif ($table['status_number'] == 2): ?>
                                    <button type="button" class="btn btn-add"
                                        onclick="redirectToAddOrder(<?php echo $table['table_id']; ?>, <?php echo $table['order_id']; ?>)">Sipariş
                                        Ekle
                                    </button>
                                <?php elseif ($table['status_number'] == 3 || !isset($table['order_id'])): ?>
                                    <button type="button" class="btn btn-order"
                                        onclick="redirectToNewOrder(<?php echo $table['table_id']; ?>)">Sipariş Oluştur</button>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <?php if (isset($table['order_id']) && $table['status_number'] == 1): ?>
                        <div class="modal fade" id="modal<?php echo $table['order_id']; ?>" tabindex="-1"
                            aria-labelledby="modalLabel<?php echo $table['order_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalLabel<?php echo $table['order_id']; ?>">Sipariş
                                            Detayları -
                                            Sipariş Numarası: <?php echo $table['order_id']; ?></h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="modalBody<?php echo $table['order_id']; ?>">
                                        <!-- Sipariş detayları burada AJAX ile yüklenecek -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success"
                                            onclick="confirmDelivery(<?php echo $table['order_id']; ?>)">Teslim Edildi</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                }
            } else {
                echo "Sipariş bulunamadı.";
            }
            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function redirectToNewOrder(tableId) {
            window.location.href = 'new_order.php?table_id=' + tableId;
        }

        function redirectToAddOrder(tableId, orderId) {
            window.location.href = 'new_order.php?table_id=' + tableId + '&order_id=' + orderId;
        }

        $(document).ready(function () {
            $('button[data-bs-toggle="modal"]').click(function () {
                var orderId = $(this).data('bs-target').replace('#modal', '');
                $.ajax({
                    url: 'get_order_details.php',
                    type: 'GET',
                    data: {
                        order_id: orderId
                    },
                    success: function (response) {
                        $('#modalBody' + orderId).html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Hatası:', status, error);
                    }
                });
            });
        });

        function confirmDelivery(orderId) {
            if (confirm('Siparişin teslim edildiğinden emin misiniz?')) {
                $.ajax({
                    url: 'update_order_status.php',
                    type: 'POST',
                    data: {
                        order_id: orderId,
                        status_number: 2
                    },
                    success: function (response) {
                        if (response === 'success') {
                            location.reload(); // Sayfayı yenileyerek güncel durumu göster
                        } else {
                            alert('Hata: ' + response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Hatası:', status, error);
                    }
                });
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>

</html>