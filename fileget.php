<? #-*- php -*-
 #   FILE: "/home/znouza/projects/pmtool/fileget.php"
 #   DESC: ""
 #   LAST MODIFICATION: "Sun, 29 Feb 2004 00:10:50 +0100 (znouza)"
 #   (C) 2002 by Tom Meinlschmidt, <tm at salome.datron.cz>
 #   $Id: fileget.php,v 1.1 2004/02/28 23:03:32 znouza Exp $
?>
<?
  define("DEMO_MODE",false);
  
  if (!file_exists("inc/includer.inc.php")) {echo "panic: inc/includer.inc.php doesn't exist";exit;} require("inc/includer.inc.php");

  if (! isset($HTTP_SESSION_VARS['loginid'])) {
  // session is not set -> authenticate

  // try to authenticate by IP
  if ($loginInst->authByIp()) {
    $HTTP_SESSION_VARS['loginid'] = $loginInst->authByIp();
  }
  // try to authenticate by username/password
  elseif (tool::securePost('loginname') &&
          $loginInst->authByPassword(tool::securePost('loginname'),tool::securePost('password')))
  {
    $HTTP_SESSION_VARS['loginid'] = $loginInst->authByPassword(tool::securePost('loginname'),tool::securePost('password'));
  }

  if (isset($HTTP_SESSION_VARS['loginid']) && $HTTP_SESSION_VARS['loginid'] != "" && ! session_is_registered("loginid")) {
    $loginid = $HTTP_SESSION_VARS['loginid'];
    if (! session_register("loginid")) {
      echo "<b>".$lang['common_unableToSaveLoginInSession']."</b><br>";
      // could not save session -> give up
      exit;
    }
  }

  elseif (!session_is_registered("loginid") && (tool::securePost('loginname') || tool::securePost('password'))) {
      // show error message only, if username/password was submitted
    $toolInst->errorStatus($lang['common_userUnknownOrPasswordWrong']);
  }

  }


  if (session_is_registered("loginid")) {
  if (! isset($HTTP_SESSION_VARS['loginid']) || $HTTP_SESSION_VARS['loginid'] == "") {
    echo "<b>".$lang['common_unableToFindloginInSession']."</b><br>";
    // could not save session -> give up
    exit;
  }

  $loginInst->activate($HTTP_SESSION_VARS['loginid']);

  $filename = preg_replace(array("/^[\.]*/","/\//"), array("",""), tool::secureGet('filename'));
  $filecreated = ereg_replace("[^0-9]", "", tool::secureGet('created'));

  $file_size = @filesize($config['attach_url']."/$filecreated/$filename");

  if ($filename && $filecreated && $file_size)
  {
    Header("Content-Type: application/octet-stream; name=\"$filename\"");
    Header("Content-Disposition: attachment; filename=\"$filename\"");
    Header("Accept-Ranges: bytes");
    Header("Content-Length: $file_size");
    $fp=fopen($config['attach_url']."/$filecreated/$filename","r");
    fpassthru($fp);
    fflush($fp);
    fclose($fp);
  }
  }
?>
