<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/login.inc.php,v $
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

class login extends user {

  /*********************************************************************
  ** constructor                                                     **/
  function login($id="-1") {
    if ($id != "-1") {
      echo "<h1>ID: ".$id."</h1>\n";
    }
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    user::activate($id);
    $this->loadRights();
  }

  // authentication
  function authByPassword($username="", $password="") {
    global $dbInst;
    $query = "select id from ".$dbInst->config['table_user']." where username = '".$username."' and password = password('".$password."')";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    return $row['id'];
  }

  function authByIp() {
    global $dbInst,$HTTP_SERVER_VARS,$REMOTE_ADDR,$HTTP_X_FORWARDED_FOR;

    if (isset($REMOTE_ADDR) && $REMOTE_ADDR != "") $ip = $REMOTE_ADDR;
    if (isset($HTTP_X_FORWARDED_FOR) && $HTTP_X_FORWARDED_FOR != "") $ip = $HTTP_X_FORWARDED_FOR;
    if (isset($HTTP_SERVER_VARS['REMOTE_ADDR']) && $HTTP_SERVER_VARS['REMOTE_ADDR'] != "") $ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
    if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']) && $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'] != "") $ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
    if (!isset($ip) || $ip == "") return false;
    $query = "select id from ".$dbInst->config['table_user']." where ip = '".$ip."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    return $row['id'];
  }

  function updatePassword($oldPassword,$newPassword,$newPassword2) {
    global $dbInst,$toolInst;

    if (! $dbInst->getValue("select id from ".$dbInst->config['table_user']." where id = '".$this->id."' AND password = password('".$oldPassword."')")) {
      $toolInst->errorStatus("old password wrong.");
    }
    elseif ($newPassword != $newPassword2) {
      $toolInst->errorStatus("new passwords does not match.");
    }
    else {
      $result = $dbInst->query("update ".$dbInst->config['table_user']." set password = password('".$newPassword."') where id = '".$this->id."' AND password = password('".$oldPassword."')");
      $dbInst->status($result[1],"u");
    }
  }

  function changeLanguage($language="") {
    global $dbInst;

    if ($language != "" && !file_exists("./lang/".$language.".inc.php")) return false;
    $this->language = $language;
    $result = $dbInst->query("update ".$dbInst->config['table_user']." set lang = '".$language."' where id = '".$this->id."'");
    $dbInst->status($result[1],"u");
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: login.inc.php,v $
 * Revision 1.1.1.1  2003/07/28 19:22:58  willuhn
 * reimport
 *
 * Revision 1.15  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.14  2002/05/13 15:44:33  willuhn
 * @B authByIp
 *
 * Revision 1.13  2002/05/03 11:17:57  willuhn
 * @B session handling
 *
 * Revision 1.12  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.11  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.10  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
