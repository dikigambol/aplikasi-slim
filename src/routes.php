<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/surat/', function (Request $request, Response $response, array $args) {
        $sql = "SELECT * FROM suratkeluar";
        $rgs = $this->db->prepare($sql);
        $rgs->execute();
        $row = $rgs->fetchAll();
        return $response->withJSON( $row, 200 );
    });

    $app->get("/edit/{id_suratkeluar}", function (Request $request, Response $response, $args){
        $id_suratkeluar = $args["id_suratkeluar"];
        $sql = "SELECT * FROM suratkeluar WHERE id_suratkeluar=:id_suratkeluar";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_suratkeluar" => $id_suratkeluar]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post('/data/esurat/{id_suratkeluar}', function (Request $request, Response $response, $args){

        $notulen_suratkeluar =$_POST['notulen_suratkeluar'];
        $file_suratkeluar = $_FILES['file_suratkeluar']['name'];

        if($file_suratkeluar != "") {
            require 'link.php';
            $ekstensi_diperbolehkan = array('pdf'); 
            $x = explode('.', $file_suratkeluar); 
            $ekstensi = strtolower(end($x));
            $file_tmp = $_FILES['file_suratkeluar']['tmp_name'];   
            $angka_acak     = rand(1,999);
            
            $id_suratkeluar = $args["id_suratkeluar"];
            $sql2 = "SELECT * FROM suratkeluar JOIN jenis ON suratkeluar.id_jenis = jenis.id_jenis WHERE id_suratkeluar='$id_suratkeluar'";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([":id_suratkeluar" => $id_suratkeluar]);
            $hasil = $stmt2->fetch();
            $a1 = "asset/file/surat/notulen/";
            $a = $hasil['kode_jenis'];
            $b = substr ($hasil['no_suratkeluar'], 0, 4);
            $c = ".pdf";
            $d = substr ($hasil['tgl_suratkeluar'], 0, 4);
            $e = substr ($hasil['tgl_suratkeluar'], 5, 2);
            $nama_file_baru = $a1.$a.$d.$e.$b.$c; 
            
            $filelama= $hasil['file_suratkeluar'];
            if (file_exists($hasil['file_suratkeluar']) ) unlink($hasil['file_suratkeluar']);

            if(in_array($ekstensi, $ekstensi_diperbolehkan) === true)  {
                            
                    move_uploaded_file($file_tmp, $nama_file_baru); 
                    
                    $query  = "UPDATE suratkeluar SET notulen_suratkeluar = '$notulen_suratkeluar',  file_suratkeluar = '$nama_file_baru', s_suratkeluar = '1'";

                    $query .= "WHERE id_suratkeluar = '$id_suratkeluar'";
                    $rgs = $this->db->prepare($query);
                    $rgs->execute();
                            if($rgs == TRUE){
                                echo "<script>alert('Berhasil Merubah Data!!');window.location='http://localhost:3000/hsuratkeluar';
                                </script>";
                            }
                            else{
                                    $result="Gagal Input Data" . $rgs->error;
                            }
            } else {     
                echo "<script>alert('Ekstensi file yang boleh hanya pdf.');
                window.location='http://localhost:3000/hsuratkeluar';</script>";
            }
        } else {

            $id_suratkeluar = $args["id_suratkeluar"];
            $query  = "UPDATE suratkeluar SET notulen_suratkeluar = '$notulen_suratkeluar', s_suratkeluar = '1' WHERE id_suratkeluar = '$id_suratkeluar'";
            $rgs = $this->db->prepare($query);
            $rgs->execute();
                if($rgs == TRUE){
                    echo "<script>alert('Berhasil Merubah Data!!'); window.location='http://localhost:3000/hsuratkeluar';</script>";
                }
                else{
                        $result="Gagal Input Data" . $rgs->error;
                }
            }
    });
};
