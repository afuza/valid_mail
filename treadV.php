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

echo color()['BPutih'] . "============================" . PHP_EOL;
echo color()['BUngu'] . "Natama Email Bounce Check" . PHP_EOL;
echo color()['BPutih'] . "============================" . PHP_EOL;
echo color()['BYellow'] . "[!] " . color()['BPutih'] . "Total list: $total" . PHP_EOL;
echo color()['BHijau'] . "[!] " . color()['BPutih'] . "[+] Folder: $folder" . PHP_EOL;
echo color()['BUngu'] . "[!] " . color()['BPutih'] . "[+] Verifying..." . PHP_EOL . PHP_EOL;

// Number of threads to use
$threads = 4; // You can adjust this value based on your server's capacity
$threadHandles = [];

function verifyEmails($emails, $validmail, $folder, $total, &$valid, &$invalid) {

    foreach ($emails as $email) {
        $email = trim($email);
        $email = strtolower($email);

        $emailfilter = filter_var($email, FILTER_SANITIZE_EMAIL);

        if ($emailfilter == true) {
            if ($validmail->check($email)) {
                echo color()['BHijau'] . "[+] " . color()['BPutih'] . "$email";
                $save = fopen($folder . "/valid.txt", 'a+');
                fwrite($save, $email . PHP_EOL);
                fclose($save);
                $valid++;
            } else {
                echo color()['BRed'] . "[-] " . color()['BPutih'] . "$email";
                $save = fopen($folder . "/invalid.txt", 'a+');
                fwrite($save, $email . PHP_EOL);
                fclose($save);
                $invalid++;
            }
        } else {
            echo color()['BYellow'] . "[!] " . color()['BPutih'] . "$email";
            $save = fopen($folder . "/uknow.txt", 'a+');
            fwrite($save, $email . PHP_EOL);
            fclose($save);
        }

        echo color()['BBlue'] . "|| BEON BOUNCE" . "\r\n";
    }
}

$emailsPerThread = ceil($total / $threads);

for ($i = 0; $i < $threads; $i++) {
    $start = $i * $emailsPerThread;
    $end = ($i + 1) * $emailsPerThread;
    $threadEmails = array_slice($maillist, $start, $emailsPerThread);
    
    $pid = pcntl_fork();
    if ($pid == -1) {
        die("Fork failed");
    } elseif ($pid) {
        $threadHandles[] = $pid;
    } else {
        verifyEmails($threadEmails, $validmail, $folder, $total, $valid, $invalid );
        exit();
    }
}

foreach ($threadHandles as $pid) {
    pcntl_waitpid($pid, $status);
}

echo PHP_EOL;
echo color()['BPutih'] . "============================" . PHP_EOL;
echo color()['BUngu'] . "Finished!" . PHP_EOL;
echo color()['BPutih'] . "============================" . PHP_EOL . color()["reset"];

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

function color() {
    $colors = array(
        "reset" => "\033[0m",
        #Bold
        "BBlack" => " \033[1;30m ",        # Hitam
        "BRed" => " \033[1;31m ",          # Merah
        "BHijau" => " \033[1;32m ",        # Hijau
        "BYellow" => " \033[1;33m ",       # Kuning
        "BBlue" => " \033[1;34m ",         # Biru
        'BUngu' => " \033[1;35m ",       # Ungu
        "BCyan" => " \033[1;36m ",         # Cyan
        "BPutih" => " \033[1;37m ",       # Putih
    );
    return $colors;
}