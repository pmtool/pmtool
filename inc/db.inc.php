<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/db.inc.php,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:22:58 $
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

class db {

  // instance vars
  var $dbLink="";
  var $config=array();

  // internally used
  var $count = 0;

  var $logger;

  /*********************************************************************
  ** constructor                                                     **/

  function db() {
    global $dbconfig,$loginInst;

    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("DB");

    // make config to public member
    $this->config = $dbconfig;
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  // abstract methods: needs to overwrite in the
  // database specific implementations
  function close() {}
  function query($query) {}
  function fetchRow($result) {}
  function fetchArray($result) {}
  function error() {}

  function getValue($query) {
    $result  = $this->query($query);
    $element = $this->fetchArray($result[0]);
    return ($element[0]);
  }

  function selectbox($table,$field,$value,$select_name,$selected="") {
    # table: used table
    # field: show this value behind <option> tag
    # value: use this value as <option value="?">
    # select_name: name of select box (<select name="?">)
    # selected: set this entry as preselected (must be contained in on of the values

    if (!$value) {$value = "id";}
    if (!$select_name) {$select_name = $field;}
    $result = $this->query("select $value,$field from $table");

    echo "<select name=".$select_name.">\n";
    while ($row = $this->fetchArray($result[0])) {
      if ($selected == $row[$value]) {
        echo "<option value=\"".$row[$value]."\" selected>&nbsp;".$row[$field]."&nbsp;&nbsp;\n";
      }
      else {
        echo "<option value=\"".$row[$value]."\">&nbsp;".$row[$field]."&nbsp;&nbsp;\n";
      }
    }
    echo "</select>\n";
  }

  function status($result_id,$type) {
    global $toolInst,$lang;

    if ($type == "d") {
      if ($result_id == 1) {$toolInst->successStatus($lang['db_recordDeleted']);}
      elseif ($result_id == 0) {$toolInst->successStatus($lang['db_recordNochange']);}
      else {$toolInst->errorStatus($lang['db_recordNotDeleted']);}
    }
    elseif($type == "i") {
      if ($result_id == 1) {$toolInst->successStatus($lang['db_recordAdded']);}
      elseif ($result_id == 0) {$toolInst->successStatus($lang['db_recordNochange']);}
      else {$toolInst->errorStatus($lang['db_recordNotAdded']);}
    }
    elseif($type == "u") {
      if ($result_id == 1) {$toolInst->successStatus($lang['db_recordChanged']);}
      elseif ($result_id == 0) {$toolInst->successStatus($lang['db_recordNochange']);}
      else {$toolInst->errorStatus($lang['db_recordNotChanged']);}
    }
    else {
      if ($result_id == 1) {$toolInst->successStatus($lang['db_recordSeemsOk']);}
      elseif ($result_id == 0) {$toolInst->successStatus($lang['db_recordNochange']);}
      else {$toolInst->errorStatus($lang['db_recordError']);}
    }
  }
  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: db.inc.php,v $
 * Revision 1.1.1.1  2003/07/28 19:22:58  willuhn
 * reimport
 *
 * Revision 1.13  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.12  2002/09/07 19:23:13  willuhn
 * @N global commit for missing files
 *
 * Revision 1.11  2002/05/05 16:25:18  willuhn
 * @B mysql stuff
 *
 * Revision 1.10  2002/04/14 18:09:29  willuhn
 * @N splitted db class into a super class (valid for all databases)
 *    and an implementation class
 * @N some more multilanguage stuff
 *
 * Revision 1.9  2002/04/14 17:46:42  willuhn
 * @N added multilanguage support
 *
 * Revision 1.8  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.7  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
