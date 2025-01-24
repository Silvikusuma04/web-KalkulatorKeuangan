<?php
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Mengambil koneksi ke database
require_once 'koneksi.php';

$id = $_GET['id'] ?? null;

// Memastikan ID budgeting valid
if (!$id) {
    echo "ID tidak tersedia.";
    exit;
}

// Query untuk mengambil data budgeting berdasarkan ID dan user_id
$sql_select = "SELECT * FROM budgeting WHERE id = ? AND user_id = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("ii", $id, $_SESSION["user_id"]);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 0) {
    echo "Data budgeting tidak ditemukan.";
    exit;
}

// Mendapatkan data budgeting dari hasil query
$row = $result->fetch_assoc();
$goals = $row['goals'];
$pendapatan = $row['pendapatan'];
$pengeluaran_pokok = $row['pengeluaran_pokok'];
$dana_darurat = $row['dana_darurat'];
$nabung_invest = $row['nabung_invest'];
$dana_healing = $row['dana_healing'];
$tanggal = $row['tanggal'];
$catatan = $row['catatan'];

$stmt_select->close();

// Proses update data budgeting jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $goals = $_POST['goals'];
    $pendapatan = $_POST['pendapatan'];
    $pengeluaran_pokok = $_POST['pengeluaran_pokok'];
    $dana_darurat = $_POST['dana_darurat'];
    $nabung_invest = $_POST['nabung_invest'];
    $dana_healing = $_POST['dana_healing'];
    $tanggal = $_POST['tanggal'];
    $catatan = $_POST['catatan'];

    // Query untuk update data budgeting
    $sql_update = "UPDATE budgeting SET goals = ?, pendapatan = ?, pengeluaran_pokok = ?, dana_darurat = ?, nabung_invest = ?, dana_healing = ?, tanggal = ?, catatan = ? WHERE id = ? AND user_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sdddddssii", $goals, $pendapatan, $pengeluaran_pokok, $dana_darurat, $nabung_invest, $dana_healing, $tanggal, $catatan, $id, $_SESSION["user_id"]);

    if ($stmt_update->execute()) {
        $stmt_update->close();
        $conn->close();
        header("Location: lihat-budgeting.php");
        exit;
    } else {
        $error_message = "Terjadi kesalahan saat menyimpan perubahan: " . $stmt_update->error;
    }

    $stmt_update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Budgeting - Pengelola Keuangan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
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
                    <a class="nav-link" href="#">Lihat Budgeting</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pengeluaran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Lihat Pengeluaran</a>
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
        <h2>Edit Budgeting</h2>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>">
            <div class="form-group">
                <label for="goals">Goals Finansial:</label>
                <input type="text" class="form-control" id="goals" name="goals" value="<?php echo $goals; ?>" required>
            </div>
            <div class="form-group">
                <label for="pendapatan">Pendapatan:</label>
                <input type="number" step="0.01" class="form-control" id="pendapatan" name="pendapatan" value="<?php echo $pendapatan; ?>" required>
            </div>
            <div class="form-group">
                <label for="pengeluaran_pokok">Pengeluaran Pokok:</label>
                <input type="number" step="0.01" class="form-control" id="pengeluaran_pokok" name="pengeluaran_pokok" value="<?php echo $pengeluaran_pokok; ?>" required>
            </div>
            <div class="form-group">
                <label for="dana_darurat">Dana Darurat:</label>
                <input type="number" step="0.01" class="form-control" id="dana_darurat" name="dana_darurat" value="<?php echo $dana_darurat; ?>" required>
            </div>
            <div class="form-group">
                <label for="nabung_invest">Tabungan/Nabung-Invest:</label>
                <input type="number" step="0.01" class="form-control" id="nabung_invest" name="nabung_invest" value="<?php echo $nabung_invest; ?>" required>
            </div>
            <div class="form-group">
                <label for="dana_healing">Dana Healing/Senang-senang:</label>
                <input type="number" step="0.01" class="form-control" id="dana_healing" name="dana_healing" value="<?php echo $dana_healing; ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $tanggal; ?>" required>
            </div>
            <div class="form-group">
                <label for="catatan">Catatan:</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="3"><?php echo $catatan; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
