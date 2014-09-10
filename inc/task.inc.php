<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/task.inc.php,v $
 * $Revision: 1.11 $
 * $Date: 2004/04/01 19:33:29 $
 * $Author: znouza $
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

class task extends request {
  // instance vars

  /**
   * posted time (UNIX-timestamp)
   * @access public
   */
  var $time;

  /**
   * planned finish time (UNIX-timestamp)
   * @access public
   */
  var $finish;

  /**
   * user id, the task is asigned to (user->id)
   * @access public
   */
  var $userId;

  /**
   * status id of task (request, in progress, waiting, done)
   * see table "taskstatus" for details
   * @access public
   */
  var $statusId;

  /**
   * user id of task creator (user->id)
   * @access public
   */
  var $posterId;

  /**
   * mount point (self->id)
   * @access public
   */
  var $mountId;

  /**
   * planned time for this task (in hours)
   * @access public
   */
  var $plannedHours;

  /**
   * array containing the status names
   * @access private
   */
  var $statusNames = array();

  /**
   * array containing the status styles
   * @access private
   */
  var $statusStyles = array();

  /**
   * fixed price
   */
  var $fixedPrice;

  /**
   * if you want to filter the matches getList() will return,
   * set here something, you want to search for
   * @access public
   */
  var $filterBody;
  var $filterTime;
  var $filterTypeId;
  var $filterUserId;
  var $filterSubject;
  var $filterStatusId;
  var $filterInvertStatus;
  var $filterProjectId;
  var $filterPriorityId;
  var $filterCustomerId;
  var $filterMountId;

  /**
   * if you set a time filter for searching, by default
   * getList() searches for tasks, newer than given timestamp
   */
  var $filterTimeBase;

  var $logger;

  /*********************************************************************
  ** constructor                                                     **/

  /**
   * if you give a valid id here, the constructor will
   * automatically activate the given task
   * @access public
   */
  function task($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("TASK");

    $this->loadStates();
    $this->loadPriorities();
    $this->loadTypes();
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  /**
   * void activate()
   * @param int valid id
   * @access public
   */
  function activate($id) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("task.activate")) return false;

    $query = "select * from ".$dbInst->config['table_task']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id           = $row['id'];
    $this->body         = $row['body'];
    $this->time         = $row['time'];
    $this->finish       = $row['finish'];
    $this->typeId       = $row['type_id'];
    $this->userId       = $row['user_id'];
    $this->subject      = $row['subject'];
    $this->mountId      = $row['mount_id'];
    $this->statusId     = $row['status_id'];
    $this->posterId     = $row['poster_id'];
    $this->projectId    = $row['project_id'];
    $this->priorityId   = $row['priority_id'];
    $this->plannedHours = $row['plannedhours'];
    $this->fixedPrice   = $row['fixedprice'];
    if ($this->plannedHours == "0") $this->plannedHours = "";
    if ($this->fixedPrice == "0") $this->fixedPrice = "";

