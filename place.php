<?php
include 'header.php';
include 'connection.php';

// Sipariş durumunu güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_detail_id = intval($_POST['order_detail_id']);
    $place_id = intval($_POST['place_id']);
    $new_status = $_POST['new_status'];

    // Sipariş durumunu ve güncellenme tarihini güncelle
    $update_sql = "UPDATE order_details SET status = '$new_status', updated_at = NOW() WHERE id = $order_detail_id";
    if ($conn->query($update_sql) === TRUE) {
        // Güncelleme başarılı olursa aynı order_id'ye sahip tüm detayların durumu kontrol edilir
        $order_id_query = "SELECT order_id FROM order_details WHERE id = $order_detail_id";
        $order_id_result = $conn->query($order_id_query);
        if ($order_id_result && $order_id_result->num_rows > 0) {
            $order_id_row = $order_id_result->fetch_assoc();
            $order_id = $order_id_row['order_id'];

            $check_status_sql = "SELECT COUNT(*) AS total, SUM(CASE WHEN status = 'Hazırlandı' THEN 1 ELSE 0 END) AS ready_count
                                 FROM order_details
                                 WHERE order_id = $order_id";
            $status_result = $conn->query($check_status_sql);
            if ($status_result && $status_result->num_rows > 0) {
                $status_row = $status_result->fetch_assoc();
                if ($status_row['total'] == $status_row['ready_count']) {
                    // Tüm sipariş detayları "Hazırlandı" olarak işaretlendiyse, siparişin durumunu güncelle
                    $update_order_sql = "UPDATE orders SET status = 'Sipariş Hazırlandı' WHERE id = $order_id";
                    $conn->query($update_order_sql);
                } else {
                    // Eğer tüm detaylar "Hazırlandı" değilse, sipariş durumunu "Sipariş Verildi" olarak geri çevir
                    $update_order_sql = "UPDATE orders SET status = 'Sipariş Verildi' WHERE id = $order_id";
                    $conn->query($update_order_sql);
                }
            }
        }
        $success_message = "Sipariş durumu başarıyla güncellendi.";
    } else {
        $error_message = "Hata: " . $conn->error;
    }
}

// Eğer URL'de place_id parametresi yoksa veya geçerli bir place_id değilse, varsayılan olarak place_id = 1 alınacak
$place_id = isset($_GET['place_id']) ? intval($_GET['place_id']) : 1;

// Place ID'sine göre "Hazırlanıyor" durumundaki siparişleri seçmek için SQL sorgusu
$sql_preparing = "SELECT od.*, mi.name AS menu_item_name, od.status AS order_status, t.table_name, p.place_name, o.created_at AS order_time, u.username AS user_name
                  FROM order_details od
                  LEFT JOIN menu_items mi ON od.menu_item_id = mi.id
                  LEFT JOIN orders o ON od.order_id = o.id
                  LEFT JOIN tables t ON o.table_id = t.id
                  LEFT JOIN place p ON mi.place_id = p.id
                  LEFT JOIN users u ON o.user_id = u.id
                  WHERE p.id = $place_id AND od.status = 'Hazırlanıyor'";

$result_preparing = $conn->query($sql_preparing);

// Place ID'sine göre "Hazırlandı" durumundaki siparişleri seçmek için SQL sorgusu
$sql_ready = "SELECT od.*, mi.name AS menu_item_name, od.status AS order_status, t.table_name, p.place_name, o.created_at AS order_time, od.updated_at AS updated_time, u.username AS user_name
              FROM order_details od
              LEFT JOIN menu_items mi ON od.menu_item_id = mi.id
              LEFT JOIN orders o ON od.order_id = o.id
              LEFT JOIN tables t ON o.table_id = t.id
              LEFT JOIN place p ON mi.place_id = p.id
              LEFT JOIN users u ON o.user_id = u.id
              WHERE p.id = $place_id AND od.status = 'Hazırlandı'";

$result_ready = $conn->query($sql_ready);

// Place name'i almak için sorgu
$place_sql = "SELECT place_name FROM place WHERE id = $place_id";
$place_result = $conn->query($place_sql);
$place_name = $place_result ? $place_result->fetch_assoc()['place_name'] : 'Place';
?>

<div class="container mt-5">
    <h1 class="mb-4"><?php echo $place_name; ?> Sipariş Detayları</h1>

    <?php if (isset($success_message)) : ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)) : ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <h2>Hazırlanıyor</h2>
    <?php if ($result_preparing && $result_preparing->num_rows > 0) : ?>
    <table class="table table-striped" style="text-align:center;">
        <thead>
            <tr>
                <th>Sipariş Numarası</th>
                <th>Menü Adı</th>
                <th>Adet</th>
                <th>Masa</th>
                <th>Sipariş Saati</th>
                <th>Sipariş Notu</th>
                <th>Durum</th>
                <th>Garson Adı</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_preparing->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['menu_item_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['table_name']; ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($row['order_time'])); ?></td>
                <td><?php echo $row['statement']; ?></td>
                <td><?php echo $row['order_status']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td>
                    <?php if ($row['order_status'] != 'Hazırlandı') : ?>
                    <form action="" method="post">
                        <input type="hidden" name="order_detail_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                        <input type="hidden" name="new_status" value="Hazırlandı">
                        <button type="submit" class="btn btn-success">Hazırlandı</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else : ?>
    <div class="alert alert-info">Gösterilecek sipariş detayı bulunamadı.</div>
    <?php endif; ?>

    <h2>Hazırlandı</h2>
    <?php if ($result_ready && $result_ready->num_rows > 0) : ?>
    <table class="table table-striped" style="text-align:center;">
        <thead>
            <tr>
                <th>Sipariş Numarası</th>
                <th>Menü Adı</th>
                <th>Adet</th>
                <th>Masa</th>
                <th>Sipariş Saati</th>
                <th>Güncellenme Saati</th>
                <th>Durum</th>
                <th>Garson Adı</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_ready->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['menu_item_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['table_name']; ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($row['order_time'])); ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($row['updated_time'])); ?></td>
                <td><?php echo $row['order_status']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td>
                    <?php if ($row['order_status'] != 'Hazırlanıyor') : ?>
                    <form action="" method="post">
                        <input type="hidden" name="order_detail_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                        <input type="hidden" name="new_status" value="Hazırlanıyor">
                        <button type="submit" class="btn btn-danger">Hazırlanmadı</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else : ?>
    <div class="alert alert-info">Gösterilecek sipariş detayı bulunamadı.</div>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
