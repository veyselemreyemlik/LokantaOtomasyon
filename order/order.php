<?php
include '../connection.php';
include '../sidebar.php';

// Önce tüm MASAları alıyoruz
$sql = "SELECT t.table_id, t.table_name, o.order_id, o.status_number, o.payment, u.username,
               SUM(od.piece * mi.price) as total_price
        FROM tables t
        LEFT JOIN orders o ON t.table_id = o.table_id
        LEFT JOIN order_details od ON o.order_id = od.order_id
        LEFT JOIN menu_items mi ON od.menu_id = mi.menu_id
        LEFT JOIN users u ON o.user_id = u.user_id
        GROUP BY t.table_id, o.order_id, o.status_number, o.payment, u.username
        ORDER BY t.table_name, o.created_at DESC";

$result = $conn->query($sql);

$tables = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($tables[$row['table_id']])) {
        $tables[$row['table_id']] = $row;
    }
}

if (count($tables) > 0) {
    echo '<div class="row">';
    foreach ($tables as $table) {
        $status_class = 'status-unknown';
        $status_text = 'Masa boş';

        if (isset($table['status_number'])) {
            switch ($table['status_number']) {
                case 0:
                    $status_class = 'status-ordered';
                    $status_text = 'Sipariş verildi';
                    break;
                case 1:
                    $status_class = 'status-prepared';
                    $status_text = 'Sipariş hazırlandı';
                    break;
                case 2:
                    $status_class = 'status-awaiting-payment';
                    $status_text = 'Ödeme bekliyor..';
                    break;
                case 3:
                    $status_class = 'status-empty';
                    $status_text = 'Masa boş';
                    break;
                default:
                    $status_class = 'status-unknown';
                    $status_text = 'Durum bilinmiyor';
            }
        }
        ?>
        <style>
            body {
                background-color: #DDDDDD;
                font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: black;
            }

            .card-body {
                height: 180px;
                width: 200px;
            }

            .btn-edit {
                background-color: #4682A9;
                color: whitesmoke;
            }

            .btn-edit:hover {
                background-color: #91C8E4;
                color: whitesmoke;
                border: 1px solid #4682A9;
            }

            .btn-delete {
                background-color: #B70404;
                color: whitesmoke;
            }

            .btn-delete:hover {
                background-color: #C40C0C;
                color: whitesmoke;
                border-color: 1px solid #B70404;
            }

            .title {
                font-weight: bold;
                padding-bottom: 5px;
                padding-left: 10px;
            }

            .status-ordered {
                background-color: #28a745 !important;
            }

            .status-prepared {
                background-color: #28a745 !important;
            }

            .status-awaiting-payment {
                background-color: #17a2b8 !important;
            }

            .status-empty {
                background-color: #6c757d !important;
            }

            .status-unknown {
                background-color: #ffffff !important;
            }
        </style>
        <div class="col-md-3">
            <div class="card mb-3 <?php echo $status_class; ?>" style="max-width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title title">MASA <?php echo htmlspecialchars($table['table_name']); ?></h5>
                    <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Sipariş Numarası: <?php echo $table['order_id']; ?></h6>
                    <?php else: ?>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Sipariş Numarası: -</h6>
                    <?php endif; ?>
                    <p class="card-text"><b><?php echo $status_text; ?></b></p>
                    <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
                        <button type="button" class="btn btn-edit" data-bs-toggle="modal"
                            data-bs-target="#modal<?php echo $table['order_id']; ?>">
                            Detaylar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
            <div class="modal fade" id="modal<?php echo $table['order_id']; ?>" tabindex="-1"
                aria-labelledby="modalLabel<?php echo $table['order_id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalLabel<?php echo $table['order_id']; ?>">Sipariş Detayları -
                                Sipariş Numarası: <?php echo $table['order_id']; ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalBody<?php echo $table['order_id']; ?>">
                            <!-- Sipariş detayları burada AJAX ile yüklenecek -->
                        </div>
                        <div class="modal-footer" style="justify-content: center;">
                            <?php
                            if ($table['status_number'] == 2) { ?>
                                <div class="row">
                                    <div class="col-md-7">
                                        <input type="number" id="paymentAmount<?php echo $table['order_id']; ?>"
                                            placeholder="Ödeme miktarı" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <select id="paymentType<?php echo $table['order_id']; ?>" class="form-select">
                                            <option value="">Ödeme Türü</option>
                                            <option value="1">Nakit</option>
                                            <option value="2">Kart</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12"></div>
                                </div>
                                <?php if ($table['status_number'] == 2): ?>
                                    <button type="button" class="btn btn-success"
                                        onclick="applyDiscount(<?php echo $table['order_id']; ?>, 5,  <?php echo $table['total_price']; ?>)">%5</button>
                                    <button type="button" class="btn btn-success"
                                        onclick="applyDiscount(<?php echo $table['order_id']; ?>, 10, <?php echo $table['total_price']; ?>)">%10</button>
                                    <button type="button" class="btn btn-success"
                                        onclick="applyDiscount(<?php echo $table['order_id']; ?>, 20, <?php echo $table['total_price']; ?>)">%20</button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="confirmPayment(<?php echo $table['order_id']; ?>)">Ödeme Yapıldı</button>
                                <?php endif;
                            } ?>
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
                }
            });
        });
    });

    function applyDiscount(orderId, discountPercentage, totalPrice) {
        var discountedAmount = totalPrice - (totalPrice * discountPercentage / 100);
        $('#paymentAmount' + orderId).val(discountedAmount.toFixed(2));
    }

    function confirmPayment(orderId) {
        var paymentAmount = $('#paymentAmount' + orderId).val();
        var paymentType = $('#paymentType' + orderId).val();

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
            success: function (response) {
                if (response == 'success') {
                    alert('Ödeme yapıldı.');
                    location.reload(); // Sayfayı yenileyerek güncel durumu göster
                } else {
                    alert('Hata: ' + response);
                }
            }
        });
    }
</script>