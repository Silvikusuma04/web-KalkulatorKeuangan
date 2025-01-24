<?php
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Mengambil koneksi ke database
require_once 'koneksi.php';

// Ambil data budgeting yang sudah ada
$sql_select = "SELECT * FROM budgeting WHERE user_id = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $_SESSION["user_id"]);
$stmt_select->execute();
$result_budgeting = $stmt_select->get_result();

$stmt_select->close();

// Inisialisasi variabel untuk menampilkan hasil perhitungan
$total_tabungan = 0;
$total_pengeluaran = 0;
$total_dana_darurat = 0;
$total_pendapatan = 0;

// Proses perhitungan jika form tanggal periode disubmit
if (isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];

    // Query untuk menghitung jumlah tabungan, pengeluaran, dana darurat, dan pendapatan berdasarkan rentang tanggal
    $sql_summary = "SELECT 
                        SUM(nabung_invest) AS total_tabungan, 
                        SUM(pengeluaran_pokok + dana_healing) AS total_pengeluaran, 
                        SUM(dana_darurat) AS total_dana_darurat, 
                        SUM(pendapatan) AS total_pendapatan
                    FROM budgeting 
                    WHERE user_id = ? AND tanggal BETWEEN ? AND ?";
    
    $stmt_summary = $conn->prepare($sql_summary);
    $stmt_summary->bind_param("iss", $_SESSION["user_id"], $tanggal_awal, $tanggal_akhir);
    $stmt_summary->execute();
    $result_summary = $stmt_summary->get_result()->fetch_assoc();
    $stmt_summary->close();

    // Assign hasil perhitungan ke variabel untuk ditampilkan di halaman
    if ($result_summary) {
        $total_tabungan = $result_summary["total_tabungan"];
        $total_pengeluaran = $result_summary["total_pengeluaran"];
        $total_dana_darurat = $result_summary["total_dana_darurat"];
        $total_pendapatan = $result_summary["total_pendapatan"];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lihat Budgeting - Pengelola Keuangan</title>
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
        <h2>Daftar Budgeting</h2>

        <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-row align-items-center mb-3">
                <div class="col-auto">
                    <label for="tanggal_awal">Pilih Tanggal Awal:</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" required>
                </div>
                <div class="col-auto">
                    <label for="tanggal_akhir">Pilih Tanggal Akhir:</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mt-4">Hitung Jumlah</button>
                </div>
            </div>
        </form>

        <?php if ($total_tabungan != 0 || $total_pengeluaran != 0 || $total_dana_darurat != 0 || $total_pendapatan != 0): ?>
        <div class="alert alert-info" role="alert">
            <strong>Periode:</strong> <?php echo $tanggal_awal; ?> s/d <?php echo $tanggal_akhir; ?><br>
            <strong>Total Tabungan:</strong> IDR <?php echo number_format($total_tabungan, 2); ?><br>
            <strong>Total Pengeluaran:</strong> IDR <?php echo number_format($total_pengeluaran, 2); ?><br>
            <strong>Total Dana Darurat:</strong> IDR <?php echo number_format($total_dana_darurat, 2); ?><br>
            <strong>Total Pendapatan:</strong> IDR <?php echo number_format($total_pendapatan, 2); ?><br>
        </div>
        <?php endif; ?>

        <?php if ($result_budgeting->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Goals Finansial</th>
                    <th>Pendapatan</th>
                    <th>Pengeluaran Pokok</th>
                    <th>Dana Darurat</th>
                    <th>Tabungan/Nabung-Invest</th>
                    <th>Dana Healing/Senang-senang</th>
                    <th>Tanggal</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_budgeting->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["goals"]; ?></td>
                    <td>IDR <?php echo number_format($row["pendapatan"], 0); ?></td>
                    <td>IDR <?php echo number_format($row["pengeluaran_pokok"], 2); ?></td>
                    <td>IDR <?php echo number_format($row["dana_darurat"], 2); ?></td>
                    <td>IDR <?php echo number_format($row["nabung_invest"], 2); ?></td>
                    <td>IDR <?php echo number_format($row["dana_healing"], 2); ?></td>
                    <td><?php echo $row["tanggal"]; ?></td>
                    <td><?php echo $row["catatan"]; ?></td>
                    <td>
                        <a href="edit_budgeting.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="hapus_budgeting.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Belum ada data budgeting tersimpan.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
