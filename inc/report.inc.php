<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/report.inc.php,v $
 * $Revision: 1.4 $
 * $Date: 2004/02/17 22:07:16 $
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

class report {
  // instance vars
  var $id;
  var $subject;
  var $xml;
  var $userId;
  var $projectId;
  var $created;
  var $number;

  var $filterUserId;
  var $filterProjectId;
  var $filterSubject;

  var $logger;
  /*********************************************************************
  ** constructor                                                     **/

  function report($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    else {
      $this->userId = $loginInst->id;

      // we want to have the creation time on new reports too
      if (!isset($this->created) || $this->created == "")
        $this->created = tool::getTime("U");
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("REPORT");
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("report.activate")) return false;

    $query = "select * from ".$dbInst->config['table_report']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id             = $row['id'];
    $this->subject        = $row['subject'];
    $this->xml            = $row['xml'];
    $this->userId         = $row['user_id'];
    $this->projectId      = $row['project_id'];
    $this->created        = $row['created'];
  }

  function clear() {
    $this->id             = "";
    $this->subject        = "";
    $this->xml            = "";
    $this->userId         = "";
    $this->projectId      = "";
    $this->created        = "";
  }

  function setFilter() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("report.setFilter")) return false;

    // this is a dummy filter
    $filter = "where id like '%%' ";
    if ($this->filterSubject) $filter .= "and ".$dbInst->config['table_report'].".subject like '%".$this->filterSubject."%' ";
    if ($this->filterProjectId) $filter .= "and ".$dbInst->config['table_report'].".project_id = '".$this->filterProjectId."' ";
    if (!$loginInst->hasAccess("report.viewOther")) $filter .= "and ".$dbInst->config['table_report'].".user_id = '".$this->userId."' ";
    elseif ($this->filterUserId) $filter .= "and ".$dbInst->config['table_report'].".user_id = '".$this->filterUserId."' ";
    return $filter;
  }

  function getList($order="created",$desc="DESC") {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("report.getList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_report']." ".$this->setFilter()." order by ".$order." ".$desc;
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  function insert() {
    global $dbInst,$toolInst,$loginInst;

    if (!$this->check()) return false;

    if (!$loginInst->hasAccess("report.insert")) return false;

    if (!isset($this->created) || $this->created == "")
      $this->created = $toolInst->getTime("U");

    $query = "insert into ".$dbInst->config['table_report']." ".
             "(subject,xml,user_id,project_id,created) ".
             "values (".
             "'".$this->subject."',".
             "'".$this->xml."',".
             "'".$this->userId."',".
             "'".$this->projectId."',".
             "'".$this->created."')";

    $result = $dbInst->query($query);
    $this->id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_report']);
    $dbInst->status($result[1],"i");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("added report ".$this->subject);

      return $this->id;
    }
    else {
      return false;
    }
  }

  function update() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("report.insert")) return false;

    if (!$this->check()) return false;

    $query = "update ".$dbInst->config['table_report']." set ".
             "subject = '".$this->subject."',".
             "xml = '".$this->xml."',".
             "user_id = '".$this->userId."',".
             "project_id = '".$this->projectId."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("changed report ".$this->subject);

      return true;
    }
    else {
      return false;
    }
  }



  function append($string) {
    $this->xml .= $string."\n";
  }

  function check() {
    global $toolInst;

    if (! $this->userId) {
      $toolInst->errorStatus("no user given");
      return false;
    }
    if (! $this->subject) {
      $toolInst->errorStatus("no subject given");
      return false;
    }

    return true;
  }

  function delete() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("report.delete")) return false;

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    # delete only, if id given
    $this->activate($this->id);
    $result = $dbInst->query("delete from ".$dbInst->config['table_report']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    // logging
    $this->logger->warn("deleted report ".$this->subject);

    return true;
  }
  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: report.inc.php,v $
 * Revision 1.4  2004/02/17 22:07:16  willuhn
 * @N added consecutive id to xml code of reports
 * @N added czech language file
 * @N added spanish language file
 *
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
 * Revision 1.7  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.6  2002/03/30 19:24:12  willuhn
 * @N added attachment code
 *
 * Revision 1.5  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
