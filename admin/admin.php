<?php
include '../connection.php';
include '../sidebar.php';

session_start();
            include '../connection.php';
            
            if (!isset($_SESSION['user_id'])) {
                header("Location: ../login.php");
                exit();
            }
            
            $user_id = $_SESSION['user_id'];
            
            // Kullanıcının place_id'sini veritabanından çek
            $result = $conn->query("SELECT place_id FROM users WHERE user_id = $user_id");
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($user['place_id'] != 4) {
                    header("Location: ../index.php");
                    exit();
                }
            } else {
                // Eğer kullanıcı bulunamazsa, oturumu sonlandır ve login sayfasına yönlendir
                session_destroy();
                header("Location: ../login.php");
                exit();
            }

// Günlük sipariş sayısı
$current_date = date('Y-m-d');
$sql_today_orders = "SELECT COUNT(*) AS today_orders 
                     FROM orders 
                     WHERE DATE(created_at) = '$current_date'";
$result_today_orders = $conn->query($sql_today_orders);

if (!$result_today_orders) {
    die("Sorgu hatası: " . $conn->error);
}
$today_orders_data = $result_today_orders->fetch_assoc();

// Anlık dolu masa sayısı (status_number 0, 1 veya 2 olan masalar)
$sql_active_tables = "SELECT COUNT(DISTINCT table_id) AS active_tables 
                      FROM orders o
                      WHERE o.status_number IN (0, 1, 2) AND DATE(o.created_at) = '$current_date'";

$result_active_tables = $conn->query($sql_active_tables);

if (!$result_active_tables) {
    die("Sorgu hatası: " . $conn->error);
}
$active_tables_data = $result_active_tables->fetch_assoc();

// Günlük hasılat
$sql_daily_revenue = "SELECT SUM(o.payment) AS total_revenue 
                      FROM orders o
                      WHERE o.status_number = 3 
                        AND DATE(o.created_at) = '$current_date'";

$result_daily_revenue = $conn->query($sql_daily_revenue);

if (!$result_daily_revenue) {
    die("Sorgu hatası: " . $conn->error);
}
$daily_revenue_data = $result_daily_revenue->fetch_assoc();

// Aylık hasılat
$current_month = date('Y-m');
$sql_monthly_revenue = "SELECT SUM(o.payment) AS total_revenue 
                        FROM orders o
                        WHERE o.status_number = 3 
                          AND DATE_FORMAT(o.created_at, '%Y-%m') = '$current_month'";


$result_monthly_revenue = $conn->query($sql_monthly_revenue);

if (!$result_monthly_revenue) {
    die("Sorgu hatası: " . $conn->error);
}
$monthly_revenue_data = $result_monthly_revenue->fetch_assoc();
?>
<!-- HTML ve CSS kodları devam ediyor... -->
<meta http-equiv="refresh" content="10">
<style>
    body {
        background-color: #DDDDDD;
        font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: indigo;
    }

    .btn-red {
        background-color: #19376D;
        border-color: #576CBC;
        color: whitesmoke;
        font-weight: bold;

    }

    .btn-red:hover {
        background-color: #576CBC;
        border-color: white;
        color: whitesmoke;
        font-weight: bold;

    }

    .col-md-6 {
        margin-top: 30px;
    }

    .h3desing {
        font-family: "Alegreya Sans", sans-serif;
        font: 1.5em sans-serif;
        color: #0B2447;
        font-weight: 700;
        text-align: center;
    }

    .row {
        margin-top: 20px;
    }

    .row_desing {
        background-color: #C7C8CC;
        border-radius: 5px;
    }

    .bg-color1 {
        background-color: #79155B;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color1:hover {
        background-color: #C23373;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color2 {
        background-color: #19376D;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color2:hover {
        background-color: #576CBC;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color3 {
        background-color: #618264;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color3:hover {
        background-color: #79AC78;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color4 {
        background-color: #6527BE;
        color: whitesmoke;
        font-weight: bold;
    }

    .bg-color4:hover {
        background-color: #9681EB;
        color: whitesmoke;
        font-weight: bold;
    }
</style>

