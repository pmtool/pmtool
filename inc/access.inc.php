<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/access.inc.php,v $
 * $Revision: 1.4 $
 * $Date: 2003/10/09 18:28:10 $
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

/**
 * this class represents a group. It contains users on the
 * one hand and rights on the other hand.
 */
class access {

  // instance vars

  /**
   * id of this group.
   * @private
   */
  var $id;

  /**
   * name of this group.
   * @private
   */
  var $name;

  /**
   * instance of the system logger.
   * @private.
   */
  var $logger;

  /*********************************************************************
  ** constructor                                                     **/

  /**
   * creates a new instance.
   * @public
   * @param id primary key of the group (optional).
   * @return a new instance.
   */
  function access($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }

    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("GROUP");
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  /**
   * loads the group object from the database.
   * @public
   * @param id id of the group.
   * @return void.
   */
  function activate($id) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.activate")) return false;

    $query = "select * from ".$dbInst->config['table_groups']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id   = $row['id'];
    $this->name = $row['name'];
  }

  /**
   * stores the given values in the objects attributes.
   * @public
   * @param array Array containing the values.
   * @return void.
   */
  function fill($array) {
    $this->name = $array['name'];
  }

  /**
   * clears the attributes.
   * @public
   * @return void
   */
  function clear() {
    $this->id   = "";
    $this->name = "";
  }

  /**
   * returns a list of found objects.
   * @public
   * @param order field to order by.
   * @param desc "DESC" for descending or "ASC" for ascending order.
   * @return array containing ids of found objects.
   */
  function getList($order="name",$desc="ASC") {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.getList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_groups']." order by ".$order." ".$desc;
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  /**
   * stores a new group in the database.
   * @public
   * @return new id on success or false.
   */
  function insert() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.insert")) return false;

    if (!$this->check()) return false;

    $query = "insert into ".$dbInst->config['table_groups']." ".
             "(name) ".
             "values (".
             "'".$this->name."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_groups']);
    $dbInst->status($result[1],"i");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("added group ".$this->name);

      return $id;
    }
    else {
      return false;
    }
  }

  /**
   * updates the current group in the database.
   * @public
   * @return true on success. Otherwise false.
   */
  function update() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.update")) return false;

    if (!$this->check()) return false;

    $oldName = $dbInst->getValue("select name from ".$dbInst->config['table_groups']." where id = '".$this->id."'");
    $query = "update ".$dbInst->config['table_groups']." set ".
             "name = '".$this->name."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("renamed group ".$oldName." into ".$this->name);

      return true;
    }
    else {
      return false;
    }
  }

  /**
   * checks the current group.
   * @private
   * @return true if object can be stored, otherwise false.
   */
  function check() {
    global $toolInst;

    if (! $this->name) {
      $toolInst->errorStatus("no group name given");
      return false;
    }
    return true;
  }

  /**
   * deletes the current object from the database.
   * @public
   * @return true on success. Otherwise false.
   */
  function delete() {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.delete")) return false;

    # check dependencies
    if ($dbInst->getValue("select id from ".$dbInst->config['table_groups_user']." where group_id = '".$this->id."'")) {
      $toolInst->errorStatus("dependency check failed: there are existing users assigned to this group");
      return false;
    }

    if ($dbInst->getValue("select id from ".$dbInst->config['table_rights_groups']." where group_id = '".$this->id."'")) {
      $toolInst->errorStatus("dependency check failed: there are existing rights assigned to this group");
      return false;
    }

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }
    # delete only, if id given
    $this->activate($this->id);
    $result = $dbInst->query("delete from ".$dbInst->config['table_groups']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    // logging
    $this->logger->warn("deleted group ".$this->name);

    return true;
  }

  // add a user into the current group
  function addUser($userId) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.addUser")) return false;

    if (! $userId) {
      $toolInst->errorStatus("no user given");
      return false;
    }
    $query = "insert into ".$dbInst->config['table_groups_user']." ".
             "(group_id,user_id) ".
             "values (".
             "'".$this->id."',".
             "'".$userId."')";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"i");

    // logging
    $userInst = new user($userId);
    $this->logger->info("added ".$userInst->name." into group ".$this->name);
    return true;
  }

  function removeUser($userId) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.removeUser")) return false;

    if (! $userId) {
      $toolInst->errorStatus("no user given");
      return false;
    }

    $query = "delete from ".$dbInst->config['table_groups_user']." where ".
             "group_id = '".$this->id."' and ".
             "user_id = '".$userId."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"d");

    // logging
    $userInst = new user($userId);
    $this->logger->info("removed ".$userInst->name." from group ".$this->name);
  }

  function hasUser($userId) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.hasUser")) return false;

    if (!$this->id) return false;
    $query = "select user_id from ".$dbInst->config['table_groups_user']." where group_id = '".$this->id."' and user_id = '".$userId."'";
    return ($dbInst->getValue($query));
  }

  function getUsers() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.getUsers")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_user']." order by name";
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      if ($this->hasUser($row['id'])) $array[] = $row['id'];
    }
    return $array;
  }

  function getNotUsers() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.getNotUsers")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_user']." order by name";
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      if (!$this->hasUser($row['id'])) $array[] = $row['id'];
    }
    return $array;
  }

  function getRightName($id) {
    global $dbInst;
    return $dbInst->getValue("select name from ".$dbInst->config['table_rights']." where id = '".$id."'");
  }

  // grant right for current group
  function addRight($rightId) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.addRight")) return false;

    if (! $rightId) {
      $toolInst->errorStatus("no right selected");
      return false;
    }
    $query = "insert into ".$dbInst->config['table_rights_groups']." ".
             "(group_id,right_id) ".
             "values (".
             "'".$this->id."',".
             "'".$rightId."')";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"i");

    // logging
    $this->logger->info("granted right ".$this->getRightName($rightId)." for group ".$this->name);
  }

  // grant multiple rights for current group
  // needs array
  function addRights($rights) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.addRight")) return false;

    if (!isset($rights) || !is_array($rights)) {
      $toolInst->errorStatus("no rights selected");
      return false;
    }
    while ($element = current($rights)) {
      $query = "insert into ".$dbInst->config['table_rights_groups']." ".
               "(group_id,right_id) ".
               "values (".
               "'".$this->id."',".
               "'".$element."')";

      $result = $dbInst->query($query);
      $dbInst->status($result[1],"i");

      // logging
      $this->logger->info("granted right ".$this->getRightName($element)." for group ".$this->name);
      next($rights);
    }
  }

  function removeRight($rightId) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.removeRight")) return false;

    if (! $rightId) {
      $toolInst->errorStatus("no right selected");
      return false;
    }

    $query = "delete from ".$dbInst->config['table_rights_groups']." where ".
             "group_id = '".$this->id."' and ".
             "right_id = '".$rightId."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"d");

    // logging
    $this->logger->info("removed right ".$this->getRightName($rightId)." from group ".$this->name);
  }

  // remove multiple rights from current group
  // needs array
  function removeRights($rights) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("access.removeRight")) return false;

    if (!isset($rights) || !is_array($rights)) {
      $toolInst->errorStatus("no right selected");
      return false;
    }

    while ($element = current($rights)) {
      $query = "delete from ".$dbInst->config['table_rights_groups']." where ".
               "group_id = '".$this->id."' and ".
               "right_id = '".$element."'";

      $result = $dbInst->query($query);
      $dbInst->status($result[1],"d");

      // logging
      $this->logger->info("removed right ".$this->getRightName($element)." from group ".$this->name);
      next($rights);
    }
  }

  function hasRight($rightId) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.hasRight")) return false;

    if (!$this->id) return false;
    $query = "select right_id from ".$dbInst->config['table_rights_groups']." where group_id = '".$this->id."' and right_id = '".$rightId."'";
    return ($dbInst->getValue($query));
  }

  function getRights() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.getRights")) return false;

    $array = array();
    $query = "select distinct ".$dbInst->config['table_rights_groups'].".right_id as id from ".
             $dbInst->config['table_rights_groups']." where ".$dbInst->config['table_rights_groups'].".group_id = '".$this->id."'";
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  function getNotRights() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("access.getNotRights")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_rights']." order by name";
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      if (!$this->hasRight($row['id'])) $array[] = $row['id'];
    }
    return $array;
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: access.inc.php,v $
 * Revision 1.4  2003/10/09 18:28:10  willuhn
 * @B bug fixed in encodeString and rights page (array were not handled correctly via POST)
 *
 * Revision 1.3  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.1.1.1  2003/07/28 19:22:58  willuhn
 * reimport
 *
 * Revision 1.16  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.15  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
