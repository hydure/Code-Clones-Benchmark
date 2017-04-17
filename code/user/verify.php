<?php
require_once 'class.user.php';
$user = new USER();
if(isset($_POST['btn-signup']))
{
if(empty($_GET['id']) && empty($_GET['code']))
{
 $user->redirect('../index.php');
}

if(isset($_GET['id']) && isset($_GET['code']))
{
 $id = base64_decode($_GET['id']);
 $code = $_GET['code'];
 
 $statusY = "Y";
 $statusN = "N";
 $vercode = trim($_POST['txtvercode']);

 $stmt = $user->runQuery("SELECT * FROM Accounts WHERE userId=:uID AND tokenCode=:code LIMIT 1");
 $stmt->execute(array(":uID"=>$id,":code"=>$code));
 $row=$stmt->fetch(PDO::FETCH_ASSOC);
 if($stmt->rowCount() > 0)
 {
  $confirmcode = $row['vercode'];
   if($confirmcode == $vercode)
   {
    if($row['userStatus']==$statusN)
    {
     $stmt = $user->runQuery("UPDATE Accounts SET userStatus=:status WHERE userId=:uID");
     $stmt->bindparam(":status",$statusY);
     $stmt->bindparam(":uID",$id);
     $stmt->execute(); 
   
     $msg = "
               <div class='alert alert-success'>
         <button class='close' data-dismiss='alert'>&times;</button>
         <strong>WoW !</strong>  Your Account is Now Activated : <a href='../index.php'>Login here</a>
            </div>
            "; 
    }
    else
    {
     $msg = "
               <div class='alert alert-error'>
         <button class='close' data-dismiss='alert'>&times;</button>
         <strong>sorry !</strong>  Your Account is allready Activated : <a href='../index.php'>Login here</a>
            </div>
            ";
    }
  }
  else
  {
   $msg = "<div class='alert alert-block'>
       <button class='close' data-dismiss='alert'>&times;</button>
       <strong>Invalid Code!</strong> 
       </div>";
  } 
 }
 else
 {
  $msg = "
         <div class='alert alert-error'>
      <button class='close' data-dismiss='alert'>&times;</button>
      <strong>sorry !</strong>  No Account Found : <a href='signup.php'>Signup here</a>
      </div>
      ";
 }
}
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Confirm Registration</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="assets/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  </head>
  <body id="login">
    <div class="container">
	<form class="form-verify" method="post">
	<h2 class="form-verify-heading">Input Verification Code</h2>
	<input type="text" class="input-block-level" placeholder="Verification code" name="txtvercode" required />
	<button class="btn btn-large btn-primary" type="submit" name="btn-signup">Verify</button>
	</form>
  <?php if(isset($msg)) { echo $msg; } ?>
    </div> <!-- /container -->
    <script src="vendors/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>