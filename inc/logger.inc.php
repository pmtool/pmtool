<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/logger.inc.php,v $
 * $Revision: 1.3 $
 * $Date: 2003/09/27 18:23:45 $
 * $Author: willuhn $
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

class logger {

  // instance vars
  var $user;
  var $token;


  var $debugHandle;

  /*********************************************************************
  ** constructor                                                     **/
  function logger() {
    global $config;

    // initialize Debug Method
    if (DEBUG) {
      $this->debugHandle = fopen($config['debug_target'],"a");
    }
  }

  /**                                                                 **
  *********************************************************************/

  function write($level,$string) {
    global $dbInst,$config;
    if ($config['enable_log']) {
      $query = "insert into ".$dbInst->config['table_log']." (level,user,comment) values('".$level."','".$this->user->name."','".$this->token.": ".$string."')";
      $dbInst->query($query);
    }
  }

  /*********************************************************************
  ** public methods                                                  **/

  function setToken($token) {
    $this->token = $token;
  }

  function setUser($user) {
    $this->user = $user;
  }

  function alert($string="") {
    if ($string != "") {
      $this->write("ALERT",$string);
    }
  }

  function info($string="") {
    if ($string != "") {
      $this->write("INFO",$string);
    }
  }

  function warn($string="") {
    if ($string != "") {
      $this->write("WARN",$string);
    }
  }

  function debug($file="",$line="",$comment="") {
    global $toolInst;
    if (!DEBUG) return true;
    $text  = "\n--- DEBUG --- (".$toolInst->getTime("Y-m-d h:i:s").")\n";
    $text .= "file   : ".$file."\n";
    $text .= "line   : ".$line."\n";
    $text .= "comment: ".$comment."\n";
    fwrite($this->debugHandle,$text);
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: logger.inc.php,v $
 * Revision 1.3  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.1.1.1  2003/07/28 19:22:53  willuhn
 * reimport
 *
 * Revision 1.4  2002/06/04 21:24:04  willuhn
 * @N added debug method in logger
 *
 * Revision 1.3  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