<body>
    <div class="container-fluid mt-6 bg-blue">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center bg-color1 mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $today_orders_data['today_orders']; ?></h3>
                        <p class="card-text">Bugünkü Siparişler</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-color2 mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $active_tables_data['active_tables']; ?></h3>
                        <p class="card-text">Anlık Dolu Masa</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-color3  mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo number_format($daily_revenue_data['total_revenue'], 2); ?> TL
                        </h3>
                        <p class="card-text">Günlük Hasılat</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-color4  mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo number_format($monthly_revenue_data['total_revenue'], 2); ?>
                            TL</h3>
                        <p class="card-text">Aylık Hasılat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Siparişler -->
        <div class="row row_desing" style="margin-top: 20px;">
            <!-- Verilen Siparişler -->
            <div class="col-md-6 div-desing">
                <h3 class="h3desing">Bugün Verilen Siparişler</h3>
                <div class="list-group">
                    <?php
                    $sql_latest_orders = "SELECT o.order_id, t.table_name, DATE_FORMAT(o.created_at, '%H:%i') AS created_at
                                          FROM orders o
                                          JOIN tables t ON o.table_id = t.table_id
                                          WHERE DATE(o.created_at) = '$current_date' AND o.status_number IN (0, 1, 2)
                                          ORDER BY o.created_at DESC 
                                          LIMIT 5";
                    $result_latest_orders = $conn->query($sql_latest_orders);
                    if ($result_latest_orders && $result_latest_orders->num_rows > 0) {
                        while ($row = $result_latest_orders->fetch_assoc()) {
                            echo '<a href="#" class="list-group-item list-group-item-action order-link" data-order-id="' . $row['order_id'] . '">';
                            echo '<small> Sipariş Numarası: ' . $row['order_id'] . ' | Masa: ' . $row['table_name'] . ' | Saat: ' . $row['created_at'] . '</small>';
                            echo '</a>';
                        }
                    } else {
                        echo '<div class="alert alert-info">Bugün verilen sipariş yok.</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Tamamlanan Siparişler -->
            <div class="col-md-6">
                <h3 class="h3desing">Tamamlanan Siparişler</h3>
                <div class="list-group">
                    <?php
                    $sql_completed_orders = "SELECT o.order_id, t.table_name, DATE_FORMAT(o.created_at, '%H:%i') AS created_at 
                                             FROM orders o
                                             JOIN tables t ON o.table_id = t.table_id
                                             WHERE o.status_number = 3 AND DATE(o.created_at) = '$current_date'
                                             ORDER BY o.created_at DESC 
                                             LIMIT 5";
                    $result_completed_orders = $conn->query($sql_completed_orders);
                    if ($result_completed_orders && $result_completed_orders->num_rows > 0) {
                        while ($row = $result_completed_orders->fetch_assoc()) {
                            echo '<a href="#" class="list-group-item list-group-item-action order-link" data-order-id="' . $row['order_id'] . '">';
                            echo '<small> Sipariş Numarası: ' . $row['order_id'] . ' | Masa: ' . $row['table_name'] . ' | Saat: ' . $row['created_at'] . '</small>';
                            echo '</a>';
                        }
                    } else {
                        echo '<div class="alert alert-info">Tamamlanmış sipariş yok.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <form id="closeCashForm" action="close_cash.php" method="post">
                    <button type="button" class="btn btn-red btn-lg" onclick="confirmCloseCash()">Kasa Kapat</button>
                </form>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Sipariş Detayları</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Sipariş detayları burada gösterilecek -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-danger" onclick="closeTable()">Masa Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5"></script>
        <script>
            function confirmCloseCash() {
                if (confirm("Kasa kapatmak istediğinizden emin misiniz?")) {
                    document.getElementById('closeCashForm').submit();
                }
            }

            $(document).ready(function () {
                $('.order-link').click(function () {
                    var orderId = $(this).data('order-id');
                    $.ajax({
                        url: 'get_order_details.php',
                        method: 'POST',
                        data: {
                            order_id: orderId
                        },
                        success: function (response) {
                            $('#orderModal .modal-body').html(response);
                            $('#orderModal').data('order-id',
                                orderId); // order-id'yi modal'a set ediyoruz

                            // Sipariş durumu kontrolü
                            var statusNumber = $('#order_status_number').val();
                            if (statusNumber == 3) { // Eğer sipariş tamamlanmışsa
                                $('#orderModal .btn-danger')
                                    .hide(); // "Masa Kapat" butonunu gizle
                            } else {
                                $('#orderModal .btn-danger')
                                    .show(); // Diğer durumda butonu göster
                            }

                            $('#orderModal').modal('show');
                        }
                    });
                });
            });


            function closeTable() {
                var orderId = $('#orderModal').data('order-id');
                $.ajax({
                    url: 'close_table.php',
                    method: 'POST',
                    data: {
                        order_id: orderId
                    },
                    success: function (response) {
                        alert(response);
                        $('#orderModal').modal('hide');
                        location.reload(); // Sayfayı yeniden yükleyerek güncellemeleri göster
                    }
                });
            }
        </script>
</body>