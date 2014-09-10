<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/project.inc.php,v $
 * $Revision: 1.8 $
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

// Constants
define("PROJECT_STATUS_TENTATIVE","1");
define("PROJECT_STATUS_INPROGRESS","2");
define("PROJECT_STATUS_DONE","3");

class project extends priority {

  // public instance vars
  var $id;
  var $name;
  var $description;
  var $rate;
  var $budget;
  var $customerId;
  var $statusId;
  var $managerId;

  var $paid;
  var $payment_date;
  var $consignment_date;

  var $filterCustomerId;
  var $filterStatusId;
  var $filterInvertStatus;
  var $filterPriorityId;

  var $matches = 0;

  /*
   * array containing project status names
   * @access private
  **/
  var $projectStatuses = array();

  var $logger;

  /*********************************************************************
  ** constructor                                                     **/

  function project($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("PROJECT");

    // call super constructor
    parent::priority();
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("project.activate")) return false;

    $query = "select * from ".$dbInst->config['table_project']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id               = $row['id'];
    $this->name             = $row['name'];
    $this->description      = $row['description'];
    $this->rate             = $row['rate'];
    $this->budget           = $row['budget'];
    $this->customerId       = $row['customer_id'];
    $this->managerId        = $row['manager_id'];
    $this->statusId         = $row['projectstatus_id'];
    $this->paid             = $row['paid'];
    $this->payment_date     = $row['payment_date'];
    $this->consignment_date = $row['consignment_date'];
    $this->priorityId       = $row['priority_id'];
  }

  function fill($array) {

    $this->name              = $array['name'];
    $this->description       = $array['description'];
    $this->rate              = $array['rate'];
    $this->budget            = $array['budget'];
    $this->customerId        = $array['customerid'];
    $this->managerId         = $array['managerid'];
    $this->statusId          = $array['statusid'];
    $this->paid              = $array['paid'];
    $this->payment_date      = $array['payment_date'];
    $this->consignment_date  = $array['consignment_date'];
    $this->priorityId        = $array['priorityid'];
  }

  function clear() {
    $this->id                = "";
    $this->name              = "";
    $this->description       = "";
    $this->rate              = "";
    $this->budget            = "";
    $this->customerId        = "";
    $this->managerId         = "";
    $this->statusId          = "";
    $this->paid              = "";
    $this->payment_date      = "";
    $this->consignment_date  = "";
    $this->priorityId        = "";
  }

  function setFilter() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("project.setFilter")) return false;

    // this is a dummy filter
    $filter = "where ".$dbInst->config['table_project'].".id like '%%' ";
    if ($loginInst->isCustomer()) $filter .= "and ".$dbInst->config['table_project'].".customer_id = '".$loginInst->customerId."'";
    if ($this->filterCustomerId) $filter .= "and ".$dbInst->config['table_project'].".customer_id = '".$this->filterCustomerId."' ";
    if ($this->filterStatusId) {
      $filter .= "and ".$dbInst->config['table_project'].".projectstatus_id ";
      if ($this->filterInvertStatus) $filter .= "!";
      $filter .= "= '".$this->filterStatusId."' ";
    }
    if ($this->filterPriorityId) $filter .= "and ".$dbInst->config['table_project'].".priority_id >= ".$this->filterPriorityId;

    return $filter;
  }

  function getList($order="name",$desc="ASC") {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("project.getList")) return false;

    $filter = $this->setFilter();
    $filter .= "and (".$dbInst->config['table_project'].".manager_id = ".$dbInst->config['table_user'].".id OR ".$dbInst->config['table_project'].".manager_id = '')";
    $filter .= "and ".$dbInst->config['table_project'].".customer_id = ".$dbInst->config['table_customer'].".id";

    if ($order == "manager")  $order = $dbInst->config['table_user'].".name";
    if ($order == "name")  $order = $dbInst->config['table_project'].".name";
    if ($order == "customer")  $order = $dbInst->config['table_customer'].".company";

    $array = array();
    $query = "select DISTINCT ".$dbInst->config['table_project'].".id as id from ".$dbInst->config['table_project'].",".$dbInst->config['table_user'].",".$dbInst->config['table_customer']." ".$filter." order by ".$order." ".$desc;
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    $this->matches = $result[1];
    return $array;
  }

  /**
   * returns the summary of all customer costs for this project
   * @return float costs
   */
  function getCustomerCosts() {
    $taskInst = new task();
    $taskInst->filterProjectId = $this->id;
    $sum = 0;
    $list = $taskInst->getList();
    while ($element = current($list)) {
      $taskInst->activate($element);
      $sum += $taskInst->getCustomerCosts();
      next($list);
    }
    return $sum;
  }

  /**
   * returns the summary of all costs for this project
   * @return float costs
   */
  function getCosts() {
    $taskInst = new task();
    $taskInst->filterProjectId = $this->id;
    $sum = 0;
    $list = $taskInst->getList();
    while ($element = current($list)) {
      $taskInst->activate($element);
      $sum += $taskInst->getCosts();
      next($list);
    }
    return $sum;
  }


  function getStatusList() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("project.getStatusList")) return false;

    $array = array();
    $query = "select id,name from ".$dbInst->config['table_projectstatus'];
    $result = $dbInst->query($query);
    $this->projectStatuses=array();
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
      $this->projectStatuses[$row['id']] = $row['name'];
    }
    return $array;
  }

  function getStatusName($id="-1") {
    global $dbInst,$loginInst,$lang;

    if (!$loginInst->hasAccess("project.getStatusName")) return false;

    if ($id == "-1") $id = $this->statusId;
  $statusName = (isset($lang['names_projects'][$id])) ? $lang['names_projects'][$id] : $this->projectStatuses[$id];
  return $statusName;
  }

  /**
   * return true, if the project is available
   * for posting tasks on it -> if status = done
   */
  function isAvailable() {
    if ($this->statusId == TASK_STATUS_INPROGRESS) return true;
    return false;
  }

  function insert() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("project.insert")) return false;

    if (!$this->check()) return false;

    $query = "insert into ".$dbInst->config['table_project']." ".
             "(name,description,rate,budget,customer_id,manager_id,projectstatus_id,paid,consignment_date,payment_date,priority_id) ".
             "values (".
             "'".$this->name."',".
             "'".$this->description."',".
             "'".$this->rate."',".
             "'".$this->budget."',".
             "'".$this->customerId."',".
             "'".$this->managerId."',".
             "'".$this->statusId."',".
             "'".$this->paid."',".
             "'".$this->consignment_date."',".
             "'".$this->payment_date."',".
             "'".$this->priorityId."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_project']);
    $dbInst->status($result[1],"i");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("added project ".$this->name);

      return $id;
    }
    else {
      return false;
    }
  }

  function update() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("project.update")) return false;

    if (!$this->check()) return false;

    $query = "update ".$dbInst->config['table_project']." set ".
             "name = '".$this->name."',".
             "description = '".$this->description."',".
             "rate = '".$this->rate."',".
             "budget = '".$this->budget."',".
             "customer_id = '".$this->customerId."',".
             "manager_id = '".$this->managerId."',".
             "projectstatus_id = '".$this->statusId."',".
             "paid = '".$this->paid."',".
             "consignment_date = '".$this->consignment_date."',".
             "payment_date = '".$this->payment_date."',".
             "priority_id = '".$this->priorityId."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("changed project ".$this->name);

      return true;
    }
    else {
      return false;
    }
  }

  function check() {
    global $dbInst,$toolInst;

    if (! $this->name) {
      $toolInst->errorStatus("no project name given");
      return false;
    }
    if (! $this->customerId) {
      $toolInst->errorStatus("no customer selected. Please create a customer first.");
      return false;
    }
    if (! $this->managerId) {
      $toolInst->errorStatus("no manager selected. Please give a user manager-access first.");
      return false;
    }
    if ($this->rate && ! $toolInst->checkFloat($this->rate)) {
      $toolInst->errorStatus("please enter a valid number as rate");
      return false;
    }
    return true;
  }

  function delete() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("project.delete")) return false;

    # check dependencies
    if ($dbInst->getValue("select id from ".$dbInst->config['table_task']." where project_id = '".$this->id."'")) {
      $toolInst->errorStatus("dependency check failed: there are existing tasks assigned to this project");
      return false;
    }

    if ($dbInst->getValue("select id from ".$dbInst->config['table_request']." where project_id = '".$this->id."'")) {
      $toolInst->errorStatus("dependency check failed: there are existing requests assigned to this project");
      return false;
    }

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    $this->activate($this->id);
    $result = $dbInst->query("delete from ".$dbInst->config['table_project']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    // logging
    $this->logger->warn("deleted project ".$this->name);

    return true;
  }
  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: project.inc.php,v $
 * Revision 1.8  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 * Revision 1.7  2004/02/28 15:17:30  znouza
 * index.php
 *  - added 'plugins' translation
 *
 * inc/project, request, task:
 *  - changed rendering of:
 *    - project status names
 *    - priority names
 *    - status names
 *    - request names
 *  with added language options (see bottom lines at lang/cz.inc.php)
 *
 * lang/cz.inc.php
 *  - added plugin/plugins translation (group common_)
 *  - added names translations (see above)
 *
 * Revision 1.6  2003/11/21 05:33:36  arneke
 * Added statusId filtering to project and roadmap plugin
 *
 * Revision 1.5  2003/11/17 20:41:13  willuhn
 * @N some more fixes at the new project status plugin
 *
 * Revision 1.4  2003/11/17 19:35:17  willuhn
 * @N added Max Nitris changes
 *
 * Revision 1.4  2003/09/27 18:23:45  willuhn
 * added paid, consignment_date, payment_date fields

 * Revision 1.3  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.1.1.1  2003/07/28 19:22:51  willuhn
 * reimport
 *
 * Revision 1.30  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.29  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.28  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
