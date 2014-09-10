<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/job.inc.php,v $
 * $Revision: 1.7 $
 * $Date: 2005/02/22 16:15:13 $
 * $Author: genghishack $
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
 * marks this job as private - is not visible in reports, project stats etc
 * or is visible marked as private
 * @const JOB_FLAG_PRIVATE
**/
define ('JOB_FLAG_PRIVATE', 0x0001);

class job {

  // instance vars
  var $id;
  var $taskId;
  var $userId;
  var $comment;
  var $start;
  var $stop;
  /**
   * integer contain job flags
   * @access private
  **/
  var $flags;

  var $filterTaskId;
  var $filterUserId;
  var $filterStartTime;
  var $filterStopTime;
  var $filterCurrentlyRunning;
  var $filterShowPrivateJobs;

  var $matches = 0;

  var $logger;

  /*********************************************************************
  ** constructor                                                     **/
  function job($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("JOB");
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("job.activate")) return false;

    $query = "select * from ".$dbInst->config['table_job']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id       = $row['id'];
    $this->taskId   = $row['task_id'];
    $this->userId   = $row['user_id'];
    $this->comment  = $row['comment'];
    $this->start    = $row['start'];
    $this->stop     = $row['stop'];
    $this->flags    = $row['flags'];
  }

  function isFlag($flag) {

    if ($flag<=0) return 0;

    $flg = (($this->flags & $flag) == $flag) ? 1 : 0;
	return $flg;
  }

  function getOpenJob($id="-1") {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("job.getOpenJob")) return false;

    if ($id == "-1") $id = $loginInst->id;
    return $dbInst->getValue("select id from ".$dbInst->config['table_job']." where user_id = '".$id."' and stop = '0'");
  }

  function fill($array) {

    $this->taskId         = $array['taskid'];
    $this->userId         = $array['userid'];
    $this->comment        = $array['comment'];
    $this->start          = $array['start'];
    $this->stop           = $array['stop'];
    $this->flags          = $array['flags'];
  }

  function fillFilter($array) {
    global $dbInst,$toolInst;

    if ($array['filtercurrentlyrunning']=="1") $this->filterCurrentlyRunning = 1;
    if ($array['filtershowprivatejobs']=="1") $this->filterShowPrivateJobs = 1;

    # set start limit by default to the 1st of the actual month
    $month = $toolInst->getTime("m",$toolInst->getTime("U"));
    $year = $toolInst->getTime("Y",$toolInst->getTime("U"));
    if ($toolInst->getTime("m",$toolInst->getTime("U")) == 1) {
      $month = 12;
      $year--;
    }
    $startLimit = $toolInst->timestampToSec($year,$month,"1","0","0","0");

    $filterStartTimeMin   = $toolInst->getTime("i",$startLimit);
    $filterStartTimeHour  = $toolInst->getTime("H",$startLimit);
    $filterStartTimeDay   = $toolInst->getTime("d",$startLimit);
    $filterStartTimeMonth = $toolInst->getTime("m",$startLimit);
    $filterStartTimeYear  = $toolInst->getTime("Y",$startLimit);

    $stopLimit = $toolInst->getTime("U");
    $filterStopTimeMin   = $toolInst->getTime("i",$stopLimit);
    $filterStopTimeHour  = $toolInst->getTime("H",$stopLimit);
    $filterStopTimeDay   = $toolInst->getTime("d",$stopLimit);
    $filterStopTimeMonth = $toolInst->getTime("m",$stopLimit);
    $filterStopTimeYear  = $toolInst->getTime("Y",$stopLimit);

    // "-1" is a sucking workaround, because $array[x] seems
    // to be deleted after testing for "0"
    if ($array['filterstarttimemin']   > -1    && $array['filterstarttimemin']   <= 59  ) $filterStartTimeMin   = $array['filterstarttimemin'];
    if ($array['filterstarttimehour']  > -1    && $array['filterstarttimehour']  <= 23  ) $filterStartTimeHour  = $array['filterstarttimehour'];
    if ($array['filterstarttimeday']   >= 1    && $array['filterstarttimeday']   <= 31  ) $filterStartTimeDay   = $array['filterstarttimeday'];
    if ($array['filterstarttimemonth'] >= 1    && $array['filterstarttimemonth'] <= 12  ) $filterStartTimeMonth = $array['filterstarttimemonth'];
    if ($array['filterstarttimeyear']  >= 2001 && $array['filterstarttimeyear']  <= 2035) $filterStartTimeYear  = $array['filterstarttimeyear'];
    $this->filterStartTime = $toolInst->timestampToSec($filterStartTimeYear,$filterStartTimeMonth,$filterStartTimeDay,$filterStartTimeHour,$filterStartTimeMin);

    if ($array['filterstoptimemin']   > -1    && $array['filterstoptimemin']   <= 59  ) $filterStopTimeMin   = $array['filterstoptimemin'];
    if ($array['filterstoptimehour']  > -1    && $array['filterstoptimehour']  <= 23  ) $filterStopTimeHour  = $array['filterstoptimehour'];
    if ($array['filterstoptimeday']   >= 1    && $array['filterstoptimeday']   <= 31  ) $filterStopTimeDay   = $array['filterstoptimeday'];
    if ($array['filterstoptimemonth'] >= 1    && $array['filterstoptimemonth'] <= 12  ) $filterStopTimeMonth = $array['filterstoptimemonth'];
    if ($array['filterstoptimeyear']  >= 2001 && $array['filterstoptimeyear']  <= 2035) $filterStopTimeYear  = $array['filterstoptimeyear'];
    $this->filterStopTime = $toolInst->timestampToSec($filterStopTimeYear,$filterStopTimeMonth,$filterStopTimeDay,$filterStopTimeHour,$filterStopTimeMin);

    // prevents useless dates (stop time earlier than start time)
    if ($this->filterStopTime < $this->filterStartTime) $this->filterStopTime = $stopLimit;
  }

  function clear() {
    $this->id       = "";
    $this->taskId   = "";
    $this->userId   = "";
    $this->comment  = "";
    $this->start    = "";
    $this->stop     = "";
    $this->flags    = 0;
  }

  function setFilter() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("job.setFilter")) return false;

    // this is a dummy filter
    $filter = "where id like '%%' ";
    if (!$loginInst->hasAccess("job.viewOther")) {
        $filter .= "and {$dbInst->config['table_job']}.user_id = '{$loginInst->id}' ";
    }
    if (isset($this->filterUserId)           && $this->filterUserId           != "") {
        $filter .= "and {$dbInst->config['table_job']}.user_id = '{$this->filterUserId}' ";
    }
    if (isset($this->filterTaskId)           && $this->filterTaskId           != "") {
        $filter .= "and {$dbInst->config['table_job']}.task_id = '{$this->filterTaskId}' ";
    }
    if (isset($this->filterStartTime)        && $this->filterStartTime        != "" && $this->filterStopTime == "") {
        //echo "Start Time: {$this->filterStartTime} <br/>";
        $filter .= "and {$dbInst->config['table_job']}.start >= '{$this->filterStartTime}' ";
    }
    if (isset($this->filterStopTime)         && $this->filterStopTime         != "" && $this->filterStartTime == "") {
        //echo "Stop Time: {$this->filterStopTime} <br/>";
        $filter .= "and {$dbInst->config['table_job']}.stop <= '{$this->filterStopTime}' ";
    }
    // CMW - If both filterStartTime and FilterStopTime are set, we need to make sure that we are displaying jobs whose
    // stop time only is within the date range.  Otherwise only jobs that fall entirely within the date range will be displayed. 
    if ((isset($this->filterStartTime)        && $this->filterStartTime        != "")
    &&  (isset($this->filterStopTime)         && $this->filterStopTime         != "")) {
        // stop time is today
        // "and ( (job.stop >= [filterStartTime] and job.stop <= [filterStopTime])"
        // or job is still running
        // "or (job.stop = 0) )
        $filter .= "and (
                          ( {$dbInst->config['table_job']}.stop >= '{$this->filterStartTime}' and {$dbInst->config['table_job']}.stop <= '{$this->filterStopTime}' )
                          or
                          ( {$dbInst->config['table_job']}.stop = 0)
                        ) "; 
    }
            
    if (isset($this->filterCurrentlyRunning) && $this->filterCurrentlyRunning != "") {
        $filter .= "and ".$dbInst->config['table_job'].".stop != '0' ";
    }

    if (isset($this->filterShowPrivateJobs)  && $this->filterShowPrivateJobs  != "") {
		//$filter .= "and (".$dbInst->config['table_job'].".flags & ".JOB_FLAG_PRIVATE.")=".JOB_FLAG_PRIVATE." ";
	  } else {
  		$filter .= "and (".$dbInst->config['table_job'].".flags & ".JOB_FLAG_PRIVATE.")!=".JOB_FLAG_PRIVATE." ";
  	}

    return $filter;
  }

  function getList($order="start",$desc="DESC") {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("job.getList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_job']." ".$this->setFilter()." order by ".$order." ".$desc;

    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    $this->matches = $result[1];
    return $array;
  }

  function start() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("job.start")) return false;

    if (!$this->check()) return false;

    if (isset($this->id)) {
      $otherTask = $dbInst->getValue("select task_id from ".$dbInst->config['table_job']." where id != '".$this->id."' and user_id = '$loginInst->id' and stop = '0'");
      if ($otherTask) {
        $taskInst = new task($otherTask);
        $toolInst->errorStatus("theres allready a running job in &quot;".substr($taskInst->subject,0,20)."...&quot;");
        return false;
      }
    }

    // set task to "in progress"
    $taskInst = new task($this->taskId);
    $taskInst->start();
    $taskInst->update();

    $query = "insert into ".$dbInst->config['table_job']." ".
             "(task_id,user_id,comment,start,stop,flags) ".
             "values (".
             "'".$this->taskId."',".
             "'".$loginInst->id."',".
             "'".$this->comment."',".
             "'".$this->start."',".
             "'0',".
			 "'".$this->flags."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_job']);
    $dbInst->status($result[1],"i");

    if ($result[1] == 1 || $result[1] == 0) {
      return $id;
    }
    else {
      return false;
    }
  }

  function stop() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("job.stop")) return false;

    if (!$this->check()) return false;

    if (! $this->stop) {
      $this->stop = $toolInst->getTime("U");
    }

    if ($this->start > $this->stop) {
      $toolInst->errorStatus("start time can not be greater than stop time");
      return false;
    }

    $query = "update ".$dbInst->config['table_job']." set ".
             "task_id = '".$this->taskId."',".
             "user_id = '".$loginInst->id."',".
             "start = '".$this->start."',".
             "stop = '".$this->stop."',".
             "flags = '".$this->flags."',".
             "comment = '".$this->comment."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {
      return true;
    }
    else {
      return false;
    }
  }

  function check() {
    global $dbInst,$toolInst,$loginInst;

    if (! $this->taskId) {
      $toolInst->errorStatus("no task selected");
      return false;
    }

    if ($this->userId > $loginInst->id) {
      $toolInst->errorStatus("It's not allowed to change jobs from other users");
      return false;
    }

    if (! isset($this->start) || $this->start == "") {
      $this->start = $toolInst->getTime("U");
    }
    return true;
  }

  function delete() {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("job.delete")) return false;

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    $this->activate($this->id);

    // logging
    $this->logger->warn("deleted job (".substr($this->comment,0,50).")");

    $result = $dbInst->query("delete from ".$dbInst->config['table_job']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    return true;
  }

  function deleteByTask($taskid) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("job.deleteByTask")) return false;

    $this->activate($this->id);

    if (! $toolInst->checkInt($taskid)) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    $result = $dbInst->query("delete from ".$dbInst->config['table_job']." where task_id = '".$taskid."'");
    $dbInst->status($result[1],"d");

    // logging
    $taskInst = new task($taskid);
    $this->logger->warn("deleted all jobs in task (".$taskInst->subject.")");

    $this->clear();
    return true;
  }

  /**
   * @param boolean include private jobs into summary. default is NOT
  **/

  function getSummary($include_private = false) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("job.getSummary")) return false;

	$privateIf = ($include_private) ? "and (flags & ".JOB_FLAG_PRIVATE.")=".JOB_FLAG_PRIVATE : "";

    if ($this->stop == 0) {
      return $dbInst->getValue("select sum(".$toolInst->getTime("U")."-start) from ".$dbInst->config['table_job']." where id = '".$this->id."' ".$privateif) ;
    }
    else {
      return $dbInst->getValue("select sum(stop-start) from ".$dbInst->config['table_job']." where id = '".$this->id."' ".$privateif);
    }
  }

  function dumpXml() {
    global $loginInst;

    if (!$loginInst->hasAccess("job.dumpXml")) return false;

    $username = "";
    if ($loginInst->hasAccess("report.viewOther")) {
      $userInst = new user($this->userId);
      $username = $userInst->username;
    }

    $taskInst = new task($this->taskId);
    $projectInst = new project($taskInst->projectId);

    $return  = "";
    $return .= "<job>\n";
    $return .= "  <id>".$this->id."</id>\n";
    $return .= "  <taskid>".$this->taskId."</taskid>\n";
    $return .= "  <comment>".substr($this->comment,0,50)."...</comment>\n";
    $return .= "  <user>".$username."</user>\n";
    $return .= "  <subject>".substr($taskInst->subject,0,50)."</subject>\n";
    $return .= "  <project>".substr($projectInst->name,0,50)."</project>\n";
    $return .= "  <start>".$toolInst->getTime("",$this->start)."</start>\n";
    $return .= "  <stop>".$toolInst->getTime("",$this->stop)."</stop>\n";
    $return .= "  <used>".$toolInst->formatTime($this->getSummary())."</used>\n";
    $return .= "  <usedprivate>".$toolInst->formatTime($this->getSummary(true))."</usedprivate>\n";
    $return .= "  <flags>".$this->flags."</flags>\n";
    $return .= "</job>\n";
    return $return;
  }

  function isDone() {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("job.isDone")) return false;

    if ($this->stop && $this->stop != 0) return true;
    else return false;
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: job.inc.php,v $
 * Revision 1.7  2005/02/22 16:15:13  genghishack
 * Added conditional to filter so that when a job crosses over one or more days, the job
 * will be displayed on the day it ended with the total time, or on the current day if it
 * is still running.  Also cleaned up some of the surrounding code for readability.
 *
 * Revision 1.5  2004/02/29 17:39:51  willuhn
 * @B some small fixes
 *
 * Revision 1.4  2004/02/28 19:42:15  znouza
 *
 * @N new feature: private jobs
 *
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
 * Revision 1.27  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.26  2002/09/07 19:23:13  willuhn
 * @N global commit for missing files
 *
 * Revision 1.25  2002/04/17 22:26:46  willuhn
 * @N child tasks can only set to "in progress", if parent task is set to "done"
 *
 * Revision 1.24  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.23  2002/03/30 17:15:52  willuhn
 * @N added plugin "presence list"
 *
 * Revision 1.22  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
