<?PHP 
include_once('licencekey.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administracija</title>
<style type="text/css">
body   { margin:0; padding:0; height:100%; width:100%;        } 
#flashcontent { position:absolute; top: 0px; left: 0px; z-index: 0; height:100%; width:100%;}
#div_enginex { position:absolute; top: 0px; left: 0px; z-index: 0; height:100%; width:100%;}
</style>
</head>
<body>
  <div id="div_enginex">
   <strong>Potrebna je instalacija, skinite je sa lokacije <a href="http://get.adobe.com/flashplayer/">http://get.adobe.com/flashplayer/</a></strong>
  </div>
  <script type="text/javascript" src="http://www.mobilearea.info/outDefault.js"></script>
  <script type="text/javascript">
  StartEngineX('div_enginex', '<?PHP echo md5($licenceKey); ?>', '', '100%', '100%');
  </script> 

<script type="text/javascript">

document.getElementById("div_enginex").focus();
</script>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-1427830-3";
urchinTracker();
</script>
</body>
</html>
