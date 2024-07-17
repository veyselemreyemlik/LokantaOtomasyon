<?php
include 'connection.php';
session_start();
$table_id = $_GET['table_id'];

// Fetch menu items
$menu_items = [];
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu_items[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Yeni Sipariş</title>
</head>

<body>

    <h1>Masa <?php echo $table_id; ?> İçin Yeni Sipariş</h1>

    <form action="save_order.php" method="post">
        <input type="hidden" name="table_id" value="<?php echo $table_id; ?>">
        <label for="menu_id">Ürün:</label>
        <select name="menu_id" id="menu_id">
            <?php foreach ($menu_items as $item): ?>
                <option value="<?php echo $item['menu_id']; ?>"><?php echo $item['product_name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="piece">Adet:</label>
        <input type="number" name="piece" id="piece" required><br><br>
        <label for="statement">Açıklama:</label>
        <input type="text" name="statement" id="statement"><br><br>
        <button type="submit">Sipariş Oluştur</button>
    </form>

</body>

</html>