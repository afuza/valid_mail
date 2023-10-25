<?php

include "./apps/core.php";

$validmail = new VerifyEmail();
$validmail->setStreamTimeoutWait(20);
$validmail->setEmailFrom('admin@9six.io');


$inputlist = readline(" Enter list: ");
$inputlist = "./email/" . $inputlist;

if (empty($inputlist) || !file_exists($inputlist)) {
    echo " [?] list not found" . PHP_EOL;
    exit;
}

$maillist = array_unique(explode("\n", str_replace("\r", "", file_get_contents($inputlist))));
$maillist = array_filter($maillist, 'trim'); // Filter out empty elements

$total = count($maillist); // Use the filtered array to get the total count
$folder = MakeFolder();
mkdir($folder, 0755, true);

echo " [!] Total list: $total" . PHP_EOL;
echo " [+] Folder: $folder". PHP_EOL;
echo " [+] Verifying...". PHP_EOL;

foreach ($maillist as $email) {
    $email = trim($email);
    $email = strtolower($email);

    $emailfilter = filter_var($email, FILTER_SANITIZE_EMAIL);

    if($emailfilter == true){
        if ($validmail->check($email)) {
            echo " [+] $email". PHP_EOL;
        } else {
            echo " [-] $email". PHP_EOL;
        }
    }else{
        echo " [!] $email". PHP_EOL;
    }
}   


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