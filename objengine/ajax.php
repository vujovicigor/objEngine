<?php
session_start();
//if ($_SESSION['loginTdummyryCount']) echo 1; else echo 0;

include_once 'buildhtmlclient.php';
$objProperties = $_REQUEST;
if(isset($_REQUEST) && isset($_REQUEST["objectname"])){
	$ObjName = $_REQUEST["objectname"]; // TODO: toLowercase
	if ($ObjName == 'login'){
//print_r($_SESSION);
		$ObjName = 'SESSION';
		//$objProperties=[];
		$objProperties['UserName'] = (isset($_REQUEST['UserName']) ? $_REQUEST['UserName']:'');
		$objProperties['Password'] = (isset($_REQUEST['Password']) ? $_REQUEST['Password']:'');  

		$ObjDataArray = getObjectFromDb($ObjName, $objProperties);
		$res = $ObjDataArray;
		//print_r($res);
		if (!empty($res)){
			//$_SESSION = $res[0];
			foreach ( $res[0] as $get_key => $get_value)	
				{  $_SESSION['_SESSION_'.$get_key] = (string)$get_value ; } // TODO: skip password
			echo "Login ok ";
			print_r($_SESSION);
		}
		else{
			$_SESSION['loginTryCount'] = (isset($_SESSION['loginTryCount']) ? $_SESSION['loginTryCount']+1:1);
			echo "login attempt ".$_SESSION['loginTryCount'];
		}
		return;

	}
	else
	{
		$ObjDataArray = getObjectFromDb($ObjName, $objProperties);
		echo json_encode($ObjDataArray);
		//print_r($ObjDataArray);
		return;
	}
}
	
?>
