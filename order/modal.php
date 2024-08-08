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
                
                    if($place_id != 4){
                        header("Location: ../index.php");
                        exit();
                    }
                    
                
          
if (!isset($_GET['order_id'])) {
    echo "Sipariş ID'si belirtilmemiş.";
    exit;
}

$order_id = $_GET['order_id'];

// Sipariş detaylarını ve toplam fiyatı hesaplamak için gerekli SQL sorgusu
$sql = "SELECT od.piece, mi.menu_name, mi.price, od.status_number as detail_status, o.status_number as order_status
        FROM order_details od
        JOIN menu_items mi ON od.menu_id = mi.menu_id
        JOIN orders o ON od.order_id = o.order_id
        WHERE od.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_details = [];
$total_price = 0;
$order_status_number = null;

while ($row = $result->fetch_assoc()) {
    $row['total_price'] = $row['piece'] * $row['price']; // Her öğenin toplam fiyatını hesaplayın
    $order_details[] = $row;
    $total_price += $row['total_price']; // Toplam fiyatı her döngüde ekleyin
    if ($order_status_number === null) {
        $order_status_number = $row['order_status'];
    }
}

$conn->close();

// Sipariş durumu metinlerini tanımlayın (order tablosu)
$order_status_texts = [
    0 => "Sipariş verildi",
    1 => "Hazırlandı",
    2 => "Teslim edildi",
    3 => "Ödendi"
];

// Sipariş detay durumu metinlerini tanımlayın (order_details tablosu)
$detail_status_texts = [
    1 => "Sipariş hazırlanıyor",
    2 => "Teslim edildi"
];

$order_status_text = isset($order_status_texts[$order_status_number]) ? $order_status_texts[$order_status_number] : "Bilinmeyen durum";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayları</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .modal-body {
            color: black;
        }

        .btn-closed {
            background-color: #DFDFDE;
            color: #006E7F;
        }

        .btn-closed:hover {
            background-color: #91C8E4;
            color: whitesmoke;
            border: 1.5px solid whitesmoke;
        }

        .btn-success, .btn-danger {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Sipariş Detayları - Sipariş Numarası: <?php echo htmlspecialchars($order_id); ?></h1>
                <a href="order.php" class="btn btn-closed">Kapat</a>
            </div>
            <div class="modal-body">
                <p><strong>Sipariş Durumu: </strong><?php echo htmlspecialchars($order_status_text); ?></p>
                <?php if (count($order_details) > 0): ?>
                <div class="row">
                    <?php foreach ($order_details as $index => $detail): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <strong>Menu: </strong> <?php echo htmlspecialchars($detail['menu_name']); ?>
                                <br>
                                <strong>Adet: </strong> <?php echo htmlspecialchars($detail['piece']); ?>
                                <br>
                                <strong>Fiyat: </strong> <?php echo htmlspecialchars(number_format($detail['total_price'], 2)); ?> TL
                                <br>
                                <strong>Durum: </strong>
                                <?php
                                $detail_status_text = isset($detail_status_texts[$detail['detail_status']]) ? $detail_status_texts[$detail['detail_status']] : "Detay bulunamadı";
                                echo htmlspecialchars($detail_status_text);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p>Sipariş detayları bulunamadı.</p>
                <?php endif; ?>
            </div>
            <?php if ($order_status_number == 2): ?>
            <div class="modal-footer">
                <div class="row w-100 mt-3">
                    <div class="col-md-12">
                        <p><strong>Toplam Fiyat: </strong><?php echo htmlspecialchars(number_format($total_price, 2)); ?> TL</p>
                    </div>
                </div>
                <div class="row w-100">
                    <div class="col-md-7">
                        <input type="number" id="paymentAmount" placeholder="Ödeme miktarı" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <select id="paymentType" class="form-control">
                            <option value="">Ödeme Türü</option>
                            <option value="1">Nakit</option>
                            <option value="2">Kart</option>
                        </select>
                    </div>
                </div>
                <div class="row w-100 mt-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" onclick="applyDiscount(5, <?php echo $total_price; ?>)">%5</button>
                        <button type="button" class="btn btn-success" onclick="applyDiscount(10, <?php echo $total_price; ?>)">%10</button>
                        <button type="button" class="btn btn-success" onclick="applyDiscount(20, <?php echo $total_price; ?>)">%20</button>
                        <button type="button" class="btn btn-danger" onclick="confirmPayment(<?php echo $order_id; ?>)">Ödeme Yapıldı</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    function applyDiscount(discountPercentage, totalPrice) {
        var discountedAmount = totalPrice - (totalPrice * discountPercentage / 100);
        $('#paymentAmount').val(discountedAmount.toFixed(2));
    }

    function confirmPayment(orderId) {
        var paymentAmount = $('#paymentAmount').val();
        var paymentType = $('#paymentType').val();

        if (!paymentAmount) {
            alert('Lütfen ödeme miktarını giriniz.');
            return;
        }

        if (!paymentType) {
            alert('Lütfen ödeme türünü seçiniz.');
            return;
        }

        $.ajax({
            url: 'update_order_status.php',
            type: 'POST',
            data: {
                order_id: orderId,
                payment: paymentAmount,
                payment_type: paymentType
            },
            success: function(response) {
                if (response === 'success') {
                    alert('Ödeme yapıldı.');
                    window.location.href = 'order.php'; // order.php sayfasına yönlendirme
                } else {
                    alert('Hata: ' + response);
                }
            }
        });
    }
    </script>
</body>
</html>
