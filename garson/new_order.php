<?php
include '../connection.php';
session_start();
$table_id = $_GET['table_id'];

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
    <title>Yeni Sipariş</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
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
            background-color: #5cb85c;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .order-form button:hover {
            background-color: #4cae4c;
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
    </style>
</head>

<body>

    <div class="order-form">
        <h1>Masa <?php echo $table_id; ?> İçin Yeni Sipariş</h1>

        <form id="orderForm">
            <input type="hidden" name="table_id" value="<?php echo $table_id; ?>">
            <label for="menu_id">Ürün:</label>
            <select name="menu_id" id="menu_id">
                <?php foreach ($menu_items as $item): ?>
                    <option value="<?php echo $item['menu_id']; ?>"><?php echo $item['product_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="piece">Adet:</label>
            <input type="number" name="piece" id="piece" required>
            <label for="statement">Açıklama:</label>
            <input type="text" name="statement" id="statement">
            <button type="button" onclick="addItem()">Ekle</button>
        </form>

        <div class="order-items" id="orderItems"></div>

        <form action="save_order.php" method="post" id="finalOrderForm">
            <input type="hidden" name="table_id" value="<?php echo $table_id; ?>">
            <input type="hidden" name="order_data" id="orderData">
            <button type="submit">Sipariş Oluştur</button>
        </form>
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
                orderItems.push({ menu_id: menuId, piece: piece, statement: statement, product_name: productName });

                const orderItemsContainer = document.getElementById('orderItems');
                const itemDiv = document.createElement('div');
                itemDiv.textContent = `Ürün: ${productName}, Adet: ${piece}, Açıklama: ${statement}`;
                orderItemsContainer.appendChild(itemDiv);

                pieceElement.value = '';
                statementElement.value = '';
            }
        }

        document.getElementById('finalOrderForm').addEventListener('submit', function (e) {
            document.getElementById('orderData').value = JSON.stringify(orderItems);
        });
    </script>

</body>

</html>