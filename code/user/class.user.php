<?php
require_once '../dataset_functions/Dbconfig.php';
class USER
{ 

 private $conn;
 
 public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->conn = $db;
    }
 
 public function runQuery($sql)
 {
  $stmt = $this->conn->prepare($sql);
  return $stmt;
 }
 
 public function lasdID()
 {
  $stmt = $this->conn->lastInsertId();
  return $stmt;
 }
 
 public function register($fname,$lname,$email,$uname,$upass,$code, $vercode)
 {
  try
  {       
   $password = $upass;
   $stmt = $this->conn->prepare("INSERT INTO Accounts(firstname, lastname, email, username, password,tokencode, vercode) 
                                                VALUES(:fname, :lname, :user_mail, :user_name, :user_pass, :active_code, :ver_code)");
   $stmt->bindparam(":user_name",$uname);
   $stmt->bindparam(":fname",$fname);
   $stmt->bindparam(":lname",$lname);
   $stmt->bindparam(":user_mail",$email);
   $stmt->bindparam(":user_pass",$password);
   $stmt->bindparam(":active_code",$code);
   $stmt->bindparam(":ver_code",$vercode);
   $stmt->execute(); 
   return $stmt;
  }
  catch(PDOException $ex)
  {
   echo $ex->getMessage();
  }
 }
 
 public function login($uname,$upass)
 {
  try
  {
   $stmt = $this->conn->prepare("SELECT * FROM Accounts WHERE username=:us_id");
   $stmt->execute(array(":us_id"=>$uname));
   $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
   
   if($stmt->rowCount() == 1)
   {
    if($userRow['password']==$upass)
    {
     if($userRow['userStatus']=="Y")
     {
      $_SESSION['userSession'] = $userRow['userId'];
      $_SESSION['userName'] = $userRow['username'];
      header("Location: ../index.php?passsuccess");
      return true;
     }
     else
     {
      $id = $userRow->lasdID();  
     $key = base64_encode($id);
     $id = $key;
     $code = $userRow['tokencode'];
     header("Location: verify.php?id=$id&code=$code");
     exit;
     }
    }
    else
    {
      header("Location: ../index.php?passerror");
      exit;
    } 
   }
   else
   {
    header("Location: ../index.php?error");
    exit;
   }  
  }
  catch(PDOException $ex)
  {
   echo $ex->getMessage();
  }
 }
 
 
 public function is_logged_in()
 {
  if(isset($_SESSION['userSession']))
  {
   return true;
  }
 }
 
 public function redirect($url)
 {
  header("Location: $url");
 }
 
 public function logout()
 {
  session_destroy();
  $_SESSION['userSession'] = false;
 }

 public function createcode()
 {
 $char = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
 $charlength = strlen($char);
 $vercode = '';
 for($i = 0; $i < 4; $i++){
	$vercode .= $char[rand(0, $charlength - 1)];
 }
 return $vercode;
 }
 
 function send_mail($email,$message,$subject)
 {      
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
 } 
}