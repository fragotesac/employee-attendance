<?php

namespace Business\Util;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
	const NOMBRE_REMITENTE = 'FraGoTe';
	const EMAIL = 'noreply@fragote.com';
	const PASSWORD = 'CLAVE_MUY_COMPLICADA';
	const HOST = 'mail.fragote.com';
	const PORT = 587;
	const SMTP_SECURE = 'tls';
	const SMTP_AUTH = true;
	const SMTP_DEBUG = 0;

   public $email;

   public function emailConfig($host = self::HOST, $port = self::PORT, $SMTPSecure = self::SMTP_SECURE,
      $SMTPAuth = self::SMTP_AUTH, $username = self::EMAIL,
      $password = self::PASSWORD, $smtpDebug = self::SMTP_DEBUG)
   {
      $mail = new PHPMailer(self::SMTP_DEBUG);
      //Server settings
      $mail->Timeout = 10;
      $mail->SMTPDebug = $smtpDebug;
      $mail->isSMTP();
      $mail->Host = $host;
      $mail->Port = $port;
      $mail->SMTPAuth = $SMTPAuth;
      $mail->Username = $username;
      $mail->Password = $password;
      $mail->SMTPSecure = $SMTPSecure;
      $mail->CharSet = 'UTF-8';

      $this->email = $mail;

      return $mail;
   }

   public function enviar(
      $titulo, $cuerpoHtml, $para,
      $attachmentString = [],
      $de = ['mail' => self::DefaultEmail, 'nombre' => self::NOMBRE_REMITENTE],
      $CCO = []
   ) {
      try {
         $email = $this->emailConfig();
         $email->setFrom($de['mail'], $de['nombre']);
         $email->addAddress($para['mail'], $para['nombre']);

         if (!empty($attachmentString)) {
            foreach($attachmentString as $attachment) {
               $email->AddStringAttachment($attachment['content'], $attachment['filename']);
            }
         }

         if (!empty($CCO)) {
             foreach ($CCO as $copia) {
                 $email->addBCC($copia);
             }
         }

         $email->isHTML(true);
         $email->Subject = $titulo;
         $email->Body = $cuerpoHtml;
         $email->AltBody = strip_tags($cuerpoHtml);

         return $email->send();
      } catch (Exception $e) {
         error_log($e->getMessage());
      }

      return 0;
   }
}
