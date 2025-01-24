<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "keuangan";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $username = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            header("Location: home.php");
            exit;
        } else {
            echo "<script>alert('Login gagal. Periksa kembali username dan password Anda.'); window.location='login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Login gagal. Periksa kembali username dan password Anda.'); window.location='login.php';</script>";
        exit;
    }
}

// Proses registrasi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = $_POST["reg_username"];
    $password = password_hash($_POST["reg_password"], PASSWORD_DEFAULT);
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];

    $username = mysqli_real_escape_string($conn, $username);
    $fullname = mysqli_real_escape_string($conn, $fullname);
    $email = mysqli_real_escape_string($conn, $email);

    $sql_check = "SELECT * FROM users WHERE username='$username'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan. Gunakan username lain.'); window.location='login.php';</script>";
        exit;
    }

    $sql = "INSERT INTO users (username, password, full_name, email) VALUES ('$username', '$password', '$fullname', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registrasi berhasil. Silakan login.'); window.location='login.php';</script>";
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Register - Pengelola Keuangan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <div class="text-center mb-4">
            <img src="finbro.png" alt="Logo Finbro" height="250">
        </div>
        <h2 class="text-center">Login</h2>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#login">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#register">Register</a>
            </li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <!-- Tab Login -->
            <div class="tab-pane fade show active" id="login">
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="login">Login</button>
                </form>
            </div>

            <!-- Tab Register -->
            <div class="tab-pane fade" id="register">
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="reg_username">Username</label>
                        <input type="text" class="form-control" id="reg_username" name="reg_username" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input type="password" class="form-control" id="reg_password" name="reg_password" required>
                    </div>
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="register">Register</button>
                </form>
            </div>
</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <footer class="text-center mt-4">
        <h4>Copyright By Silvi Kusuma Wardhani Gunawan&copy;</h4>
    </footer>
</body>
</html>
