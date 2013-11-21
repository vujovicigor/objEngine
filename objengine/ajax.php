<?php
	include_once 'buildhtmlclient.php';
    $objProperties = $_REQUEST;
	if(isset($_REQUEST) && isset($_REQUEST["ObjName"])){
		$ObjDataArray = getObjectFromDb($_REQUEST["ObjName"], $objProperties);
		echo json_encode($ObjDataArray);
		return;
	}
	
?>
