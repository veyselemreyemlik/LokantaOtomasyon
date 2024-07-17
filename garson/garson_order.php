<?php
include '../connection.php';
include '../sidebar.php';

// Önce tüm masaları alıyoruz
$sql = "SELECT t.table_id, t.table_name, o.order_id, o.status_number, o.payment, u.username
        FROM tables t
        LEFT JOIN orders o ON t.table_id = o.table_id
        LEFT JOIN users u ON o.user_id = u.user_id
        ORDER BY t.table_name, o.created_at DESC";

$result = $conn->query($sql);

$tables = [];
while($row = $result->fetch_assoc()) {
    if (!isset($tables[$row['table_id']])) {
        $tables[$row['table_id']] = $row;
    }
}

if (count($tables) > 0) {
    echo '<div class="row">';
    foreach($tables as $table) {
        $card_color = 'bg-secondary';
        $status_text = 'Masa boş';

        if (isset($table['status_number'])) {
            switch ($table['status_number']) {
                case 0:
                    $card_color = 'bg-success';
                    $status_text = 'Sipariş verildi';
                    break;
                case 1:
                    $card_color = 'bg-warning';
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
<style>
    .card-body {
        height: 180px;
        width: 200px;
    }
</style>
    <div class="col-md-3">
        <div class="card mb-3 <?php echo $card_color; ?>" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title">Masa <?php echo htmlspecialchars($table['table_name']); ?></h5>
                <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Sipariş Numarası: <?php echo $table['order_id']; ?></h6>
                <?php else: ?>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Sipariş Numarası: -</h6>
                <?php endif; ?>
                <p class="card-text"><b><?php echo $status_text; ?></b></p>
                <?php if (isset($table['order_id']) && $table['status_number'] == 1): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal<?php echo $table['order_id']; ?>">
                      Detaylar
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <?php if (isset($table['order_id']) && $table['status_number'] == 1): ?>
    <div class="modal fade" id="modal<?php echo $table['order_id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $table['order_id']; ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalLabel<?php echo $table['order_id']; ?>">Sipariş Detayları - Sipariş Numarası: <?php echo $table['order_id']; ?></h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="modalBody<?php echo $table['order_id']; ?>">
            <!-- Sipariş detayları burada AJAX ile yüklenecek -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" onclick="confirmDelivery(<?php echo $table['order_id']; ?>)">Teslim Edildi</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
<?php
    }
    echo '</div>';
} else {
    echo "Sipariş bulunamadı.";
}
$conn->close();
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('button[data-bs-toggle="modal"]').click(function() {
        var orderId = $(this).data('bs-target').replace('#modal', '');
        $.ajax({
            url: 'get_order_details.php',
            type: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                $('#modalBody' + orderId).html(response);
            }
        });
    });
});

function confirmDelivery(orderId) {
    if (confirm('Siparişin teslim edildiğinden emin misiniz?')) {
        $.ajax({
            url: 'update_order_status.php',
            type: 'POST',
            data: { order_id: orderId, status_number: 2 },
            success: function(response) {
                if (response == 'success') {
                    alert('Sipariş teslim edildi ve durum güncellendi.');
                    location.reload(); // Sayfayı yenileyerek güncel durumu göster
                } else {
                    alert('Hata: ' + response);
                }
            }
        });
    }
}
</script>
