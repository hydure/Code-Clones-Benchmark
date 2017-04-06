<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in())
{
 $user_home->redirect('index.php');
}

$stmt = $user_home->runQuery("SELECT * FROM Accounts WHERE userId=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

?>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">

<?php 
$code_file = file_get_contents('/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractTableRendering.java');
$code_file = nl2br($code_file);
$code_file = json_encode($code_file, JSON_HEX_TAG);
?>

function injectHTML(){

  //step 1: get the DOM object of the iframe.
  var iframe = document.getElementById('iframe_one');


  //step 1.5: get the correct string to be printed!
  <?php
  $code_array = array();
  array_push($code_array, '<pre>');
  $handle = fopen('/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractTableRendering.java', "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      $line = '<code>' . substr($line, 0, -1) . '</code><br>';
      array_push($code_array, $line);
      //echo $line;
    }
    fclose($handle);
  }
  array_push($code_array, '</pre>');
  $code_string = implode("", $code_array);
  $code_string = json_encode($code_string, JSON_HEX_TAG);
  ?>


  var css = '<style>pre{counter-reset: line;}code{counter-increment: line;}code:before{content: counter(line); -webkit-user-select: none; display: inline-block; border-right: 1px solid #ddd; padding: 0 .5em; margin-right: .5em;}</style>';
  var code = <?php echo $code_string; ?>;
  var html_string = css + '<html><head></head><body><p>' + code + '</p></body></html>';
  /* if jQuery is available, you may use the get(0) function to obtain the DOM object like this:
  var iframe = $('iframe#target_iframe_id').get(0);
  */

  //step 2: obtain the document associated with the iframe tag
  //most of the browser supports .document. Some supports (such as the NetScape series) .contentDocumet, while some (e.g. IE5/6) supports .contentWindow.document
  //we try to read whatever that exists.
  var iframedoc = iframe.document;
    if (iframe.contentDocument)
      iframedoc = iframe.contentDocument;
    else if (iframe.contentWindow)
      iframedoc = iframe.contentWindow.document;

   if (iframedoc){
     // Put the content in the iframe
     iframedoc.open();
     iframedoc.writeln(html_string);
     iframedoc.close();
   } else {
    //just in case of browsers that don't support the above 3 properties.
    //fortunately we don't come across such case so far.
    alert('Cannot inject dynamic contents into iframe.');
   }


}

$(document).ready(function() {
  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });
});
</script>
<html lang="en">
<!-- still need to create sidebar, etc. -->
<head>
	<title>Code Clones Benchmark</title>
	<link href="CCB1.1.css" type = "text/css" rel="stylesheet">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<div class="page-container">
  
	<!-- top navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
       <div class="container">
    	<div class="navbar-header">
           <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".sidebar-nav">
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>
           <a class="navbar-brand" href="#">Code Clones Benchmark</a>
           <div style="position: absolute; top: 8; right: 70; width: 80px; height: 30px;">
              <input type="button" onclick="location.href='logout.php';" value="Logout" 
            class="btn btn-primary center-block" />
           </div>
           <div style="position: absolute; top: 15; right: 170;">
            <?php echo "Hello, " . ($_SESSION['userName']); ?>
           </div>
    	</div>
       </div>
    </div>
      
    <div class="container">
      <div class="row row-offcanvas row-offcanvas-left">
        
        <!-- sidebar -->
        <div class="col-xs-6 col-sm-1 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome.php">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li class"active"><a href="#">Reports</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>
            </ul>
        </div>
        <!-- main area -->
        <div class="col-xs-12 col-sm-11">

            <!--frames for adding results. each iframe should contain one set-->
            <!--add inside of quotes after iframe src=" "-->
            
            <button onClick="javascript:injectHTML();">Inject HTML</button>
            </div>

            <div align="center">
                <iframe id="iframe_one" width=60% height=70%></iframe>
               <!-- <iframe id="iframe_two" width=40% height=70%></iframe> -->
            </div>
            <!--frames for adding results above-->
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>