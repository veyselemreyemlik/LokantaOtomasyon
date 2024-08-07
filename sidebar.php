<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            display: flex;


        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #27374D;
            padding-top: 40px;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #DDE6ED;
            display: block;
        }

        .sidebar a:hover {
            background-color: #526D82;
            color: #A5D7E8;
            border: #27374D;
        }

        .sidebar .active {
            background-color: #007bff;
            color: #A5D7E8;
        }

        .sidebar i {
            margin-right: 10px;
        }

        .content {
            margin-left: 250px;
            /* Sidebar genişliği kadar offset */
            padding: 20px;
            width: 100%;
        }

        logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 50%;
            margin-bottom: 20px;
            margin-left: 50px;
            margin-right: auto;
            margin-top: 50px;
        }

        .sidebar .logo {
            width: 100%;
            /* Sidebar'ın genişliğine göre ayarlanabilir */
            height: auto;
            /* Otomatik olarak orantıyı korur */
            margin-bottom: 20px;
            /* Logonun altında boşluk bırakır */
            /* margin-left: 50px; */
            margin-right: auto;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <div class="sidebar">
        <div class="logo ">
            <img style="width: %100;" src="../image/logo.png" alt="Logo" class="logo">
        </div>
        <a href="../admin/admin.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="../admin/transfer.php"><i class="fas fa-exchange-alt"></i> Yönlendirme</a>
        <a href="../menu/menu.php"><i class="fas fa-utensils"></i> Menü </a>
        <a href="../table/table.php"><i class="fas fa-table"></i> Masalar</a>
        <a href="../user/users.php"><i class="fas fa-users"></i> Kullanıcılar</a>
        <a href="../order/order.php"><i class="fas fa-receipt"></i> Siparişler</a>
        <a href="../statistics/statistics.php"><i class="fas fa-cog"></i> İstatislik</a>

    </div>
    <div class="content">