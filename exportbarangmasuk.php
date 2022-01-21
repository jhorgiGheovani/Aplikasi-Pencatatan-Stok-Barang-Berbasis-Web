<?php
//import koneksi ke database
require 'function.php';
require 'tanggal.php';
?>

<html>
<head>
  <title>Barang Masuk</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>

<body>
<div class="container">
			<h2>Barang Masuk</h2>
				<div class="data-tables datatable-dark">
					
					<!-- Masukkan table nya disini, dimulai dari tag TABLE -->
                    <!-- ID HARUS SAMA -->
                    <table class="table table-bordered" id="exportdata" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Nama Kue</th>
                                                <th>Jenis Kue</th>
                                                <th>Jumlah</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                            <?php

                                            if(isset($_POST['filter_tgl'])){
                                                $mulai = $_POST['tgl_mulai'];
                                                $selesai = $_POST['tgl_selesai'];
                                                if($mulai != null || $selesai != null){
                                                    $ambilsemuadatastock = mysqli_query($conn, "select * from masuk m , stock s where s.idbarang=m.idbarang and tanggal BETWEEN '$mulai' and DATE_ADD('$selesai', INTERVAL 1 DAY) order by idmasuk DESC");
                                                    
                                                }else {
                                                    $ambilsemuadatastock = mysqli_query($conn, "select * from masuk m , stock s where s.idbarang=m.idbarang order by idmasuk DESC");
                                                    
                                                }
                                            }else {
                                                $ambilsemuadatastock = mysqli_query($conn, "select * from masuk m , stock s where s.idbarang=m.idbarang order by idmasuk DESC");
                                            }

                                            // $ambilsemuadatastock = mysqli_query($conn, "select * from masuk m , stock s where s.idbarang=m.idbarang");
                                            while($data=mysqli_fetch_array($ambilsemuadatastock)){
                                                $idb =$data['idbarang'];
                                                $idm =$data['idmasuk'];
                                                $tanggal = $data['tanggal'];
                                                $nama_kue = $data['nama_kue'];
                                                $jenis_kue = $data['jenis_kue'];
                                                $qty = $data['qty'];
                                                $keterangan = $data['keterangan'];

                                            ?>

                                            <tr>
                                                <td><?=tgl_indonesia($tanggal);?></td>
                                                <td><?=$nama_kue;?></td>
                                                <td><?=$jenis_kue;?></td>
                                                <td><?=$qty;?></td>
                                                <td><?=$keterangan;?></td>
                                            </tr>

                                            <?php
                                            };
                                            ?>

                                        </tbody>
                                    </table>
					
				</div>
</div>
	
<script>
$(document).ready(function() {
    $('#exportdata').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy','csv','excel', 'pdf', 'print'
        ]
    } );
} );
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

	

</body>

</html>