<?php
session_start();
require_once 'class.user.php';
$user = new USER();

if($user->is_logged_in()!="")
{
 $user->redirect('CCBHome1.1.html');
}

if(isset($_POST['btn-submit']))
{
 $email = $_POST['txtemail'];
 
 $stmt = $user->runQuery("SELECT * FROM Accounts WHERE email=:email LIMIT 1");
 $stmt->execute(array(":email"=>$email));
 $row = $stmt->fetch(PDO::FETCH_ASSOC); 
 if($stmt->rowCount() == 1)
 {
  $id = base64_encode($row['userID']);
  $code = md5(uniqid(rand()));
  $uname = $row['username'];
  $vercode = $row['vercode'];
  
  $stmt = $user->runQuery("UPDATE Accounts SET tokenCode=:token WHERE email=:email");
  $stmt->execute(array(":token"=>$code,"email"=>$email));
  
#need to reset link for the local location of resetpass.php
  $message= "
       Hello $uname,
       <br /><br />
       If you requested a new password please follow the link below. Otherwise, please disregard this email.
       <br /><br />
       Use the following code to reset your password: $vercode 
       <br /><br />
       thank you,
       Code Clones Benchmark
       ";
  $subject = "Password Reset";
  
  $user->send_mail($email,$message,$subject);
  
  $msg = "<div class='alert alert-success'>
     <button class='close' data-dismiss='alert'>&times;</button>
     We've sent an email to $email.
                    Please use the verification code to change your password. 
      </div>";
	header("refresh:5;resetpass.php?id=$id&code=$code");
 }
 else
 {
  $msg = "<div class='alert alert-danger'>
     <button class='close' data-dismiss='alert'>&times;</button>
     <strong>Sorry!</strong>  this email not found. 
       </div>";
 }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Forgot Password</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="assets/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body id="login">
    <div class="container">

      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Forgot Password</h2><hr />
        
         <?php
   if(isset($msg))
   {
    echo $msg;
   }
   else
   {
    ?>
               <div class='alert alert-info'>
    Please enter your email address. You will receive a link to create a new password via email.!
    </div>  
                <?php
   }
   ?>
        
        <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
      <hr />
        <button class="btn btn-danger btn-primary" type="submit" name="btn-submit">Generate new Password</button>
      </form>

    </div> <!-- /container -->
    <script src="bootstrap/js/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>