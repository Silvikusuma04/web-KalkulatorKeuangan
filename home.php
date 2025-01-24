<?php
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Data berita keuangan beserta nama file gambar
$berita_keuangan = array(
    array(
        "judul" => "Pasar Saham Menguat Setelah Laporan Keuangan Perusahaan",
        "sumber" => "BBC News",
        "tanggal" => "2024-06-21",
        "deskripsi" => "Pasar saham dunia mengalami kenaikan setelah sejumlah besar perusahaan melaporkan laba yang lebih baik dari perkiraan.",
        "gambar" => "saham1.jpg"
    ),
    array(
        "judul" => "Bank Sentral Menjaga Suku Bunga Tetap di Level Rendah",
        "sumber" => "Financial Times",
        "tanggal" => "2024-06-20",
        "deskripsi" => "Bank sentral utama di seluruh dunia memutuskan untuk menjaga suku bunga tetap rendah untuk merangsang pertumbuhan ekonomi pasca-pandemi.",
        "gambar" => "bunga.webp"
    ),
    array(
        "judul" => "Inflasi Naik Akibat Kenaikan Harga Bahan Bakar",
        "sumber" => "CNBC",
        "tanggal" => "2024-06-19",
        "deskripsi" => "Inflasi konsumen meningkat pada bulan lalu karena kenaikan harga bahan bakar, memicu kekhawatiran tentang tekanan inflasi yang lebih tinggi.",
        "gambar" => "y.jpg"
    )
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Pengelola Keuangan</title>
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
        <h2>Selamat Datang di Pengelola Keuangan</h2>
        <p>Halaman ini adalah halaman utama aplikasi pengelola keuangan Anda. Anda dapat mengelola dan merencanakan keuangan Anda dengan lebih baik.</p>
        <h4>Berita Keuangan Terbaru</h4>
        
        <?php foreach ($berita_keuangan as $berita): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $berita['judul']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $berita['sumber']; ?> - <?php echo $berita['tanggal']; ?></h6>
                    <p class="card-text"><?php echo $berita['deskripsi']; ?></p>
                </div>
                <img src="<?php echo $berita['gambar']; ?>" class="card-img-bottom" alt="Gambar Uang" width="200">
            </div>
        <?php endforeach; ?>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