    // load attachment ids
    $query = "select id from ".$dbInst->config['table_attachment']." where task_id = '".$this->id."'";
    $result = $dbInst->query($query);
    $this->attachments = array();
    while ($row = $dbInst->fetchArray($result[0])) {
      $this->attachments[] = $row['id'];
    }
  }

  function loadStates() {
    // initialize only once
    if (count($this->statusNames) < 2) {
      global $dbInst;
    global $lang;
      $result = $dbInst->query("select id,name,style from ".$dbInst->config['table_taskstatus']);
      while($row = $dbInst->fetchArray($result[0])) {
        $this->statusNames[$row['id']] = (isset($lang['names_status'][$row['id']])) ? $lang['names_status'][$row['id']] : $row['name'];
        $this->statusStyles[$row['id']] = $row['style'];
      }
    }
  }

  /**
   * void fill()
   * fills the task instance with form data in $array[]
   * the form fields must have the following names:
   * finishminute,finishhour,finishday,finishmonth,finishyear,plannedhours
   * body,typeid,userid,subject,statusid,projectid,priorityid,attachment
   * @access public
   */
  function fill($array) {
    global $toolInst,$loginInst;

    $this->posterId     = $loginInst->id;
    $this->time         = $toolInst->getTime("U");
    $this->finish       = $toolInst->timestampToSec($array['finishyear'],$array['finishmonth'],$array['finishday'],$array['finishhour'],$array['finishminute'],"0");
    $this->body         = $array['body'];
    $this->typeId       = $array['typeid'];
    $this->userId       = $array['userid'];
    $this->subject      = $array['subject'];
    $this->mountId      = $array['mountid'];
    $this->statusId     = $array['statusid'];
    $this->projectId    = $array['projectid'];
    $this->priorityId   = $array['priorityid'];
    $this->file         = tool::secureFiles('userfile','name');
    $this->plannedHours = $array['plannedhours'];
    $this->fixedPrice   = $array['fixedprice'];
  }

  /**
   * void clear()
   * makes a task empty
   * @access public
   */
  function clear() {
    $this->id           = "";
    $this->body         = "";
    $this->time         = "";
    $this->file         = "";
    $this->finish       = "";
    $this->typeId       = "";
    $this->userId       = "";
    $this->subject      = "";
    $this->mountId      = "";
    $this->statusId     = "";
    $this->posterId     = "";
    $this->projectId    = "";
    $this->priorityId   = "";
    $this->plannedHours = "";
    $this->fixedPrice   = "";
    $this->attachments  = array();
  }

  /**
   * void fillFilter()
   * sets filter vars based on Form data
   * the form fields must have the following names:
   * filtertimemin
   * filtertimehour
   * filtertimeday
   * filtertimemonth
   * filtertimeyear
   * filtertimebase
   * filterbody
   * filtertypeid
   * filteruserid
   * filtersubject
   * filterinvertstatus
   * filterstatusid
   * filterprojectid
   * filterpriorityid
   * filtercustomerid
   * filtermountid

   * @access public
   */
  function fillFilter($array) {
    global $toolInst,$loginInst,$config;

    if (!$loginInst->hasAccess("task.fillFilter")) return false;

    // filter by time
    $x = $config['defaulttasktimelimit'] * 60 * 60 * 24;
    $filterTimeMin   = $toolInst->getTime("i",time()-$x);
    $filterTimeHour  = $toolInst->getTime("H",time()-$x);
    $filterTimeDay   = $toolInst->getTime("d",time()-$x);
    $filterTimeMonth = $toolInst->getTime("m",time()-$x);
    $filterTimeYear  = $toolInst->getTime("Y",time()-$x);
    // "-1" is a sucking workaround, because $array[x] seems
    // to be deleted after testing for "0"
    if ($array['filtertimemin']   > -1    && $array['filtertimemin']   <= 59  ) $filterTimeMin   = $array['filtertimemin'];
    if ($array['filtertimehour']  > -1    && $array['filtertimehour']  <= 23  ) $filterTimeHour  = $array['filtertimehour'];
    if ($array['filtertimeday']   >= 1    && $array['filtertimeday']   <= 31  ) $filterTimeDay   = $array['filtertimeday'];
    if ($array['filtertimemonth'] >= 1    && $array['filtertimemonth'] <= 12  ) $filterTimeMonth = $array['filtertimemonth'];
    if ($array['filtertimeyear']  >= 2001 && $array['filtertimeyear']  <= 2035) $filterTimeYear  = $array['filtertimeyear'];

    $this->filterTime     = $toolInst->timestampToSec($filterTimeYear,$filterTimeMonth,$filterTimeDay,$filterTimeHour,$filterTimeMin);
    if ($array['filtertimebase'] == "<" || $array['filtertimebase'] == ">") $this->filterTimeBase = $array['filtertimebase'];
    else $this->filterTimeBase = "";

    if ($array['filterinvertstatus'] != "") $this->filterInvertStatus = $array['filterinvertstatus'];
    if ($array['filterstatusid'] != "") $this->filterStatusId = $array['filterstatusid'];

    $this->filterBody           = $array['filterbody'];
    $this->filterTypeId         = $array['filtertypeid'];
    $this->filterUserId         = $array['filteruserid'];
    $this->filterSubject        = $array['filtersubject'];
    $this->filterProjectId      = $array['filterprojectid'];
    $this->filterPriorityId     = $array['filterpriorityid'];
    $this->filterCustomerId     = $array['filtercustomerid'];
    $this->filterMountId        = $array['filtermountid'];

  }

  /**
   * void setFilter()
   * creates needed SQL-filter based on the given filter vars
   * @access private
   */
  function setFilter() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("task.setFilter")) return false;

    if (! isset($this->filterBody))    $this->filterBody = "";
    if (! isset($this->filterSubject)) $this->filterSubject = "";
    $filter = "where ".$dbInst->config['table_task'].".body like '%".$this->filterBody."%' ";
    $filter .= "and ".$dbInst->config['table_task'].".subject like '%".$this->filterSubject."%' ";
    if (isset($this->filterTimeBase)     && $this->filterTimeBase     != "") $filter .= "and ".$dbInst->config['table_task'].".time ".$this->filterTimeBase." '".$this->filterTime."' ";
    if (isset($this->filterUserId)       && $this->filterUserId       != "") $filter .= "and ".$dbInst->config['table_task'].".user_id = '".$this->filterUserId."' ";
    if (isset($this->filterTypeId)       && $this->filterTypeId       != "") $filter .= "and ".$dbInst->config['table_task'].".type_id = '".$this->filterTypeId."' ";
    if (isset($this->filterPriorityId)   && $this->filterPriorityId   != "") $filter .= "and ".$dbInst->config['table_task'].".priority_id = '".$this->filterPriorityId."' ";
    $invert = "";
    if (isset($this->filterInvertStatus) && $this->filterInvertStatus != "" && isset($this->filterStatusId) && $this->filterStatusId != "") $invert = "!";
    if (isset($this->filterStatusId)     && $this->filterStatusId     != "") $filter .= "and ".$dbInst->config['table_task'].".status_id ".$invert."= '".$this->filterStatusId."' ";
    if (isset($this->filterProjectId)    && $this->filterProjectId    != "") $filter .= "and ".$dbInst->config['table_task'].".project_id = '".$this->filterProjectId."' ";
    if (isset($this->filterCustomerId)   && $this->filterCustomerId   != "") $filter .= "and ".$dbInst->config['table_customer'].".id = '".$this->filterCustomerId."' and ".$dbInst->config['table_project'].".customer_id = ".$dbInst->config['table_customer'].".id ";
    if (isset($this->filterMountId)      && ereg("[0-9]",$this->filterMountId)) $filter .= "and ".$dbInst->config['table_task'].".mount_id = '".$this->filterMountId."' ";

    return $filter;
  }

  /**
   * array getList()
   * returns an array with found tasks
   * @param String order of array
   *        to prevent ordering by if given field is a foreign key,
   *        use one of the following keywords instead of:
   *        project,username,type,status,priority
   * @param String desc
   * @return array containing the ids, you haved searched for
   * @access public
   */
  function getList($order="time",$desc="ASC") {
    global $dbInst,$loginInst;

    if (!$order || $order == "" || !isset($order)) $order = "time";
    if (!$loginInst->hasAccess("task.getList")) return false;

    if ($loginInst->isCustomer()) {
      // customers should only be able to view tasks from projects where they are assigned to.
      $this->filterCustomerId = $loginInst->customerId;
    }

    $filter = $this->setFilter();
    $filter .= "and ".$dbInst->config['table_user'].".id = ".$dbInst->config['table_task'].".user_id ";
    $filter .= "and ".$dbInst->config['table_tasktype'].".id = ".$dbInst->config['table_task'].".type_id ";
    $filter .= "and ".$dbInst->config['table_taskstatus'].".id  = ".$dbInst->config['table_task'].".status_id ";
    $filter .= "and ".$dbInst->config['table_project'].".id = ".$dbInst->config['table_task'].".project_id ";
    $filter .= "and ".$dbInst->config['table_taskpriority'].".id = ".$dbInst->config['table_task'].".priority_id ";

    // if user has no manager access, user is only allowed to
    // view own tasks
    if (!$loginInst->hasAccess("task.viewOther")) $filter .= "and ".$dbInst->config['table_task'].".user_id = '".$loginInst->id."'";

    // replace orders, if order is an id field
    // prevents sorting by id instead of name ;)
    if ($order == "project")  $order = $dbInst->config['table_project'].".name";
    if ($order == "username") $order = $dbInst->config['table_user'].".username";
    if ($order == "type")     $order = $dbInst->config['table_tasktype'].".name";
    if ($order == "status")   $order = $dbInst->config['table_taskstatus'].".name";
    if ($order == "priority") $order = $dbInst->config['table_taskpriority'].".id";

    $array = array();
    $query = "select distinct ".$dbInst->config['table_task'].".id as id from ".
              $dbInst->config['table_task'].",".
              $dbInst->config['table_customer'].",".
              $dbInst->config['table_project'].",".
              $dbInst->config['table_user'].",".
              $dbInst->config['table_tasktype'].",".
              $dbInst->config['table_taskstatus'].",".
              $dbInst->config['table_taskpriority']." ".
              $filter." order by ".$order." ".$desc;
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    $this->matches = $result[1];
    return $array;
  }

  /**
   * array getStatusList()
   * returns an array with status ids
   * @return array status list
   * @access public
   */
  function getStatusList() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("task.getStatusList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_taskstatus'];
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  /**
   * String getStatusName()
   * returns the name of the given status id (by default the name of the current task status)
   * @param int statusId (optionally)
   * @return status name
   * @access public
   */
  function getStatusName($id="-1") {
    global $loginInst;

    if (!$loginInst->hasAccess("task.getStatusName")) return false;
    if ($id == "-1") $id = $this->statusId;
    return $this->statusNames[$id];
  }

  /**
   * String getStatusStyle()
   * returns the style name of the given status id (by default the style name of the current task status)
   * see styles.css for defined styles
   * @param int statusId (optionally)
   * @return status style
   * @access public
   */
  function getStatusStyle($id="-1") {
    global $loginInst;

    if (!$loginInst->hasAccess("task.getStatusStyle")) return false;
    if ($id == "-1") $id = $this->statusId;
    return $this->statusStyles[$id];
  }

  /**
   * float getCosts()
   * returns the costs of the current task
   * @param boolean include private jobs or not, default is NOT
   * @return float costs
   * @access public
   */
  function getCosts($include_private = false) {
    global $toolInst,$loginInst;

    if (!$loginInst->hasAccess("task.getCosts")) return false;

    if ($this->isFixedPrice()) {
      return $this->fixedPrice;
    }
    return ($toolInst->deductibleHours($this->getSummary($include_private)) * $this->getRate());
  }

  /**
   * float getCustomerCosts()
   * returns the costs of the current task.
   * @param boolean include private jobs or not, default is NOT
   * @return float CustomerCosts
   * @access public
   */
  function getCustomerCosts($include_private = false) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("task.getCustomerCosts")) return false;

    if ($this->isFixedPrice()) {
      return $this->fixedPrice;
    }

    if (!$this->hasToPay()) {
      return 0;
    }
    else return ($toolInst->deductibleHours($this->getSummary($include_private)) * $this->getRate());
  }

  /**
   * int getRate()
   * returns the cost rate of the current task
   * this function checks, if the project, this task is assigned to, has a
   * valid cost rate. If not, the rate of the assigned user will be returned
   * @return int rate
   * @access public
   */
  function getRate() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("task.getRate")) return false;

    $userInst = new user($this->userId);
    $projectInst = new project($this->projectId);
    $rate = $userInst->rate;
    if ($projectInst->rate) $rate = $projectInst->rate;
    return $rate;
  }

  /**
   * int getSummary()
   * returns the summary of seconds of all jobs, assigned to this task
   * with or without private jobs
   * @param boolean include private jobs or not, default is NOT
   * @return int summary
   * @access public
   */
  function getSummary($include_private = false) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("task.getSummary")) return false;
	
	$privateIf = ($include_private) ? "and (flags & ".JOB_FLAG_PRIVATE.")=".JOB_FLAG_PRIVATE : "";

    $sum = $dbInst->getValue("select sum(stop-start) from ".$dbInst->config['table_job']." where task_id = '".$this->id."' and stop != '0' ".$privateif );
    return ($sum + $dbInst->getValue("select sum(".$toolInst->getTime("U")."-start) from ".$dbInst->config['table_job']." where task_id = '".$this->id."' and stop = '0' ".$privateif));
  }

  /**
   * int getCustomerSummary()
   * returns the summary of seconds of all jobs.
   * note: this method returns 0 if the customer doesnt need to pay for this task (see config.inc.php).
   * @param boolean include private jobs or not, default is NOT
   * @return int customerSummary
   * @access public
   */
  function getCustomerSummary($include_private = false) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("task.getCustomerSummary")) return false;

    $privateIf = ($include_private) ? "=" : "!=";

    if (!$this->hasToPay()) {
      return 0;
    }
    else {
      $sum = $dbInst->getValue("select sum(stop-start) from ".$dbInst->config['table_job']." where task_id = '".$this->id."' and stop != '0' and (flags & ".JOB_FLAG_PRIVATE.")$privateIf".JOB_FLAG_PRIVATE);
      return ($sum + $dbInst->getValue("select sum(".$toolInst->getTime("U")."-start) from ".$dbInst->config['table_job']." where task_id = '".$this->id."' and stop = '0' and (flags & ".JOB_FLAG_PRIVATE.")$privateIf".JOB_FLAG_PRIVATE));
    }
  }

  /**
   * checks if the customer has to pay for this task.
   * @return boolean true, if the customer has to pay for it.
   * @access public
   */
  function hasToPay() {
    global $config;
    return in_array($this->typeId,$config['paytasks']);
  }

  /**
   * checks if the task has a fixed price.
   * @return boolean true, if the task has a fixed price.
   * @access public
   */
  function isFixedPrice() {
    return (isset($this->fixedPrice) && tool::checkFloat($this->fixedPrice) && $this->fixedPrice > "0");
  }

  /**
   * return true, if the task is set to "done"
   */
  function isDone() {
    global $loginInst;

    if (!$loginInst->hasAccess("task.isDone")) return false;

    if ($this->statusId == TASK_STATUS_DONE) return true;
    return false;
  }

  /**
   * return true, if the task is available
   */
  function isAvailable() {
    global $loginInst;

    if (!$loginInst->hasAccess("task.isAvailable")) return false;

    if ($this->statusId == TASK_STATUS_REQUEST || $this->statusId == TASK_STATUS_INPROGRESS) return true;
    return false;
  }

  /**
   * sets the task to status "in progress"
   */
  function start() {
    global $loginInst,$toolInst;

    if (!$loginInst->hasAccess("task.start")) return false;

    $this->statusId = TASK_STATUS_INPROGRESS;
    return true;
  }

  /**
   * sets the task to status "done"
   */
  function stop() {
    global $loginInst;

    if (!$loginInst->hasAccess("task.stop")) return false;

    $this->statusId = TASK_STATUS_DONE;
    return true;
  }

  /**
   * int insert()
   * saves a new task in database
   * @return int id, if saving was successful, else false
   * @access public
   */
  function insert() {
    global $dbInst,$loginInst,$toolInst;
    if (!$loginInst->hasAccess("task.insert")) return false;

    if (!$this->check()) return false;

    if (!$this->statusId) $this->statusId = TASK_STATUS_REQUEST;
    $query = "insert into ".$dbInst->config['table_task']." ".
             "(body,time,finish,plannedhours,fixedprice,type_id,user_id,subject,mount_id,status_id,poster_id,project_id,priority_id) ".
             "values (".
             "'".$this->body."',".
             "'".$this->time."',".
             "'".$this->finish."',".
             "'".$this->plannedHours."',".
             "'".str_replace(',','.',$this->fixedPrice)."',".
             "'".$this->typeId."',".
             "'".$this->userId."',".
             "'".$this->subject."',".
             "'".$this->mountId."',".
             "'".$this->statusId."',".
             "'".$this->posterId."',".
             "'".$this->projectId."',".
             "'".$this->priorityId."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_task']);
    $dbInst->status($result[1],"i");

    if ($result[1] == 1 || $result[1] == 0) {
      $this->id = $id;
      $this->mail($this->userId,"there's a NEW task for you:");

      // logging
      $userInst = new user($this->userId);
      $this->logger->info("added task (".$this->subject.") to ".$userInst->name);

      if (tool::secureFiles('userfile','name') && @file_exists(tool::secureFiles('userfile','tmp_name'))) {
        // attachment successfully uploaded, we can save it
        $attach = new attachment();
        $attach->taskId = $this->id;
        $attach->insert();
        $this->logger->info("added attachment (".$this->file.") to task ".$this->subject);
      }

      return $id;
    }
    else {
      return false;
    }
  }

  /**
   * boolean update()
   * updates a task in database
   * @return true on success, else false
   * @access public
   */
  function update() {
    global $dbInst,$config,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("task.update")) return false;

    if (!$this->check()) return false;

    if ($this->id == $this->mountId) {
      $toolInst->errorStatus("recursion error: unable to mount task into itself");
      return false;
    }
    if (in_array($this->id,$this->treeId($this->mountId))) {
      $toolInst->errorStatus("recursion error: unable to mount task into one of its members");
      return false;
    }

    $query = "update ".$dbInst->config['table_task']." set ".
             "finish = '".$this->finish."',".
             "plannedhours = '".$this->plannedHours."',";
    if ($loginInst->hasAccess("task.fixedPrice")) {
      $query .= "fixedprice = '".str_replace(',','.',$this->fixedPrice)."',";
    }
    $query .= "body = '".$this->body."',".
              "type_id = '".$this->typeId."',".
              "user_id = '".$this->userId."',".
              "subject = '".$this->subject."',".
              "mount_id = '".$this->mountId."',".
              "status_id = '".$this->statusId."',".
              "poster_id = '".$this->posterId."',".
              "project_id = '".$this->projectId."',".
              "priority_id = '".$this->priorityId."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {
      // notification mail to manager, if task set to done
      if ($result[1] == 1 && $this->isDone()) {
        $projectInst = new project($this->projectId);
        $this->mail($projectInst->managerId," --== TASK DONE ==--\n\nused time: ".$toolInst->formatTime($this->getSummary()));
      }

      // logging
      $userInst = new user($this->userId);

      $this->logger->info("changed task (".$this->subject.") for ".$userInst->name);

      if (tool::secureFiles('userfile','name') && @file_exists(tool::secureFiles('userfile','tmp_name'))) {
        // attachment successfully uploaded, we can save it
        $attach = new attachment();
        $attach->taskId = $this->id;
        $attach->insert();
        $this->logger->info("added attachment (".$this->file.") to task ".$this->subject);
        unset($_FILES['userfile']);
      }

      // process all childs
      $childs = $this->childs();
      while ($element = current($childs)) {

        $child = new task($element);
        // we need to update the project id in all child tasks
        $child->projectId = $this->projectId;
        // task is set to status "done", we should start all child tasks now
        if ($this->isDone()) {
          $child->start();
          $child->mail($child->userId,"There's an automatically started task for you:");
        }
        $child->update();
        next($childs);
      }
      return true;
    }
    return false;
  }

  /**
   * boolean check()
   * makes a consistency check befor saving any values into database
   * @return false, if something is crap, else true
   * @access private
   */
  function check() {
    global $toolInst;

    if (! $this->subject) {
      $toolInst->errorStatus("no subject given");
      return false;
    }
    if (! $this->time) {
      $this->time = $toolInst->getTime("U");
    }
    if (! $this->finish) {
      // scheduled stop time is not set, we give him 1 week ;)
      $this->finish = $toolInst->getTime("U") + (7*24*60*60);
    }
    if (! $this->userId) {
      $toolInst->errorStatus("no user ID given. please try again");
      return false;
    }
    if (! $this->projectId) {
      $toolInst->errorStatus("no project ID given. please try again");
      return false;
    }
    if ($this->plannedHours != "" && $this->plannedHours != "0" && !$toolInst->checkFloat($this->plannedHours)) {
      $toolInst->errorStatus("please give me useful data for your planned hours");
      return false;
    }

    if ($this->fixedPrice != "" && $this->fixedPrice != "0" && !$toolInst->checkFloat($this->fixedPrice)) {
      $toolInst->errorStatus("please give me useful data for your the fixed price");
      return false;
    }

    // get parent task, you are only able to start a task, if parent tasks is done
    if ($this->statusId == TASK_STATUS_INPROGRESS && ! $this->IsParentDone()) {
        $toolInst->errorStatus("not alloewd to start a child task, if parent task is not set to done.");
        return false;
    }
    return true;
  }

  /**
   * void delete()
   * saves a new task in database
   * @return int id, if saving was successful, else false
   * @access public
   */
  function delete() {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("task.delete")) return false;

    $this->activate($this->id);

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    if ($dbInst->getValue("select id from ".$dbInst->config['table_task']." where mount_id = '".$this->id."'")) {
      $toolInst->errorStatus("dependency check failed: unable to delete task with existing member tasks");
      return false;
    }

    if (count($this->attachments) > 0) {
      $toolInst->errorStatus("dependency check failed: there are existing attachments. Please delete them first.");
      return false;
    }

    # delete assigned jobs
    $jobInst = new job();
    $jobInst->deleteByTask($this->id);

    $this->activate($this->id);
    $result = $dbInst->query("delete from ".$dbInst->config['table_task']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    // logging
    $userInst = new user($this->userId);
    $this->logger->warn("deleted task (".$this->subject.") for ".$userInst->name);

    $this->clear();
  }

  /**
   * Array treeName()
   * create an array with full task path
   * @param  task id
   * @ return full task path width names as array
   */
  function treeName($task = "-1") {
    global $config,$dbInst;

    if ($task == -1) $task = $this->id;

    $path = array();
    $i =0;
    // walk through task tree until task id is not 0
    while ($task != "0" && $i < 50) {
      // get mountpoint and name for next task
      $result = $dbInst->query("select mount_id,subject from ".$dbInst->config['table_task']." where id = '".$task."'");
      $row = $dbInst->fetchArray($result[0]);

      // update task id
      $task = $row['mount_id'];

      // insert name into array
      array_unshift($path,$row['subject']);
      $i++;
    }
    return ($path);
  }

  /**
   * Array treeId()
   * create an array with full task path
   * @param task id
   * @return full task path width ids as array
   */
  function treeId($task = "-1") {
    global $config,$dbInst;

    if ($task == -1) $task = $this->id;

    $path = array();
    $i =0;
    // walk through task tree until task id is not 0
    while ($task != "0" && $i < 50) {

      // get mountpoint and name for next task
      $result = $dbInst->query("select mount_id,id from ".$dbInst->config['table_task']." where id = '".$task."'");
      $row = $dbInst->fetchArray($result[0]);

      // update task id
      $task = $row['mount_id'];

      // insert id into array
      array_unshift($path,$row['id']);
      $i++;
    }
    return ($path);
  }

  /**
   * Array childs()
   * returns an array containing the task ids of all childs from
   * the given/current task
   * @param task id
   * @return array with child tasks
   */
  function childs($task = "-1") {
    global $config,$dbInst;

    if ($task == -1) $task = $this->id;
    $childs = array();
    $result = $dbInst->query("select id from ".$dbInst->config['table_task']." where mount_id = '".$task."'");
    while ($row = $dbInst->fetchArray($result[0])) {
      $childs[] = $row['id'];
    }
    return $childs;
  }

  /**
   * checks whether an existing parent task is set to done
   * @return true if parent task is done, otherwise false
   */
  function isParentDone() {

    if (!isset($this->mountId) || $this->mountId == 0) return true;

    $task = new task($this->mountId);
    if ($task->isDone()) return true;

    return false;
  }

  /**
   * void mail()
   * sends a mail after creating/changing a task
   * @access private
   */
  function mail($recipientId,$msgBody) {
    global $config,$loginInst,$toolInst;

    if (!$config['automail']) return false;


    $userInst = new user($this->userId);
    $projectInst = new project($this->projectId);
    $link = $config['root_url']."/index.php?content=taskdetails.php&view=details&taskid=".$this->id;

    $mailInst = new mail();
    $mailInst->senderId    = $loginInst->id;
    $mailInst->recipientId = $recipientId;
    $mailInst->priorityId  = $this->priorityId;
    $mailInst->subject     = "PMTool: ".$projectInst->name." (".$this->subject.")";

    $mailbody  = "Hello ".$userInst->name.",\n\n";
    $mailbody .= $msgBody."\n\n";
    $mailbody .= "ID            : ".$this->id."\n";
    $mailbody .= "project       : ".$projectInst->name."\n";
    $mailbody .= "subject       : ".$this->subject."\n";
    $mailbody .= "priority      : ".$this->getPriorityName()."\n";
    $mailbody .= "type          : ".$this->getTypeName()."\n";
    $mailbody .= "status        : ".$this->getStatusName()."\n";
    $mailbody .= "finish        : ".$toolInst->getTime("",$this->finish)."\n";
    $mailbody .= "planned hours : ".$this->plannedHours."\n";
    $mailbody .= "body          : ".eregi_replace("\n","\n                ",$this->body)."\n\n";
    $mailbody .= "link          : ".$link."\n\n";
    $mailbody .= "\n--- \nHave fun ;)\n";

    $mailInst->body = $mailbody;
    $mailInst->send();
  }
  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: task.inc.php,v $
 * Revision 1.11  2004/04/01 19:33:29  znouza
 * @C updated summary cost functions in both task and job includes
 * @C task detail summary costs
 *
 * Revision 1.10  2004/03/17 19:30:53  willuhn
 * @N configurable behavior for task types the customer has to pay for
 *
 * Revision 1.9  2004/02/29 17:39:51  willuhn
 * @B some small fixes
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
 * Revision 1.7  2004/02/28 19:42:15  znouza
 *
 * @N new feature: private jobs
 *
 * Revision 1.6  2004/02/28 15:17:30  znouza
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
 * Revision 1.5  2003/11/05 20:31:34  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.2  2003/08/07 22:54:12  willuhn
 * @B bug id 771449 on sf.net
 *
 * Revision 1.1.1.1  2003/07/28 19:22:58  willuhn
 * reimport
 *
 * Revision 1.60  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.59  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.58  2002/09/07 19:23:13  willuhn
 * @N global commit for missing files
 *
 * Revision 1.57  2002/06/26 14:14:29  willuhn
 * @N added form "query task by id"
 *
 * Revision 1.56  2002/05/05 20:12:42  willuhn
 * @N added feature "fixed price" for tasks
 *
 * Revision 1.55  2002/05/03 06:56:06  willuhn
 * @B removed useless debug output
 *
 * Revision 1.54  2002/05/02 22:43:49  willuhn
 * @B order
 *
 * Revision 1.53  2002/05/02 21:42:40  willuhn
 * @N pretty cool new feature in "task hotlist" -> order by planned time left
 *
 * Revision 1.52  2002/05/02 19:57:25  willuhn
 * @N added field in task table "plannedhours"
 *
 * Revision 1.51  2002/05/02 19:51:32  willuhn
 * @N task->isAvailable() checks if task is "in progress" or "request"
 * @N link to parent task in taskdetails
 * @N link to all child tasks in taskdetails
 * @B in task hotlist
 *
 * Revision 1.50  2002/04/29 23:04:07  willuhn
 * @N planned time in task (only reminder - needs to implement ;)
 *
 * Revision 1.49  2002/04/29 20:29:36  willuhn
 * @B when changing the project id of a task, all child task will be
 *    changed also in this point
 *
 * Revision 1.48  2002/04/17 22:26:46  willuhn
 * @N child tasks can only set to "in progress", if parent task is set to "done"
 *
 * Revision 1.47  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.46  2002/04/14 18:09:30  willuhn
 * @N splitted db class into a super class (valid for all databases)
 *    and an implementation class
 * @N some more multilanguage stuff
 *
 * Revision 1.45  2002/04/09 22:53:48  willuhn
 * @N added some grafx
 *
 * Revision 1.44  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.43  2002/03/31 17:40:10  willuhn
 * @N added right "attachment.update"
 * @N attachments are now also available for requests
 *
 * Revision 1.42  2002/03/30 19:55:54  willuhn
 * @N deleting of attachments (in taskdetails)
 *
 * Revision 1.41  2002/03/30 19:24:12  willuhn
 * @N added attachment code
 *
 * Revision 1.40  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.39  2002/02/27 19:41:24  willuhn
 * @N added attachment field
 *
 * Revision 1.38  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 ***************************************************************************/

?>
