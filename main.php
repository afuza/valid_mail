<?php

include "./apps/core.php";

$validmail = new VerifyEmail();
$validmail->setStreamTimeoutWait(20);
$validmail->setEmailFrom('admin@9six.io');


$inputlist = readline(" Enter list: ");

if (empty($inputlist) || !file_exists($inputlist)) {
    echo " [?] list not found" . PHP_EOL;
    exit;
}

$maillist = array_unique(explode("\n", str_replace("\r", "", file_get_contents($inputlist))));
$maillist = array_filter($maillist, 'trim'); // Filter out empty elements

$total = count($maillist); // Use the filtered array to get the total count
$valid = 0;
$invalid = 0;

$folder = MakeFolder();
mkdir($folder, 0755, true);

echo color()['BPutih'] . "============================".PHP_EOL;
echo color()['BUngu'] . " Natama Email Bounce Check ".PHP_EOL;
echo color()['BPutih'] . "============================".PHP_EOL;
echo color()['BYellow'] . "[!] ". color()['BPutih'] . "Total list: $total" . PHP_EOL;
echo color()['BHijau'] . "[!] ". color()['BPutih'] . "[+] Folder: $folder". PHP_EOL;
echo color()['BUngu'] . "[!] ". color()['BPutih'] . "[+] Verifying...". PHP_EOL . PHP_EOL;

foreach ($maillist as $email) {
    $email = trim($email);
    $email = strtolower($email);

    $emailfilter = filter_var($email, FILTER_SANITIZE_EMAIL);

    if($emailfilter == true){
        if ($validmail->check($email)) {
            echo color()['BHijau'] . "[+] ". color()['BPutih'] . "$email";
            $save = fopen($folder . "/valid.txt", 'a+');
            fwrite($save, $email . PHP_EOL);
            fclose($save);
            $valid++;
        } else {
            echo color()['BRed'] . "[-] ". color()['BPutih'] . "$email";
            $save = fopen($folder. "/invalid.txt", 'a+');
            fwrite($save, $email . PHP_EOL);
            fclose($save);
            $invalid++;
        }
    }else{
        echo color()['BYellow'] . "[!] ". color()['BPutih'] . "$email";
        $save = fopen($folder. "/uknow.txt", 'a+');
        fwrite($save, $email. PHP_EOL);
        fclose($save);
    }

    echo color()['BBlue'] . "       [". number_format(($valid + $invalid), 0, '', ','). "/". number_format($total, 0, '', ','). "] Progress => ". round(($valid + $invalid) / $total * 100, 2). "%\r\n";
}   


echo color()['BPutih'] . "============================".PHP_EOL;
echo color()['BUngu'] .  " Finished!\r\n";
echo color()['BPutih'] . "============================".PHP_EOL;
echo color()['BHijau'] . "[=] ". color()['BPutih'] . "Total list: $total". PHP_EOL;
echo color()['BYellow'] . "[+] ". color()['BPutih'] . "Valid: $valid". PHP_EOL;
echo color()['BRed'] . "[-] ". color()['BPutih'] . "Invalid: $invalid". PHP_EOL;
echo "============================".PHP_EOL .color()["reset"];


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


function color()
{
    $colors = array(
        "reset" => "\033[0m",
        # Warna Reguler
        "Hitam"     => " \033[0;30m ",        # Hitam
        "Merah"     => " \033[0;31m ",         # Merah
        "Hijau"     => " \033[0;32m ",         # Hijau
        "Kuning"    => " \033[0;33m ",        # Kuning
        "Biru"      => " \033[0;34m ",          # Biru
        "Ungu"      => " \033[0;35m ",        # Ungu
        "Cyan"      => " \033[0;36m ",          # Cyan
        "Putih"     => " \033[0;37m ",        # Putih
        #Bold
        "BBlack" => " \033[1;30m ",        # Hitam
        "BRed" => " \033[1;31m ",          # Merah
        "BHijau" => " \033[1;32m ",        # Hijau
        "BYellow" => " \033[1;33m ",       # Kuning
        "BBlue" => " \033[1;34m ",         # Biru
        'BUngu' => " \033[1;35m ",       # Ungu
        "BCyan" => " \033[1;36m ",         # Cyan
        "BPutih" => " \033[1;37m ",       # Putih
        # Garis bawah
        "UBlack" => " \033[4;30m ",        # Hitam
        "URed" => " \033[4;31m ",         # Merah
        "UGreen" => " \033[4;32m ",        # Hijau
        "UKuning" => " \033[4;33m ",       # Kuning
        "UBlue" => " \033[4;34m ",        # Biru
        "UUngu" => " \033[4;35m ",      # Ungu
        "UCyan" => " \033[4;36m ",        # Cyan
        "UPutih" => " \033[4;37m ",       # Putih

        # Latar Belakang
        "Aktif_Hitam" => " \033[40m ",       # Hitam
        "Aktif_Merah" => " \033[41m ",        # Merah
        "On_Green" => " \033[42m ",  # Hijau
        "On_Yellow" => " \033[43m ", # Kuning
        "On_Blue" => " \033[44m ", # Biru
        "On_Purple" => " \033[45m ",  # Ungu
        "On_Cyan" => " \033[46m ",   #Cyan
        "On_White" => " \033[47m ",  # Putih

        # Intensitas Tinggi
        "IBlack" => " \033[0;90m ",    # Hitam
        "IRed" => " \033[0;91m ",   # Merah
        "IHijau" => " \033[0;92m ",  # Hijau
        "IKuning" => " \033[0;93m ", # Kuning
        "IBlue" => " \033[0;94m ",        # Biru
        "IPungu" => " \033[0;95m ",       # Ungu
        "ICyan" => " \033[0;96m ",       # Cyan
        "IPutih" => " \033[0;97m ",      # Putih

        # Berani Intensitas Tinggi
        "BIBlack" => " \033[1;90m ",       # Hitam
        "BIRed" => " \033[1;91m ",       # Merah
        "BIGreen" => " \033[1;92m ",       # Hijau
        "BIYellow" => " \033[1;93m ",      # Kuning
        "BIBlue" => " \033[1;94m ",     # Biru
        "BIUngu" => " \033[1;95m ",   # Ungu
        "BICyan" => " \033[1;96m ",     # Cyan
        "BIPutih" => " \033[1;97m ",    # Putih

        # Latar belakang Intensitas Tinggi
        "On_IBlack" => " \033[0;100m ",  # Hitam
        "On_IRed" => " \033[0;101m ",   # Merah
        "On_IGreen" => " \033[0;102m ",   # Hijau
        "On_IYellow" => " \033[0;103m ",  # Kuning
        "On_IBlue" => " \033[0;104m ",  # Biru
        "On_IPurple" => " \033[10;95m ",  # Ungu
        "On_ICyan" => " \033[0;106m ",  # Cyan
        "On_IWhite" => " \033[0;107m ",  # Putih
    );
    return $colors;
}