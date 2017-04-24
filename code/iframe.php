<?php
session_start();
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
	//step 1: get the DOM object of the iframe.
	var iframe = document.getElementById('test_iframe');
	var html_string = '<html>HELLO</html>';
	/* if jQuery is available, you may use the get(0) function to obtain the DOM object like this:
	var iframe = $('iframe#target_iframe_id').get(0);
	*/

	//step 2: obtain the document associated with the iframe tag
	//most of the browser supports .document. Some supports (such as the NetScape series) .contentDocumet, while some (e.g. IE5/6) supports .contentWindow.document
	//we try to read whatever that exists. 
/**	var iframedoc = iframe.document;
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
	 **/
	injectIframeContent(iframe, "hello");
}

function injectIframeContent(iframe, code) {
  var script1 = "<link rel='stylesheet' type='text/css' href='hlns.css' media='screen'>";
  var script2 = "<script src='//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/highlight.min.js'>";
  var script3 = "<script type='text/javascript' src='highlightjs-line-numbers.min.js'>";
  var scriptA = "</";
  var scriptB = "script>";
  var scriptC = scriptA + scriptB;
  var script4 = "<script>hljs.initHighlightingOnLoad();hljs.initLineNumbersOnLoad();";
  var script = script1 + script2 + scriptC + script3 + scriptC + script4 + scriptC;
  var html_string = script + '<html><head></head><body><p>' + code + '</p></body></html>';
  html_string = "<html>HELLO</html>";
  alert(html_string);
  //step 2: obtain the document associated with the iframe tag
  var iframedoc = iframe.document;
    if (iframe.contentDocument)
      iframedoc = iframe.contentDocument;
    else if (iframe.contentWindow)
      iframedoc = iframe.contentWindow.document;

   if (iframedoc){
     iframedoc.open();
     iframedoc.writeln(html_string);
     iframedoc.close();
   } else {
    alert('Cannot inject dynamic contents into iframe.');
   } 
}
</script> 
</html>