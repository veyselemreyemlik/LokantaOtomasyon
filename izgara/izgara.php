<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5">
    <title>Izgara Siparişleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: black;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            margin-top: 20px;
            color: #002254;
            text-align: center;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif
        }

        .card {
            margin-bottom: 1rem;
        }

        .row {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 50px;
        }

        .card-body {
            padding: 1rem;
            height: auto;
        }
    </style>
</head>

<body style="padding-top: 20px;">
    <h1 style="text-align:center">IZGARA SİPARİŞLERİ</h1>
    <div class="container">
        <div class="row">
            <?php

            include '../connection.php';



            $sql = "SELECT o.order_id, t.table_name, o.status_number
            FROM orders o
            JOIN tables t ON o.table_id = t.table_id
            WHERE o.status_number = 0
            AND EXISTS (
                SELECT 1
                FROM order_details od
                JOIN menu_items mi ON od.menu_id = mi.menu_id
                WHERE od.order_id = o.order_id
                AND mi.place_id = 1
                AND od.status_number = 0
            )
            ORDER BY o.order_id DESC";

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
                                    <?php echo htmlspecialchars($order['table_name']); ?>
                                </h6>
                                <p class="card-text"><b><?php echo $status_text; ?></b></p>
                                <a href="modal.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary">
                                    Detaylar
                                </a>
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
</body>

</html>