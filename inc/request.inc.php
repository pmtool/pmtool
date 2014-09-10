<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/request.inc.php,v $
 * $Revision: 1.7 $
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

class request extends priority {

  // public instance vars
  /**
  * contains database id
  * @access public
  */
  var $id;
  /**
  * contains body of request (description)
  * @access public
  */
  var $body;
  /**
  * contains the time, the request was posted ar
  * @access public
  */
  var $time;
  /**
  * subject of request
  * @access public
  */
  var $subject;
  /**
  * poster id, the request is assigned to (user->id)
  * @access public
  */
  var $posterId;
  /**
  * project id, the request is assigned to (project->id)
  * @access public
  */
  var $projectId;

  /**
  * id of task type (bug,new,change)
  * see table "tasktype" for ids/names
  * @access public
  */
  var $typeId;

  /**
   * array containing the type names
   * @access private
   */
  var $typeNames = array();

  /**
   * array containing the type styles
   * @access private
   */
  var $typeStyles = array();

  /**
   * attachments (ids)
   * @access public
   */
  var $attachments = array();

  /**
   * contains the file name of the current attachment
   * @access private
   */
  var $file;

  /**
  * contains number of matches after getList()
  */
  var $matches = 0;

  var $filterManagerId;

  var $logger;

  /*********************************************************************
  ** constructor                                                     **/

  function request($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("REQUEST");

    $this->loadTypes();

    // call super constructor
    parent::priority();
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.activate")) return false;

    $query = "select * from ".$dbInst->config['table_request']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id          = $row['id'];
    $this->subject     = $row['subject'];
    $this->body        = $row['body'];
    $this->time        = $row['time'];
    $this->priorityId  = $row['priority_id'];
    $this->typeId      = $row['type_id'];
    $this->posterId    = $row['poster_id'];
    $this->projectId   = $row['project_id'];

    // load attachment ids
    $query = "select id from ".$dbInst->config['table_attachment']." where task_id = '".$this->id."'";
    $result = $dbInst->query($query);
    $this->attachments = array();
    while ($row = $dbInst->fetchArray($result[0])) {
      $this->attachments[] = $row['id'];
    }
  }

  // load task types into cache
  function loadTypes() {
    // initialize only once
    if (count($this->typeNames) < 2) {
      global $dbInst;
    global $lang;
      $result = $dbInst->query("select id,name,style from ".$dbInst->config['table_tasktype']);
      while($row = $dbInst->fetchArray($result[0])) {
        $this->typeNames[$row['id']] = (isset($lang['names_type'][$row['id']])) ? $lang['names_type'][$row['id']]:$row['name'];
        $this->typeStyles[$row['id']] = $row['style'];
      }
    }
  }


  function fill($array) {
    global $loginInst;

    $this->subject     = $array['subject'];
    $this->body        = $array['body'];
    $this->time        = $array['time'];
    $this->priorityId  = $array['priorityid'];
    $this->typeId      = $array['typeid'];
    $this->posterId    = $loginInst->id;
    $this->projectId   = $array['projectid'];
    $this->file        = tool::secureFiles('userfile','name');
  }

  function clear() {
    $this->id          = "";
    $this->subject     = "";
    $this->file        = "";
    $this->body        = "";
    $this->time        = "";
    $this->priorityId  = "";
    $this->typeId      = "";
    $this->posterId    = "";
    $this->projectId   = "";
    $this->attachments = array();
  }

  function setFilter() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.setFilter")) return false;

