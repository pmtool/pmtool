<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/reports.php,v $
 * $Revision: 1.14 $
 * $Date: 2005/02/20 23:48:53 $
 * $Author: matchboy $
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
?>
<?PHP
#######################################################################
## check login again
if (!$loginInst->id) {
  echo $lang['common_accessDenied']."\n";
  exit;
}

# check if projects available
$projectInst = new project();
if (!$projectInst->getList()) {
  echo $lang['reports_noProjectNoTaskNoReport']."\n";
  exit;
}

?>

<h1><?PHP echo $lang['common_reports'];?></h1>

<?PHP
$taskInst = new task();
$taskInst->filterTimeBase="";
$taskInst->fillFilter(tool::securePostAll());

$jobInst = new job();
$jobInst->fillFilter(tool::securePostAll());

$reportInst = new report();
$reportInst->projectId = tool::securePost('filterprojectid');

#######################################################################
## perform action
if (tool::securePost('action') == "delete") {
  $reportInst = new report(tool::securePost('viewreport'));
  $reportInst->delete();
  $reportInst->clear();
}

#######################################################################
## make edit / new form

?>

<form method="post" name="form1">
<input type="hidden" name="action" value="">

<table border="0" cellpadding="0" cellspacing="0" width="99%">
  <tr>
    <td valign="top" rowspan="5">
      <h2><?PHP echo $lang['reports_queryRecords'];?></h2>
      <table border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td><?PHP echo $lang['common_user'];?>:&nbsp;</td>
          <td>
            <?PHP
              if (!$loginInst->hasAccess("report.viewOther")) {
                 ?><input type="hidden" name="filteruserid" value="<?PHP echo $loginInst->id;?>"><?PHP
                 echo $loginInst->name;
               }
               else { ?>
                 <select name="filteruserid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
                   <?PHP
                   $userInst = new user();
                   $list = $userInst->getList("name");
                   while ($element = current($list)) {
                     $userInst->activate($element);
                     $selected = "";
                     if ($userInst->id == tool::securePost('filteruserid')) $selected = "selected";
                     echo "<option ".$selected." value=\"".$userInst->id."\">".$userInst->name."\n";
                     next($list);
                   }
                   ?>
                 </select>
            <?PHP } ?>
          </td>
        </tr><tr>
          <td><?PHP echo $lang['common_customer'];?>:&nbsp;</td>
          <td><select name="filtercustomerid" onchange="javascript:document.form1.submit();"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP
            $customerInst = new customer();
            $list = $customerInst->getList();
            while ($element = current($list)) {
              $customerInst->activate($element);
              $selected = "";
              if ($customerInst->id == tool::securePost('filtercustomerid')) $selected = "selected";
              echo "<option ".$selected." value=\"".$customerInst->id."\">".$customerInst->company."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr><tr>
          <td><?PHP echo $lang['common_project'];?>:&nbsp;</td>
          <td><select name="filterprojectid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP
            $projectInst = new project();
            if (tool::securePost('filtercustomerid')) $projectInst->filterCustomerId = tool::securePost('filtercustomerid');
            $list = $projectInst->getList();
            while ($element = current($list)) {
              $projectInst->activate($element);
              $selected = "";
              if ($projectInst->id == tool::securePost('filterprojectid')) $selected = "selected";
              echo "<option ".$selected." value=\"".$projectInst->id."\">".$projectInst->name."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr><tr>
          <td><?PHP echo $lang['common_subjectContains'];?>:&nbsp;</td>
          <td><input type="text" name="filtersubject" value="<?PHP echo $taskInst->filterSubject;?>" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
        </tr><tr>
          <td valign="top"><?PHP echo $lang['common_bodyContains'];?>:&nbsp;</td>
          <td><textarea name="filterbody" rows="<?PHP echo $htmlconfig['textarea_rows'];?>" cols="<?PHP echo $htmlconfig['textarea_cols'];?>"><?PHP echo $taskInst->filterBody;?></textarea></td>
        </tr><tr>
          <td><?PHP echo $lang['reports_jobsFrom'];?>:&nbsp;</td>
          <td>
            <input type="text" name="filterstarttimeyear" value="<?PHP echo $toolInst->getTime("Y",$jobInst->filterStartTime);?>" size="4">-
            <input type="text" name="filterstarttimemonth" value="<?PHP echo $toolInst->getTime("m",$jobInst->filterStartTime);?>" size="2">-
            <input type="text" name="filterstarttimeday" value="<?PHP echo $toolInst->getTime("d",$jobInst->filterStartTime);?>" size="2">,&nbsp;
            <input type="text" name="filterstarttimehour" value="<?PHP echo $toolInst->getTime("H",$jobInst->filterStartTime);?>" size="2">:
            <input type="text" name="filterstarttimemin" value="<?PHP echo $toolInst->getTime("i",$jobInst->filterStartTime);?>" size="2">
          &nbsp;(yyyy-mm-dd, hh:mm)</td>
        </tr><tr>
          <td valign="top"><?PHP echo $lang['reports_jobsTo'];?>:&nbsp;</td>
          <td>
            <input type="text" name="filterstoptimeyear" value="<?PHP echo $toolInst->getTime("Y",$jobInst->filterStopTime);?>" size="4">-
            <input type="text" name="filterstoptimemonth" value="<?PHP echo $toolInst->getTime("m",$jobInst->filterStopTime);?>" size="2">-
            <input type="text" name="filterstoptimeday" value="<?PHP echo $toolInst->getTime("d",$jobInst->filterStopTime);?>" size="2">,&nbsp;
            <input type="text" name="filterstoptimehour" value="<?PHP echo $toolInst->getTime("H",$jobInst->filterStopTime);?>" size="2">:
            <input type="text" name="filterstoptimemin" value="<?PHP echo $toolInst->getTime("i",$jobInst->filterStopTime);?>" size="2">
          &nbsp;(yyyy-mm-dd, hh:mm)</td>
        </tr><tr>
          <td><?PHP echo $lang['reports_hideRunningJobs'];?>:&nbsp;</td>
          <?PHP $checked = ($jobInst->filterCurrentlyRunning) ? "checked" : "";?>
          <td><input type="checkbox" class="checkbox" value="1" <?PHP echo $checked;?> name="filtercurrentlyrunning"></td>
        </tr><tr>
          <td><?PHP echo $lang['reports_showPrivateJobs'];?>:&nbsp;</td>
          <?PHP $checked = ($jobInst->filterShowPrivateJobs) ? "checked" : "";?>
          <td><input type="checkbox" class="checkbox" value="1" <?PHP echo $checked;?> name="filtershowprivatejobs"></td>
        </tr><tr>
          <td><?PHP echo $lang['common_priority'];?>:&nbsp;</td>
          <td><select name="filterpriorityid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP
            $list = $taskInst->getPriorityList();
            while ($element = current($list)) {
              $selected = "";
              if ($element == tool::securePost('filterpriorityid')) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getPriorityName($element)."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr><tr>
          <td><?PHP echo $lang['common_type'];?>:&nbsp;</td>
          <td><select name="filtertypeid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP
            $list = $taskInst->getTypeList();
            while ($element = current($list)) {
              $selected = "";
              if ($element == tool::securePost('filtertypeid')) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getTypeName($element)."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr><tr>
          <td><?PHP echo $lang['common_status'];?>:&nbsp;</td>
          <td><select name="filterstatusid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP
            $list = $taskInst->getStatusList();
            while ($element = current($list)) {
              $selected = "";
              if ($element == tool::securePost('filterstatusid')) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getStatusName($element)."\n";
              next($list);
            }
            ?>
          </select>
          <input type="checkbox" name="filterinvertstatus" value="1" <?PHP if (tool::securePost('filterinvertstatus')) echo "checked";?> class="checkbox"> <?PHP echo $lang['common_invert'];?>
        </tr><tr>
          <td><?PHP echo $lang['common_orderBy'];?>:&nbsp;</td>
          <td><select name="order"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP if ($loginInst->hasAccess("report.viewOther")) echo "<option value=\"username\">by Username";?>
            <option value="project" <?PHP if (tool::securePost('order') == "project") echo "selected";?>>by project
            <option value="subject" <?PHP if (tool::securePost('order') == "subject") echo "selected";?>>by subject
            <option value="body" <?PHP if (tool::securePost('order') == "body") echo "selected";?>>by body
            <option value="time" <?PHP if (tool::securePost('order') == "time") echo "selected";?>>by time
            <option value="priority" <?PHP if (tool::securePost('order') == "priority") echo "selected";?>>by priority
            <option value="type" <?PHP if (tool::securePost('order') == "type") echo "selected";?>>by type
            <option value="status" <?PHP if (tool::securePost('order') == "status") echo "selected";?>>by status
          </select>
          <input type="checkbox" name="desc" value="DESC" <?PHP if (tool::securePost('desc')) echo "checked";?> class="checkbox"> <?PHP echo $lang['common_invert'];?>
        </tr><tr>
          <td><?PHP echo $lang['reports_createOnlyJobList'];?>:&nbsp;</td>
          <?PHP $checked = "";?>
          <?PHP if (tool::securePost('joblist')) $checked = "checked";?>
          <td><input type="checkbox" class="checkbox" value="1" <?PHP echo $checked;?> name="joblist"> (<?PHP echo $lang['reports_chronologicalOrder'];?>)</td>
        </tr>
        <tr><td>&nbsp;</td><td colspan="2"><input type="submit" value="<?PHP echo $lang['common_search'];?>"></td></tr>
      </table>
    </td>
    <td rowspan="5"><img src="grafx/dummy.gif" width="10" height="1" border="0"></td>
    <td rowspan="5" bgcolor="#909090"><img src="grafx/dummy.gif" width="1" height="1" border="0"></td>
    <td rowspan="5"><img src="grafx/dummy.gif" width="10" height="1" border="0"></td>
    <td valign="top">
      <h2><?PHP echo $lang['reports_generateReport'];?></h2>
      <table border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td><?PHP echo $lang['common_subject'];?>:&nbsp;</td>
          <td><input type="text" name="subject" value="<?PHP echo tool::securePost('subject');?>" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
        </tr><tr>
          <td><?PHP echo $lang['reports_recipient'];?>:&nbsp;</td>
          <td>
            <select name="recipient">
            <?PHP
              $customerInst = new customer();
              $list = $customerInst->getList();
               while ($element = current($list)) {
                 $customerInst->activate($element);
                 $selected = "";
                 if ($customerInst->id == tool::securePost('filtercustomerid')) $selected = "selected";
                 echo "<option $selected value=\"".$customerInst->id."\">".$customerInst->company."\n";
                 next($list);
               }
            ?>
            </select>
          </td>
        </tr><tr>
          <td><?PHP echo $lang['reports_template'];?>:&nbsp;</td>
          <td>
            <select name="templatecreate">
              <?PHP $dir = $toolInst->getDir("./templates/report");
              while ($file = current($dir)) {
                echo "<option value=\"".$file."\">".$file."\n";
                next($dir);
              }
              ?>
            </select>
          </td>
        </tr>
        <tr><td>&nbsp;</td><td colspan="2"><input type="submit" value="<?PHP echo $lang['reports_generate'];?>" onclick="javascript:document.form1.action.value='save';return Check()"></td></tr>
      </table>
    </td>
  </tr>
  <tr><td><img src="grafx/dummy.gif" width="1" height="10" border="0"></td></tr>
  </tr><tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td bgcolor="#909090"><img src="grafx/dummy.gif" width="1" height="1" border="0"></td></tr></table></td></tr>
  <tr><td><img src="grafx/dummy.gif" width="1" height="10" border="0"></td></tr>
  <?PHP
    $reportInst2 = new report();
    $list = $reportInst2->getList();
    if ($list) {
      ?>
      <tr>
        <td valign="top">
          <h2><?PHP echo $lang['reports_viewExistingReport'];?></h2>
          <table border="0" cellpadding="2" cellspacing="0">
            <tr>
              <td><?PHP echo $lang['common_subject'];?>:&nbsp;</td>
              <td><select name="viewreport">
                <?PHP
                while ($element = current($list)) {
                  $reportInst2->activate($element);
                  $selected = "";
                  if ($element == tool::securePost('viewreport')) $selected = "selected";
                  echo "<option ".$selected." value=\"".$element."\">[".$toolInst->getTime("Y-m-d",$reportInst2->created)."] ";
                  if ($loginInst->hasAccess("report.viewOther")) {
                    $userInst = new user($reportInst2->userId);
                    echo $userInst->username.": ";
                  }
                  echo $reportInst2->subject."\n";
                  next($list);
                }
                ?>
              </select>
              </tr><tr>
                <td><?PHP echo $lang['reports_template'];?>:&nbsp;</td>
                <td>
                  <select name="templateview">
                    <?PHP $dir = $toolInst->getDir("./templates/report");
                    while ($file = current($dir)) {
                      echo "<option value=\"".$file."\">".$file."\n";
                      next($dir);
                    }
                    ?>
                  </select>
                </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2">
                <input type="submit" value="<?PHP echo $lang['reports_view'];?>" onclick="document.form1.action.value='view';">
                <input type="submit" value="<?PHP echo $lang['common_delete'];?>" onclick="document.form1.action.value='delete';return Check()">
              </td>
            </tr>
          </table>
        </td>
      </tr>
    <?PHP } ?>
</table>
<?PHP

#######################################################################
## list found records

# order
$order = "time";
if (tool::securePost('order')) {$order = tool::securePost('order');}

# create report
$sum = 0;
$reportInst->append("<report>");

?>
<br><h2><?PHP echo $lang['common_matches'];?></h2>
<table border=0 cellpadding=2 cellspacing=1 width=99% bgcolor="#ffffff">
<tr>

<?PHP
if (tool::securePost('joblist')) {
  // generate only a plain job list
  if ($loginInst->hasAccess("report.viewOther")) echo "<th align=left>user</th>"; ?>
  <th align=left><?PHP echo $lang['common_project'];?></th>
  <th align=left><?PHP echo $lang['common_subject'];?></th>
  <th align=left><?PHP echo$lang['common_comment'];?></th>
  <th align=left><?PHP echo $lang['common_start'];?></th>
  <th align=left><?PHP echo $lang['common_stop'];?></th>
  <th align=left><nobr><?PHP echo $lang['common_usedTime'];?></nobr></th>
  </tr>
  <?PHP
    $array = array();
    $list = $taskInst->getList($order,$desc);
    while ($element = current($list)) {
      $jobInst->filterTaskId = $element;
      $jobList = $jobInst->getList();
      if ($jobInst->matches > 0) {
        while ($jobElement = current($jobList)) {
          $array[] = $jobElement;
          next($jobList);
        }
      }
      next($list);
    }
    sort($array);

    ###
    $day = 0;
    $newDay = 0;
    $firstrun = 1;
    $mySummary = 0;
    ###

    while ($element = current($array)) {
      $jobInst->activate($element);
      $taskInst->activate($jobInst->taskId);
      $userInst = new user($taskInst->userId);
      $projectInst->activate($taskInst->projectId);
      $reportInst->append("  <task>");
      $reportInst->append("    <job>");
      if ($loginInst->hasAccess("report.viewOther")) $reportInst->append("      <user>".$userInst->username."</user>");
      $reportInst->append($toolInst->encodeXml("      <project>".substr($projectInst->name,0,50)."</project>"));
      $reportInst->append($toolInst->encodeXml("      <subject>".substr($taskInst->subject,0,50)."</subject>"));
      $reportInst->append("      <start>".$toolInst->getTime("",$jobInst->start)."</start>");
      $reportInst->append("      <stop>".$toolInst->getTime("",$jobInst->stop)."</stop>");
      $reportInst->append("      <used>".$toolInst->formatTime($jobInst->getSummary())."</used>");
      $reportInst->append($toolInst->encodeXml("      <comment>".substr($jobInst->comment,0,50)."...</comment>"));
      $reportInst->append("    </job>");
      $reportInst->append("  </task>");

      ###
      $day = $newDay;
      $newDay = $toolInst->getTime("d",$jobInst->start);
      if ($day < $newDay && $firstrun != "1") {
        echo "<tr>\n";
        echo "<td colspan=\"5\" class=list align=right><b>".$lang['common_summary'].":</b></td>\n";
        echo "<td class=list align=right><b>".$toolInst->formatTime($mySummary)."</b></td>\n";
        echo "</tr>\n";
        echo "<tr><td colspan=\"6\" class=list>&nbsp;</td></tr>\n";
        $mySummary = 0;
      }
      ###

      echo "<tr>";

      ###
      $myTime = $jobInst->getSummary(true);
      $mySummary += $myTime;
      ###

      if ($loginInst->hasAccess("report.viewOther")) echo "<td class=list>".$userInst->username."</td>";
      $stylePrivate = ($jobInst->isFlag(JOB_FLAG_PRIVATE)) ? "_private" : "";
      echo "<td class=list>".substr($projectInst->name,0,50)."</td>";
      echo "<td class=list>".substr($taskInst->subject,0,50)."...</td>";
      echo "<td class=list$stylePrivate>".$jobInst->comment."...</td>";
      echo "<td class=list$stylePrivate><nobr>".$toolInst->getTime("",$jobInst->start)."</nobr></td>";
      echo "<td class=list$stylePrivate><nobr>".$toolInst->getTime("",$jobInst->stop)."</nobr></td>";

      ###
      echo "<td class=list$stylePrivate align=right>".$toolInst->formatTime($myTime)."</td>";
      ###

      echo "</tr>";

      ##
      $firstrun = 0;
      ##

      next($array);
    }

    ###
    echo "<tr>\n";
    echo "<td colspan=\"6\" class=list align=right><b>".$lang['common_summary'].":</b></td>\n";
    echo "<td class=list align=right><b>".$toolInst->formatTime($mySummary)."</b></td>\n";
    echo "</tr>\n";
    ###
}

else {
  // generate a full report
  if ($loginInst->hasAccess("report.viewOther")) echo "<th align=left>".$lang['common_user']."</th>"; ?>

  <th align=left><?PHP echo $lang['common_project'];?></th>
  <th align=left><?PHP echo $lang['common_subject'];?></th>
  <th align=left><?PHP echo $lang['common_body'];?></th>
  <th align=left><?PHP echo $lang['common_posted'];?></th>
  <th align=left><?PHP echo $lang['common_priority'];?></th>
  <th align=left><?PHP echo $lang['common_type'];?></th>
  <th align=left><?PHP echo $lang['common_status'];?></th></tr>
  <?PHP
  if (tool::securePost('action') == "save") {
    // write customer data to xml file
    $customerInst = new customer(tool::securePost('recipient'));
    $reportInst->append($toolInst->encodeXml("  <firstname>".$customerInst->firstname."</firstname>"));
    $reportInst->append($toolInst->encodeXml("  <lastname>".$customerInst->lastname."</lastname>"));
    $reportInst->append($toolInst->encodeXml("  <company>".$customerInst->company."</company>"));
    $reportInst->append($toolInst->encodeXml("  <street>".$customerInst->street."</street>"));
    $reportInst->append("  <zip>".$customerInst->zip."</zip>");
    $reportInst->append($toolInst->encodeXml("  <city>".$customerInst->city."</city>"));
    $reportInst->append("  <number>".$customerInst->customerNumber."</number>");
    $reportInst->append($toolInst->encodeXml("  <subject>".tool::securePost('subject')."</subject>"));
  }

  $costs = 0;
  $summary = 0;
  $summaryRounded = 0;
  $customerCosts = 0;
  $customerSummary = 0;
  $customerSummaryRounded = 0;

  $completeCosts = 0;
  $completeSummary = 0;
  $completeSummaryRounded = 0;
  $completeCustomerCosts = 0;
  $completeCustomerSummary = 0;
  $completeCustomerSummaryRounded = 0;

  $list = $taskInst->getList($order,$desc);

  while ($element = current($list)) {
    $taskInst->activate($element);
    $jobInst->filterTaskId = $taskInst->id;
    $jobList = $jobInst->getList();

    // tasks with fixed price don't need assigned jobs.
    // So we search also for these tasks.
    $fixed = false;

    if ($taskInst->isfixedPrice()) {
      // But they need to be posted in the given range.
      if ($taskInst->time >= $jobInst->filterStartTime && $taskInst->time <= $jobInst->filterStopTime) {
        $fixed = true;
      }

    }
    // is >= 0 because we need to display task even if no job (maybe hidden
    // private jobs
    if ($jobInst->matches >= 0 || $fixed) {

      $projectInst = new project($taskInst->projectId);

      // write task details to xml file
      $reportInst->append("  <task>");
      $reportInst->append($toolInst->encodeXml("    <project>".$projectInst->name."</project>"));
      $reportInst->append($toolInst->encodeXml("    <subject>".substr($taskInst->subject,0,50)."</subject>"));
      $reportInst->append($toolInst->encodeXml("    <body>".substr($taskInst->body,0,200)."</body>"));
      $reportInst->append("    <user>".$userInst->username."</user>");
      $reportInst->append("    <time>".$toolInst->getTime("d.m.Y, H:i",$taskInst->time)."</time>");
      $reportInst->append("    <priority>".$taskInst->getPriorityName()."</priority>");
      $reportInst->append("    <prioritystyle>".$taskInst->getPriorityStyle()."</prioritystyle>");
      $reportInst->append("    <type>".$taskInst->getTypeName()."</type>");
      $reportInst->append("    <typestyle>".$taskInst->getTypeStyle()."</typestyle>");
      $reportInst->append("    <status>".$taskInst->getStatusName()."</status>");
      $reportInst->append("    <statusstyle>".$taskInst->getStatusStyle()."</statusstyle>");

      echo "<tr class=\"light\">";
      if ($loginInst->hasAccess("report.viewOther")) {
        $userInst = new user($taskInst->userId);
        echo "<td class=list>".$userInst->username."</td>";
      }
      echo "<td class=list>".$projectInst->name."</td>";
      echo "<td class=list>".substr($taskInst->subject,0,50)."...</td>";
      echo "<td class=list>";
      if ($taskInst->body) echo substr($taskInst->body,0,200)."...";
      echo "&nbsp;</td>";
      echo "<td class=list><nobr>".$toolInst->getTime("d.m.Y, H:i",$taskInst->time)."</nobr></td>";
      echo "<td><nobr class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</nobr></td>";
      echo "<td><nobr class=".$taskInst->getTypeStyle().">".$taskInst->getTypeName()."</nobr></td>";
      echo "<td><nobr class=".$taskInst->getStatusStyle().">".$taskInst->getStatusName()."</nobr></td>";
      echo "</tr>";

      echo "<tr>";
      if ($loginInst->hasAccess("report.viewOther")) {
        echo "<td class=list colspan=3 valign=top align=right>&nbsp;</td>";
      }
      else {
        echo "<td class=list colspan=2 valign=top align=right>&nbsp;</td>";
      }
      echo "<td class=list colspan=5>";
      echo "<table border=0 cellpadding=2 cellspacing=1 width=100% bgcolor=#ffffff>";
      // display all jobs
      if ($jobInst->matches > 0) {
        echo "<tr><th>".$lang['common_comment']."</th>";
        echo "<th>".$lang['common_start']."</th>";
        echo "<th>".$lang['common_stop']."</th>";
        echo "<th>".$lang['common_usedTime']."</th></tr>";
        $taskSum = 0;
        while ($jobElement = current($jobList)) {
          // list all jobs in xml file
          $jobInst->activate($jobElement);
          $taskSum += $jobInst->getSummary();
          $reportInst->append("    <job>");
          $reportInst->append($toolInst->encodeXml("      <comment>".$jobInst->comment."</comment>"));
          $reportInst->append("      <start>".$toolInst->getTime("",$jobInst->start)."</start>");
          $reportInst->append("      <stop>".$toolInst->getTime("",$jobInst->stop)."</stop>");
          $reportInst->append("      <used>".$toolInst->formatTime($jobInst->getSummary())."</used>");
          $reportInst->append("    </job>");

          $stylePrivate = ($jobInst->isFlag(JOB_FLAG_PRIVATE)) ? "_private" : "";
          echo "<tr>";
          echo "<td class=list$stylePrivate>".$jobInst->comment."</td>";
          echo "<td class=list$stylePrivate><nobr>".$toolInst->getTime("",$jobInst->start)."</nobr></td>";
          echo "<td class=list$stylePrivate><nobr>".$toolInst->getTime("",$jobInst->stop)."</nobr></td>";
          echo "<td class=list$stylePrivate align=right>".$toolInst->formatTime($jobInst->getSummary())."</td>";
          echo "</tr>";
          next($jobList);
        }
      }

      // calculate task summary
      if ($taskInst->isfixedPrice()) {
      $taskCosts = $taskInst->fixedPrice;
      } else {
      $taskCosts = ($toolInst->deductibleSeconds($taskSum)/3600)*$taskInst->getRate();
      }

      // calculate customer part
      $customerTaskCosts = 0;
      $customerTaskSummary = 0;
      $customerTaskSummaryRounded = 0;
      if ($taskInst->hasToPay()) {
        $customerTaskCosts = $taskCosts;
        $customerTaskSummary = $taskSum;
        $customerTaskSummaryRounded = $toolInst->deductibleSeconds($taskSum);
      }

      // determine summaries for completed jobs (not only the shown jobs)
      $completeTaskSummary = $taskInst->getSummary();
      $completeTaskCustomerSummary = $taskInst->getCustomerSummary();

      // add summaries to overall summaries
      $costs += $taskCosts;
      $summary += $taskSum;
      $summaryRounded += $toolInst->deductibleSeconds($taskSum);
      $customerCosts += $customerTaskCosts;
      $customerSummary += $customerTaskSummary;
      $customerSummaryRounded += $customerTaskSummaryRounded;

      $completeCosts += $taskInst->getCosts();
      $completeSummary += $completeTaskSummary;
      $completeSummaryRounded += $toolInst->deductibleSeconds($completeTaskSummary);
      $completeCustomerCosts += $taskInst->getCustomerCosts();
      $completeCustomerSummary += $completeTaskCustomerSummary;
      $completeCustomerSummaryRounded += $toolInst->deductibleSeconds($completeTaskCustomerSummary);

      if ($loginInst->hasAccess("task.getRate") && !$taskInst->isFixedPrice()) {
        // rate for current task
        echo "<tr><td colspan=3 class=list align=right>";
        echo "<b>".$lang['common_rate'].":&nbsp;</td>";
        echo "<td align=right class=list><b>".$toolInst->formatCurrency($taskInst->getRate())."</b></td></tr>";
        $reportInst->append("    <rate>".$toolInst->formatCurrency($taskInst->getRate())."</rate>");
      }

      if ($loginInst->hasAccess("task.getCosts")) {
        // effective cost for current task (only listed jobs)
        // echo "<tr><td colspan=3 class=list align=right><b>costs:&nbsp;</td>";
        // echo "<td align=right class=list><b>".$toolInst->formatCurrency($taskCosts)."</b></td></tr>";
        $reportInst->append("    <costs>".$toolInst->formatCurrency($taskCosts)."</costs>");

        // effective cost for current task (all jobs in this task)
        $reportInst->append("    <completecosts>".$toolInst->formatCurrency($taskInst->getCosts())."</completecosts>");
      }

      if ($loginInst->hasAccess("task.getSummary")) {
        // used time for current task  (only listed jobs)
        // echo "<tr><td colspan=3 class=list align=right><b>summary:&nbsp;</td>";
        // echo "<td align=right class=list><b>".$toolInst->formatTime($taskSum)."</b></td></tr>";
        $reportInst->append("    <summary>".$toolInst->formatTime($taskSum)."</summary>");

        // used time for current task (all jobs in this task)
        $reportInst->append("    <completesummary>".$toolInst->formatTime($completeTaskSummary)."</completesummary>");
      }

      if ($loginInst->hasAccess("task.getSummary") && !$taskInst->fixedPrice > "0") {
        // used time for current task, but rounded  (only listed jobs)
        echo "<tr><td colspan=3 class=list align=right>";
        echo "<b>".$lang['reports_roundedSummary'].":&nbsp;</td>";
        echo "<td align=right class=list><b>".$toolInst->formatTime($toolInst->deductibleSeconds($taskSum))."</b></td></tr>";
        $reportInst->append("    <summaryrounded>".$toolInst->formatTime($toolInst->deductibleSeconds($taskSum))."</summaryrounded>");

        // used time for current task, but rounded  (all jobs in this task)
        $reportInst->append("    <completesummaryrounded>".$toolInst->formatTime($toolInst->deductibleSeconds($completeTaskSummary))."</completesummaryrounded>");
      }

      if ($loginInst->hasAccess("task.getCustomerSummary")) {
        // used time for current task except bugs and todos (only listed jobs)
        // echo "<tr><td colspan=3 class=list align=right><b>customer summary:&nbsp;</td>";
        // echo "<td align=right class=list><b>".$toolInst->formatTime($customerTaskSummary)."</b></td></tr>";
        $reportInst->append("    <customersummary>".$toolInst->formatTime($customerTaskSummary)."</customersummary>");

        // used time for current task except bugs and todos (all jobs in this task)
        $reportInst->append("    <completecustomersummary>".$toolInst->formatTime($completeTaskCustomerSummary)."</completecustomersummary>");
      }

      if ($loginInst->hasAccess("task.getCustomerSummary") && !$taskInst->isFixedPrice()) {
        // used time for current task except bugs and todos, but rounded (only listed jobs)
        echo "<tr><td colspan=3 class=list align=right>";
        echo "<b>".$lang['reports_roundedCustomerSummary'].":&nbsp;</td>";
        echo "<td align=right class=list><b>".$toolInst->formatTime($customerTaskSummaryRounded)."</b></td></tr>";
        $reportInst->append("    <customersummaryrounded>".$toolInst->formatTime($toolInst->deductibleSeconds($customerTaskSummaryRounded))."</customersummaryrounded>");

        // used time for current task except bugs and todos, but rounded (all jobs in this task)
        $reportInst->append("    <completecustomersummaryrounded>".$toolInst->formatTime($toolInst->deductibleSeconds($completeTaskCustomerSummary))."</completecustomersummaryrounded>");
      }

      if ($loginInst->hasAccess("task.getCustomerCosts")) {
        // costs for current task except bugs and todos (only listed jobs)
        echo "<tr><td colspan=3 class=list align=right>";
        echo "<b>".$lang['reports_customerCosts'];
        if ($taskInst->isFixedPrice()) echo " (".$lang['tasks_fixedPrice'].")";
        echo ":&nbsp;</td>";
        echo "<td align=right class=list><b>".$toolInst->formatCurrency($customerTaskCosts)."</b></td></tr>";
        $reportInst->append("    <customercosts>".$toolInst->formatCurrency($customerTaskCosts)."</customercosts>");

        // costs for current task except bugs and todos (all jobs in this task)
        $reportInst->append("    <completecustomercosts>".$toolInst->formatCurrency($taskInst->getCustomerCosts())."</completecustomercosts>");
      }

      $reportInst->append("  </task>");

      echo "</table>";
      echo "</td>";
      echo "</tr>";
      if ($loginInst->hasAccess("report.viewOther")) echo "<tr><td class=list colspan=8>&nbsp;</td></tr>";
      else echo "<tr><td class=list colspan=7>&nbsp;</td></tr>";
    }
    next($list);
  }


  if ($loginInst->hasAccess("report.viewOther"))
    echo "<tr><td class=list colspan=8><b>".$lang['reports_overall'].":</b></td></tr>";
  else
    echo "<tr><td class=list colspan=7><b>".$lang['reports_overall'].":</b></td></tr>";

  $colspan="5";
  if ($loginInst->hasAccess("task.viewOther")) $colspan="6";

  // overall summaries

  if ($loginInst->hasAccess("task.getSummary")) {
    echo "<tr><td class=list colspan=".$colspan." align=right><b>".$lang['common_summary'].":</b></td>";
    echo "<td class=list colspan=2 align=right><b>".$toolInst->formatTime($summary)."</b></td></tr>";

    $reportInst->append("  <summary>".$toolInst->formatTime($summary)."</summary>");
    $reportInst->append("  <completesummary>".$toolInst->formatTime($completeSummary)."</completesummary>");
  }

  if ($loginInst->hasAccess("task.getSummary")) {
    // echo "<tr><td class=list colspan=".$colspan." align=right><b>rounded summary:</b></td>";
    // echo "<td class=list colspan=2 align=right><b>".$toolInst->formatTime($summaryRounded)."</b></td></tr>";

    $reportInst->append("  <summaryrounded>".$toolInst->formatTime($summaryRounded)."</summaryrounded>");
    $reportInst->append("  <completesummaryrounded>".$toolInst->formatTime($completeSummaryRounded)."</completesummaryrounded>");
  }

  if ($loginInst->hasAccess("task.getCustomerSummary")) {
    // echo "<tr><td class=list colspan=".$colspan." align=right><b>customer summary:</b></td>";
    // echo "<td class=list colspan=2 align=right><b>".$toolInst->formatTime($customerSummary)."</b></td></tr>";

    $reportInst->append("  <customersummary>".$toolInst->formatTime($customerSummary)."</customersummary>");
    $reportInst->append("  <completecustomersummary>".$toolInst->formatTime($completeCustomerSummary)."</completecustomersummary>");
  }

  if ($loginInst->hasAccess("task.getCustomerSummary")) {
    echo "<tr><td class=list colspan=".$colspan." align=right><b>".$lang['reports_roundedCustomerSummary'].":</b></td>";
    echo "<td class=list colspan=2 align=right><b>".$toolInst->formatTime($customerSummaryRounded)."</b></td></tr>";

    $reportInst->append("  <customersummaryrounded>".$toolInst->formatTime($customerSummaryRounded)."</customersummaryrounded>");
    $reportInst->append("  <completecustomersummaryrounded>".$toolInst->formatTime($completeCustomerSummaryRounded)."</completecustomersummaryrounded>");
  }

  if ($loginInst->hasAccess("task.getCosts")) {
    // echo "<tr><td class=list colspan=".$colspan." align=right><b>costs:</b></td>";
    // echo "<td class=list colspan=2 align=right><b>".$toolInst->formatCurrency($costs)."</b></td></tr>";

    $reportInst->append("  <costs>".$toolInst->formatCurrency($costs)."</costs>");
    $reportInst->append("  <completecosts>".$toolInst->formatCurrency($completeCosts)."</completecosts>");
  }

  if ($loginInst->hasAccess("task.getCustomerCosts")) {
    echo "<tr><td class=list colspan=".$colspan." align=right><b>".$lang['reports_customerCosts'].":</b></td>";
    echo "<td class=list colspan=2 align=right><b>".$toolInst->formatCurrency($customerCosts)."</b></td></tr>";

    $reportInst->append("  <customercosts>".$toolInst->formatCurrency($customerCosts)."</customercosts>");
    $reportInst->append("  <completecustomercosts>".$toolInst->formatCurrency($completeCustomerCosts)."</completecustomercosts>");
  }

  $reportInst->append("  <currency>".$config['currency']."</currency>");

  $taxCosts = ($config['taxrate'] / 100) * $customerCosts;
  $completeTaxCosts = ($config['taxrate'] / 100) * $completeCustomerCosts;
  $reportInst->append("  <taxrate>".$config['taxrate']." %</taxrate>");


  if ($loginInst->hasAccess("task.getCosts")) {
    echo "<tr><td class=list colspan=".$colspan." align=right><b>".$lang['reports_tax']." ({$config['taxrate']} %):</b></td>";
    echo "<td class=list colspan=2 align=right><b>".$toolInst->formatCurrency($taxCosts)."</b></td></tr>";

    $reportInst->append("  <taxcosts>".$toolInst->formatCurrency($taxCosts)."</taxcosts>");
    $reportInst->append("  <completetaxcosts>".$toolInst->formatCurrency($completeTaxCosts)."</completetaxcosts>");
  }

  if ($loginInst->hasAccess("task.getCustomerCosts")) {
    echo "<tr><td class=list colspan=".$colspan." align=right><b>".$lang['reports_customerCostsInclTax'].":</b></td>";
    echo "<td class=list colspan=2 align=right><b>".$toolInst->formatCurrency($customerCosts + $taxCosts)."</b></td></tr>";

    $reportInst->append("  <customercoststax>".$toolInst->formatCurrency($customerCosts + $taxCosts)."</customercoststax>");
    $reportInst->append("  <completecustomercoststax>".$toolInst->formatCurrency($completeCustomerCosts + $completeTaxCosts)."</completecustomercoststax>");
  }

  $reportInst->append("  <date>".$toolInst->getTime("Y-m-d")."</date>");

}

echo "</table>";

$reportId = "";
if (tool::securePost('action') == "save") {
  $reportInst->subject = tool::securePost('subject');
  $reportInst->projectId = tool::securePost('filterprojectid');

  // Fuck, the german law says:
  // every bill must have a consecutive id :-/
  // So, we first create the report without xml.
  // after that we store the database id in the xml
  // code and update the report
  $_xml = $reportInst->xml;  // backup of current xml code
  $reportId = $reportInst->insert(); //insert the report

  $reportInst->append("  <id>".$reportId."</id>"); // append id
  $reportInst->append("</report>");
  $reportInst->update();
}
else {
  // don't forget the closing tag
  $reportInst->append("</report>");
}

if (tool::securePost('action') == "view") {
  $reportId = tool::securePost('viewreport');
}

if ($reportId != "") {
  $template = tool::securePost('templateview');
  if (tool::securePost('action') == "save") $template = tool::securePost('templatecreate');
  ?>

  <script language="javascript">
    <!--
     reportwindow('<?PHP echo $toolInst->encodeUrl("reportviewer.php?reportid=".$reportId."&template=".$template)?>');
    //-->
  </script>
<?PHP
}
?>
</form>
<?PHP
/***************************************************************************
 * $Log: reports.php,v $
 * Revision 1.14  2005/02/20 23:48:53  matchboy
 * Changed substr of 50 to 200 for body of comment for task.
 *
 * Revision 1.13  2005/02/20 21:14:34  matchboy
 * Changed the dropdown to sort by name rather than username.
 *
 * Revision 1.12  2004/03/23 20:43:09  willuhn
 * @B one "(" was to much ;)
 *
 * Revision 1.11  2004/03/17 19:30:52  willuhn
 * @N configurable behavior for task types the customer has to pay for
 *
 * Revision 1.10  2004/03/12 00:42:08  znouza
 * @C bug in reports - bad values in customers costs
 * @N added taxrate displaying in total sum of report
 *
 * Revision 1.9  2004/02/29 17:39:51  willuhn
 * @B some small fixes
 *
 * Revision 1.8  2004/02/28 19:51:42  znouza
 *
 * index.php
 * @B some minor bugs
 *
 * projectdetails.php
 * @N feature private job support added
 *
 * taskdetails.php
 * @N feature private job support added
 *
 * reports.php
 * @B javascript (reports not shown) fixed
 * @B missing } fixed
 * @B some bugs fixed
 * @C displaying tasks even no jobs added (for private jobs support)
 * @N feature private job support added
 *
 * styles.css
 * @N added list_private item for displaying private jobs (italics)
 *
 * Revision 1.7  2004/02/21 00:38:41  arneke
 * arneke (Arne Kepp) 2004-02-20, sourceforge bug 876387
 * seriours summary error, hopefully fixed
 *
 * Revision 1.6  2004/02/17 22:07:15  willuhn
 * @N added consecutive id to xml code of reports
 * @N added czech language file
 * @N added spanish language file
 *
 * Revision 1.5  2003/11/18 01:54:51  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/11/05 20:31:34  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/26 22:23:46  willuhn
 * @N tasks without job but fixed price can be reported now
 * @N added a lot of indexes in sql script (for performance reasons)
 *
 * Revision 1.1.1.1  2003/07/28 19:22:21  willuhn
 * reimport
 *
 * Revision 1.38  2002/11/07 22:57:20  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.37  2002/05/31 12:41:05  willuhn
 * @C some stylin'
 *
 * Revision 1.36  2002/05/31 12:36:22  willuhn
 * @N added some "<nobr>" tags
 *
 * Revision 1.35  2002/05/31 12:21:08  willuhn
 * @N added a dirty hack to show daily summary in joblist
 *
 * Revision 1.34  2002/05/05 20:12:42  willuhn
 * @N added feature "fixed price" for tasks
 *
 * Revision 1.33  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.32  2002/04/08 22:30:02  willuhn
 * @C project availability check on home page
 *
 * Revision 1.31  2002/03/30 14:14:39  willuhn
 * @N added plugin loader
 *
 * Revision 1.30  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.29  2002/02/27 22:37:43  willuhn
 * @C some styling in css
 *
 * Revision 1.28  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
