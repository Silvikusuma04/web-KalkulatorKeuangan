<?php
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Mengambil koneksi ke database
require_once 'koneksi.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_budget"])) {
    $goals_finansial = $_POST["goals_finansial"];
    $pendapatan = $_POST["pendapatan"];
    $pengeluaran_pokok = $_POST["pengeluaran_pokok"];
    $dana_darurat = $_POST["dana_darurat"];
    $nabung_invest = $_POST["nabung_invest"];
    $dana_healing = $_POST["dana_healing"];
    $tanggal = $_POST["tanggal"];
    $catatan = $_POST["catatan"];

    // Simpan data ke database
    $sql = "INSERT INTO budgeting (user_id, goals, pendapatan, pengeluaran_pokok, dana_darurat, nabung_invest, dana_healing, tanggal, catatan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isddddsss", $_SESSION["user_id"], $goals_finansial, $pendapatan, $pengeluaran_pokok, $dana_darurat, $nabung_invest, $dana_healing, $tanggal, $catatan);

    if ($stmt->execute()) {
        $success_message = "Data budgeting berhasil disimpan.";
    } else {
        $error_message = "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil data budgeting yang sudah ada
$sql_select = "SELECT * FROM budgeting WHERE user_id = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $_SESSION["user_id"]);
$stmt_select->execute();
$result_budgeting = $stmt_select->get_result();

$stmt_select->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budgeting - Pengelola Keuangan</title>
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
        <h2>Budgeting</h2>
        <p>Silakan masukkan informasi di bawah ini untuk merencanakan budgeting Anda.</p>

        <?php if (isset($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="goals_finansial">Goals Finansial</label>
                <input type="text" class="form-control" id="goals_finansial" name="goals_finansial" required>
            </div>
            <div class="form-group">
                <label for="pendapatan">Pendapatan Bulanan (IDR)</label>
                <input type="number" class="form-control" id="pendapatan" name="pendapatan" required min="0">
            </div>
            <div class="form-group">
                <label for="pengeluaran_pokok">Pengeluaran Pokok (IDR)</label>
                <input type="number" class="form-control" id="pengeluaran_pokok" name="pengeluaran_pokok" required min="0">
            </div>
            <div class="form-group">
                <label for="dana_darurat">Dana Darurat (IDR)</label>
                <input type="number" class="form-control" id="dana_darurat" name="dana_darurat" required min="0">
            </div>
            <div class="form-group">
                <label for="nabung_invest">Tabungan/Nabung-Invest (IDR)</label>
                <input type="number" class="form-control" id="nabung_invest" name="nabung_invest" required min="0">
            </div>
            <div class="form-group">
                <label for="dana_healing">Dana Healing/Senang-senang (IDR)</label>
                <input type="number" class="form-control" id="dana_healing" name="dana_healing" required min="0">
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="submit_budget">Simpan</button>
        </form>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
