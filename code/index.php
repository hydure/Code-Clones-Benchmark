<?php
session_start();
require_once 'class.user.php';
$user_login = new USER();

if($user_login->is_logged_in()!="")
{
 $user_login->redirect('CCBHome1.1.php');
}

if(isset($_POST['btn-login']))
{
 $uname = trim($_POST['txtuname']);
 $upass = trim($_POST['txtupass']);
 echo "$uname $upass entered";
 if($user_login->login($uname,$upass))
 {
  $user_login->redirect('CCBHome1.1.php');
 }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Login | Code Clones Benchmark</title>
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

        <form class="form-signin" method="post">
        <?php
        if(isset($_GET['error']))
  {
   ?>
            <div class='alert alert-success'>
    <button class='close' data-dismiss='alert'>&times;</button>
    <strong>Wrong Details!</strong> 
   </div>
            <?php
  }
  ?>
        <h2 class="form-signin-heading">Welcome To Code Clones Benchmark.</h2><hr />
<p>Code Clones are pieces of source code that are similar. While there are several code clone detection techniques, an open crowd-sourced benchmark of true clones to evaluate these clones effectively is missing. The goal of this project is to create a web solution for running and evaluating clone detectors. </p>

<p>	The system will allow users to select and parameterize a set of clone detectors on an uploaded dataset (or upload a new one). The web interface is also a web-app where a user can explore the database and see the actual source code of the clone pairs. Then, it offers the possibility for any user to evaluate clone pairs (either on their own private results or the public benchmark).</p>
<p> Important! Please allow Code Clones Benchmark to set cookies on your computer. This is necessary to access user accounts.</p>
<p> <strong> Please sign in or register to begin.</strong></p>
        <input type="text" class="input-block-level" placeholder="Username" name="txtuname" required />
        <input type="password" class="input-block-level" placeholder="Password" name="txtupass" required />
      <hr />
        <button class="btn btn-large btn-primary" type="submit" name="btn-login">Sign in</button>
        <a href="signup.php" style="float:right;" class="btn btn-large">Register</a><hr />
        <a href="fpass.php">Lost your Password ? </a>
      </form>

    </div> <!-- /container -->
    <script src="bootstrap/js/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
