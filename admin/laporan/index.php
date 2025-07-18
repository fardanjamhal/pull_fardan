<?php
  include ('../../config/koneksi.php');
  include ('../part/akses.php');
  include ('../part/header.php');

  // Variabel untuk Pagination
  $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Jumlah data per halaman, default 10
  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;   // Halaman saat ini, default 1
  $offset = ($page - 1) * $limit; // Offset untuk query SQL

  // Membangun URL dasar untuk pagination dan filter
  $base_url = 'index.php';
  $query_params = array();
  if (isset($_GET['filter'])) {
      $query_params['filter'] = $_GET['filter'];
      if ($_GET['filter'] == '2' && isset($_GET['tanggal'])) {
          $query_params['tanggal'] = $_GET['tanggal'];
      } else if ($_GET['filter'] == '3' && isset($_GET['bulan']) && isset($_GET['tahun'])) {
          $query_params['bulan'] = $_GET['bulan'];
          $query_params['tahun'] = $_GET['tahun'];
      } else if ($_GET['filter'] == '4' && isset($_GET['tahun'])) {
          $query_params['tahun'] = $_GET['tahun'];
      }
  }
  $current_url = $base_url . '?' . http_build_query($query_params);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type='text/javascript'>
  $(window).load(function(){
    $("#ktp").change(function() {
      console.log($("#ktp option:selected").val());
      if ($("#ktp option:selected").val() == 'Tidak Ada') {
        $('#no_ktp').prop('hidden', 'true');
      } else {
        $('#no_ktp').prop('hidden', false);
      }
    });

    // JavaScript untuk menyembunyikan/menampilkan form filter berdasarkan pilihan
    $('#filter').change(function(){
      if($(this).val() == '2'){ // Per Tanggal
        $('#form-tanggal').prop('hidden', false);
        $('#form-bulan').prop('hidden', true);
        $('#form-tahun').prop('hidden', true);
      } else if($(this).val() == '3'){ // Per Bulan
        $('#form-tanggal').prop('hidden', true);
        $('#form-bulan').prop('hidden', false);
        $('#form-tahun').prop('hidden', false);
      } else if($(this).val() == '4'){ // Per Tahun
        $('#form-tanggal').prop('hidden', true);
        $('#form-bulan').prop('hidden', true);
        $('#form-tahun').prop('hidden', false);
      } else { // Semua Waktu (default)
        $('#form-tanggal').prop('hidden', true);
        $('#form-bulan').prop('hidden', true);
        $('#form-tahun').prop('hidden', true);
      }
    }).change(); // Panggil .change() saat halaman dimuat untuk inisialisasi
  });
</script>

<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      <div class="pull-left image">
        <?php
          if(isset($_SESSION['lvl']) && ($_SESSION['lvl'] == 'Administrator')){
            echo '<img src="../../assets/img/ava-admin-female.png" class="img-circle" alt="User Image">';
          }else if(isset($_SESSION['lvl']) && ($_SESSION['lvl'] == 'Kepala Desa')){
            echo '<img src="../../assets/img/ava-kades.png" class="img-circle" alt="User Image">';
          }
        ?>
      </div>
      <div class="pull-left info">
        <p><?php echo $_SESSION['lvl']; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li>
        <a href="../dashboard/">
          <i class="fas fa-tachometer-alt"></i> <span>&nbsp;&nbsp;Dashboard</span>
        </a>
      </li>
      <li>
        <a href="../profil_desa/">
          <i class="fa fa-home"></i> <span>&nbsp;Profil Desa</span>
        </a>
      </li>
      <li>
   			<a href="../data_kades_kel/">
     			<i class="fa fa-user"></i> <span>&nbsp;Data Kades / Kelurahan</span>
   			</a>
   		</li>
      <li>
        <a href="../penduduk/">
          <i class="fa fa-users"></i> <span>&nbsp;Data Penduduk</span>
        </a>
      </li>
      <?php
        if(isset($_SESSION['lvl']) && ($_SESSION['lvl'] == 'Administrator')){
      ?>
      <li>
        <a href="../surat/permintaan_surat/">
          <i class="fa fa-file-alt"></i> <span>&nbsp;Permintaan Surat</span>
        </a>
      </li>
      <li>
        <a href="../surat/surat_selesai/">
          <i class="fa fa-check-circle"></i> <span>&nbsp;Surat Selesai</span>
        </a>
      </li>
      <?php
        }else{

        }
      ?>
      <li class="active">
        <a href="#"><i class="fas fa-chart-line"></i> <span>&nbsp;&nbsp;&nbsp;Laporan</span></a>
      </li>
    </ul>
  </section>
