<?php
session_start();
require_once 'class.user.php';

$reg_user = new USER();

if($reg_user->is_logged_in()!="")
{
 $reg_user->redirect('CCBHome1.1.html');
}


if(isset($_POST['btn-signup']))
{
 $uname = trim($_POST['txtuname']);
 $fname = trim($_POST['txtfname']);
 $lname = trim($_POST['txtlname']);
 $email = trim($_POST['txtemail']);
 $upass = trim($_POST['txtpass']);
 $code = md5(uniqid(rand()));
 $vercode = $reg_user->createcode();
 
 $stmt = $reg_user->runQuery("SELECT * FROM Accounts WHERE email=:email_id");
 $stmt->execute(array(":email_id"=>$email));
 $row = $stmt->fetch(PDO::FETCH_ASSOC);

 $stmt1 = $reg_user->runQuery("SELECT * FROM Accounts WHERE username=:user_id");
 $stmt1->execute(array(":user_id"=>$uname));
 $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

 if($stmt->rowCount() > 0)
 {
  $msg = "
        <div class='alert alert-error'>
    <button class='close' data-dismiss='alert'>&times;</button>
     <strong>Sorry !</strong>  email allready exists , Please Try another one
     </div>
     ";
 }
 else if($stmt1->rowCount() > 0)
{
  $msg = "
        <div class='alert alert-error'>
    <button class='close' data-dismiss='alert'>&times;</button>
     <strong>Sorry !</strong>  Username allready exists , Please Try another one
     </div>
     ";
 }
 else
 {
  if($reg_user->register($fname,$lname,$email,$uname,$upass,$code,$vercode))
  {   
   $id = $reg_user->lasdID();  
   $key = base64_encode($id);
   $id = $key;
   


   $message = "     
      Hello $uname,
      <br /><br />
      Welcome to Code Clones Benchmark!<br/>
      To complete your registration  please use the following 4 character code: $vercode<br/>
      <br /><br />

      
      <br /><br />
      Thanks, <br/> Code Clones Benchmark";
      
   $subject = "Confirm Registration";
      
   $reg_user->send_mail($email,$message,$subject); 
   $msg = "
     <div class='alert alert-success'>
      <button class='close' data-dismiss='alert'>&times;</button>
      <strong>Success!</strong>  We've sent an email to $email.
                    Please use the verification code therein to complete your account. 
       </div>
     ";
	header("refresh:5;verify.php");
  }
  else
  {
   echo "sorry , Query could no execute...";
  }  
 }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Signup | Coding Cage</title>
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
    <?php if(isset($msg)) echo $msg;  ?>
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Sign Up</h2><hr />
        <input type="text" class="input-block-level" placeholder="First name" name="txtfname" required />
	<input type="text" class="input-block-level" placeholder="Last name" name="txtlname" required />
	<input type="text" class="input-block-level" placeholder="Username" name="txtuname" required />
        <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
        <input type="password" class="input-block-level" placeholder="Password" name="txtpass" required />
      <hr />
        <button class="btn btn-large btn-primary" type="submit" name="btn-signup">Sign Up</button>
        <a href="index.php" style="float:right;" class="btn btn-large">Sign In</a>
      </form>

    </div> <!-- /container -->
    <script src="vendors/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>