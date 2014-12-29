<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/tool.inc.php,v $
 * $Revision: 1.9 $
 * $Date: 2005/02/20 23:31:21 $
 * $Author: matchboy $
 * $Locker:  $
 * $State: Exp $
 *
 * Copyright (c) by willuhn.webdesign
 * All rights reserved
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 ***************************************************************************/

/**
 * a collection of some helper methods.
 */
class tool {

  /*********************************************************************
  ** constructor                                                     **/

  /**
   * creates a new instance of the helper class.
   * @public
   * @return new tool instance.
   */
  function tool() {
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/


  /**
   * store the given message as error message in the application.
   * This message will be shown on the website.
   * @public
   * @param message errormessage.
   * @return void.
   */
  static function errorStatus($message) {
    global $global_status;
    $global_status = "<font color=#ff0000>error: ".$message."</font>";
  }

  /**
   * store the given message as success message in the application.
   * This message will be shown on the website.
   * @public
   * @param message successmessage.
   * @return void.
   */
  static function successStatus($message) {
    global $global_status;
    $global_status = "<font color=#009900>ok: ".$message."</font>";
  }

  /**
   * append a message to the application.
   * This message will be shown on the website.
   * @public
   * @param message message.
   * @return void.
   */
  static function appendStatus($message) {
    global $global_status;
    $global_status .= $message;
  }

  /**
   * convert timestamp into epoch (unix seconds since 1.1.1970).
   * @public
   * @param year the year.
   * @param month the month of the year (1-12).
   * @param day the day of month (1-31).
   * @param hour the hour of the day (0-23).
   * @param min the minute of the hour (0-59).
   * @param sec the seconds of the minute (0-59).
   * @return unix seconds since 1.1.1970.
   */
  static function timestampToSec($year,$month,$day,$hour,$min,$sec="0") {
    return mktime($hour,$min,$sec,$month,$day,$year);

  }

  /**
   * convert unix seconds into human readable format.
   * @public
   * @param format format of output (i.e.: Y-m-d, H:i:s).
   * @param actTime the time in seconds since 1.1.1970.
   * @return formatted time.
   */
  static function getTime($format,$actTime="") {
    if (!$actTime || $actTime == "") {$actTime = time();}
    if (!$format || $format == "") {
      return date("Y-m-d, H:i:s",$actTime);
    }
    else {
      return date($format,$actTime);
    }
  }

  static function deductibleSeconds($time) {
    global $config;

    $minutes = ceil($time/60);
    $minutes2 = ceil($time/60);

    if (!$config['roundrate'] || $config['roundrate'] == 0 || !$config['roundstyle'] || $config['roundstyle'] == 0)
      return ($minutes * 60);

    if ($config['roundstyle'] == 2) {
      if ($config['roundrate'] == 0.25) {
        $minutes = ceil($minutes / 15) * 15;
        if ($minutes2 % 15) $minutes -= 15;
      }
      if ($config['roundrate'] == 0.5) {
        $minutes = ceil($minutes / 30) * 30;
        if ($minutes2 % 30) $minutes -= 30;
      }
      if ($config['roundrate'] == 1) {
        $minutes = ceil($minutes / 60) * 60;
        if ($minutes2 % 60) $minutes -= 60;
      }
    }
    else {
      if ($config['roundrate'] == 0.25) {
        $minutes = ceil($minutes / 15) * 15;
        if ($minutes % 15) $minutes += 15;
      }
      if ($config['roundrate'] == 0.5) {
        $minutes = ceil($minutes / 30) * 30;
        if ($minutes % 30) $minutes += 30;
      }
      if ($config['roundrate'] == 1) {
        $minutes = ceil($minutes / 60) * 60;
        if ($minutes % 60) $minutes += 60;
      }
    }

    return ($minutes * 60);
  }

  static function deductibleHours($time) {

    $min = ceil(tool::deductibleSeconds($time) / 60);
    return tool::numberRound(($min/60),2);
  }

  // math
  static function numberRound($number,$decimals=2) {
    // this is needed, because round precision is not available in PHP3's round()
    $multiplier = "1";
    for($i=0;$i<$decimals;$i++) $multiplier *= 10;
    return floor($number * $multiplier)/$multiplier;
  }

  static function checkInt($string) {
    if (!isset($string) || $string == "") return false;
    if (!eregi("[^0-9]",$string)) return true;
  }

  static function checkFloat($string) {
    if (!isset($string) || $string == "") return false;
    if (!eregi("[^0-9\.,]",$string)) return true;
  }

  static function formatTimestamp($timestamp) {
    $year  = substr($timestamp,0,4);
    $month = substr($timestamp,4,2);
    $day   = substr($timestamp,6,2);
    $hour  = substr($timestamp,8,2);
    $min   = substr($timestamp,10,2);
    $sec   = substr($timestamp,12,2);
    return $year."-".$month."-".$day." ".$hour.":".$min;
  }

  // format
  static function formatCurrency($value) {
    global $config;
    $value = tool::numberRound($value,2);
    if (ereg(",",$value)) {
      if (strlen(strstr($value,",")) == 2) $value .= "0";
    }
    else {
      if (strlen(strstr($value,".")) == 2) $value .= "0";
    }
    if (!ereg("[,\.]",$value)) $value .= ".00";
    return $value." ".$config['currency'];
  }

  static function formatTime($time) {
    $time = ceil($time/60);
    $time_hour = floor($time/60);
    $time_min = $time - $time_hour*60;
    if (strlen($time_min) == 1) {$time_min = "0".$time_min;}
    return ($time_hour.":".$time_min." h");
  }

  // html
  static function encodeUrl($url) {
    $sessionId = session_id();
    
    return $url;
#    
#	security issue, session hijacking too easy.
#    
#    if ($sessionId == "") return $url;
#    if (!ereg("\?",$url)) return $url."?PHPSESSID=".$sessionId;
#    else                  return $url."&PHPSESSID=".$sessionId;
  }

  static function encodeXml($string) {
    $string = ereg_replace("&","&amp;",$string);

    return $string;
  }

  static function encodeString($string="") {
    global $toolInst;

    if ($string == "") return "";

    // if $string is an array we have to encode all elements
    if (is_array($string)) {
      $array = array();
      while ($element = current($string)) {
        $array[] = tool::encodeString($element);
        next($string);
      }
      return $array;
    }

    // if magic_quotes_gpc is enabled, we have to remove all escapings first
    if (get_magic_quotes_gpc()) {
      $string = stripslashes($string);
    }

    if((eregi("<[^>]*script*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*xml*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*style*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*form*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*window.*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*alert*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*img*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*document.*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*cookie*\"?[^>]*>", $string)) ||
       (eregi(".*[[:space:]](or|and)[[:space:]].*(=|like).*", $string)) ||
       (eregi("<[^>]*object*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*iframe*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*applet*\"?[^>]*>", $string)) ||
       (eregi("<[^>]*meta*\"?[^>]*>", $string)))
    {
      $toolInst->appendStatus("WARN: data contained cross site scripting code.");
    }

    // replace dangerous chars ;)
    $string = htmlspecialchars($string,ENT_QUOTES);

    // now we escape all
    return addslashes($string);
  }

  static function getDir($dir) {
    if (!$dir) return "";

    $dirs = array();
    $dirHandle = opendir($dir);
    while ($file = readdir($dirHandle)) {
      if ($file != "." && $file != ".." && $file != "CVS") $dirs[] = $file;
    }
    closedir($dirHandle);
    sort($dirs);
    return $dirs;
  }

  static function getVersion($version) {
    return eregi_replace("[^0-9\.]","",$version);
  }

  static function loadPlugin($plugin) {
    global $toolInst, $loginInst, $config, $content;

    // no plugin given
    if (!$plugin) return false;

    // valid chars for plugin names
    if (eregi("/",$plugin)) return false;

    // plugin does not exist
    if (! file_exists("plugins/".$plugin."/index.php")) return false;

    // we include the plugin config (if exists)
    if (file_exists("plugins/".$plugin."/config.inc.php")) {
      require("plugins/".$plugin."/config.inc.php");
    }

    // fallback language
    $importLang = "en";

    // system language
    if (file_exists("plugins/".$plugin."/lang/".$config['language'].".inc.php")) $importLang = $config['language'];

    // user language
    if ($loginInst->language && file_exists("plugins/".$plugin."/lang/".$loginInst->language.".inc.php")) $importLang = $loginInst->language;

    // we include the plugin language settings (if exists)
    if (file_exists("plugins/".$plugin."/lang/".$importLang.".inc.php")) {
      require("plugins/".$plugin."/lang/".$importLang.".inc.php");
    }

    $content = "plugins/".$plugin."/index.php";
    return true;
  }

  static function secureGet($name=null) {
    if ($name == null) {
      return null;
    }
    if (PHP_VERSION >= 4.1) {
      global $_GET;
      if (isset($_GET[$name]) && $_GET[$name] != "") {
        return tool::encodeString($_GET[$name]);
      }
    }
    else {
      global $HTTP_GET_VARS;
      if (isset($HTTP_GET_VARS[$name]) && $HTTP_GET_VARS[$name] != "") {
        return tool::encodeString($HTTP_GET_VARS[$name]);
      }
    }
    return null;
  }

  static function secureGetAll() {
    $array = array();
    if (PHP_VERSION >= 4.1) {
      global $_GET;
      foreach($_GET as $key => $value) {
        $array[$key] = tool::encodeString($value);
      }
    }
    else {
      global $HTTP_GET_VARS;
      foreach($HTTP_GET_VARS as $key) {
        $array[$key] = tool::encodeString($value);
      }
    }
    return $array;
  }

  static function securePost($name=null) {
    if ($name == null) return null;

    if (PHP_VERSION >= 4.1) {
      global $_POST;
      if (isset($_POST[$name]) && $_POST[$name] != "") {
        return tool::encodeString($_POST[$name]);
      }
    }
    else {
      global $HTTP_POST_VARS;
      if (isset($HTTP_POST_VARS[$name]) && $HTTP_POST_VARS[$name] != "") {
        return tool::encodeString($HTTP_POST_VARS[$name]);
      }
    }
    return null;
  }

  static function securePostAll() {
    $array = array();
    if (PHP_VERSION >= 4.1) {
      global $_POST;
      foreach($_POST as $key => $value) {
        $array[$key] = tool::encodeString($value);
      }
    }
    else {
      global $HTTP_POST_VARS;
      foreach($HTTP_POST_VARS as $key => $value) {
        $array[$key] = tool::encodeString($value);
      }
    }
    return $array;
  }

  /**
   * handles file uploads
   *
  **/

  static function secureFiles($name = null, $subname = null) {
    if ($name == null) return null;

    if (PHP_VERSION >= 4.1) {
      global $_FILES;
      if (isset($_FILES[$name]) && $_FILES[$name][$subname] != "") {
        return $_FILES[$name][$subname];
      }
    }
    else {
      global $HTTP_POST_FILES;
      if (isset($HTTP_POST_FILES[$name]) && $HTTP_POST_FILES[$name][$subname] != "") {
        return $HTTP_POST_FILES[$name][$subname];
      }
    }
    return null;
  }

  /**                                                                 **
  *********************************************************************/

}

/***************************************************************************
 * $Log: tool.inc.php,v $
 * Revision 1.9  2005/02/20 23:31:21  matchboy
 * Removed the phpsession id from the URLs as session hijacking is too easy.
 *
 * Revision 1.8  2004/02/28 23:01:27  znouza
 *
 * tool.inc.php
 * @N secureFiles - handles file uploads more securely
 *
 * attachment, request, task
 * @C handling uploaded files
 *
 * attachment.inc.php
 * @C copy() to move_uploaded_file() = more security
 *
 * Revision 1.7  2003/11/17 20:41:14  willuhn
 * @N some more fixes at the new project status plugin
 *
 * Revision 1.6  2003/10/09 18:28:10  willuhn
 * @B bug fixed in encodeString and rights page (array were not handled correctly via POST)
 *
 * Revision 1.5  2003/09/27 19:32:52  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.2  2003/07/28 21:05:37  willuhn
 * @N added some comments
 * @N added doxyfile
 *
 * Revision 1.1.1.1  2003/07/28 19:23:02  willuhn
 * reimport
 *
 * Revision 1.34  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.33  2002/09/07 19:23:13  willuhn
 * @N global commit for missing files
 *
 * Revision 1.32  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.31  2002/04/15 20:41:40  willuhn
 * @N pluginLoader
 *
 * Revision 1.30  2002/04/10 22:57:20  willuhn
 * @N added config and language files for plugins
 *
 * Revision 1.29  2002/04/08 22:30:02  willuhn
 * @C project availability check on home page
 *
 * Revision 1.28  2002/03/30 17:15:52  willuhn
 * @N added plugin "presence list"
 *
 * Revision 1.27  2002/03/30 14:14:39  willuhn
 * @N added plugin loader
 *
 * Revision 1.26  2002/02/24 22:41:20  willuhn
 * updated content-type in reportviewer
 *
 * Revision 1.25  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
