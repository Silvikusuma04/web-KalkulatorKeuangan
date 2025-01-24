<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

// Ambil id budgeting dari parameter URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data budgeting
    $sql_delete = "DELETE FROM budgeting WHERE id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $id, $_SESSION["user_id"]);

    if ($stmt_delete->execute()) {
        header("Location: lihat-budgeting.php");
    } else {
        echo "Terjadi kesalahan saat menghapus data.";
    }

    $stmt_delete->close();
} else {
    echo "ID tidak tersedia.";
    exit;
}

$conn->close();
?>
