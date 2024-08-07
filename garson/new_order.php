<?php
include '../connection.php';


// Oturum kontrolü


$table_id = $_GET['table_id'];

// Masa adını almak için sorgu
$table_name = '';
$sql = "SELECT table_name FROM tables WHERE table_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $table_id);
$stmt->execute();
$stmt->bind_result($table_name);
$stmt->fetch();
$stmt->close();

// Menü öğelerini al
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Sipariş</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #dddddd;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .order-form {
        background-color: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 400px;
    }

    .order-form h1 {
        margin-bottom: 20px;
        font-size: 24px;
    }

    .order-form label {
        display: block;
        margin-bottom: 5px;
        color: #333;
    }

    .order-form select,
    .order-form input[type="number"],
    .order-form input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        color: #666;
    }

    .order-form button {
        display: inline-block;
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        background-color: #91C8E4;
        color: #176B87;
        border: 1.5px solid whitesmoke;
    }

    .order-form button:hover {
        background-color: #134B70;
        color: whitesmoke;
        border: 1px solid #4682A9;
    }

    .order-items {
        margin-top: 20px;
    }

    .order-items div {
        background-color: #f9f9f9;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    h1 {
        text-align: center;
    }

    label {
        font-weight: initial;
    }

    .header {
        color: #4f709c;
        font-family: 'Times New Roman', Times, serif;
        font-weight: bold;
    }

    label {
        color: #91C8E4;
        font-family: 'Times New Roman', Times, serif;
        font-weight: bold;
    }

    .btn-olustur {}
    </style>
</head>

<body>

    <div class="order-form">
        <h1 class="header">Masa <?php echo htmlspecialchars($table_name); ?> Sipariş Ekranı</h1>

        <form id="orderForm">
            <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table_id); ?>">
            <label for="menu_id">Ürün:</label>
            <select name="menu_id" id="menu_id">
                <?php foreach ($menu_items as $item): ?>
                <option value="<?php echo htmlspecialchars($item['menu_id']); ?>">
                    <?php echo htmlspecialchars($item['menu_name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <label for="piece">Adet:</label>
            <input type="number" name="piece" id="piece" required>
            <label for="statement">Açıklama:</label>
            <input type="text" name="statement" id="statement">
            <button type="button" onclick="addItem()">Ekle</button>
        </form>

        <div class="order-items" id="orderItems"></div>

        <button class="btn-olustur" type="button" onclick="submitOrder()">Siparişi Oluştur</button>
    </div>

    <script>
    let orderItems = [];

    function addItem() {
        const menuIdElement = document.getElementById('menu_id');
        const pieceElement = document.getElementById('piece');
        const statementElement = document.getElementById('statement');

        const menuId = menuIdElement.value;
        const piece = pieceElement.value;
        const statement = statementElement.value;
        const productName = menuIdElement.options[menuIdElement.selectedIndex].text;

        if (piece && menuId) {
            orderItems.push({
                menu_id: menuId,
                piece: piece,
                statement: statement,
                menu_name: productName
            });

            const orderItemsContainer = document.getElementById('orderItems');
            const itemDiv = document.createElement('div');
            itemDiv.textContent = `Ürün: ${productName}, Adet: ${piece}, Açıklama: ${statement}`;
            orderItemsContainer.appendChild(itemDiv);

            pieceElement.value = '';
            statementElement.value = '';
        }
    }

    function redirectToAddOrder(tableId, orderId) {
        window.location.href = 'new_order.php?table_id=' + tableId + '&order_id=' + orderId;
    }

    function submitOrder() {
        if (orderItems.length === 0) {
            alert('Sipariş eklemeden oluşturamazsınız.');
            return;
        }

        const formData = new FormData();
        formData.append('table_id', document.querySelector('input[name="table_id"]').value);
        formData.append('order_items', JSON.stringify(orderItems));

        fetch('save_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sipariş başarıyla oluşturuldu.');
                    window.location.href = 'garson_order.php'; // Yerel bir yönlendirme yapabilirsiniz
                } else {
                    alert('Sipariş oluşturulurken bir hata oluştu: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
            });
    }
    </script>

</body>

</html>