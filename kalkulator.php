<?php
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["hitung"])) {
    $pendapatan = $_POST["pendapatan"];
    $biaya_pokok = $_POST["biaya_pokok"];

    // Hitung dana darurat (30% dari biaya pendapatan yang telah dikurangi biaya pokok)
    $dana_darurat = 0.3 * ($pendapatan - $biaya_pokok);

    // Hitung tabungan (60% dari biaya pendapatan yang telah dikurangi biaya pokok)
    $tabungan = 0.6 * ($pendapatan - $biaya_pokok);

    // Hitung biaya healing/senang-senang (sisanya)
    $biaya_healing = $pendapatan - $biaya_pokok - $dana_darurat - $tabungan;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kalkulator Keuangan - Pengelola Keuangan</title>
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
        <h2>Kalkulator Keuangan</h2>
        <p>Silakan masukkan pendapatan bulanan Anda dan biaya pokok bulanan untuk menghitung alokasi dana keuangan Anda.</p>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="pendapatan">Pendapatan Bulanan (IDR)</label>
                <input type="number" class="form-control" id="pendapatan" name="pendapatan" required min="0">
            </div>
            <div class="form-group">
                <label for="biaya_pokok">Biaya Pokok Bulanan (IDR)</label>
                <input type="number" class="form-control" id="biaya_pokok" name="biaya_pokok" required min="0">
            </div>
            <button type="submit" class="btn btn-primary" name="hitung">Hitung</button>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["hitung"])): ?>
        <h4 class="mt-4">Hasil Pembagian Budgeting</h4>
        <ul class="list-group">
            <li class="list-group-item">Pendapatan: IDR <?php echo number_format($pendapatan, 2); ?></li>
            <li class="list-group-item">Biaya Pokok: IDR <?php echo number_format($biaya_pokok, 2); ?></li>
            <li class="list-group-item">Dana Darurat: IDR <?php echo number_format($dana_darurat, 2); ?></li>
            <li class="list-group-item">Tabungan: IDR <?php echo number_format($tabungan, 2); ?></li>
            <li class="list-group-item">Biaya Healing/Senang-senang: IDR <?php echo number_format($biaya_healing, 2); ?></li>
        </ul>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
