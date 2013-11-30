<?php

if(!function_exists('apache_request_headers'))
{
        function apache_request_headers()
        {
                static $arh = array();
 
                if (!$arh)
                {
                        $rx_http = '/\AHTTP_/';
                        foreach ($_SERVER as $key => $val)
                        {
                                if(preg_match($rx_http, $key))
                                {
                                        $arh_key = preg_replace($rx_http, '', $key);
                                        $rx_matches = array();
                                        // do some nasty string manipulations to restore the original letter case
                                        // this should work in most cases
                                        $rx_matches = explode('_', $arh_key);
                                        if( count($rx_matches) > 0 and strlen($arh_key) > 2 )
                                        {
                                                foreach($rx_matches as $ak_key => $ak_val)
                                                {
                                                        $rx_matches[$ak_key] = ucfirst($ak_val);
                                                }
 
                                                $arh_key = implode('-', $rx_matches);
                                        }
 
                                        $arh[$arh_key] = $val;
                                }
                        }
                }
 
                return $arh;
        }
}	
  include_once 'dt.system.image.02.php';
  include_once 'class.GetRemoteImage.php';

  $imageBase='images/'; 
  $imageDir='';
  

	$fx1=(isset($_GET['x1'])?$_GET['x1']:null);
	$fy1=(isset($_GET['y1'])?$_GET['y1']:null);
	$fx2=(isset($_GET['x2'])?$_GET['x2']:null);
	$fy2=(isset($_GET['y2'])?$_GET['y2']:null);
	$fwidth=(isset($_GET['w'])?$_GET['w']:null);
	$fheight=(isset($_GET['h'])?$_GET['h']:null);
	if (isset($_GET['w']) || isset($_GET['h'])) $imageDir=$fwidth.'x'.$fheight.'/';
    
    
	$filename=trim($_GET['filename']);
	$filename = str_replace("%20", " ", $filename); 

  if (strpos($filename, '/') === false) $remoteSourcePath='';
  else { $remoteSourcePath = $filename; $filename = basename($filename);}
	
	if(trim($filename)=="") $filename="noimage.jpg";
  $ffilename= $imageBase.$imageDir.$filename;

	$headers = apache_request_headers(); 
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($ffilename))) {
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($ffilename)).' GMT', true, 304);
			return ;
	} 

	
	$cachefile = $ffilename;
	// Ako postoji slika na lokalu
	if (file_exists($cachefile)){
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($cachefile )).' GMT', true, 200);
    header('Content-Length: '.filesize($cachefile ));
    header('Content-Type: image/jpeg');
		readfile($cachefile); 

		exit(0);
		return;
  }
 
 // ako ne postoji orginalna slika (ne resajzovana)	na lokalu - dovuci je sa glavnog servera preko CURLa
  $orgfilename = $imageBase.$filename;
  if (!file_exists($orgfilename)){
    $image = new GetRemoteImage;
    if ($remoteSourcePath == '')
      $image->source = 'http://www.mobilearea.info/files/'.$filename;
     else
      $image->source = $remoteSourcePath;
      
    $image->save_to = 'images/'; 
    $get = $image->download(); 
    //if($get) echo 'Image saved.';
    
    if ($imageDir==''){  // ako ne treba resize prikazi 
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($cachefile )).' GMT', true, 200);
        header('Content-Length: '.filesize($cachefile ));
        header('Content-Type: image/jpeg');
        readfile($cachefile); 
        exit(0);
        return;
    }
  }
  // sad bi trebalo da je org slika u images/
		
		
//	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($ffilename )).' GMT', true, 200);
//	header('Content-Length: '.filesize($ffilename ));
//	header('Content-Type: image/jpeg');
   
	if (!is_dir($imageBase.$fwidth.'x'.$fheight)) 
		mkdir($imageBase.$fwidth.'x'.$fheight);

	$imager=new cimage();	//image manager

	
//	$ftype=strtolower(substr($ffilename, strlen($ffilename)-4));
	$imager->doLoad($imageBase.$filename);

/*
  echo($imager->sourceURL.'<br>');
  echo($imager->isLoaded.'<br>');
  
  echo($imager->srcW.'<br>');
  echo($imager->srcH.'<br>');
*/

  
	//if(empty($ftype))	return null;

	$imager->doResize($fwidth, $fheight, $fx1, $fy1, $fx2, $fy2);
  
  $imager->doEcho($fileName, $ffilename);

		
?>
