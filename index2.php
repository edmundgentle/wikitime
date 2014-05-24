<?php
require_once('includes/functions.php');
$date_preg='/((((January)|(February)|(March)|(April)|(May)|(June)|(July)|(August)|(September)|(October)|(November)|(December))\W([0-9]{1,2}),\W([0-9]{1,4}))|([0-9]{1,2})\W(((January)|(February)|(March)|(April)|(May)|(June)|(July)|(August)|(September)|(October)|(November)|(December))\W([0-9]{1,4}))|(((January)|(February)|(March)|(April)|(May)|(June)|(July)|(August)|(September)|(October)|(November)|(December))\W([0-9]{1,4}))|((in )[0-9]{4})|(^[0-9]{4}$))/i';
if(isset($_GET['p'])) {
	$article=$_GET['p'];
}else{
	$article='Steve_Jobs';
}
include('dom.php');
if($article=='Steve_Jobs') {
	$html = file_get_html('sj.htm');
}else{
	$html = file_get_html('http://en.wikipedia.org/wiki/'.$article);
}
$table=$html->find('div[class=mw-content-ltr]',0);
$title=$html->find('h1[id=firstHeading]',0);
$infobox = $html->find('table[class=infobox] tbody',0);
$links=array();
$page_title=$title->plaintext;
$page_image='';
$opstr='';
$first='';
$pics=array();
$page_info=array();
$stories=array();
$inlinks=array();
if($infobox) {
	if($img=$infobox->find('a[class=image] img',0)) {
		$page_image=$img->src;
	}
	foreach($infobox->find('tr') as $tr) {
		$title=$tr->find('th',0);
		$body=$tr->find('td',0);
		if($title && $body) {
			$btext=preg_replace('/\[(.*?)\]/','',$body->plaintext);
			$btext=preg_replace('/\([0-9]{4}-[0-9]{2}-[0-9]{2}\)/','',$btext);
			$btext=str_replace('&#160;',' ',$btext);
			if(strlen($btext)) {
				$page_info[$title->plaintext]=$btext;
				if(preg_match_all($date_preg,$btext,$matches)) {
					foreach($matches[0] as $m) {
						if(!(preg_match('/((since)(.*?)'.$m.')/i',$btext) and strpos($m,'in ')!==false)) {
							$m=str_ireplace('in ',' ',$m);
							$stories[strtodate($m)][]=array('story'=>preg_replace('/\[(.*?)\]/','',$title->plaintext),'matches'=>$matches[0],'additional_info'=>$btext,'key'=>true);

						}
					}
				}
			}
		}
	}
}
//find images
foreach($table->find('div[class=thumbinner]') as $img) {
	$cap=$img->find('div[class=thumbcaption]',0);
	if(preg_match_all($date_preg,$cap->plaintext,$matches)) {
		foreach($matches[0] as $m) {
			if(!(preg_match('/((since)(.*?)'.$m.')/i',$cap->plaintext) and (strpos($m,'in ')!==false or strpos($m,'at ')!==false))) {
				$imag=$img->find('img',0);
				$m=str_ireplace('in ','',$m);
				$m=str_ireplace('at ','',$m);
				$fullsize=str_replace('/thumb/','/',$imag->src);
				$fs=explode('/',$fullsize);
				$fullsize=str_replace('/'.end($fs),'',$fullsize);
				$stories[strtodate($m)][]=array('src'=>$imag->src,'caption'=>trim($cap->plaintext),'fullsize'=>$fullsize);
			}
		}
	}
}
//find internal links
foreach($table->find('a') as $l) {
	if(substr($l->href,0,6)=='/wiki/' && strlen($l->plaintext)>4 && $l->plaintext==preg_replace('/[^\w]+/',' ', $l->plaintext) && strpos(substr($l->href,6),':')==false) {
		$inlinks[$l->plaintext]=substr($l->href,6);
	}
}
//go through paragraphs
foreach($table->find('p') as $ptag) {
	if(!$first) {
		$first=preg_replace('/([.])"/','"$1',preg_replace('/\[(.*?)\]/','',$ptag->plaintext));
	}
	$opstr.=preg_replace('/([.])"/','"$1',preg_replace('/\[(.*?)\]/','',$ptag->plaintext)).' ';
}
//important dates
preg_match_all($date_preg,$first,$matches);
$important_dates=array();
foreach($matches[0] as $m) {
	$m=str_ireplace('in ',' ',$m);
	$important_dates[]=strtodate($m);
}
$oparr=preg_split('/[.]\W/', $opstr);
$datearr=array();
$last=strtodate('now');
foreach($oparr as $string) {
	if(preg_match_all($date_preg,$string,$matches)) {
		foreach($matches[0] as $m) {
			if(!(preg_match('/((since)(.*?)'.$m.')/i',$string) and strpos($m,'in ')!==false)) {
				$m=str_ireplace('in ',' ',$m);
				$ta=array('story'=>trim($string),'matches'=>$matches[0]);
				$d=strtodate($m,$last);
				if(in_array($d,$important_dates)) {
					$ta['important']=true;
				}
				$stories[$d][]=$ta;
				$last=$d;
			}
		}
	}
}
krsort($stories);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><? echo $page_title;?> | Wiki Time</title>
		<link rel="stylesheet" href="../style.css" />
		<script type="text/javascript" src="../jquery.js"></script>
		<script type="text/javascript" src="../easing.js"></script>
		<script type="text/javascript" src="../facebox.js"></script>
		<script>
		function is_mobile() {
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
				return true;
			}
			return false;
		}
		if(is_mobile()) {
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "../mobile.css"
			}).appendTo("head");
		}
		$(function() {
			if(is_mobile()) {
				$('html, body').stop().animate({scrollTop: 0}, 1);
			}
			$('#years a').bind('click',function(event){
				var $anchor = $(this);

				$('html, body').stop().animate({
					scrollTop: $($anchor.attr('href')).offset().top
				}, 500,'easeInOutExpo');
				/*
				if you don't want to use the easing effects:
				$('html, body').stop().animate({
					scrollTop: $($anchor.attr('href')).offset().top
				}, 1000);
				*/
				event.preventDefault();
			});
			$("#sidebar .social-links a").hover(
			  function () {
			    $(this).animate({'margin-left': '13px'}, 200,'easeInOutExpo');
			  }, 
			  function () {
			    $(this).animate({'margin-left': '3px'}, 200,'easeInOutExpo');
			  }
			);
			$('a[rel*=facebox]').facebox();
			resize_timeline();
			window.setTimeout("resize_timeline()",500);
			window.setTimeout("resize_timeline()",2500);
			window.setTimeout("resize_timeline()",5000);
		});
		function resize_timeline() {
			var first_aub=$('.aub').first();
			var last_aub=$('.aub').last();
			$('.timeline_line').css('height',last_aub.offset().top-first_aub.offset().top);
		}
		</script>
	</head>
	<body>
		<div class="container">
			<div class="title">
				<h1><a href="../">Wiki Time</a></h1>
			</div>
			<div class="infopane">
				<h2><? echo $page_title;?></h2>
				<? if($page_image) {?>
				<div class="image"><img src="<? echo $page_image;?>" /></div>
				<? }?>
				<ul class="info">
					<? foreach($page_info as $t=>$d) {?>
					<li><span class="description"><? echo $t;?></span> <span class="content"><? echo $d;?></span></li>
					<? }?>
				</ul>
			</div>
			<div class="timeline">
				<div class="timeline_line"></div>
				<ul class="events">
					<?
					foreach($stories as $date=>$story) {
						$story=remove_duplicates($story);
						foreach($story as $s) {
							if(isset($s['src'])) {//its a picture
								echo'<div><li class="bubble picture"><span class="aub"></span><a href="'.$s['fullsize'].'" rel="facebox"><img src="'.$s['src'].'" title="'.$s['caption'].'" /></a></li></div>';
							}elseif(isset($s['important'])) {//it's important
								$fd=format_date('jS F Y',$date);
								echo'<li class="bubble important"><span class="aub"></span><span class="date">'.$fd.'</span> <span class="main">'.make_sense($s['story'],$s['matches']).'</span></li>';
							}elseif(isset($s['key'])) {//it's key!
								$fd=format_date('jS F Y',$date);
								echo'<li class="bubble key"><span class="aub"></span><span class="main">'.$s['story'].'</span> <span class="details">'.$fd.'<br />'.make_sense($s['additional_info'],$s['matches']).'</span></li>';
							}else{//its not important
								$fd=format_date('j M Y',$date);
								echo'<li class="bubble"><span class="aub"></span><span class="date">'.$fd.'</span> <span class="main">'.make_sense($s['story'],$s['matches']).'</span></li>';
							}
						}
					}
					?>
				</ul>
			</div>
		</div>
	</body>
</html>