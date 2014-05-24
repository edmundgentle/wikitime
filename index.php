<?php
if(isset($_GET['q'])) {
	$url="http://en.wikipedia.org/w/api.php?action=opensearch&search=".urlencode($_GET['q']);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'wikitime');
	$result = curl_exec($ch);
	curl_close($ch);
	$res=json_decode($result,true);
	if(isset($res[1][0])) {
		header("Location: wiki/".str_replace(' ','_',$res[1][0]));
		exit();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>wiki time</title>
<style>
@font-face{
	font-family:'Basic Sans Light SF';
	src:url('basicsanslightsf.ttf');
}
*{
	outline:0;
}
body {
	height:100%;
	margin:0px;
	padding-top:10px;
	background-image:url(images/bg.gif);
}
#topbar {
	width:100%;
	background-color:#B94406;
	color:#FFFFFF;
	-moz-box-shadow: 0px 0px 2px rgba(0,0,0,0.4);
	-webkit-box-shadow: 0px 0px 2px rgba(0,0,0,0.4);
	box-shadow: 0px 0px 2px rgba(0,0,0,0.4);
}
#topbar h1 {
	text-align:center;
	font-size:100px;
	text-shadow: 2px 2px 2px rgba(0,0,0,0.4);
	margin:0px;
	margin-top:5px;
	padding:0px;
	font-weight:normal;
}
#topbar a {
	color:inherit;
	text-decoration:none;
}
#main {
	margin-left:200px;
	margin-right:200px;
	padding-top:9px;
	padding-bottom:20px;
}
#main h2 {
	font-size:30px;
	color:#B94406;
	text-shadow: 2px 2px 2px rgba(0,0,0,0.2);
	font-weight:normal;
	margin:0px;
	padding:0px;
}
#main img {
	max-height:120px;
	-moz-box-shadow: 1px 1px 1px rgba(0,0,0,0.4);
	-webkit-box-shadow: 1px 1px 1px rgba(0,0,0,0.4);
	box-shadow: 1px 1px 1px rgba(0,0,0,0.4);
	margin:5px;
	border:0px;
}
body,td,th {
	font-family: 'Basic Sans Light SF', Arial;
	font-size: 16px;
}
#facebox {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 100;
  text-align: left;
}


#facebox .popup{
  position:relative;
  border:3px solid rgba(0,0,0,0);
  -webkit-border-radius:5px;
  -moz-border-radius:5px;
  border-radius:5px;
  -webkit-box-shadow:0 0 18px rgba(0,0,0,0.4);
  -moz-box-shadow:0 0 18px rgba(0,0,0,0.4);
  box-shadow:0 0 18px rgba(0,0,0,0.4);
}

#facebox .content {
  display:table;
  width: 370px;
  padding: 10px;
  background: #fff;
  -webkit-border-radius:4px;
  -moz-border-radius:4px;
  border-radius:4px;
}

#facebox .content > p:first-child{
  margin-top:0;
}
#facebox .content > p:last-child{
  margin-bottom:0;
}

#facebox .close{
  position:absolute;
  top:5px;
  right:5px;
  padding:2px;
  background:#fff;
}
#facebox .close img{
  opacity:0.3;
}
#facebox .close:hover img{
  opacity:1.0;
}

#facebox .loading {
  text-align: center;
}

#facebox .image {
  text-align: center;
}

#facebox img {
  border: 0;
  margin: 0;
	max-width:800px;
	max-height:600px;
}

#facebox_overlay {
  position: fixed;
  top: 0px;
  left: 0px;
  height:100%;
  width:100%;
}

.facebox_hide {
  z-index:-100;
}

.facebox_overlayBG {
  background-color: #000;
  z-index: 99;
}
#inputty {
	border: 1px solid rgba(0, 0, 0, 0.1);
	width:571px;
	height:30px;
	line-height:30px;
	font:17px arial,sans-serif;
	padding-left:5px;
}
#inputty:hover {
	border: 1px solid rgba(0, 0, 0, 0.2);
}
#inputty:focus {
	border: 1px solid #B94406;
}
input[type="submit"] {
	background-image: -webkit-linear-gradient(top,#f5f5f5,#f1f1f1);
	background-color: whiteSmoke;
	background-image: linear-gradient(top,#f5f5f5,#f1f1f1);
	background-image: -o-linear-gradient(top,#f5f5f5,#f1f1f1);
	border: 1px solid rgba(0, 0, 0, 0.1);
	color: #666;
	cursor: pointer;
	font-family: arial,sans-serif;
	font-size: 14px;
	height: 29px;
	line-height: 27px;
	margin: 11px 6px;
	min-width: 54px;
	padding: 0 8px;
	text-align: center;
}
input[type="submit"]:hover {
	border: 1px solid rgba(0, 0, 0, 0.2);
}
</style>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="easing.js"></script>
<script type="text/javascript" src="facebox.js"></script>
<script>
$(function() {
	$('a[rel*=facebox]').facebox();
	$("#inputty").focus();
});


</script>
</head>

<body>
<div id="topbar">
	<h1>wiki time</h1>
</div>
<div id="main">
	<div align="center">
		<h2>What are you looking for?</h2>
		<form>
			<input type="text" id="inputty" name="q" /><br />
			<input type="submit" value="Search" />
		</form>
	</div>
</div>
</body>
</html>