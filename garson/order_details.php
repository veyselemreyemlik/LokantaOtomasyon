<?php
// Bağlantı dosyasını ve header dosyasını dahil ediyoruz
include '../connection.php';
include '../header.php';

// Oturumu başlatıyoruz
session_start();

// URL'den gelen 'table_id' parametresini alıyoruz
$table_id = isset($_GET['table_id']) ? $_GET['table_id'] : null;

// Eğer 'table_id' tanımlı ise sipariş detaylarını çekiyoruz
if ($table_id) {
    $order_details = [];
    // SQL sorgusu: Sadece belirli 'table_id' için sipariş detaylarını çekiyoruz
    $sql = "SELECT o.order_id, o.created_at, u.username, mi.menu_name, od.piece, od.statement
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN order_details od ON o.order_id = od.order_id
            JOIN menu_items mi ON od.menu_id = mi.menu_id
            WHERE o.table_id = $table_id AND o.status_number = 1";
    $result = $conn->query($sql);

    // Eğer sorgu başarısız olursa hata mesajı gösteriyoruz
    if ($result === FALSE) {
        die("Sorgu başarısız: " . $conn->error);
    }

    // Eğer sorgudan veri geldiyse, verileri döngü ile alıyoruz
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_details[] = $row;
        }
    }

    // Bağlantıyı kapatıyoruz
    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sipariş Detayları</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .content {
            padding: 50px;
        }

        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 50px;
        }

        form {
            background-color: #ffffff;
            border-radius: 1%;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #343a40;
            text-align: center;
            margin-top: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #343a40;
            color: #ffffff;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .table tbody tr td {
            vertical-align: middle;
        }


        .table {
            text-align: center;
        }



        .table-container {
            margin: auto;
            padding-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Masa <?php echo $table_id; ?> Sipariş Detayları</h1>


        <?php if (!empty($order_details)): ?>
            <form>
                <?php foreach ($order_details as $detail): ?>
                    <div class="form-row">
                        <label>Sipariş ID</label>
                        <input type="text" class="form-control" value="<?php echo $detail['order_id']; ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label>Sipariş Saati</label>
                        <input type="text" class="form-control" value="<?php echo $detail['created_at']; ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label>Siparişi Alan</label>
                        <input type="text" class="form-control" value="<?php echo $detail['username']; ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label>Ürün</label>
                        <input type="text" class="form-control" value="<?php echo $detail['menu_name']; ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label>Adet</label>
                        <input type="text" class="form-control" value="<?php echo $detail['piece']; ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label>Açıklama</label>
                        <input type="text" class="form-control" value="<?php echo $detail['statement']; ?>" readonly>
                    </div>
                    <hr>
                    <div class="btn-container">
                        <button class="btn btn-success btn-add-order"
                            onclick="location.href='add_order.php?table_id=<?php echo $table_id; ?>'">Sipariş Ekle</button>
                    </div>

                <?php endforeach; ?>
            </form>
        <?php else: ?>
            <p>Bu masada henüz sipariş yok.</p>
        <?php endif; ?>
    </div>

</body>

</html>