<?php
class GetRemoteImage {

 var $source;
 var $save_to;

function download() 
{
  $new_name = basename($this->source);
  $save_to = $this->save_to.$new_name;
  $save_image = $this->LoadImageCURL($save_to);
  return $save_image;
}

function LoadImageCURL($save_to)
{
  $ch = curl_init($this->source);
  $fp = fopen($save_to, "wb");

  $options = array(CURLOPT_FILE => $fp,
                   CURLOPT_HEADER => 0,
                   CURLOPT_FOLLOWLOCATION => 1,
             //      CURLOPT_RETURNTRANSFER => TRUE, 
                 CURLOPT_TIMEOUT => 60); // 1 minit timeout

  curl_setopt_array($ch, $options);
  $save = curl_exec($ch);

 /* Check for 404 (file not found). */     
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);
  fclose($fp);
  //echo ("http code = ".$httpCode."<br>");
  if ($httpCode != 200 ) {         
   $save =  false;  
   unlink($save_to);   // bezveze fora, ispravi nekad
   //echo ($save_to." deleted, 404" );
  } 
  
  return $save;
}

}
?>
