<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function sendAlertEmail($toEmail, $username, $ip) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'zikra.220170001@mhs.unimal.ac.id';
        $mail->Password   = 'sdpydpthmbmudynd';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('agielqin07@gmail.com', 'Sistem Keamanan Login');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = "Peringatan Aktivitas Login Mencurigakan";
        $mail->Body    = "
            <h3>Peringatan Keamanan!</h3>
            <p>Akun <b>$username</b> mengalami 3 kali percobaan login gagal berturut-turut.</p>
            <p>IP: $ip</p>
            <p>Waktu: " . date('Y-m-d H:i:s') . "</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email gagal dikirim: {$mail->ErrorInfo}");
    }
}
?>
