<?php include "../connection.php";
include "../sidebar.php"; ?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
    }

    .content {
        padding: 50px;
    }

    .table-container {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        padding: 50px;

    }

    h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #343a40;
        text-align: center;
    }

    .table {
        margin-bottom: 0;
        text-align: center;

    }

    .table thead th {
        background-color: #343a40;
        color: #ffffff;
    }

    .table tbody tr:nth-of-type(odd) {
        background-color: #f2f2f2;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
    }

    .table tbody tr td {
        vertical-align: middle;
    }

    .table-container {
        margin: auto;
        padding-top: 20px;
    }
    </style>
</head>

<body>
    <div class="table-container">
        <h1>Menü Yönetimi</h1>

        <!-- Ekleme Butonu -->
        <div class="mb-3 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Yeni Ürün Ekle
            </button>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Ürün Adı</th>
                    <th scope="col">Fiyat</th>
                    <th scope="col">Yer</th>
                    <th scope="col">Aksiyon</th>
                </tr>
            </thead>
            <tbody class="table-secondary">
                <?php
                $sql = "SELECT mi.menu_id, mi.product_name, mi.price, p.place_name, mi.place_id 
                    FROM menu_items mi 
                    LEFT JOIN place p ON mi.place_id = p.place_id
                    ORDER BY p.place_name, mi.product_name";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <th scope='row'><?php echo $row["menu_id"]; ?></th>
                    <td><?php echo $row["product_name"]; ?></td>
                    <td><?php echo $row["price"]; ?> TL</td>
                    <td><?php echo $row["place_name"]; ?></td>
                    <td>
                        <button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal'
                            data-bs-target='#exampleModal<?php echo $row["menu_id"]; ?>'>
                            Düzenle
                        </button>
                        <a href='menu_delete.php?menu_id=<?php echo $row["menu_id"]; ?>' class='btn btn-danger btn-sm'
                            onclick='return confirm("Bu ürünü silmek istediğinizden emin misiniz?")'>
                            Sil
                        </a>
                    </td>
                </tr>

                <!-- Modal for editing each menu item -->
                <div class='modal fade' id='exampleModal<?php echo $row["menu_id"]; ?>' tabindex='-1'
                    aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='exampleModalLabel'>Ürünü Düzenle</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <!-- Form fields for editing -->
                                <form action='menu_update.php' method='POST'>
                                    <input type='hidden' name='menu_id' value='<?php echo $row["menu_id"]; ?>'>
                                    <div class='mb-3'>
                                        <label for='product_name' class='form-label'>Ürün Adı</label>
                                        <input type='text' class='form-control' id='product_name' name='product_name'
                                            value='<?php echo $row["product_name"]; ?>' required>
                                    </div>
                                    <div class='mb-3'>
                                        <label for='price' class='form-label'>Fiyat</label>
                                        <input type='text' class='form-control' id='price' name='price'
                                            value='<?php echo $row["price"]; ?>' required>
                                    </div>
                                    <div class='mb-3'>
                                        <label for='place_id' class='form-label'>Yer</label>
                                        <select class='form-select' id='place_id' name='place_id' required>
                                            <option value='1' <?php echo ($row['place_id'] == 1 ? 'selected' : ''); ?>>
                                                Izgara</option>
                                            <option value='2' <?php echo ($row['place_id'] == 2 ? 'selected' : ''); ?>>
                                                Mutfak</option>
                                            <option value='3' <?php echo ($row['place_id'] == 3 ? 'selected' : ''); ?>>
                                                Fırın</option>
                                        </select>
                                    </div>
                                    <button type='submit' class='btn btn-primary'>Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>Menüde ürün bulunmamaktadır.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for adding a new menu item -->
    <div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='exampleModalLabel'>Yeni Ürün Ekle</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <!-- Form fields for adding a new menu item -->
                    <form action='menu_add.php' method='POST'>
                        <div class='mb-3'>
                            <label for='product_name' class='form-label'>Ürün Adı</label>
                            <input type='text' class='form-control' id='product_name' name='product_name' required>
                        </div>
                        <div class='mb-3'>
                            <label for='price' class='form-label'>Fiyat</label>
                            <input type='text' class='form-control' id='price' name='price' required>
                        </div>
                        <div class='mb-3'>
                            <label for='place_id' class='form-label'>Yer</label>
                            <select class='form-select' id='place_id' name='place_id' required>
                                <option value='1'>Izgara</option>
                                <option value='2'>Mutfak</option>
                                <option value='3'>Fırın</option>
                            </select>
                        </div>
                        <button type='submit' class='btn btn-success'>Ekle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>