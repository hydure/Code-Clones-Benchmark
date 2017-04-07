<?php
session_start();
$clone_selected = intval($_POST['clone_selected']);
//echo $clone_selected;
//if (!empty($_POST['row'])) {
//$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
//if(!$con) {
 //       die('could not connect: ' . mysqli_connect_error());
//}
?>
<html>
    <head>
    </head>
<body>
    <h1>Test iframe</h1>
    <iframe id="test_iframe" src="about:blank" width=1000 height=400></iframe>
	<button onClick="javascript:injectHTML();">Inject HTML</button>
</body>

<script language="javascript">

function injectHTML() {
	//alert('shame');
	//step 1: get the DOM object of the iframe.
	var iframe = document.getElementById('test_iframe');
//alert('Cannot inject dynamic contents into iframe.');


	<?php 
	$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
	if(mysqli_connect_errno()) {
	    die("MySQL connection failed: ". mysqli_connect_error());
	}
	$start_array = array();
	$end_array = array(); 
	$sql = "SELECT start, end FROM Clones where cloneID = '$clone_selected'";
	$result = $con->query($sql);
	while ($row = $result->fetch_assoc()) {
	  unset($start, $end);
	  $start = $row['start'];
	  $end = $row['end'];
	  array_push($start_array, $start);
	  array_push($end_array, $end);
	} 
	array_push($start_array, 0); //temp fix for out of index array error
	array_push($end_array, 0); //temp fix for out of index array error
	$con->close();

	$code_array = array();
	$line_counter = 1;
	$array_counter = 0;
	$highlighting = false;
	array_push($code_array, '<pre>');
	$handle = fopen('/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractTableRendering.java', "r");
	if ($handle) {
		while (($line = fgets($handle)) != false) {
			if ($line_counter == $start_array[$array_counter] || $highlighting) {
				$line = '<code><mark>' . substr($line, 0, -1) . '</mark></code><br>';
				
				if ($highlighting == false) {
					$highlighting = true;
				}
			} else {
				$line = '<code>' . substr($line, 0, -1) . '</code><br>';				
			}
			array_push($code_array, $line);
			$line_counter += 1; 
			if ($line_counter == $end_array[$array_counter]) {
				$highlighting = false;
				if ($array_counter <= count($start_array)) {
					$array_counter += 1;
				}
			} 
		} 
		fclose($handle);
	}
	array_push($code_array, '</pre>');
	$code_string = implode("", $code_array);
	$code_string = json_encode($code_string, JSON_HEX_TAG);
	
	?>


	var css = '<style>pre{counter-reset: line;}code{counter-increment: line;}code:before{content: counter(line); -webkit-user-select: none; display: inline-block; border-right: 1px solid #ddd; padding: 0 .5em; margin-right: .5em;}</style>';
	
	var code = <?php echo $code_string; ?>;
	//var code = 'penis';


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

</script> 
</html>