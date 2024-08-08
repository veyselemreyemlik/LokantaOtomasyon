<?php
include "../connection.php";
include "../sidebar.php";
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
                    
                
          
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <style>
        body {
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: indigo;
        }

        .content {
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
            border-color: #19376D;
            color: whitesmoke;
            font-weight: bold;

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
            color: #002254;
            text-align: center;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif
        }

        .table {
            margin-bottom: 0;
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


        .table {
            text-align: center;
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
        <h1>Masa Yönetimi</h1>

        <!-- Ekleme Butonu -->
        <div class="mb-3 text-end">
            <button type="button" class="btn btn-red" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Yeni Masa Ekle
            </button>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Masa Adı</th>
                    <th scope="col">Aksiyon</th>
                </tr>
            </thead>
            <tbody class="table-secondary">
                <?php
                $sql = "SELECT * FROM tables";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <th scope='row'><?php echo $row["table_id"]; ?></th>
                            <td><?php echo $row["table_name"]; ?></td>
                            <td>
                                <button type='button' class='btn btn-edit btn-sm' data-bs-toggle='modal'
                                    data-bs-target='#exampleModal<?php echo $row["table_id"]; ?>'>
                                    Düzenle
                                </button>
                                <a href='table_delete.php?table_id=<?php echo $row["table_id"]; ?>'
                                    class='btn btn-delete btn-sm'
                                    onclick='return confirm("Bu masayı silmek istediğinizden emin misiniz?")'>
                                    Sil
                                </a>
                            </td>
                        </tr>

                        <!-- Modal for editing each table item -->
                        <div class='modal fade' id='exampleModal<?php echo $row["table_id"]; ?>' tabindex='-1'
                            aria-labelledby='exampleModalLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='exampleModalLabel'>Masa Düzenle</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal'
                                            aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <!-- Form fields for editing -->
                                        <form action='table_update.php' method='POST'>
                                            <input type='hidden' name='table_id' value='<?php echo $row["table_id"]; ?>'>
                                            <div class='mb-3'>
                                                <label for='table_name' class='form-label'>Masa Adı</label>
                                                <input type='text' class='form-control' id='table_name' name='table_name'
                                                    value='<?php echo $row["table_name"]; ?>' required>
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
                    echo "<tr><td colspan='3'>Masa bulunmamaktadır.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for adding a new table item -->
    <div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='exampleModalLabel'>Yeni Masa Ekle</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <!-- Form fields for adding a new table item -->
                    <form action='table_add.php' method='POST'>
                        <div class='mb-3'>
                            <label for='table_name' class='form-label'>Masa Adı</label>
                            <input type='text' class='form-control' id='table_name' name='table_name' required>
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