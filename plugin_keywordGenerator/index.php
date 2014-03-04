<?php
	error_reporting(1);
	include ('lib\class.colossal-mind-mb-keyword-generator.php');
	$mysqli = new mysqli("localhost", "mobilepoc", "m1mos#", "mobile_laravel");

	// Define table to be pulled in keyword search
	$table = ['device', 'user', 'app']; 

	// table column to be included in tags
    $add = ['device_name', 'device_model', 'device_os_version', 'email', 'job_title', 'division', 'department', 'alias', 'app_detail', 'app_name'];


/*
	app - alias, app_detail, platform
	device - user_id, device_model_no, device_model, product_name, platform_id, device_type, device_version, 
	user - employee_id, cn, email, job_title, division, department
*/
	// Scroll thru all the tables and extract all values
	foreach ($table as $v) {
		$sql = "SELECT * FROM $v;";
		$getquery = $mysqli->query($sql);
	    while ($row = $getquery->fetch_assoc()) {
	    	$results[$v][] = $row;
		}
	}

	// Pull out all possible tags, limited to $add values
    $i = 0;
    foreach ($results as $tabId => $result) {
    	foreach ($result as $k1) {
	    foreach ($k1 as $k2 => $v2) {
	    	if(findInclude($k2, $add)){	
    			$v2 = strtolower($v2);
	    		if(str_word_count($v2) > 4){
    				$params = array('content'=> $v2,'encoding'=> 'utf-8','lang'=> 'en_GB','ignore'=> array('zh_CN', 'zh_TW', 'ja_JP'),'min_word_length'=> 4,'min_word_occur'=> 3,'min_2words_length'=> 4,'min_2words_phrase_length'=> 10,'min_2words_phrase_occur'=> 3,'min_3words_length'=> 4,'min_3words_phrase_length'=> 12,'min_3words_phrase_occur'=> 2);
					$keyword = new colossal_mind_mb_keyword_gen($params);
					$temp = explode(',', $keyword->get_keywords());
    				$temp = array_filter($temp);
	    			foreach ($temp as $k3) {
	    				$tags[$i] = $k3;
	    				$tagRel[$i] = array('tagId'=> $i, 'tabId'=> $k1['id'], 'table'=> $tabId);
	    				$i++;
	    			}
	    		} else {
	    			$tags[$i] = strip_tags($v2);
    				$tagRel[$i] = array('tagId'=> $i, 'tabId'=> $k1['id'], 'table'=> $tabId);
    				$i++;
	    		}
	    	}
	    }
	    }
    }

	// Bitches need a good cleaning
	$tags = cleanTags($tags);
	// DB Insert $tags
	pushIntoDB('search_tags', ['id', 'keyword'], $tags, $mysqli);

	// Generate Tags vs Contents Relation table
	$left = array_keys($tagRel);
	$right = array_keys($tags);
	$diff = array_diff($left, $right);

	// Tally back $tagRel to $tags(these bitches cleaned already)
	foreach ($diff as $key) {
		unset($tagRel[$key]);
	} 
	pushIntoDB('search_tags_rltn', ['id', 'tagId', 'tabId', 'table'], $tagRel, $mysqli);
	// DB Insert $tagRel
	// echo json_encode($tagRel,TRUE);

	// Generate Tags vs Tags Relation table
	// Compare $tags against it's values similarity, 50% above
	$temp = $tags;
	foreach ($tags as $k1 => $v1) {
		$similar[$k1] = array();
		foreach ($temp as $k2 => $v2) {
			similar_text($v1, $v2, $percent); 
			if($percent>=50 && $percent!=100){
				// echo $k1.$v1."|".$k2.$v2." ----------------- ".$percent."</br>";	
				array_push($similar[$k1], $k2);
			}
		}
		$tags2[] = array('tagId' => $k1,'similar' => implode(",", $similar[$k1]));
	}

	// DB Insert $tags2
	pushIntoDB('search_tags_smlr', ['id', 'tagId', 'tagsId'], $tags2, $mysqli);

	function pushIntoDB($table, $column, $data, $mysqli){
		$truncate = "TRUNCATE `$table`;";
		$mysqli->query($truncate);

		$insert = "INSERT INTO `$table` (`".implode("`, `", $column)."`) VALUES ";

		foreach ($data as $k1 => $v1) {
			if(!is_array($v1)){
				$insert .= "($k1, '$v1'), ";
			} else {
				$temp = array_values($v1);
				$insert .= "(".$k1.", '".implode("', '", $temp)."'), ";				
			}
		}
		$insert = substr($insert, 0, -2); // delete last comma

		$result = $mysqli->query($insert);
		if (!$result) {
			echo $truncate."</br>".$insert;
			printf("%s\n", $mysqli->error);
			exit();
		}
	}


    function cleanTags($tags){
    	$tags = array_unique($tags);
    	$tags = array_filter($tags);
    	return $tags;
    }

    function findInclude($str, $add){
    	foreach ($add as $k => $v) {
    		if($v == strtolower($str)){
    			return true;
    		} 
    	}
    }
?>