</aside>
<div class="content-wrapper">
  <section class="content-header">
    <?php
      if(isset($_GET['filter']) && ! empty($_GET['filter'])){
        $filter = $_GET['filter'];
        if($filter == '1'){
          echo '<h1>Laporan Surat Administrasi Desa - Surat Keluar</h1>';
        }else if($filter == '2'){
          $tgl_lhr = date($_GET['tanggal']);
          $tgl = date('d ', strtotime($tgl_lhr));
          $bln = date('F', strtotime($tgl_lhr));
          $thn = date(' Y', strtotime($tgl_lhr));
          $blnIndo = array(
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
          );
          echo '<h1>Laporan Surat Administrasi Desa - Surat Keluar (Tanggal '.$tgl . $blnIndo[$bln] . $thn.')</b>';
        }else if($filter == '3'){
          $nama_bulan = array('', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
          echo '<h1>Laporan Surat Administrasi Desa - Surat Keluar (Bulan '.$nama_bulan[$_GET['bulan']].' '.$_GET['tahun'].')</b>';
        }else if($filter == '4'){
          echo '<h1>Laporan Surat Administrasi Desa - Surat Keluar (Tahun '.$_GET['tahun'].')</b>';
        }
      }else{
        echo '<h1>Laporan Surat Administrasi Desa - Surat Keluar</h1>';
      }
    ?>
    <h1></h1>
    <ol class="breadcrumb">
      <li><a href="../dashboard/"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
      <li class="active">Laporan</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">

    <!-- Tambahkan di <head> -->
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
        .tombol-hp {
          display: none; /* Sembunyikan di desktop */
        }

        @media (max-width: 768px) {
          .tombol-hp {
            display: flex;
            justify-content: flex-end;
            padding: 16px;
          }

          .tombol-hp a {
            text-decoration: none;
          }

          .tombol-hp div {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
          }

          .tombol-hp div:hover {
            background-color: #0056b3;
          }
        }
      </style>

      <!-- Tambahkan di <body> -->
      <div class="tombol-hp">
        <a href="../dashboard/">
          <div>
            Kembali Ke Menu
          </div>
        </a>
      </div>

      <div class="col-md-12">
        <div class="col-md-9">
          <?php
            if(isset($_GET['filter']) && ! empty($_GET['filter'])){
              $filter = $_GET['filter'];
              if($filter == '1'){
                echo '<a name="cetak" target="output" class="btn btn-primary btn-md" href="cetak-laporan.php"><i class="fas fa-print"></i> Cetak Laporan</a>';
              }else if($filter == '2'){
                $tgl = date('d-m-y', strtotime($_GET['tanggal']));
                echo '<a name="cetak" target="output" class="btn btn-primary btn-md" href="cetak-laporan.php?filter=2&tanggal='.$_GET['tanggal'].'"><i class="fas fa-print"></i> Cetak Laporan</a>';
              }else if($filter == '3'){
                $nama_bulan = array('', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
                echo '<a name="cetak" target="output" class="btn btn-primary btn-md" href="cetak-laporan.php?filter=3&bulan='.$_GET['bulan'].'&tahun='.$_GET['tahun'].'"><i class="fas fa-print"></i> Cetak Laporan</a>';
              }else if($filter == '4'){
                echo '<a name="cetak" target="output" class="btn btn-primary btn-md" href="cetak-laporan.php?filter=4&tahun='.$_GET['tahun'].'"><i class="fas fa-print"></i> Cetak Laporan</a>';
              }
            }else{
              echo '<a name="cetak" target="output" class="btn btn-primary btn-md" href="cetak-laporan.php"><i class="fas fa-print"></i> Cetak Laporan</a>';
            }
          ?>
        </div>
        <div class="col-md-3" align="right">
          <a name="filter" target="output" class="btn btn-primary btn-md" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-filter"></i> Filter</a>
          <a href="../laporan/" name="filter" class="btn btn-danger btn-md"><i class="fas fa-eraser"></i> Reset Filter</a>
        </div><br>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="get" action="">
                <div class="modal-body">
                  <div class="form-group">
                    <label>Filter Berdasarkan</label>
                    <select class="form-control" name="filter" id="filter">
                      <option value="1" <?php echo (isset($_GET['filter']) && $_GET['filter'] == '1') ? 'selected' : ''; ?>>Semua Waktu</option>
                      <option value="2" <?php echo (isset($_GET['filter']) && $_GET['filter'] == '2') ? 'selected' : ''; ?>>Per Tanggal</option>
                      <option value="3" <?php echo (isset($_GET['filter']) && $_GET['filter'] == '3') ? 'selected' : ''; ?>>Per Bulan</option>
                      <option value="4" <?php echo (isset($_GET['filter']) && $_GET['filter'] == '4') ? 'selected' : ''; ?>>Per Tahun</option>
                    </select>
                  </div>
                  <div class="form-group" id="form-tanggal">
                    <label>Tanggal</label><br>
                    <input class="form-control" type="date" name="tanggal" value="<?php echo isset($_GET['tanggal']) ? $_GET['tanggal'] : ''; ?>">
                  </div>
                  <div class="form-group" id="form-bulan">
                    <label>Bulan</label><br>
                    <select class="form-control" name="bulan">
                      <option value="">Pilih</option>
                      <?php
                        $nama_bulan_arr = array('', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
                        for ($i=1; $i <= 12; $i++) {
                          $selected = (isset($_GET['bulan']) && $_GET['bulan'] == $i) ? 'selected' : '';
                          echo '<option value="'.$i.'" '.$selected.'>'.$nama_bulan_arr[$i].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                  <div class="form-group" id="form-tahun">
                    <label>Tahun</label><br>
                    <select class="form-control" name="tahun">
                      <option value="">Pilih</option>
                      <?php
                        $query_tahun = "SELECT YEAR(tanggal_surat) AS tahun FROM surat_keterangan GROUP BY YEAR(tanggal_surat) ORDER BY tahun DESC";
                        $sql_tahun = mysqli_query($connect, $query_tahun);
                        while($data_tahun = mysqli_fetch_array($sql_tahun)){
                          $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $data_tahun['tahun']) ? 'selected' : '';
                          echo '<option value="'.$data_tahun['tahun'].'" '.$selected.'>'.$data_tahun['tahun'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
              </form>
            </div>
          </div>
        </div><br><br>

       <?php
          // Daftar nama tabel surat
          $daftar_tabel_surat = [
            'surat_keterangan',
            'surat_keterangan_berkelakuan_baik',
            'surat_keterangan_domisili',
            'surat_keterangan_kepemilikan_kendaraan_bermotor',
            'surat_keterangan_perhiasan',
            'surat_keterangan_usaha',
            'surat_lapor_hajatan',
            'surat_pengantar_skck',
            'surat_keterangan_tidak_mampu',
            'formulir_pengantar_nikah',
            'formulir_permohonan_kehendak_nikah',
            'formulir_persetujuan_calon_pengantin',
            'formulir_persetujuan_calon_pengantin_istri',
            'formulir_surat_izin_orang_tua',
            'surat_keterangan_kematian',
            'surat_keterangan_domisili_usaha',
            'surat_keterangan_pengantar',
            'surat_keterangan_beda_identitas',
            'surat_keterangan_beda_identitas_kis',
            'surat_keterangan_penghasilan_orang_tua'
          ];

          // Loop untuk membentuk bagian UNION
          $unions = [];
          foreach ($daftar_tabel_surat as $tabel) {
            $unions[] = "
              (SELECT penduduk.nama, penduduk.dusun, penduduk.rt, penduduk.rw,
                      $tabel.no_surat, $tabel.tanggal_surat, $tabel.jenis_surat
              FROM penduduk
              LEFT JOIN $tabel ON $tabel.nik = penduduk.nik
              WHERE $tabel.status_surat = 'selesai')
            ";
          }

          // Gabungkan semua query UNION
          $main_query_base = implode(" UNION ALL ", $unions);

          // Tambahkan filter jika ada
          $where_clause = "";
          if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            $filter = $_GET['filter'];
            if ($filter == '2') {
              $where_clause = " WHERE DATE(tanggal_surat) = '{$_GET['tanggal']}'";
            } elseif ($filter == '3') {
              $where_clause = " WHERE MONTH(tanggal_surat) = '{$_GET['bulan']}' AND YEAR(tanggal_surat) = '{$_GET['tahun']}'";
            } elseif ($filter == '4') {
              $where_clause = " WHERE YEAR(tanggal_surat) = '{$_GET['tahun']}'";
            }
          }

          // Hitung total record
          $total_records_query = "SELECT COUNT(*) AS total FROM (" . $main_query_base . ") AS total_surat" . $where_clause;
          $total_records_result = mysqli_query($connect, $total_records_query);
          $total_records_row = mysqli_fetch_assoc($total_records_result);
          $total_records = $total_records_row['total'];
          $total_pages = ceil($total_records / $limit);
          ?>


        <div class="table-responsive">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                  <th>No.</th> <th>No. Surat</th>
                  <th>Tanggal</th>
                  <th>Nama</th>
                  <th>Jenis Surat</th>
                  <th>Alamat</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // Query untuk menampilkan data dengan pagination
                $query = "SELECT * FROM (" . $main_query_base . ") AS filtered_surat" . $where_clause . " ORDER BY tanggal_surat DESC LIMIT $offset, $limit";
                $sql = mysqli_query($connect, $query);
                $row_count = mysqli_num_rows($sql);

                if($row_count > 0){
                  $no = $offset + 1; // Inisialisasi nomor baris
                  while($data = mysqli_fetch_array($sql)){
              ?>
                    <tr>
                      <td><?php echo $no++;?></td> <td><?php echo $data['no_surat'];?></td>
                      <?php
                        $tgl_lhr = date($data['tanggal_surat']);
                        $tgl = date('d ', strtotime($tgl_lhr));
                        $bln = date('F', strtotime($tgl_lhr));
                        $thn = date(' Y', strtotime($tgl_lhr));
                        $blnIndo = array(
                          'January' => 'Januari',
                          'February' => 'Februari',
                          'March' => 'Maret',
                          'April' => 'April',
                          'May' => 'Mei',
                          'June' => 'Juni',
                          'July' => 'Juli',
                          'August' => 'Agustus',
                          'September' => 'September',
                          'October' => 'Oktober',
                          'November' => 'November',
                          'December' => 'Desember'
                        );
                      ?>
                      <td><?php echo $tgl . $blnIndo[$bln] . $thn;?></td>
                      <td><?php echo $data['nama'];?></td>
                      <td><?php echo $data['jenis_surat'];?></td>
                      <td><?php echo "Dusun ".$data['dusun']." RT ".$data['rt']." RW ".$data['rw'];?></td>
                    </tr>
              <?php
                  }
                }else{
                  echo '<tr><td colspan="6" class="text-center">Tidak ada data laporan.</td></tr>'; // Sesuaikan colspan
                }
              ?>
            </tbody>
          </table>
        </div>


        <div class="row">
            <div class="col-md-6">
                <p>Menampilkan <?php echo min($limit, $total_records - $offset); ?> dari <?php echo $total_records; ?> data.</p>
            </div>

            <div class="col-md-6 text-right">
                <form class="form-inline" method="get" action="<?php echo $base_url; ?>">
                    <?php foreach ($query_params as $key => $value): ?>
                        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                    <?php endforeach; ?>
                    <label for="limit">Tampilkan:</label>
                    <select name="limit" id="limit" class="form-control" onchange="this.form.submit()">
                        <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo ($limit == 100) ? 'selected' : ''; ?>>100</option>
                    </select> data per halaman
                </form>

                <ul class="pagination">
                    <?php
                    $max_pages_to_show = 10; // Maksimal 10 nomor halaman yang ditampilkan
                    $start_page = max(1, $page - floor($max_pages_to_show / 2));
                    $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);

                    // Sesuaikan start_page jika end_page terlalu dekat dengan total_pages
                    if ($end_page - $start_page + 1 < $max_pages_to_show) {
                        $start_page = max(1, $end_page - $max_pages_to_show + 1);
                    }

                    if ($page > 1): ?>
                        <li><a href="<?php echo $current_url . '&page=' . ($page - 1) . '&limit=' . $limit; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php if ($start_page > 1): ?>
                        <li><a href="<?php echo $current_url . '&page=1&limit=' . $limit; ?>">1</a></li>
                        <?php if ($start_page > 2): ?>
                            <li class="disabled"><span>...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="<?php echo ($i == $page) ? 'active' : ''; ?>"><a href="<?php echo $current_url . '&page=' . $i . '&limit=' . $limit; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="disabled"><span>...</span></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $current_url . '&page=' . $total_pages . '&limit=' . $limit; ?>"><?php echo $total_pages; ?></a></li>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <li><a href="<?php echo $current_url . '&page=' . ($page + 1) . '&limit=' . $limit; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
                
            </div>
        </div>


        </div>
    </div>
  </section>
</div>
<?php include ('../part/footer.php');?>