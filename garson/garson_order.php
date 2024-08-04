<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5">
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

    h6 {
        color: #000000;
    }

    .modal-header {
        color: #176B87;
    }

    .bg-secondary {
        background-color: #F5A623 !important;
    }

    .bg-danger {
        background-color: #6f6f6f !important;
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
           
            include '../connection.php';
            
            
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
                                $card_color = 'bg-danger';
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
                        <button type="button" class="btn btn-detail"
                            onclick="redirectToDetails(<?php echo $table['order_id']; ?>)">
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

    function redirectToDetails(orderId) {
        window.location.href = 'modal.php?order_id=' + orderId;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVym6JNldB0F58BY3tzfWCKv7x5RSit0bKiUlu"
        crossorigin="anonymous"></script>
</body>

</html>
