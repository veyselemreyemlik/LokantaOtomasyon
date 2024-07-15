<?php
include 'header.php';
include 'connection.php';
session_start();

// Oturum açmış kullanıcı kimliğini alın
$user_id = $_SESSION['user_id'];

// Hazırlandı durumundaki siparişleri seçmek için SQL sorgusu
$sql_prepared_orders = "SELECT o.id AS order_id, t.table_name, o.created_at AS order_time,
                               GROUP_CONCAT(mi.name SEPARATOR ', ') AS menu_items
                        FROM orders o
                        LEFT JOIN order_details od ON o.id = od.order_id
                        LEFT JOIN menu_items mi ON od.menu_item_id = mi.id
                        LEFT JOIN tables t ON o.table_id = t.id
                        WHERE o.status = 'Sipariş Hazırlandı'
                        GROUP BY o.id";

$result_prepared_orders = $conn->query($sql_prepared_orders);

// Teslim edilen siparişleri seçmek için SQL sorgusu
$sql_delivered_orders = "SELECT o.id AS order_id, t.table_name, o.created_at AS order_time,
                               GROUP_CONCAT(mi.name SEPARATOR ', ') AS menu_items
                        FROM orders o
                        LEFT JOIN order_details od ON o.id = od.order_id
                        LEFT JOIN menu_items mi ON od.menu_item_id = mi.id
                        LEFT JOIN tables t ON o.table_id = t.id
                        WHERE o.status = 'Sipariş Teslim Edildi'
                        GROUP BY o.id";

$result_delivered_orders = $conn->query($sql_delivered_orders);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garson Sipariş Paneli</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <style>
        .quantity-input {
            text-align: center;
        }

        body {
            background-color: gainsboro;
        }

        .form-label {
            color: darkblue;

        }

        .container.mt-4 {
            width: 170%;


        }

        h1 {
            color: darkblue;
            text-align: center;
            font-family: 'Times New Roman', Times, serif
        }

        form-select-lg.mb-4 {
            width: max-content;
        }
    </style>
</head>

<body>
    <hr>
    <div class="container-xl mb-4 bg-3">

        <div class="col-md-6">
            <h3>Masa Seçimi Yapın</h3>
            <select name="table_id" class="form-select-lg mb-4" aria-label=".from-select-lg example">
                <?php
                // Masaları veritabanından çekerek seçenekleri oluşturma
                $sql = "SELECT * FROM tables";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['table_name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Masa bulunamadı</option>";
                }
                ?>
            </select>
            <div class="invalid-feedback">
                Please select a valid state.
            </div>


        </div>
        <h1 class="mb-5">Sipariş Oluştur</h1>
        <form action="garson.php" method="post"
            class="d-flex flex-column flex-md-row align-items-start align-items-md-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Yemek Adı</th>
                                    <th>Fiyat</th>
                                    <th>Adet</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM menu_items";
                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['product_name'] . "</td>";
                                        echo "<td>" . $row['price'] . "</td>";
                                        echo "<td>";
                                        echo "<div class='input-group'>";
                                        echo "<button type='button' class='btn btn-sm btn-outline-secondary minus-btn'>-</button>";
                                        echo "<input type='number' name='piece[" . $row['menu_id'] . "]' min='0' value='0' class='form-control quantity-input' style='width: 60px;' />";
                                        echo "<button type='button' class='btn btn-sm btn-outline-secondary plus-btn'>+</button>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "<td><input type='text' name='note[" . $row['menu_id'] . "]' class='form-control' placeholder='Açıklama girin'></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>Menü öğesi bulunamadı</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" name="create_order" class="btn btn-primary">Sipariş Oluştur</button>
                    </div>

                </div>
            </div>
        </form>

        <hr>
    </div>

    <?php
    if (isset($_POST['create_order'])) {
        // Oturum açmış kullanıcı kimliğini kullan
        $table_id = $_POST['table_id']; // Seçilen masa ID'si
        $status = "Sipariş verildi"; // Başlangıç durumu
    
        // Masa seçimi yapılmışsa
        if ($table_id) {
            $sql = "INSERT INTO orders (user_id, table_id, status) VALUES ($user_id, $table_id, '$status')";
            if ($conn->query($sql) === TRUE) {
                $order_id = $conn->insert_id;

                foreach ($_POST['quantity'] as $menu_item_id => $quantity) {
                    if ($quantity > 0) {
                        $note = $_POST['note'][$menu_item_id];
                        $sql = "INSERT INTO order_details (order_id, menu_item_id, quantity, note) VALUES ($order_id, $menu_item_id, $quantity, '$note')";
                        $conn->query($sql);
                    }
                }

                echo "<div class='alert alert-success'>Sipariş başarıyla oluşturuldu.</div>";
            } else {
                echo "<div class='alert alert-danger'>Sipariş oluşturulamadı: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Lütfen bir masa seçin.</div>";
        }
    }

    if (isset($_POST['deliver_order'])) {
        $order_id = $_POST['order_id'];
        $sql = "UPDATE orders SET status = 'Sipariş Teslim Edildi' WHERE id = $order_id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Sipariş teslim edildi.</div>";
        } else {
            echo "<div class='alert alert-danger'>Sipariş teslim edilemedi: " . $conn->error . "</div>";
        }
    }

    if (isset($_POST['undeliver_order'])) {
        $order_id = $_POST['order_id'];
        $sql = "UPDATE orders SET status = 'Sipariş Hazırlandı' WHERE id = $order_id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Sipariş durumu 'Sipariş Hazırlandı' olarak güncellendi.</div>";
        } else {
            echo "<div class='alert alert-danger'>Sipariş durumu güncellenemedi: " . $conn->error . "</div>";
        }
    }

    $conn->close();
    ?>
</body>

</html>