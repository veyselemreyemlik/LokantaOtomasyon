<?php
include 'connection.php';
include 'header.php';
session_start();

// Fetch table statuses
$tables = [];
$sql = "SELECT t.table_id, IFNULL(o.status_number, 0) AS status
        FROM tables t
        LEFT JOIN orders o ON t.table_id = o.table_id AND o.status_number = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Garson - Sipariş Ver</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-fluid {
            width: 80%;
            height: 100%;
            padding-left: 200px;
            padding-right: 100px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .table-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            padding-left: 0px;
            padding-top: 50px;
            grid-gap: 10px;
        }

        .table-box {
            padding: 35px;
            text-align: center;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            color: white;
            font-size: 20px;
            cursor: pointer;
            border-radius: 5px;
            width: 220px;
            height: 100px;
            transition: background-color 0.3s ease;
        }

        .empty {
            background-color: blue;
        }

        .occupied {
            background-color: green;
        }

        .table-box:hover {
            opacity: 0.8;
        }

        .table-box:hover:after {
            content: attr(data-text);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 16px;
        }

        h2 {
            text-align: center;
            padding: 40px;
        }
    </style>
</head>

<body>
    <h2>Sipariş Oluşturma Sayfası</h2>
    <div class="container-fluid">
        <div class="table-container">
            <?php foreach ($tables as $table): ?>
                <div class="table-box <?php echo $table['status'] == 1 ? 'occupied' : 'empty'; ?>"
                    data-text="<?php echo $table['status'] == 1 ? '' : 'Masa Boş'; ?>"
                    onclick="handleClick(<?php echo $table['table_id']; ?>, <?php echo $table['status']; ?>)">
                    Masa <?php echo $table['table_id']; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function handleClick(tableId, status) {
            if (status == 1) {
                // Dolu masa: sipariş içeriğini göster
                window.location.href = 'order_details.php?table_id=' + tableId;
            } else {
                // Boş masa: yeni sipariş al
                window.location.href = 'new_order.php?table_id=' + tableId;
            }
        }
    </script>
</body>

</html>