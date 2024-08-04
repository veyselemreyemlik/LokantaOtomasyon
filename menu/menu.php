<?php include "../connection.php";
include "../sidebar.php"; 

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <style>
        body {
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #000d20;
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

        .btn-red {
            background-color: white;
            border-color: #576CBC;
            color: #576CBC;
            font-weight: bold;

        }

        .btn-red:hover {
            background-color: #576CBC;
            border-color: 1px solid #19376D;
            color: whitesmoke;
            font-weight: bold;

        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #002254;
            text-align: center;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif
        }

        .table {
            margin-bottom: 0;
            text-align: center;

        }

        .table thead th {
            background-color: #0B2447;
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

        tr {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="table-container">
        <h1>Menü Yönetimi</h1>

        <!-- Ekleme Butonu -->
        <div class="mb-3 text-end">
            <button type="button" class="btn btn-success btn-red" data-bs-toggle="modal" data-bs-target="#exampleModal">
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


                $sql = "SELECT mi.menu_id, mi.menu_name, mi.price, p.place_name, mi.place_id 
                    FROM menu_items mi 
                    LEFT JOIN place p ON mi.place_id = p.place_id
                    ORDER BY p.place_name, mi.menu_name";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <th scope='row'><?php echo $row["menu_id"]; ?></th>
                            <td><?php echo $row["menu_name"]; ?></td>
                            <td><?php echo $row["price"]; ?> TL</td>
                            <td><?php echo $row["place_name"]; ?></td>
                            <td>
                                <button type='button' class='btn btn-edit btn-sm' data-bs-toggle='modal'
                                    data-bs-target='#exampleModal<?php echo $row["menu_id"]; ?>'>
                                    Düzenle
                                </button>
                                <a href='menu_delete.php?menu_id=<?php echo $row["menu_id"]; ?>' class='btn btn-delete btn-sm'
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
                                        <button type='button' class='btn-close btn-red' data-bs-dismiss='modal'
                                            aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <!-- Form fields for editing -->
                                        <form action='menu_update.php' method='POST'>
                                            <input type='hidden' name='menu_id' value='<?php echo $row["menu_id"]; ?>'>
                                            <div class='mb-3'>
                                                <label for='menu_name' class='form-label'>Ürün Adı</label>
                                                <input type='text' class='form-control' id='menu_name' name='menu_name'
                                                    value='<?php echo $row["menu_name"]; ?>' required>
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
                                            <button type='submit' class='btn btn-red'>Kaydet</button>
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
                    <button type='button' class='btn-close btn-red' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <!-- Form fields for adding a new menu item -->
                    <form action='menu_add.php' method='POST'>
                        <div class='mb-3'>
                            <label for='menu_name' class='form-label'>Ürün Adı</label>
                            <input type='text' class='form-control' id='menu_name' name='menu_name' required>
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
                        <button type='submit' class='btn btn-red'>Ekle</button>
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