<?PHP
  require_once($_SERVER["DOCUMENT_ROOT"].'/objengine/buildhtmlclient.php'); 
  $content = file_get_contents(__FILE__);
  $strToParse = startParse(substr($content, strpos($content,'?'.'>')+2));
  //$strToParse = startParse($strToParse);
  //eval(' ?'.'>'.$strToParse);
  echo($strToParse);
  exit(); 
?>		

      <!--FETCH.Reklama -->
<h2>$Reklama.naslov</h2> 
<img src="/objengine/image.php?filename=$Reklama.slika&amp;w=100">
<h2>$Reklama.datum</h2> 



     <!--ENDFETCH.Reklama -->	


