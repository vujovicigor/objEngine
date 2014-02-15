<?php	
  session_start();
  require_once( 'Michelf/MarkdownExtra.inc.php');

  $_SESSION['_SESSION_UID'] = session_id();
  error_reporting(E_ALL);
  $serviceURL = 'http://ws.mobilearea.info/objengineservice.php';
require_once('licencekey.php');

function attributesToObject($objPropertiesStr=''){
  $objProperties=array();
  $pxml = simplexml_load_string("<f><objProp $objPropertiesStr /></f>");
  foreach($pxml->objProp[0]->attributes() as $pk => $pv)
  {
    $objProperties[(string)$pk]=(string)$pv;
    //echo (string)$pk.'='.(string)$pv."</br>";
  }
  return $objProperties;
}

function getObjectFromDb($objectName, $ObjAlias, $objProperties){
  global $serviceURL;
  global $licenceKey;

  $objectId = '';
//  $object_Name = str_replace(".", "_", $objectName);
  $object_Name = str_replace(".", "_", $ObjAlias);

  // TODO: Dynamic, check type 
  if (isset($_REQUEST[$object_Name.'_limit'])) $objProperties['limit']= $_REQUEST[$object_Name.'_limit'];
  if (isset($_REQUEST[$object_Name.'_id'])) $objProperties['id']= $_REQUEST[$object_Name.'_id'];
  $objProperties['objectname'] = $objectName;
  $objProperties['_licenceKey'] = $licenceKey;

  foreach ( $objProperties as $get_key => $get_value)
    if(substr((string)$get_key,0,9) == '_SESSION_')
      unset($objProperties[(string)$get_key]);	
  foreach ( $_SESSION as $get_key => $get_value)
    $objProperties[$get_key] = (string)$get_value ; 

  //  print_r($objProperties);
  ////
  //$content = json_encode("data to send");
  //$content = array('objectname' => $objectName, 'last' => 'Smith'); 
  $curl = curl_init($serviceURL);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $objProperties);

  $json_response = curl_exec($curl);

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  if ( $status != 200 ) {
      die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
  }

  curl_close($curl);
  //echo ($json_response );
  $response = json_decode($json_response, true);
  return $response;
}

function populateHtmlFromObject($src, $ObjData, $objName, $ObjAlias, $htmlspecialchars=true){
	$res='';
	//$dkeys = array_keys($ObjData[0]);
	//echo "src=$rval ";
	if ($htmlspecialchars)
	  foreach ($ObjData as $objRow) {
	      $rval = $src;
	      foreach (array_keys($objRow) as $kName){
	      $rval = str_ireplace("\$$ObjAlias.$kName", htmlspecialchars($objRow[$kName], ENT_QUOTES), $rval);
	    }
	    $res .= $rval; //."\n\r";
	  }
	else
	  foreach ($ObjData as $objRow) {
	      $rval = $src;
	      foreach (array_keys($objRow) as $kName){
	      // Dodatak spec za Frontech HelpTopic, tj za svako polje koje u nazivu ima "markdown"
	      if(stripos($kName,"markdown")===FALSE)
          $rval = str_ireplace("\$$ObjAlias.$kName", $objRow[$kName], $rval);
          else
          {
	          $rval = str_ireplace("\$$ObjAlias.$kName", \Michelf\MarkdownExtra::defaultTransform($objRow[$kName]), $rval);
	          $rval = preg_replace('/src=\"(.*)\"/i', 'src="objengine/image.php?filename=$1"', $rval);
          }
	    }

	    $res .= $rval; //."\n\r";
	  }

  	return $res;
}

function startParse($fHTML){
	return parseEng(parseEng(parseEng($fHTML)));
}

function parseEng($fHTML){
  preg_match_all('/<!-- *Fetch\.([\S]*)\s(.*)-->([\S\s]*?)<!-- *End *Fetch\.\1 *-->/i', $fHTML, $matches, PREG_OFFSET_CAPTURE );
	// $matches
	// [0][0..n][0] - full (with <!-- -->)
	// [0][0..n][1] - strPos
	//
	// [1][0..n][0] - Object name
	// [1][0..n][1] - strPos
	//
	// [2][0..n][0] - Obj properties
	// [2][0..n][1] - strPos
	//
	// [3][0..n][0] - Inner html (without <!-- -->) 
	// [3][0..n][1] - strPos

  $outHTML=$fHTML;
//print_r($matches);
  foreach ($matches[1] as $ix => $val) {
    $ObjName = $val[0];
    $ObjAlias = $ObjName; 
    $ObjPropertiesStr = $matches[2][$ix][0];
    $innerHtml = $matches[3][$ix][0];
    //echo( ' Obj name: '.$ObjName."\n\r");
    //echo( ' ix: '.$ix."\n\r");
    //echo( ' innerHtml: '.$innerHtml."\n\r");
    //print_r($ObjProperties);
    $objProperties = attributesToObject($ObjPropertiesStr);
    if (isset($objProperties['alias']))
      $ObjAlias = $objProperties['alias'];
      //$objProperties['alias'] = $ObjName;

    $ObjDataArray = getObjectFromDb( $ObjName, $ObjAlias, $objProperties);
    //print_r($ObjDataArray);
    $htmlspecialchars =(isset($objProperties['htmlspecialchars']) && (strtolower($objProperties['htmlspecialchars']))=='true');
    $parsed = populateHtmlFromObject($innerHtml, $ObjDataArray, $ObjName, $ObjAlias, $htmlspecialchars);
    $outHTML = str_replace($matches[0][$ix][0], $parsed, $outHTML);
  }
  //echo "\n\r $outHTML";
  return $outHTML;	
}
// TODO
function sortByLen($a,$b){
    return strlen($b)-strlen($a);
}

//usort($array,'sortByLen');
?>



