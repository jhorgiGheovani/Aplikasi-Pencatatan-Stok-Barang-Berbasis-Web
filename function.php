<?php
session_start();

//Membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","stockkue");

//Menambah barang baru (DONE)
if(isset($_POST['addnewbarang'])){
    $nama_kue=$_POST['nama_kue'];
    $jenis_kue=$_POST['jenis_kue'];
    $harga=$_POST['harga'];
    $stock=$_POST['stock'];

    //UPLOAD GAMBAR
    $allowed_extenstion = array('png','jpg','jpeg');
    $nama = $_FILES['file']['name'];//ambil nama gamabr
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot));//ambil ekstensi
    $ukuran = $_FILES['file']['size'];//ambil size file
    $file_tmp = $_FILES['file']['tmp_name'];//ambil lokasi file

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true).time()).'.'.$ekstensi;//menggambarkan nama file yang dienkripsi dengan ekstensinya
    //proses upload gambar
    if(in_array($ekstensi,$allowed_extenstion)==true&&$ukuran!=0){
        //validasi ukuran
        if($ukuran<15000000){
            move_uploaded_file($file_tmp, 'images/'.$image);
            $addtotable =mysqli_query($conn,"INSERT INTO stock (nama_kue, jenis_kue,harga, stock, image) VALUES('$nama_kue','$jenis_kue', '$harga','$stock','$image')");
            if($addtotable){
                header('location:index.php');
            }else{
                echo'Gagal';
                // header('location:masuk.php');
            }
        }else{
             //jika file>15mb
             echo '
             <script>
                alert("file terlalu besar maksimal ukuran file 15mb");
                window.location.href="index.php";
             </script>
             ';
        }
    }else if (in_array($ekstensi,$allowed_extenstion)==false&&$ukuran!=0){
        //jika file tidak png/jpg/jpeg
        echo '
             <script>
                alert("format file harus png/jpg/jpeg");
                window.location.href="index.php";
             </script>
             ';

    }else{
        $addtotable =mysqli_query($conn,"INSERT INTO stock (nama_kue, jenis_kue,harga, stock) VALUES('$nama_kue','$jenis_kue', '$harga','$stock')");
            if($addtotable){
                header('location:index.php');
            }else{
                echo'Gagal';
                // header('location:masuk.php');
            }
    }
}
//Menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya=$_POST['barangnya'];
    $keterangan = $_POST['keterangan'];
    $qty=$_POST['qty'];
    

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang ='$barangnya'");
    $ambildatanya =mysqli_fetch_array($cekstocksekarang);

    $stocksekarang =$ambildatanya['stock'];
    $tambahstocksekarangdenganquantity =$stocksekarang+$qty;

    $addtomasuk =mysqli_query($conn,"INSERT INTO masuk(idbarang, keterangan,qty) VALUES('$barangnya','$keterangan','$qty')");
    $updatestockmasuk =mysqli_query($conn,"update stock set stock='$tambahstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk && $updatestockmasuk){
        header('location:masuk.php');
    }else{
        echo'Gagagal';
        header('location:masuk.php');
    }
}

//Menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya=$_POST['barangnya'];
    $keterangan = $_POST['keterangan'];
    $qty=$_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang ='$barangnya'");
    $ambildatanya =mysqli_fetch_array($cekstocksekarang);

    $stocksekarang =$ambildatanya['stock'];
    $hargasekarang =$ambildatanya['harga'];
   
    
    if($stocksekarang>=$qty){
        $tambahstocksekarangdenganquantity =$stocksekarang-$qty;
        $hargaitem= $hargasekarang*$qty;

        $addtokeluar =mysqli_query($conn,"INSERT INTO keluar(idbarang, keterangan,qty,total_harga) VALUES('$barangnya','$keterangan','$qty','$hargaitem')");
        $updatestockmasuk =mysqli_query($conn,"update stock set stock='$tambahstocksekarangdenganquantity' where idbarang='$barangnya'");
        if($addtokeluar && $updatestockmasuk){
            header('location:keluar.php');
        }else{
            echo'Gagagal';
            header('location:keluar.php');
        }
    }else{
        //Kalau brangnya gak cukup
        echo '
        <script>
            alert("Stock saat ini tidak mencukupi");
            window.location.href="keluar.php";
        </script>
        ';
    }    
}

