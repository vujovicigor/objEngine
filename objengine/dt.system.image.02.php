<?php


class cimage
{
  var $srcImage; //gd image resource
  var $sourceURL; 
  var $sourceType; 
  var $sourceTimeStamp;
  var $srcW;
  var $srcH;

  var $destImage; //gd image resource
  var $destW;
  var $destH;
  var $destX1;
  var $destX2;
  var $destY1;
  var $destY2;
  var $destQuality;

  var $isLoaded;
  var $useCache;

  function cImage($useCache=false)
  {
    $this->isLoaded = false;
    $this->destQuality = 100;
    $this->useCache = $useCache;
  }

  function doLoad($url = '', $useCache=false)
  {
    //Staviti proveru da li postoji ili ima cache
    
    if ($url != '') $this->sourceURL  = $url;
  
 //   if(!file_exists($this->sourceURL)) 
 //     $this->sourceURL = 'noimage.jpg';
 //   else
      $this->sourceTimeStamp = filectime($this->sourceURL);
    
    $this->sourceType = strtolower(substr($this->sourceURL, strlen($this->sourceURL)-4));
         
    switch($this->sourceType)
    {
    case '.gif':    $this->srcImage = imagecreatefromgif($this->sourceURL);    break;
    case '.png';    $this->srcImage = imagecreatefrompng($this->sourceURL);    break;
    case '.jpg':    $this->srcImage = imagecreatefromjpeg($this->sourceURL);     break;
    case 'jpeg':    $this->srcImage = imagecreatefromjpeg($this->sourceURL);    break;
    default:
        //TODO: "Not Available" image
        $this->isLoaded = false;
        return false;
      break;
    }

    list($this->srcW, $this->srcH, $ft, $fa) = getimagesize($this->sourceURL);
    
    $this->isLoaded = true;
    
    return true;
  }

  function doResize($newWidth=0, $newHeight=0, $x1=0, $y1=0, $x2=0, $y2=0, $keepProportion=true)
  {
    $this->destW = ($newWidth==0)? $this->srcW:$newWidth;
    $this->destH = ($newHeight==0)? $this->srcH:$newHeight; 
    $this->destImage = imagecreatetruecolor($this->destW, $this->destH);

//IGOR
    if(($newWidth==0)||($newHeight==0))
    {
    $this->destW = $newWidth;
    $this->destH = $newHeight; 

    if ($newWidth==0)
      $this->destW = ($newHeight * $this->srcW) / $this->srcH;
     else
      $this->destH = ($newWidth * $this->srcH) / $this->srcW;
    
     $this->destImage = imagecreatetruecolor($this->destW, $this->destH);

      $background=imagecolorallocate($this->destImage, 255, 255, 255);//white background;
      imagefill($this->destImage, 0, 0, $background);

      ImageCopyResampled($this->destImage, $this->srcImage, 0, 0, 0, 0, $this->destW, $this->destH, $this->srcW, $this->srcH);
      return;
    }
// -- IGOR


    //echo "-".$this->destImage.", ".$this->srcImage.", ".'0'.", ".'0'.", ".$x1.", ".$y1.", ".$this->destW.", ".$this->destH.", ".$this->srcW.", ".$this->srcH;
    if($keepProportion)
    {
      $background=imagecolorallocate($this->destImage, 255, 255, 255);//white background;
      imagefill($this->destImage, 0, 0, $background);

      $srcProportion=$this->srcW/$this->srcH;
      $dstProportion=$this->destW/$this->destH;
      $k=1;
      $dstX=0;
      $dstY=0;
      $dstH=$this->destH;
      $dstW=$this->destW;
      
      if($dstProportion>$srcProportion)
      { //ako je slika uza;
        $this->destW=$this->destH*$srcProportion;
      }
      else if($dstProportion<$srcProportion)
      { //ako je slika sira
        $this->destH=$this->destW/$srcProportion;
      }
      
      $dstX=($this->destH-$dstW)/2;
      $dstY=($this->destH-$dstH)/2;

      //echo "imagecopyresized($this->destImage, $this->srcImage, $dstX, $dstY, 0, 0, $dstW, $dstH, $this->srcW, $this->srcH);";
      // imagecopyresized(Resource id #6, Resource id #4, -100, -200, 0, 0, 200, 400, 640, 510);
      
      imagecopyresized($this->destImage, $this->srcImage, $dstX, $dstY, 0, 0, $dstW, $dstH, $this->srcW, $this->srcH);
    }
    else
    {
      if ($x1 == 0 && $y1 == 0 && $x2 == 0 && $y2 == 0)
        return imagecopyresized($this->destImage, $this->srcImage, 0, 0, 0, 0, $this->destW, $this->destH, $this->srcW, $this->srcH);

      $fsrcW = round(abs($x2 - $x1));
      $fsrcH = round(abs($y2 - $y1));

      return imagecopyresized($this->destImage, $this->srcImage, 0, 0, $x1, $y1, $this->destW, $this->destH, $fsrcW, $fsrcH);
    }
  }
    
  function doEcho($AsFileName='', $fSaveAsFile = null)
  {
    $AsFileName = ($AsFileName!='')? $AsFileName:basename($this->sourceURL);
    $AsType = strtolower(substr($AsFileName, strlen($AsFileName)-4));
    
    //$fSaveAsFile = null;
    
    switch(strtolower($AsType))
    {
    case '.gif':    
        header('Content-type: image/gif');   
        header('Content-Disposition: inline; filename="'.$AsFileName.'"');   
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $this->timeStamp).' GMT');        
        header('Content-transfer-encoding: binary');
        //header('Content-length: '.filesize($filename));
        imagegif($this->destImage, null, $this->destQuality); 
        if (!is_null($fSaveAsFile )) imagegif($this->destImage, $fSaveAsFile, $this->destQuality); 
      break;
    case '.png';    
        header('Content-type: image/png');   
        header('Content-Disposition: inline; filename="'.$AsFileName.'"');   
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $this->timeStamp).' GMT');        
        header('Content-transfer-encoding: binary');
        //header('Content-length: '.filesize($filename));
        imagepng($this->destImage, null);     
        if (!is_null($fSaveAsFile )) imagepng($this->destImage, $fSaveAsFile); 
      break;
    case '.jpg':    
        header('Content-type: image/jpeg');   
        header('Content-Disposition: inline; filename="'.$AsFileName.'"');   
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $this->timeStamp).' GMT');        
        header('Content-transfer-encoding: binary');
        //header('Content-length: '.filesize($filename));
        imagejpeg($this->destImage, null, $this->destQuality); 
        if (!is_null($fSaveAsFile )) imagejpeg($this->destImage, $fSaveAsFile, $this->destQuality);    
      break;
    case 'jpeg':    imagejpeg($this->destImage);    break;
        header('Content-type: image/jpeg');   
        header('Content-Disposition: inline; filename="'.$AsFileName.'"');   
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $this->timeStamp).' GMT');        
        header('Content-transfer-encoding: binary');
        //header('Content-length: '.filesize($filename));
        imagejpeg($this->destImage, null, $this->destQuality); 
        if (!is_null($fSaveAsFile )) imagejpeg($this->destImage, $fSaveAsFile, $this->destQuality);    
      break;
    default:
        //ovde bismo mogli vratiti neku Not Available sliku
        return false;
      break;
    }

    return true;
  }

}
?>
