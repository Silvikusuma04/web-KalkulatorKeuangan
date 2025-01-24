<?php 
session_start(); 
if(isset($_SESSION['username'])) { 
    session_destroy(); 
    echo '<meta http-equiv="refresh" content="2; url=./login.php"/>'; 
} else { 
    echo '<meta http-equiv="refresh" content="2; url=./login.php"/>'; 
    echo '<center><h2>Gagal Logout</h2>Silahkan login terlebih dahulu<br/><br/>Kamu akan dialihkan kembali ke halaman login dalam 2 detik</center>'; 
} 
?>

