<?php

function MakeFolder() {
   // Mendapatkan tanggal, bulan, dan tahun saat ini
   $tanggal = date('d');
   $bulan = date('m');
   $tahun = date('Y');

   // Menyusun nama folder
   $folder_name = $tanggal."_".$bulan."_".$tahun;

   // Membuat angka acak 5 digit
   $random_number = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

   // Menyusun jalur direktori
   $folder_path = "result/$folder_name/$random_number";

   return $folder_path;
}

// Panggil fungsi untuk membuat folder dan mendapatkan jalur direktori
$folder = MakeFolder();
mkdir($folder, 0755, true);
echo $folder . PHP_EOL;