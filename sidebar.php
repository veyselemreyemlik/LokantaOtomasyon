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
        background-color: #343a40;
        padding-top: 20px;
    }

    .sidebar a {
        padding: 15px 20px;
        text-decoration: none;
        font-size: 18px;
        color: #ddd;
        display: block;
    }

    .sidebar a:hover {
        background-color: #575d63;
        color: #fff;
    }

    .sidebar .active {
        background-color: #007bff;
        color: #fff;
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
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <div class="sidebar">
        <a href="../admin/admin.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="../menu/menu.php"><i class="fas fa-utensils"></i> Menü </a>
        <a href="../table/table.php"><i class="fas fa-table"></i> Masalar</a>
        <a href="../user/users.php"><i class="fas fa-users"></i> Kullanıcılar</a>
        <a href="../order/order.php"><i class="fas fa-receipt"></i> Siparişler</a>
        <a href="../statistics/statistics.php"><i class="fas fa-cog"></i> İstatislik(yok)</a>
        
    </div>
    <div class="content">