    $filter = "where ".$dbInst->config['table_request'].".project_id = ".$dbInst->config['table_project'].".id ";
    $filter .= "and ".$dbInst->config['table_request'].".priority_id = ".$dbInst->config['table_taskpriority'].".id ";
    $filter .= "and ".$dbInst->config['table_request'].".type_id = ".$dbInst->config['table_tasktype'].".id ";
    // customers should only be able to view requests for own projects
    if ($loginInst->isCustomer()) {
      $projectInst = new project();
      $list = $projectInst->getList();
      if ($projectInst->matches >= 1) $filter .= "and (";
      $or = "";
      while($element = current($list)) {
        $filter .= " ".$or." (".$dbInst->config['table_request'].".project_id = '".$element."')";
        $or = "or";
        next($list);
      }
      if ($projectInst->matches >= 1) $filter .= ")";
    }
    return $filter;
  }

  function getList($order="subject",$desc="ASC") {
    global $dbInst,$loginInst;

    if (!$order || $order == "" || !isset($order)) $order = "subject";
    if (!$loginInst->hasAccess("request.getList")) return false;

    // replace orders, if order is an id field
    // prevents sorting by id instead of name ;)
    if ($order == "project")  $order = $dbInst->config['table_project'].".name";
    if ($order == "type")     $order = $dbInst->config['table_tasktype'].".name";
    if ($order == "priority") $order = $dbInst->config['table_taskpriority'].".name";

    $array = array();
    $query = "select distinct ".$dbInst->config['table_request'].".id as id from ".
              $dbInst->config['table_request'].",".
              $dbInst->config['table_project'].",".
              $dbInst->config['table_tasktype'].",".
              $dbInst->config['table_taskpriority']." ".
              $this->setFilter()." order by ".$order." ".$desc;

    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    $this->matches = $result[1];
    return $array;
  }

  /**
  * array getTypeList()
  * returns an array with type ids
  * @return array type list
  * @access public
  */
  function getTypeList() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.getTypeList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_tasktype'];
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  /**
  * String getTypeName()
  * returns the name of the given type id (by default the name of the current task type)
  * @param int typeId (optionally)
  * @return type name
  * @access public
  */
  function getTypeName($id="-1") {
    global $loginInst;

    if (!$loginInst->hasAccess("request.getTypeName")) return false;
    if ($id == "-1") $id = $this->typeId;
    return $this->typeNames[$id];
  }

  /**
  * String getTypeStyle()
  * returns the style name of the given type id (by default the style name of the current task type)
  * see styles.css for defined styles
  * @param int typeId (optionally)
  * @return type style
  * @access public
  */
  function getTypeStyle($id="-1") {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.getTypeStyle")) return false;
    if ($id == "-1") $id = $this->typeId;
    return $this->typeStyles[$id];
  }

  function insert() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("request.insert")) return false;

    if (!$this->check()) return false;

    $query = "insert into ".$dbInst->config['table_request']." ".
             "(subject,body,time,priority_id,poster_id,type_id,project_id) ".
             "values (".
             "'".$this->subject."',".
             "'".$this->body."',".
             "'".$toolInst->getTime("U")."',".
             "'".$this->priorityId."',".
             "'".$this->posterId."',".
             "'".$this->typeId."',".
             "'".$this->projectId."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_request']);

    $dbInst->status($result[1],"i");
    if ($result[1] == 1 || $result[1] == 0) {
      $this->id = $id;
      $this->mail("there's a NEW request for you. Please assign this to a developer:");

      // logging
      $projectInst = new project($this->projectId);
      $this->logger->info("added request (".$this->subject.") to project ".$projectInst->name);

      if (tool::secureFiles('userfile','name') && @file_exists(tool::secureFiles('userfile','tmp_name'))) {
        // attachment successfully uploaded, we can save it
        $attach = new attachment();
        $attach->taskId = $this->id;
        $attach->insert();
        $this->logger->info("added attachment (".$this->file.") to request ".$this->subject);
      }

      return $id;
    }
    else {
      return false;
    }
  }

  function update() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.update")) return false;

    if (!$this->check()) return false;

    $query = "update ".$dbInst->config['table_request']." set ".
             "subject = '".$this->subject."',".
             "body = '".$this->body."',".
             "priority_id = '".$this->priorityId."',".
             "type_id = '".$this->typeId."',".
             "project_id = '".$this->projectId."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {
      // mail only, if request was really modified
      if ($result[1] == 1) $this->mail("there is a MODIFIED request for you:");

      // logging
      $projectInst = new project($this->projectId);
      $this->logger->info("changed request (".$this->subject.") for project ".$projectInst->name);

      return true;
    }
    else {
      return false;
    }
  }

  function check() {
    global $dbInst,$toolInst;

    if (! $this->projectId) {
      $toolInst->errorStatus("no project selected");
      return false;
    }
    if (! $this->subject) {
      $toolInst->errorStatus("no subject given.");
      return false;
    }
    return true;
  }

  function delete() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("request.delete")) return false;

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    $this->activate($this->id);
    $result = $dbInst->query("delete from ".$dbInst->config['table_request']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    // delete all assigned attachments
    while ($element = current($this->attachments)) {
      $attachment = new attachment($element);
      $attachment->delete();
      next($this->attachments);
    }
    // logging
    $projectInst = new project($this->projectId);
    $this->logger->warn("deleted request (".$this->subject.") for project ".$projectInst->name);

    return true;
  }

  function assignTo($userId) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("request.assignTo")) return false;

    $taskInst = new task();
    $taskInst->subject = $this->subject;
    $taskInst->body = $this->body;
    $taskInst->projectId = $this->projectId;
    $taskInst->typeId = $this->typeId;
    $taskInst->priorityId = $this->priorityId;
    $taskInst->userId = $userId;
    $taskInst->statusId = TASK_STATUS_REQUEST;
    $taskInst->posterId = $loginInst->id;
    $taskInst->attachments = $this->attachments;

    $taskId = $taskInst->insert();
    if (! $taskId) return false;

    // task saved successfully. Now whe can assign the attachments to the task id
    $taskInst->id = $taskId;
    while ($element = current($taskInst->attachments)) {
      $attachment = new attachment($element);
      $attachment->taskId = $taskId;
      $attachment->update();
      next($taskInst->attachments);
    }
    // the attachments are now assigned to the task -> so we should
    // clean the attachment array in the request instance, to prevent
    // deleting of the attachments
    $this->attachments = array();
    if ($this->delete()) return $taskId;
    return false;
  }

  /**
  * void mail()
  * sends a mail after creating/changing a request
  * @access private
  */
  function mail($msgBody) {
    global $config,$loginInst;

    if (!$config['automail']) return false;


    $link = $config['root_url']."/index.php?content=requestdetails.php&view=details&requestid=".$this->id;
    $projectInst = new project($this->projectId);
    $userInst = new user($projectInst->managerId);

    $mailInst = new mail();
    $mailInst->senderId    = $loginInst->id;
    $mailInst->recipientId = $userInst->id;
    $mailInst->priorityId  = $this->priorityId;
    $mailInst->subject     = "PMTool: (REQUEST) ".$projectInst->name." (".$this->subject.")";

    $mailbody  = "Hello ".$userInst->name.",\n\n";
    $mailbody .= $msgBody."\n\n";
    $mailbody .= "project  : ".$projectInst->name."\n";
    $mailbody .= "subject  : ".$this->subject."\n";
    $mailbody .= "priority : ".$this->getPriorityName()."\n";
    $mailbody .= "type     : ".$this->getTypeName()."\n";
    $mailbody .= "body     : ".eregi_replace("\n","\n           ",$this->body)."\n\n";
    $mailbody .= "link     : ".$link."\n\n";
    $mailbody .= "\n--- \nHave fun ;)\n";

    $mailInst->body = $mailbody;
    $mailInst->send();
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: request.inc.php,v $
 * Revision 1.7  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 * Revision 1.6  2004/03/17 19:30:52  willuhn
 * @N configurable behavior for task types the customer has to pay for
 *
 * Revision 1.5  2004/02/28 23:01:27  znouza
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
 * Revision 1.4  2004/02/28 15:17:30  znouza
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
 * Revision 1.3  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.1.1.1  2003/07/28 19:23:00  willuhn
 * reimport
 *
 * Revision 1.20  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.19  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.18  2002/06/29 17:27:20  willuhn
 * @B fixed in sql query (id 161)
 *
 * Revision 1.17  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.16  2002/03/31 17:40:10  willuhn
 * @N added right "attachment.update"
 * @N attachments are now also available for requests
 *
 * Revision 1.15  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.14  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
