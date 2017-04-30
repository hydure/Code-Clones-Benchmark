<?php
$email=$argv[1];
$subject=$argv[2];
$message=$argv[3];

require_once('mailer/PHPMailerAutoload.php');
$mail = new PHPMailer();
$mail->IsSMTP(); 
$mail->SMTPDebug  = 0;                     
$mail->SMTPAuth   = true;                  
$mail->SMTPSecure = "ssl";                 
$mail->Host       = "smtp.gmail.com";      
$mail->Port       = 465;             
$mail->AddAddress($email);
$mail->Username="codeclonesbenchmark@gmail.com";  
$mail->Password="1Dh%6yMQ";
$mail->From = 'codeclonesbenchmark@gmail.com';
$mail->FromName = 'Code Clones Benchmark';            
$mail->AddReplyTo("codeclonesbenchmark@gmail.com","Code Clones Benchmark");
$mail->Subject    = $subject;
$mail->MsgHTML($message);
$mail->Send();
?>
