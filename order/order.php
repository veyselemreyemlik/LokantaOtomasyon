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
<meta http-equiv="refresh" content="5">
<style>
body {
    background-color: #DDDDDD;
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    color: whitesmoke;
}


.text-style {
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    color: #DDE6ED;
}

.card.mb-3.status-prepared {
    background-color: #749BC2 !important;
}

.fs-5 {
    color: black;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: var(--bs-modal-padding);
    color: black;
}

.card-body {
    height: 180px;
    width: 200px;
}

.btn-edit {
    background-color: #DFDFDE;
    color: #006E7F;
}

.btn-edit:hover {
    background-color: #66BFBF;
    color: whitesmoke;
    border: 1.5px solid whitesmoke;
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
    background-color: #006E7F !important;
}

.status-prepared {
    background-color: #006E7F !important;
}

.status-awaiting-payment {
    background-color: #F5A623 !important;
}

.status-empty {
    background-color: #6c757d !important;
}

.status-unknown {
    background-color: #606676 !important;
}

.col-md-3 {
    background-color: #DDDDDD;
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
</style>
<div class="col-md-3">
    <div class="card mb-3 <?php echo $status_class; ?>" style="max-width: 18rem;">
        <div class="card-body">
            <h5 class="card-title title text-style">MASA <?php echo htmlspecialchars($table['table_name']); ?></h5>
            <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
            <h6 class="card-subtitle mb-2 text-body-secondary">Sipariş Numarası: <?php echo $table['order_id']; ?></h6>
            <?php else: ?>
            <h6 class="card-subtitle mb-2 text-body-secondary">Sipariş Numarası: -</h6>
            <?php endif; ?>
            <p class="card-text"><b><?php echo $status_text; ?></b></p>
            <?php if (isset($table['order_id']) && $table['status_number'] != 3): ?>
            <a href="modal.php?order_id=<?php echo $table['order_id']; ?>" class="btn btn-edit">Detaylar</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    }
    echo '</div>';
} else {
    echo "Sipariş bulunamadı.";
}
$conn->close();
?>