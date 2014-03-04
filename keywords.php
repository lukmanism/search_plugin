<?php
	error_reporting(1);
	switch ($_GET['action']) {
		// on page load
		case 'load':
			$results = getQuery('search_tags');
		break;
		// on keyword clicks, pull search id parameter for further dig
		case 'search':
			$id = (isset($_GET['id']))? $_GET['id']: $_GET['kw'];

			// if user manual search triggers
			if($_GET['kw']){	
				$id = getQuery('search_tags', " WHERE keyword LIKE '%$id%' Limit 1");
				$id = $id[0]['id'];
			}

			$tags_smlr = getQuery('search_tags_smlr', " WHERE tagId = $id Limit 1");
			$other_tags = ($tags_smlr[0]['tagsId'] != '')? "$id,".$tags_smlr[0]['tagsId']: '';

			if($other_tags == ""){ // if no related id return
				$tags_rltn = getQuery('search_tags_rltn', " WHERE tagId = $id");
			} else {
				$tags_rltn = getQuery('search_tags_rltn', " WHERE tagId IN($other_tags) ORDER BY FIND_IN_SET(tagId,'$other_tags')");
			}

			foreach ($tags_rltn as $v) { $temp[$v['tabId']] = $v['table']; }
			foreach ($temp as $k => $v) { $results[] = getQuery($v, " WHERE id = $k", true); }
		break;
	}

	// bitches needs printing
	echo json_encode($results,TRUE);

	function getQuery($table, $condition, $final){
		$mysqli = new mysqli("localhost", "mobilepoc", "m1mos#", "mobile_laravel");
		$sql = "SELECT * FROM $table $condition";
		$getquery = $mysqli->query($sql);
		// YOLO
		if($getquery){
		    while ($rows = $getquery->fetch_assoc()) {
		    	$row[] = $rows;
			}			
		}
		mysqli_close($mysqli);
		if($final){ $row = filter($row); }
		return $row;
	}

	function getIdRel($table, $condition){
		$mysqli = new mysqli("localhost", "mobilepoc", "m1mos#", "mobile_laravel");
		$sql = "SELECT * FROM $table $condition";
		$getquery = $mysqli->query($sql);

		// user define column name for value lookup
		$getColName = [
			'name',
			'username',
			'role',
		];

		if($getquery){
			$rows = $getquery->fetch_assoc();
			foreach ($rows as $key => $value) {
				if(in_array($key, $getColName)){
					return $value;
				}
			}
		}
		mysqli_close($mysqli);
	}

	function filter($row){
    	$included = ['device_name','device_model','device_model_no','device_os_version','imei','serial_number','kernel_version','base_band_version','build_number','carrier_network','app_detail','app_name','alias','app_path','app_publish_date','app_publisher','app_version_code','byte_size','employee_id','email','job_title','division','department','reporting_to','alias','cn','IP_address'];

		// user define column name for value lookup (database)
		$getIdRelDB = [
			'organization_id',
			'role_id',
			'app_status',
			'user_id',
			'device_type',
			'app_type_id',// table empty
			'enroll_type_id',// table name enrollment_type_id
		];    	

		// user define column name for value lookup (flatfile)
		$getIdRelFile = [
			'platform_id' 		=> array(1=>'Android',2=>'IOS'),
			'platform' 			=> array(1=>'Android',2=>'IOS'),
			'app_category_id' 	=> array(1=>'Phone',2=>'Tablet'),
			'app_device_type' 	=> array(1=>'Phone',2=>'Tablet',2=>'Both'),
			'app_status' 		=> array(1=>'Active',2=>'Inactive',3=>'Resigned'),
			'device_type' 		=> array(1=>'Phone',2=>'Tablet'),
			'status' 			=> array(0=>'New',1=>'Active',2=>'Inactive',3=>'Pending',4=>'Unsupported')
		];

    	foreach ($row as $k => $v) {
	    	foreach ($v as $k2 => $v2) {
	    		if(!in_array($k2, $included)){
		    		if(in_array($k2, $getIdRelDB)){
	    				if(strstr($k2,'_id')){
	    					$table = str_replace("_id", "", $k2);
	    					$row[$k][$k2] =  getIdRel($table, " WHERE id = $v2 Limit 1");
	    				}
		    		} elseif(in_array($k2, $getIdRelFile)){
	    					$row[$k][$k2] =  $getIdRelFile[$k2][$v2];
		    		} else {
    					unset($row[$k][$k2]); 
		    		}
	    		}
	    	}
    	}
    	return $row;
	}


?>