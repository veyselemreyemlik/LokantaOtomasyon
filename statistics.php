<?php
include 'connection.php';
include 'sidebar.php';

// Satışları aylara göre almak için SQL sorgusu
$sql = "SELECT MONTH(o.created_at) as month, SUM(mi.price) as total_revenue
        FROM orders o
        INNER JOIN order_details od ON o.id = od.order_id
        INNER JOIN menu_items mi ON od.menu_item_id = mi.id
        WHERE o.status = 'Sipariş Teslim Edildi'
        GROUP BY MONTH(o.created_at)";

$result = $conn->query($sql);

// Aylık toplam satış tutarlarını tutmak için bir dizi
$total_revenues = array_fill(1, 12, 0); // 1'den 12'ye kadar ayları kapsayan bir dizi oluştur

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_revenues[$row['month']] = $row['total_revenue'];
    }
}

// Ay adlarını bir diziye koy
$months = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
?>

<div class="container mt-5">
    <h2 class="mb-4">Aylık Satış İstatistikleri (TL cinsinden)</h2>
    <canvas id="salesChart" style="height: 400px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
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
                            callback: function(value) {
                                return value.toLocaleString('tr-TR') + ' TL';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
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