//Update Info Barang
if(isset($_POST['updatebarang'])){
    $idb=$_POST['idb'];
    $nama_kue = $_POST['nama_kue'];
    $jenis_kue = $_POST['jenis_kue'];
    $harga = $_POST['harga'];
    //UPLOAD GAMBAR
    $allowed_extenstion = array('png','jpg','jpeg');
    $nama = $_FILES['file']['name'];//ambil nama gamabr
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot));//ambil ekstensi
    $ukuran = $_FILES['file']['size'];//ambil size file
    $file_tmp = $_FILES['file']['tmp_name'];//ambil lokasi file

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true).time()).'.'.$ekstensi;//menggambarkan nama file yang dienkripsi dengan ekstensinya


    if($ukuran==0){
        //jika tidak ingin upload
        $update = mysqli_query($conn,"UPDATE stock SET nama_kue='$nama_kue', jenis_kue='$jenis_kue', harga='$harga' WHERE idbarang='$idb'");
        if($update){
            header('location:index.php');
            echo 'berhasil';
        }else{
            header('location:keluar.php');
        }
    }else{
        //jika ingin upload
        move_uploaded_file($file_tmp, 'images/'.$image);
        $update = mysqli_query($conn,"UPDATE stock SET nama_kue='$nama_kue', jenis_kue='$jenis_kue', harga='$harga',image='$image' WHERE idbarang='$idb'");
        if($update){
            header('location:index.php');
            echo 'berhasil';
        }else{
            header('location:keluar.php');
        }
    }
    
}

//Menghapus barnag dari stock
if(isset($_POST['hapusbarang'])){
    $idb = $_POST['idb'];

    $gambar =mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $img = 'images/'.$get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "DELETE from stock where idbarang='$idb'");
    if($hapus){
        header('location:index.php');
    }else{
        echo'Gagagal';
        header('location:index.php');
    }
}

//Mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn,"SELECT * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrng = $stocknya['stock'];

    $qtyskrng =mysqli_query($conn, "SELECT * from masuk where idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtyskrng = $qtynya['qty'];

    if($qty>$qtyskrng){
        $selisih = $qty-$qtyskrng;
        $kurangin = $stockskrng+$selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock set stock='$kurangin' where idbarang='$idb' ");
        $updatenya = mysqli_query($conn, "UPDATE masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else{
                echo'gagal';
                header('location:masuk.php');
            }
    }else{
        $selisih = $qtyskrng-$qty;
        $kurangin = $stockskrng-$selisih;
        $kurangistocknya=mysqli_query($conn,"UPDATE stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya =mysqli_query($conn,"UPDATE masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'" );
        if($kurangistocknya&&$updatenya){
            header('location:masuk.php');
        }else{
            echo'gagal';
            header('location:masuk.php');
        }
    }
}

//Menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb=$_POST['idb'];
    $qty=$_POST['kty'];
    $idm=$_POST['idm'];

    $getdatastock = mysqli_query($conn, "SELECT * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih=$stok-$qty;

    $update = mysqli_query($conn,"UPDATE stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn,"DELETE from masuk where idmasuk= '$idm'");
    if($update&&$hapusdata){
        header('location:masuk.php');
    }else{
        echo'gagal';
        header('location:masuk.php');
    }

}

//Mengubah data keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn,"SELECT * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);

    $stockskrng = $stocknya['stock'];
    $hargasekarang =$stocknya['harga']; 
    $hargaitem= $hargasekarang*$qty;
    
    $qtyskrng =mysqli_query($conn, "SELECT * from keluar where idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtyskrng = $qtynya['qty'];
    
    if($qty>$qtyskrng){
        $selisih = $qty-$qtyskrng;
        $kurangin = $stockskrng-$selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock set stock='$kurangin' where idbarang='$idb' ");
        $updatenya = mysqli_query($conn, "UPDATE keluar set qty='$qty', keterangan='$keterangan', total_harga='$hargaitem' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else{
                echo'gagal';
                header('location:keluar.php');
            }
    }else{
        $selisih = $qtyskrng-$qty;
        $kurangin = $stockskrng+$selisih;
        $kurangistocknya=mysqli_query($conn,"UPDATE stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya =mysqli_query($conn,"UPDATE keluar set qty='$qty', keterangan='$keterangan', total_harga='$hargaitem' where idkeluar='$idk'" );
        if($kurangistocknya&&$updatenya){
            header('location:keluar.php');
        }else{
            echo'gagal';
            header('location:keluar.php');
        }
    }
    
}

//Menghapus barang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb=$_POST['idb'];
    $qty=$_POST['qty'];
    $idk=$_POST['idk'];

    $getdatastock = mysqli_query($conn, "SELECT * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih=$stok+$qty;

    $update = mysqli_query($conn,"UPDATE stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn,"DELETE from keluar where idkeluar= '$idk'");
    if($update&&$hapusdata){
        header('location:keluar.php');
    }else{
        echo'gagal';
        header('location:keluar.php');
    }

}

?>