<?php
    include ('../../config/koneksi.php');

    if (isset($_POST['submit'])) {
        $jenis_surat = "Surat Keterangan Tidak Mampu";
        $nik = $_POST['fnik'];
        $keperluan = addslashes($_POST['fkeperluan']);
        $alamat = addslashes($_POST['falamat']);
        $pekerjaan = addslashes($_POST['fpekerjaan']);
        $status_surat = "PENDING";
        $id_profil_desa = "1";

        $qTambahSurat = "INSERT INTO surat_keterangan_tidak_mampu 
        (jenis_surat, nik, keperluan, alamat, pekerjaan, status_surat, id_profil_desa) 
        VALUES 
        ('$jenis_surat', '$nik', '$keperluan', '$alamat', '$pekerjaan', '$status_surat', '$id_profil_desa')";

        $TambahSurat = mysqli_query($connect, $qTambahSurat);
        header("location:../index.php?pesan=berhasil");
    }
?>
