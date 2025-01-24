<?php
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Mengambil koneksi ke database
require_once 'koneksi.php';

// Inisialisasi variabel
$deskripsi = $nominal = $jenis_pengeluaran = $tanggal_input = '';
$error_message = '';

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deskripsi = $_POST['deskripsi'];
    $nominal = $_POST['nominal'];
    $jenis_pengeluaran = $_POST['jenis_pengeluaran'];
    $tanggal_input = $_POST['tanggal_input']; // Tanggal input dari form

    // Validasi dan masukkan data ke database jika data valid
    if (!empty($deskripsi) && !empty($nominal) && !empty($jenis_pengeluaran) && !empty($tanggal_input)) {
        // Query untuk menyimpan pengeluaran
        $sql_insert = "INSERT INTO pengeluaran (user_id, deskripsi, nominal, jenis_pengeluaran, tanggal_input) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isiss", $_SESSION["user_id"], $deskripsi, $nominal, $jenis_pengeluaran, $tanggal_input);

        if ($stmt_insert->execute()) {
            $stmt_insert->close();
            $conn->close();
            header("Location: pengeluaran.php");
            exit;
        } else {
            $error_message = "Terjadi kesalahan saat menyimpan pengeluaran: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    } else {
        $error_message = "Semua kolom harus diisi.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Input Pengeluaran - Pengelola Keuangan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <a class="navbar-brand" href="#">Pengelola Keuangan</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="home.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kalkulator.php">Kalkulator Keuangan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="budgeting.php">Budgeting</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lihat-budgeting.php">Lihat Budgeting</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pengeluaran.php">Pengeluaran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lihat_pengeluaran.php">Lihat Pengeluaran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
        <span class="navbar-text">
            Selamat datang, <?php echo $_SESSION["username"]; ?>! 
        </span>
    </div>
</nav>

    <div class="container mt-4">
        <h2>Input Pengeluaran Harian</h2>

        <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <input type="text" class="form-control" id="deskripsi" name="deskripsi" required>
            </div>
            <div class="form-group">
                <label for="nominal">Nominal:</label>
                <input type="number" step="0.01" class="form-control" id="nominal" name="nominal" required>
            </div>
            <div class="form-group">
                <label for="jenis_pengeluaran">Jenis Pengeluaran:</label>
                <select class="form-control" id="jenis_pengeluaran" name="jenis_pengeluaran" required>
                    <option value="">Pilih Jenis Pengeluaran</option>
                    <option value="direncanakan">Pengeluaran Direncanakan</option>
                    <option value="tidak_direncanakan">Pengeluaran Tidak Direncanakan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_input">Tanggal Input:</label>
                <input type="date" class="form-control" id="tanggal_input" name="tanggal_input" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
