<?php
session_start(); // Session başlatma
include 'connection.php'; // Veritabanı bağlantısı

// Kullanıcının user_id'sini al
$user_id = $_SESSION['user_id'];

// Kullanıcının place_id'sini veritabanından al
$sql = "SELECT place_id FROM users WHERE id = $user_id";
$result = $conn->query($sql);
if ($result && $result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $place_id = $user['place_id'];

    // Kullanıcının place_id'sine göre yönlendirme yap
    switch ($place_id) {
        case 2:
            header("Location: user_Controller/ızgara.php");
            break;
        case 3:
            header("Location: user_Controller/fırın.php");
            break;
        case 4:
            header("Location: garson/garson.php");
            break;
        case 5:
            header("Location: user_Controller/mutfak.php");
            break;
        default:
            header("Location: index.php");
            break;
    }
    exit;
} else {
    // Kullanıcının place_id'si bulunamazsa ana sayfaya yönlendir
    header("Location: index.php");
}
?>

<?php
// Veritabanı sorgularını yap
$sql_preparing = "SELECT * FROM orders WHERE order_status = 'Hazırlanıyor'";
$result_preparing = $conn->query($sql_preparing);

$sql_ready = "SELECT * FROM orders WHERE order_status = 'Hazırlandı'";
$result_ready = $conn->query($sql_ready);
?>

<div class="container mt-5">
    <h1 class="mb-4"><?php echo $place_name; ?> Sipariş Detayları</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <h2>Hazırlanıyor</h2>
    <?php if ($result_preparing && $result_preparing->num_rows > 0): ?>
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
                <?php while ($row = $result_preparing->fetch_assoc()): ?>
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
                            <?php if ($row['order_status'] != 'Hazırlandı'): ?>
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
    <?php else: ?>
        <div class="alert alert-info">Gösterilecek sipariş detayı bulunamadı.</div>
    <?php endif; ?>

    <h2>Hazırlandı</h2>
    <?php if ($result_ready && $result_ready->num_rows > 0): ?>
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
                <?php while ($row = $result_ready->fetch_assoc()): ?>
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
                            <?php if ($row['order_status'] != 'Hazırlanıyor'): ?>
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
    <?php else: ?>
        <div class="alert alert-info">Gösterilecek sipariş detayı bulunamadı.</div>
    <?php endif; ?>

</div>