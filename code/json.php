    <!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8" />
    <title></title>
    <link href="style.css" rel="stylesheet" />
    <script src="http://code.jquery.com/jquery-2.1.3.min.js" data-semver="2.1.3" data-require="jquery"></script>
    <script src="script.js"></script>
    <script>
    function sendJSON() {
   var dataToStringify = 'pretzels';
   var stringifiedData = JSON.stringify(dataToStringify);
   obj = {}
   obj.name = "sam"
   obj.value = "12345"

		   jQuery.ajax({
		      url: 'echo.php',
		      type: "post",
		      data: obj,
		      dataType:"json",
		      //data: {stringified: stringifiedData},
		      success: function(data) {
		      	alert("h");
//		      	var obj = jQuery.parse(JsonData);

		      },
		      error(XMLHttpRequest, textStatus, errorThrown) {
		         //code to handle errors
		      }
		   });

		}


	</script>

	</head>
	<form action='#'>
	<button id="detector_button" onClick="javascript:sendJSON();">Generate Datasets</button>
	</form>
</html>
