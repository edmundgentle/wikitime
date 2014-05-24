<?php
function strtodate($str,$now=null) {
	$sst=$str;
	$str=preg_replace('/\s{2,}|^\s|\s$/',' ',$str);
	$str=trim(strtolower(preg_replace('/[\t\r\n]/',' ',$str)));
	if(is_null($now)) {
		$now=date("Y/m/d H:i:s");
	}
	if(is_numeric($str)) {
		return $str;
	}
	if($str=='now') {
		return $now;
	}
	$year='';
	$month='';
	$day='';
	$time='';
	if(preg_match('/([0-9]{1,2}):([0-9]{2})(:([0-9]*))?\s?((am)|(pm))?/',$str,$m)) {
		$str=trim(str_replace($m[0],'',$str));
		$time=' ';
		if(isset($m[6]) and $m[6]=='pm') {
			$m[1]+=12;
		}
		if($m[1]<24 and $m[1]>=0) {
			if(strlen($m[1])==1) {
				$m[1]='0'.$m[1];
			}
			$time.=$m[1];
			if($m[3]>=0 and $m[3]<61) {
				if(strlen($m[3])==1) {
					$m[3]='0'.$m[3];
				}
				$time.=':'.$m[3];
				if($m[5]>0 and $m[5]<61) {
					if(strlen($m[5])==1) {
						$m[5]='0'.$m[5];
					}
					$time.=':'.$m[5];
				}
			}
		}
	}
	if(preg_match('/^([0-9]{1,4})([\/-])([0-9]{1,2})[\/-]([0-9]{1,4})$/',$str,$m)) {
		if($m[2]=='-') {
			$year=$m[1];
			$month=$m[3];
			$day=$m[4];
		}else{
			if($m[1]>31) {
				$year=$m[1];
				$month=$m[3];
				$day=$m[4];
			}else{
				$year=$m[4];
				if($m[3]>12) {
					$month=$m[1];
					$day=$m[3];
				}else{
					$month=$m[3];
					$day=$m[1];
				}
			}
		}
	}else{
		$months=array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
		foreach($months as $n=>$m) {
			if(strpos($str,$m)>-1) {
				$month=$n;
			}
		}
		if($month) {
			if(preg_match('/([0-9]{1,2})((st)|(nd)|(rd)).*?([0-9]{1,4})\b/',$str,$m)) {
				$day=$m[1];
				$year=$m[6];
			}elseif(preg_match('/(('.$months[$month].')(\w)*) ([0-9]{1,2}), ([0-9]{1,4})\b/',$str,$m)) {
				$day=$m[4];
				$year=$m[5];
			}elseif(preg_match('/([0-9]{1,2}) (('.$months[$month].')(\w)*) ([0-9]{1,4})\b/',$str,$m)) {
				$day=$m[1];
				$year=$m[5];
			}elseif(preg_match('/(('.$months[$month].')(\w)*) ([0-9]{1,2})\b/',$str,$m)) {
				$day=$m[4];
				$year=format_date('Y',$now);
			}
		}
		if(!$year) {
			if(preg_match('/([0-9]{3,4})/',$str,$m)) {
				$year=$m[1];
			}
		}
	}
	$op='';
	//include relativity!
	if(is_numeric($year)) {
		$op.=$year;
		if(is_numeric($month)) {
			if(strlen($month)==1) {
				$month='0'.$month;
			}
			$op.='/'.$month;
			if(is_numeric($day)) {
				if(strlen($day)==1) {
					$day='0'.$day;
				}
				$op.='/'.$day;
				if(strlen($time)) {
					$op.=$time;
				}
			}
		}
	}
	if(!$op) {
		echo $sst."\n";
	}
	return $op;
}
function format_date($format,$date) {
	if(strlen($format)==1) {
		$d=array();
		if(strpos($date,' ')) {
			list($d['d'],$d['t'])=explode(' ',$date,2);
		}else{
			$d['d']=$date;
			$d['t']='';
		}
		$d['d']=explode('/',$d['d']);
		$d['t']=explode(':',$d['t']);
		switch($format) {
			case 'd':
				if(isset($d['d'][2])) {
					if(strlen($d['d'][2])==1) {
						return '0'.$d['d'][2];
					}else{
						return $d['d'][2];
					}
				}
				break;
			case 'j':
				if(isset($d['d'][2])) {
					if($d['d'][2]{0}==0) {
						return $d['d'][2]{1};
					}else{
						return $d['d'][2];
					}
				}
				break;
			case 'S':
				if(isset($d['d'][2])) {
					$sb=substr($d['d'][2],-1);
					if($d['d'][2]{0}=='1' and strlen($d['d'][2])==2) {
						return 'th';
					}
					if($sb==1) {
						return 'st';
					}
					if($sb==2) {
						return 'nd';
					}
					if($sb==3) {
						return 'rd';
					}
					return 'th';
				}
				break;
			case 'F':
				$months=array(1=>"January",'February','March','April','May','June','July','August','September','October','November','December');
				if(isset($d['d'][1])) {
					if($d['d'][1]{0}==0) {
						return $months[$d['d'][1]{1}];
					}else{
						return $months[$d['d'][1]];
					}
				}
				break;
			case 'm':
				if(isset($d['d'][1])) {
					if(strlen($d['d'][1])==1) {
						return '0'.$d['d'][1];
					}else{
						return $d['d'][1];
					}
				}
				break;
			case 'M':
				$months=array(1=>"Jan",'Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
				if(isset($d['d'][1])) {
					if($d['d'][1]{0}==0) {
						return $months[$d['d'][1]{1}];
					}else{
						return $months[$d['d'][1]];
					}
				}
				break;
			case 'n':
				if(isset($d['d'][1])) {
					if($d['d'][1]{0}==0) {
						return $d['d'][1]{1};
					}else{
						return $d['d'][1];
					}
				}
				break;
			case 't':
				$fe=28;
				if($d['d'][0]/4==0) {$fe=29;}
				$months=array(1=>31,$fe,31,30,31,30,31,31,30,31,30,31);
				if(isset($d['d'][1])) {
					return $months[$d['d'][1]];
				}
				break;
			case 'L':
				if($d['d'][0]/4==0) {
					return true;
				}
				return false;
				break;
			case 'Y':
				return $d['d'][0];
				break;
			case 'y':
				return substr($d['d'][0],-2);
				break;
			case 'a':
				if(isset($d['t'][0])) {
					if($d['t'][0]>12) {
						return 'pm';
					}else{
						return 'am';
					}
				}
				break;
			case 'A':
				if(isset($d['t'][0])) {
					if($d['t'][0]>12) {
						return 'PM';
					}else{
						return 'AM';
					}
				}
			break;
			case 'G':
				if(isset($d['t'][0])) {
					if($d['t'][0]>12) {
						return $d['t'][0]-12;
					}else{
						return $d['t'][0];
					}
				}
			break;
			default:
				return $format;
				break;
		}
		return '';
	}else{
		$op='';
		for($x=0;$x<strlen($format);$x++) {
			$op.=format_date($format{$x},$date);
		}
		return $op;
	}
}
function remove_stopwords($sentence) {
	$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
	foreach ($stopwords as $word) {
		$word = rtrim($word);
		$sentence = preg_replace("/\b$word\b/i", "", $sentence);
	}
	return $sentence;
}
function remove_flowwords($sentence) {
	$stopwords=array("later","subsequently","furthermore");
	foreach ($stopwords as $word) {
		$word = rtrim($word);
		$sentence = preg_replace("/\b$word\b/i", "", $sentence);
	}
	return $sentence;
}
function remove_from_beginning($prefix, $str) {
	if (strtoupper(substr($str, 0, strlen($prefix))) == strtoupper($prefix)) {
	    $str = substr($str, strlen($prefix), strlen($str) );
	}
	return $str;
}
function make_sense($story, $matches) {
	if(count($matches)>1) {
		//sort out duplication
	}
	foreach($matches as $m) {
		$story=remove_from_beginning('In '.$m,$story);
		$story=remove_from_beginning('On '.$m,$story);
		$story=remove_from_beginning('As of '.$m,$story);
	}
	$story=str_replace('&#160;',' ',$story);
	$story=preg_replace('/(—(.*?)—)|(\((.*?)(\)|$))/',' ',$story);
	$story=remove_flowwords($story);
	$story=trim($story,' ,.');
	$story=ucfirst($story);
	global $inlinks;
	foreach($inlinks as $n=>$l) {
		if(!is_numeric($n)) {
			$story=preg_replace('/\b('.$n.')\b/i','<a href="'.$l.'">$1</a>',$story);
		}
	}
	return $story;
}
function str_compare($str1, $str2) {
    $count = 0;
    
    $str1 = preg_replace("/\W/i", ' ', strtolower($str1));
    while(strstr($str1, '  ')) {
        $str1 = str_replace('  ', ' ', $str1);
    }
    $str1 = explode(' ', $str1);
    
    $str2 = preg_replace("/\W/i", ' ', strtolower($str2));
    while(strstr($str2, '  ')) {
        $str2 = str_replace('  ', ' ', $str2);
    }
    $str2 = explode(' ', $str2);
    
    if(count($str1)<count($str2)) {
        $tmp = $str1;
        $str1 = $str2;
        $str2 = $tmp;
        unset($tmp);
    }
    
    for($i=0; $i<count($str1); $i++) {
        if(in_array($str1[$i], $str2)) {
            $count++;
        }
    }
    
    return $count/count($str2)*100;
}
function compare_stories($story1,$story2) {
	if(isset($story1['story']) and isset($story2['story'])) {
		if(strtoupper($story1['story'])==strtoupper($story2['story'])) {
			$story1['ti']=true;
			return array($story1);
		}
		if(strlen($story1['story'])>strlen($story2['story'])) {
			if(strpos(strtolower($story1['story']),strtolower($story2['story']))!==false) {
				$story1['ti']=true;
				return array($story1);
			}
		}else{
			if(strpos(strtolower($story2['story']),strtolower($story1['story']))!==false) {
				$story2['ti']=true;
				return array($story2);
			}
		}
		if(str_compare($story1['story'],$story2['story'])>80) {
			if(strlen($story1['story'])>strlen($story2['story'])) {
				$story1['ti']=true;
				return array($story1);
			}else{
				$story2['ti']=true;
				return array($story2);
			}
		}
	}
	return array($story1,$story2);
}
function remove_duplicates($story) {
	$is_key=false;
	$ts=array();
	foreach($story as $s) {
		if(isset($s['key'])) {
			$is_key=true;
			$ts=$s;
			break;
		}
	}
	if($is_key) {
		$fs=array();
		foreach($story as $s) {
			if(isset($s['src'])) {
				$fs[]=$s;
			}elseif(!isset($s['key'])) {
				if($s['story']>$ts['additional_info']) {
					$ts['additional_info']=$s['story'];
				}
			}
		}
		$fs[]=$ts;
		return $fs;
	}
	if(count($story)>1) {
		if(count($story)==2) {
			$story=compare_stories($story[0],$story[1]);
		}
		/*elseif(count($story)==3) {
			$s1=compare_stories($story[0],$story[1]);
			if(count($s1)==1) {
				$s2=compare_stories($s1[0],$story[2]);
			}else{
				$s2=compare_stories($s1[0],$story[2]);
			}
		}*/
	}
	return $story;
}
?>