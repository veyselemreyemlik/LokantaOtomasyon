<?php
include '../connection.php';
include '../sidebar.php';
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
          


$current_year = date("Y");
$current_date = date("Y-m-d");

// Satışları aylara göre almak için SQL sorgusu
$sql = "SELECT MONTH(o.created_at) as month, SUM(o.payment) as total_revenue
        FROM orders o
        WHERE o.status_number = '3' AND YEAR(o.created_at) = YEAR(CURDATE())
        GROUP BY MONTH(o.created_at)";

$result = $conn->query($sql);

// Aylık toplam satış tutarlarını tutmak için bir dizi
$total_revenues = array_fill(1, 12, 0); // 1'den 12'ye kadar ayları kapsayan bir dizi oluştur

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_revenues[$row['month']] = $row['total_revenue'];
    }
}

// Her menü elemanından kaç tane satıldığını almak için SQL sorgusu
$sql2 = "SELECT mi.menu_name as menu_item, SUM(od.piece) as total_sold
        FROM orders o
        INNER JOIN order_details od ON o.order_id = od.order_id
        INNER JOIN menu_items mi ON od.menu_id = mi.menu_id
        WHERE o.status_number = '3' AND YEAR(o.created_at) = YEAR(CURDATE())
        GROUP BY mi.menu_name
        ORDER BY total_sold DESC";


$result2 = $conn->query($sql2);

// Satışları saatlere göre almak için SQL sorgusu
$sql3 = "SELECT HOUR(o.created_at) as hour, SUM(o.payment) as total_revenue
        FROM orders o
        WHERE o.status_number = '3' AND DATE(o.created_at) = DATE(CURDATE())
        GROUP BY HOUR(o.created_at)";


$result3 = $conn->query($sql3);

$total_revenue = array_fill(0, 24, 0); // 0'dan 23'e kadar saatleri kapsayan bir dizi oluştur

if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        $total_revenue[$row['hour']] = $row['total_revenue'];
    }
}

// Saatleri bir diziye koy
$hours = range(0, 23);

// Ay adlarını bir diziye koy
$months = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
?>

<style>
    body {
        background-color: #DDDDDD;
        font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #071952;
    }

    .col-md-7,
    .col-md-4 {
        margin-bottom: 50px;
        margin-top: 50px;
    }

    h3 {
        font-size: 26px;
        font-weight: 550;
        margin-bottom: 20px;
        color: #002254;
        text-align: center;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif
    }
</style>

<div class="row ">
    <div class="col-md-7">

        <h3 class="mb-4"><?php echo date("d/m/Y", strtotime($current_date)); ?> Saatlik Satış Grafiği</h3>
        <canvas id="dailySalesChart" style="height:400px;"></canvas>

    </div>
    <div class="col-md-4">

        <h3 class="mb-4"><?php echo date("d/m/Y", strtotime($current_date)); ?> Kullanıcı Siparişleri</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kullanıcı Adı</th>
                        <th>Sipariş Adedi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql4 = "SELECT u.username as username, COUNT(o.order_id) as order_count
               FROM orders o
               INNER JOIN users u ON o.user_id = u.user_id
               WHERE o.status_number = '3' AND DATE(o.created_at) = DATE(CURDATE())
               GROUP BY u.username";


                    $result4 = $conn->query($sql4);

                    if ($result4->num_rows > 0) {
                        while ($row = $result4->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['order_count']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='2'>Hiç sipariş bulunamadı.</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <hr>
    <div class="col-md-7">
        <h3 class="mb-4"><?php echo $current_year; ?> Yılı Satış Grafiği</h3>
        <canvas id="monthlySalesChart" style="height:400px;"></canvas>
    </div>
    <div class="col-md-4">
        <h3 class="mb-4"><?php echo $current_year; ?> Yılı Satış Tablosu</h3><br>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Menü Öğesi</th>
                        <th>Satılan Adet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $max_rows = 10; // Görüntülenecek maksimum satır sayısı
                    $row_count = 0;

                    if ($result2->num_rows > 0) {
                        while ($row = $result2->fetch_assoc()) {
                            if ($row_count >= $max_rows)
                                break;
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['menu_item']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['total_sold']) . "</td>";
                            echo "</tr>";
                            $row_count++;
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='2'>Satılan menü öğesi bulunamadı</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx1 = document.getElementById('monthlySalesChart').getContext('2d');
        var monthlySalesChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Toplam Satış Tutarı (TL)',
                    data: <?php echo json_encode(array_values($total_revenues)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString('tr-TR') + ' TL';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString('tr-TR') + ' TL';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        var ctx2 = document.getElementById('dailySalesChart').getContext('2d');
        var dailySalesChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($hours); ?>,
                datasets: [{
                    label: 'Toplam Satış Tutarı (TL)',
                    data: <?php echo json_encode(array_values($total_revenue)); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString('tr-TR') + ' TL';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Saatler'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString('tr-TR') + ' TL';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php
$conn->close();
?>