<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function convertirAWebP($original, $destino) {
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'jpeg':
        case 'jpg':
            $image = @imagecreatefromjpeg($original);
            break;
        case 'png':
            $image = @imagecreatefrompng($original);
            break;
        default:
            return false; // Formato no soportado
    }

    if (!$image) {
        return false; // No se pudo abrir la imagen
    }

    // Crear un lienzo truecolor del mismo tamaño
    $width = imagesx($image);
    $height = imagesy($image);
    $truecolor = imagecreatetruecolor($width, $height);

    // Si es PNG con transparencia, preservar canal alfa
    if ($extension === 'png') {
        imagealphablending($truecolor, false);
        imagesavealpha($truecolor, true);
        $transparent = imagecolorallocatealpha($truecolor, 0, 0, 0, 127);
        imagefilledrectangle($truecolor, 0, 0, $width, $height, $transparent);
    }

    // Copiar al nuevo lienzo
    imagecopy($truecolor, $image, 0, 0, 0, 0, $width, $height);
    imagedestroy($image);

    // Guardar como WebP
    $resultado = @imagewebp($truecolor, $destino, 70);
    imagedestroy($truecolor);

    // Solo eliminar el original si la conversión fue exitosa
    if ($resultado && file_exists($destino)) {
        unlink($original);
        return true;
    }

    return false;
}

function sendEmail($subject, $user,$title,$body,$textLink,$link, $email){
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth= true;
    $mail->Username='soporte@dokidokispanish.club';
    $mail->Password=getenv('MAIL_PASS');
    $mail->SMTPSecure = 'ssl';
    $mail->Port=465;
    $mail->setFrom('soporte@dokidokispanish.club', 'Soporte DDSC');
    $mail ->addAddress($email);
    $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');
    $mail->addCustomHeader('Auto-Submitted', 'auto-generated');
    $mail->isHTML(true);
    $body = generarCorreoHTML(
              $user,
              $title,
              $body,
              $textLink,
              $link
            );
    $mail->Subject= $subject;
    $mail->msgHTML($body);
    if($mail->send()){
        return True;
    }else{
        return False;
    }
}