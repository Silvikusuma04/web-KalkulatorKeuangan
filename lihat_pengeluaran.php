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
$tanggal_awal = $tanggal_akhir = '';
$total_pengeluaran = 0;
$error_message = '';

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];

    // Query untuk menghitung jumlah pengeluaran berdasarkan rentang tanggal
    $sql_summary = "SELECT SUM(nominal) AS total_pengeluaran FROM pengeluaran WHERE user_id = ? AND tanggal_input BETWEEN ? AND ?";
    
    $stmt_summary = $conn->prepare($sql_summary);
    $stmt_summary->bind_param("iss", $_SESSION["user_id"], $tanggal_awal, $tanggal_akhir);
    $stmt_summary->execute();
    $result_summary = $stmt_summary->get_result()->fetch_assoc();
    $stmt_summary->close();

    // Assign hasil perhitungan ke variabel untuk ditampilkan di halaman
    if ($result_summary) {
        $total_pengeluaran = $result_summary["total_pengeluaran"];
    } else {
        $error_message = "Tidak ada data pengeluaran untuk rentang tanggal yang dipilih.";
    }
}

// Ambil data pengeluaran yang sudah ada
$sql_select = "SELECT tanggal_input, deskripsi, nominal, jenis_pengeluaran FROM pengeluaran WHERE user_id = ? ORDER BY tanggal_input DESC";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $_SESSION["user_id"]);
$stmt_select->execute();
$result_pengeluaran = $stmt_select->get_result();

$stmt_select->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lihat Pengeluaran - Pengelola Keuangan</title>
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
        <h2>Lihat Pengeluaran</h2>

        <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-row align-items-center mb-3">
                <div class="col-auto">
                    <label for="tanggal_awal">Pilih Tanggal Awal:</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" required value="<?php echo $tanggal_awal; ?>">
                </div>
                <div class="col-auto">
                    <label for="tanggal_akhir">Pilih Tanggal Akhir:</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" required value="<?php echo $tanggal_akhir; ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mt-4">Hitung Jumlah</button>
                </div>
            </div>
        </form>

        <?php if ($total_pengeluaran != 0): ?>
        <div class="alert alert-info" role="alert">
            <strong>Periode:</strong> <?php echo $tanggal_awal; ?> s/d <?php echo $tanggal_akhir; ?><br>
            <strong>Total Pengeluaran:</strong> IDR <?php echo number_format($total_pengeluaran, 2); ?><br>
        </div>
        <?php endif; ?>

        <?php if ($result_pengeluaran->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal Input</th>
                    <th>Deskripsi</th>
                    <th>Nominal</th>
                    <th>Jenis Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_pengeluaran->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($row["tanggal_input"])); ?></td>
                    <td><?php echo $row["deskripsi"]; ?></td>
                    <td>IDR <?php echo number_format($row["nominal"], 2); ?></td>
                    <td><?php echo ucfirst($row["jenis_pengeluaran"]); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Belum ada data pengeluaran tersimpan.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
