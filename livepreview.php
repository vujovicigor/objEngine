<?PHP
  require_once('objengine/buildhtmlclient.php'); 
  $url = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  
  $parts = parse_url($url);
  //echo($url);
  //print_r($parts);
  parse_str($parts['query'], $query);
  //echo $query['email'];
  $_REQUEST=$query;
  //$key=substr($parts['path'], 1);
  //echo(end(explode('/',$parts['path'])));
  
  $content ='<!--FETCH.WebPages Name="' .end(explode('/',$parts['path']))  . '"  -->'.
  ' $WebPages.Html  <!--ENDFETCH.WebPages -->	';
  $strToParse = startParse($content);
  //eval(' ?'.'>'.$strToParse);
  echo($strToParse);
  exit(); 
  
?>		
