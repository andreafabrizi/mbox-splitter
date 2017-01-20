<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 26/08/15
 * Time: 16:26
 */

if(isset($argv[1]) && !empty($argv[1]))
    $mbox_input_file = $argv[1];
else die('No mbox entry file specified');

if(isset($argv[2]) && !empty($argv[2]))
    $mbox_output_folder = $argv[2];
else die('No output folder specified');


if(!file_exists($mbox_output_folder))
    mkdir($mbox_output_folder);

// Kill all files inside
$files = glob("$mbox_output_folder/*");
foreach($files as $file) {
    if(is_file($file))
        unlink($file);
}

$handle = fopen($mbox_input_file, "r") or die("Couldn't get handle");
if ($handle) {
    $mail_counter = 1;
    $big_mails_counter = 0;
    $file_counter = 0;
    $i= 0;
    $current_mail = "";

    while (!feof($handle)) {
        $buffer = fgets($handle);
        if(substr($buffer, 0, 5) == "From ") {

            // Handle end of previous mail
            if(!empty($current_mail)) {
                // Reach of the file limit.

                $mail_size = strlen($current_mail);

                $big_mails_counter++;
                file_put_contents("$mbox_output_folder/email_$big_mails_counter.eml", $current_mail, FILE_APPEND);
                $current_mail = "";
            }

            echo "Mail #$mail_counter\n";
            $mail_counter++;
        }
        $current_mail .= $buffer;
        $i++;
        clearstatcache();
    }
    fclose($handle);
}
