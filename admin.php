<?php
include 'connection.php';
include 'sidebar.php';

session_start();

// Masaları ve sipariş durumlarını veritabanından çekme
$sql_active_tables = "SELECT t.id AS table_id, t.table_name, u.username AS waiter_name
                      FROM tables t
                      LEFT JOIN orders o ON t.id = o.table_id
                      LEFT JOIN users u ON o.user_id = u.id
                      WHERE o.status IS NOT NULL
                      GROUP BY t.id, t.table_name, u.username";
$result_active_tables = $conn->query($sql_active_tables);

if (!$result_active_tables) {
    die("Sorgu hatası: " . $conn->error);
}

$sql_inactive_tables = "SELECT t.id AS table_id, t.table_name
                        FROM tables t
                        LEFT JOIN orders o ON t.id = o.table_id
                        WHERE o.status IS NULL";
$result_inactive_tables = $conn->query($sql_inactive_tables);

if (!$result_inactive_tables) {
    die("Sorgu hatası: " . $conn->error);
}
// Günlük sipariş sayısı ve toplam ücret
$current_date = date('Y-m-d');
$sql_daily_orders = "SELECT COUNT(*) AS total_orders, SUM(mi.price * od.quantity) AS total_revenue
                     FROM orders o
                     JOIN order_details od ON o.id = od.order_id
                     JOIN menu_items mi ON od.menu_item_id = mi.id
                     WHERE DATE(o.created_at) = '$current_date'";
$result_daily_orders = $conn->query($sql_daily_orders);

if (!$result_daily_orders) {
    die("Sorgu hatası: " . $conn->error);
}

$daily_orders_data = $result_daily_orders->fetch_assoc();

// Toplam sipariş sayısı
$sql_total_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$result_total_orders = $conn->query($sql_total_orders);
if (!$result_total_orders) {
    die("Sorgu hatası: " . $conn->error);
}
$total_orders_data = $result_total_orders->fetch_assoc();

// Bugünkü sipariş sayısı
$sql_today_orders = "SELECT COUNT(*) AS today_orders FROM orders WHERE DATE(created_at) = '$current_date'";
$result_today_orders = $conn->query($sql_today_orders);
if (!$result_today_orders) {
    die("Sorgu hatası: " . $conn->error);
}
$today_orders_data = $result_today_orders->fetch_assoc();

// Toplam müşteri sayısı
$sql_total_customers = "SELECT COUNT(DISTINCT user_id) AS total_customers FROM orders";
$result_total_customers = $conn->query($sql_total_customers);
if (!$result_total_customers) {
    die("Sorgu hatası: " . $conn->error);
}
$total_customers_data = $result_total_customers->fetch_assoc();

// Toplam teslim edilen sipariş sayısı
$sql_total_delivered = "SELECT COUNT(*) AS total_delivered FROM orders WHERE status = 'delivered'";
$result_total_delivered = $conn->query($sql_total_delivered);
if (!$result_total_delivered) {
    die("Sorgu hatası: " . $conn->error);
}
$total_delivered_data = $result_total_delivered->fetch_assoc();

/* Toplam rezervasyon sayısı
$sql_total_reservation = "SELECT COUNT(*) AS total_reservation FROM reservations";
$result_total_reservation = $conn->query($sql_total_reservation);
if (!$result_total_reservation) {
    die("Sorgu hatası: " . $conn->error);
}
$total_reservation_data = $result_total_reservation->fetch_assoc();*/
?>

<body style="background-color: red;">
    <div class="container-fluid mt-6 bg-blue">
        <div class="row">
            <div class="col-md-2">
                <div class="card text-center bg-red text-black mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $total_orders_data['total_orders']; ?></h3>
                        <p class="card-text">Toplam Sipariş</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-pink text-black mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $today_orders_data['today_orders']; ?></h3>
                        <p class="card-text">Bugünkü Siparişler</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-green text-black mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo number_format($daily_orders_data['total_revenue'], 1); ?>k
                        </h3>
                        <p class="card-text">Günlük Tutar</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-purple text-black mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $total_customers_data['total_customers']; ?>k</h3>
                        <p class="card-text">Toplam Müşteri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-blue text-black mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $total_delivered_data['total_delivered']; ?></h3>
                        <p class="card-text">Teslim Edilen Sipariş</p>
                    </div>
                </div>
            </div>

        </div>
        <div class="row" style="margin-top: 20px;">
            <!-- Latest Order -->
            <div class="col-md-4">
                <h3>Son Siparişler</h3>
                <div class="list-group">
                    <?php
                    $sql_latest_orders = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
                    $result_latest_orders = $conn->query($sql_latest_orders);
                    if ($result_latest_orders && $result_latest_orders->num_rows > 0) {
                        while ($row = $result_latest_orders->fetch_assoc()) {
                            echo '<a href="#" class="list-group-item list-group-item-action">';
                            echo '<small> Order ID: ' . $row['id'] . ' | Table No: ' . $row['table_id'] . ' | Time: ' . $row['created_at'] . '</small>';
                            echo '</a>';
                        }
                    } else {
                        echo '<div class="alert alert-info">No recent orders.</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Latest Online Order -->
            <!-- Latest Completed Orders -->
            <div class="col-md-4">
                <h3>Tamamlanan Siparişler</h3>
                <div class="list-group">
                    <?php
                    $sql_latest_completed_orders = "SELECT * FROM orders WHERE status = 'completed' ORDER BY created_at DESC LIMIT 5";
                    $result_latest_completed_orders = $conn->query($sql_latest_completed_orders);
                    if ($result_latest_completed_orders && $result_latest_completed_orders->num_rows > 0) {
                        while ($row = $result_latest_completed_orders->fetch_assoc()) {
                            echo '<a href="#" class="list-group-item list-group-item-action">';
                            echo '<small>Order No.: (' . $row['id'] . ') | Table No: ' . $row['table_no'] . ' | Time: ' . $row['created_at'] . '</small>';
                            echo '</a>';
                        }
                    } else {
                        echo '<div class="alert alert-info">Son tamamlanmış sipariş yok.</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-4">

            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5