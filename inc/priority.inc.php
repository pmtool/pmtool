<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/priority.inc.php,v $
 * $Revision: 1.1 $
 * $Date: 2004/03/17 20:19:50 $
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
 * a small class which adds the feature "priority" to other objects.
 */
class priority {

  // public instance vars

  /**
  * priority id (low,medium,high)
  * see table "taskpriority" for details
  * @access public
  */
  var $priorityId;

  /**
   * array containing the priority names
   * @access private
   */
  var $priorityNames = array();

  /**
   * array containing the priority styles
   * @access private
   */
  var $priorityStyles = array();

  /*********************************************************************
  ** constructor                                                     **/

  function priority() {
    $this->loadPriorities();
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  // load prios into cache
  function loadPriorities() {
    // initialize only once
    if (count($this->priorityNames) < 2) {
      global $dbInst,$lang;
      $result = $dbInst->query("select id,name,style from ".$dbInst->config['table_taskpriority']);
      while($row = $dbInst->fetchArray($result[0])) {
        $this->priorityNames[$row['id']] = (isset($lang['names_priority'][$row['id']])) ? $lang['names_priority'][$row['id']] : $row['name'];
        $this->priorityStyles[$row['id']] = $row['style'];
      }
    }
  }

  /**
  * array getPriorityList()
  * returns an array with priority ids
  * @return array priority list
  * @access public
  */
  function getPriorityList() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.getPriorityList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_taskpriority'];
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  /**
  * String getPriorityName()
  * returns the name of the given priority id (by default the name of the current request priority)
  * @param int priorityId (optionally)
  * @return priority name
  * @access public
  */
  function getPriorityName($id="-1") {
    global $loginInst;

    if (!$loginInst->hasAccess("request.getPriorityName")) return false;
    if ($id == "-1") $id = $this->priorityId;
    return $this->priorityNames[$id];
  }

  /**
  * String getPriorityStyle()
  * returns the style name of the given priority id (by default the style name of the current request priority)
  * see styles.css for defined styles
  * @param int priorityId (optionally)
  * @return priority style
  * @access public
  */
  function getPriorityStyle($id="-1") {
    global $loginInst;

    if (!$loginInst->hasAccess("request.getPriorityStyle")) return false;
    if ($id == "-1") $id = $this->priorityId;
    return $this->priorityStyles[$id];
  }


  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: priority.inc.php,v $
 * Revision 1.1  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 ***************************************************************************/

?>
