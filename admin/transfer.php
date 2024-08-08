<?php

session_start();
include '../connection.php';
include '../sidebar.php';

// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir

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
                    
                
          
// Kullanıcı kimliğini oturumdan al
$user_id = $_SESSION['user_id'];

// Kullanıcının place_id bilgisini al
$sql = "SELECT u.place_id, p.place_name FROM users u JOIN place p ON u.place_id = p.place_id WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$place_id = $user['place_id'];
$place_name = $user['place_name'];

?>

<body style="background-color: #DDDDDD;">
    <div class="container mt-5">
        <h1>Hoşgeldiniz, Kullanıcı!</h1>
        <p><?php echo $place_name; ?> Yetkisine Sahipsiniz.</p>
        
        <div class="btn-group btn-group-lg" role="group">
            <a href="admin.php" class="btn btn-danger">Admin Sayfası</a>
            <a href="../garson/garson_order.php" class="btn btn-warning">Garson Sayfası</a>
            <a href="../izgara/izgara.php" class="btn btn-primary">Izgara Sayfası</a>
            <a href="../firin/firin.php" class="btn btn-light">Fırın Sayfası</a>
            <a href="../mutfak/mutfak.php" class="btn btn-success">Mutfak Sayfası</a>
        </div>
    </div>
</body>

</html>
