<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/taskdetails.php,v $
 * $Revision: 1.12 $
 * $Date: 2005/02/20 23:47:34 $
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

$taskInst = new task();
if (tool::securePost('action')=="save") {
  $taskId = tool::securePost('taskid');
}

if ($taskId) {
  $taskInst->activate($taskId);
}
else {
  $taskId = tool::secureGet('taskid');
  $taskInst->activate($taskId);
}

if (!$taskInst->id) {
  echo "no task found.\n";
  exit;
}

$projectInst           = new project($taskInst->projectId);
$userInst              = new user($taskInst->userId);

$jobInst               = new job();
$jobInst->filterTaskId = $taskInst->id;

#######################################################################
## perform action
$status = 0;

if (tool::securePost('action') == "save" && tool::securePost('id')) {
  // stop job
  $jobInst->fill(tool::securePostAll());
  $jobInst->id = tool::securePost('id');
  $jobInst->taskId = $taskInst->id;
  $jobInst->start = $toolInst->timestampToSec(tool::securePost('startyear'),tool::securePost('startmonth'),tool::securePost('startday'),tool::securePost('starthour'),tool::securePost('startmin'));
  $jobInst->stop = $toolInst->timestampToSec(tool::securePost('stopyear'),tool::securePost('stopmonth'),tool::securePost('stopday'),tool::securePost('stophour'),tool::securePost('stopmin'));
  $saveflags = 0;
  // handle job flags
  if (tool::securePost('privatejob') == "1") $saveflags+=JOB_FLAG_PRIVATE;
  $jobInst->flags = $saveflags;
  $jobInst->stop();
  if (tool::securePost('taskdone') == "1") {
    $taskInst->stop();
    $taskInst->update();
  }
}
elseif (tool::securePost('action') == "save") {
  // start job
  $jobInst->fill(tool::securePostAll());
  $jobInst->taskId = $taskInst->id;
  $jobInst->start = $toolInst->timestampToSec(tool::securePost('startyear'),tool::securePost('startmonth'),tool::securePost('startday'),tool::securePost('starthour'),tool::securePost('startmin'));
  $saveflags = 0;
  // handle job flags
  if (tool::securePost('privatejob') == "1") $saveflags+=JOB_FLAG_PRIVATE;
  $jobInst->flags = $saveflags;
  $jobId = $jobInst->start();
  $jobInst->activate($jobId);
}
elseif (tool::securePost('action') == "deleteattach") {
  $attachment = new attachment(tool::securePost('id'));
  $attachment->delete();
  // we need to reload the task to clear the attachment member in this object
  $taskInst->activate($taskInst->id);
}

if (tool::securePost('action') == "delete") {
  $jobInst->activate(tool::securePost('id'));
  $jobInst->delete();
}
if (tool::securePost('action') == "edit") {
  $status = 1;
  $jobInst->activate(tool::securePost('id'));
}

#######################################################################
## show task details

?>

<h1><?PHP echo $lang['tasks_taskdetails'];?></h1>
<div align="center">
<table border="0" cellpadding="2" cellspacing="1" width="96%">
  <tr>
    <td><h2><?PHP echo $taskInst->subject;?></h2></td>
  </tr>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
  <tr>
    <th><?PHP echo $lang['common_ID'];?></th>
    <th><?PHP echo $taskInst->id;?></th>
  </tr><tr>
    <th><?PHP echo $lang['common_project'];?></th>
    <th><?PHP echo $projectInst->name;?></th>
  </tr><tr>
    <th><?PHP echo $lang['tasks_depends'];?></th>
    <th>/<?PHP if ($taskInst->mountId != "0") {
        echo "<a href=\"".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$taskInst->mountId)."\" title=\"".$lang['tasks_parentTask']."\">".implode("/",$taskInst->treeName($taskInst->mountId))."</a>\n";
      } ?>
    </th>
  </tr>
  <?PHP
    $childs = $taskInst->childs();
    if (count($childs) > 0) {
      echo "<tr><th valign=\"top\" rowspan=\"".count($childs)."\">child tasks</th><th>\n";
      $childTask = new task(current($childs));
      echo "<a href=\"".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$childTask->id)."\" title=\"".$lang['tasks_childTask']." 1\">".substr($childTask->subject,0,40)."</a></th></tr>\n";
      next($childs);
      $i = 2;
      while ($element = current($childs)) {
        $childTask = new task(current($childs));
        echo "<tr><th><a href=\"".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$childTask->id)."\" title=\"".$lang['tasks_childTask']." ".$i."\">".substr($childTask->subject,0,40)."</a></th></tr>\n";
        $i++;
        next($childs);
      }
    }
  ?>
  <tr>
    <th><?PHP echo $lang['common_user'];?></th>
    <th><a href="mailto:<?PHP echo $userInst->email;?>"><?PHP echo $userInst->name;?></a></th>
  </tr><tr>
    <th><?PHP echo $lang['common_posted'];?></th>
    <th><nobr><?PHP echo $toolInst->getTime("d.m.Y, H:i",$taskInst->time);?></nobr></th>
  </tr><tr class="light">
    <td><nobr><?PHP echo $lang['tasks_plannedFinish'];?></nobr></td>
    <td><nobr><?PHP echo $toolInst->getTime("d.m.Y, H:i",$taskInst->finish);?></nobr></td>
  </tr>
  <?PHP if ($taskInst->plannedHours > "0") { ?>
    <tr class="light">
      <td><nobr><?PHP echo $lang['tasks_plannedHours'];?></nobr></td>
      <td><?PHP echo $taskInst->plannedHours;?></td>
    </tr>
  <?PHP } ?>
  <?PHP if ($loginInst->hasAccess("task.fixedPrice") && $taskInst->isFixedPrice()) { ?>
    <tr class="light">
      <td><nobr><?PHP echo $lang['tasks_fixedPrice'];?></nobr></td>
      <td><?PHP echo $toolInst->formatCurrency($taskInst->fixedPrice);?></td>
    </tr>
  <?PHP } ?>
    <tr class="light">
      <td><nobr><?PHP echo $lang['tasks_hasToPay'];?></nobr></td>
      <td><?PHP echo $taskInst->hasToPay() ? $lang['common_yes'] : $lang['common_no'];?></td>
    </tr>
  <tr class="light">
    <td><?PHP echo $lang['common_priority'];?></td>
    <td><span class="<?PHP echo $taskInst->getPriorityStyle();?>"><?PHP echo $taskInst->getPriorityName();?></span></td>
  </tr><tr class="light">
    <td><?PHP echo $lang['common_type'];?></td>
    <td><span class="<?PHP echo $taskInst->getTypeStyle();?>"><?PHP echo $taskInst->getTypeName();?></span></td>
  </tr><tr class="light">
    <td><?PHP echo $lang['common_status'];?></td>
    <td><span class="<?PHP echo $taskInst->getStatusStyle();?>"><?PHP echo $taskInst->getStatusName();?></span></td>
  <?PHP if ($taskInst->body != "") { ?>
    <tr class="light">
      <td><?PHP echo $lang['common_body'];?></td>
      <td><?PHP echo ereg_replace("\n","<br>",$taskInst->body);?></td>
    </tr>
  <?PHP } ?>

  <form method="post" name="form3" action="taskdetails.php">
  <input type="hidden" name="action" value="deleteattach">
  <input type="hidden" name="id" value="">
  <tr class="light">
    <td><?PHP echo $lang['common_attachment'];?></td>
    <td>
      <table border="0" cellpadding="2" cellspacing="1" width="100%">
      <?PHP
      while ($a = current($taskInst->attachments)) {
        $attachment = new attachment($a);
        echo "<tr class=light>\n";
        echo "<td><a href=\"".tool::encodeUrl("fileget.php?created=".$attachment->created."&filename=".$attachment->name)."\" title=\"".$lang['common_open']." ".$attachment->name." ".$lang['common_inANewWindow']."\">".$attachment->name."</a></td>\n";
        echo "<td>".$attachment->getSize()."</td>\n";
        ?><td align=right><a href="javascript:document.form3.id.value='<?PHP echo $a;?>';document.form3.submit()"><img src="grafx/delete.gif" onClick="return Check();" width="16" height="15" border="0" title="<?PHP echo $lang['tasks_deleteThisJob'];?>"></a></td><?PHP
        next($taskInst->attachments);
        echo "</tr>\n";
      }
      ?>
      </table>
    </td>
  </tr>
  </form>
</table>

<?PHP
#######################################################################
## show form to post jobs
if ($status == 0) $jobInst->clear();
?>

<?PHP if (! $taskInst->isDone() && $loginInst->id == $taskInst->userId) { ?>
  <br>
  <a name="jobform"></a>
  <form method="post" name="form1">
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="taskid" value="<?PHP echo $taskInst->id;?>">
  <input type="hidden" name="id" value="<?PHP echo $jobInst->id;?>">
  <table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
    <tr>
      <th colspan="2">
        <?PHP
        if ($status == 1) echo $lang['tasks_editTask'];
        else {
          echo $lang['tasks_postNewJob'];
        }
    // is that private job?
    $privatejob_status = ($jobInst->isFlag(JOB_FLAG_PRIVATE)) ? "checked" : "";
        ?>
      </th>
    </tr><tr>
      <td class="list" valign="top"><?PHP echo $lang['common_comment'];?>:&nbsp;</td>
      <td class="list"><textarea wrap="physical" name="comment" rows="<?PHP echo $htmlconfig['textarea_rows'];?>" cols="<?PHP echo $htmlconfig['textarea_cols'];?>"><?PHP echo $jobInst->comment;?></textarea></td>
    </tr>
    <tr>
      <td class="list"><?PHP echo $lang['common_start'];?>:</td>
      <td class="list">
        <input type="text" name="startyear"  value="<?PHP echo $toolInst->getTime("Y",$jobInst->start);?>" size="4">-
        <input type="text" name="startmonth" value="<?PHP echo $toolInst->getTime("m",$jobInst->start);?>" size="2">-
        <input type="text" name="startday"   value="<?PHP echo $toolInst->getTime("d",$jobInst->start);?>" size="2">,&nbsp;
        <input type="text" name="starthour"  value="<?PHP echo $toolInst->getTime("H",$jobInst->start);?>" size="2">:
        <input type="text" name="startmin"   value="<?PHP echo $toolInst->getTime("i",$jobInst->start);?>" size="2">
      </td>
    </tr>
    <?PHP if (tool::securePost('action') == "edit") {?>
    <tr>
      <td class="list"><?PHP echo $lang['common_stop'];?>:</td>
      <td class="list">
        <input type="text" name="stopyear"  value="<?PHP echo $toolInst->getTime("Y",$jobInst->stop);?>" size="4">-
        <input type="text" name="stopmonth" value="<?PHP echo $toolInst->getTime("m",$jobInst->stop);?>" size="2">-
        <input type="text" name="stopday"   value="<?PHP echo $toolInst->getTime("d",$jobInst->stop);?>" size="2">,&nbsp;
        <input type="text" name="stophour"  value="<?PHP echo $toolInst->getTime("H",$jobInst->stop);?>" size="2">:
        <input type="text" name="stopmin"   value="<?PHP echo $toolInst->getTime("i",$jobInst->stop);?>" size="2">
        <input type="checkbox" class="checkbox" name="taskdone" value="1"><?PHP echo $lang['common_closeTask'];?>
      </td>
    </tr>
    <?PHP } ?>
  <tr>
    <td class="list"><?PHP echo $lang['common_private_job']?></td>
    <td class="list">
      <input type="checkbox" name="privatejob" value="1" <?PHP echo $privatejob_status?>><?PHP echo $lang['common_private_job_desc']?>
    </td>
  </tr>
    <tr>
      <td class="list">&nbsp;</td>
      <?PHP if (tool::securePost('action') == "edit") {?>
        <td class="list"><input type="submit" value="<?PHP echo $lang['common_save'];?>"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
      <?PHP } else {?>
        <td class="list"><input type="submit" value="<?PHP echo $lang['common_insert'];?>"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
      <?PHP }?>
    </tr>
  </table>
  </form>
<?PHP } ?>

<?PHP
# order
if (!$order) {$order = "start";}
else {$order = tool::secureGet('order');}
if (tool::secureGet('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}
$list = $jobInst->getList($order,$desc);

if ($jobInst->matches > 0) {
  #######################################################################
  ## show existing jobs
  $colspan=4;
  if ($loginInst->id != $taskInst->userId) $colspan --;
  ?>
    <br>
    <a name="joblist"></a>
    <form method="post" name="form2">
    <input type="hidden" name="action" value="">
    <input type="hidden" name="id" value="">
    <input type="hidden" name="taskid" value="<?PHP echo $taskInst->id;?>">
    <table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
      <tr>
        <th colspan="<?PHP echo $colspan+1;?>"><?PHP echo $jobInst->matches;?> <?PHP echo $lang['tasks_jobsUntilNow'];?></th>
      </tr><tr>
        <?PHP
          if ($loginInst->id == $taskInst->userId) echo "<th>".$lang['common_action']."</th>\n";
          echo "<th>".$lang['common_comment']."</th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$taskInst->id."&order=start&desc=".$desc)."#joblist\" title=\"".$lang['tasks_orderByStartTime']."\">".$lang['common_start']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$taskInst->id."&order=stop&desc=".$desc)."#joblist\" title=\"".$lang['tasks_orderByStopTime']."\">".$lang['common_stop']."</a></th>\n";
          echo "<th>".$lang['common_usedTime']."</th></tr>\n";

  while ($element = current($list)) {
    $jobInst->activate($element);
  $jobStyle = ($jobInst->isFlag(JOB_FLAG_PRIVATE)) ? "_private" : "";
    ?>
      <tr>
      <?PHP if ($loginInst->id == $taskInst->userId) {?>
        <td class=list<?PHP echo $jobStyle?>>
        <a href="javascript:document.form2.id.value='<?PHP echo $element;?>';document.form2.action.value='delete';document.form2.submit()"><img src="grafx/delete.gif" onClick="return Check();" width="16" height="15" border="0" title="<?PHP echo $lang['tasks_deleteThisJob'];?>"></a>
        <?PHP if (!$jobInst->isDone()) {?>
          <a href="javascript:document.form2.id.value='<?PHP echo $element;?>';document.form2.action.value='edit';document.form2.submit()"><img src="grafx/edit.gif" width="16" height="15" border="0" title="<?PHP echo $lang['tasks_editThisJob'];?>"></a>
        <?PHP } ?>
        </td>
      <?PHP } ?>
      <td class=list<?PHP echo $jobStyle?>>
    <?PHP
    if ($jobInst->comment) echo $jobInst->comment;
    echo "&nbsp;</td>\n";
    echo "<td class=list$jobStyle>".$toolInst->getTime("d.m.Y, H:i",$jobInst->start)."</td>\n";
    if ($jobInst->id == $jobInst->getOpenJob()) {
      echo "<td class=list$jobStyle>".$lang['tasks_running']."</td>\n";
    }
    else {
      echo "<td class=list$jobStyle>".$toolInst->getTime("d.m.Y, H:i",$jobInst->stop)."</td>\n";
    }
    echo "<td class=list$jobStyle align=right>".$toolInst->formatTime($jobInst->getSummary())."</td>\n";
    echo "</tr>\n";
    next($list);
  }
  echo "<tr>\n";
  echo "<td class=list colspan=".$colspan." align=right><b>".$lang['common_summaryTime']." : </b></td>\n";
  echo "<td class=list align=right><b>".$toolInst->formatTime($taskInst->getSummary())."</b></td>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "<td class=list_private colspan=".$colspan." align=right><b>".$lang['common_private_jobs']." - ".$lang['common_summaryTime']." : </b></td>\n";
  echo "<td class=list_private align=right><b>".$toolInst->formatTime($taskInst->getSummary(true))."</b></td>\n";
  echo "</tr>\n";

  if ($projectInst->rate || $userInst->rate) {
    if ($loginInst->hasAccess("task.getSummary")) {
      echo "<tr>\n";
      echo "<td class=list colspan=".$colspan." align=right><b>".$lang['common_roundedSummaryTime']." : </b></td>\n";
      echo "<td class=list align=right><b>".$toolInst->formatTime($toolInst->deductibleSeconds($taskInst->getSummary()))."</b></td>\n";
      echo "</tr>\n";
    }
    if ($loginInst->hasAccess("task.getCosts")) {
      echo "<tr>\n";
      if ($taskInst->fixedPrice > "0") {
        echo "<td class=list colspan=".$colspan." align=right><b>".$lang['common_summaryCosts']." </b> (".$lang['tasks_fixedPrice'].") <b>:</b></td>\n";
      }
      else {
        echo "<td class=list colspan=".$colspan." align=right><b>summary costs </b> (".$taskInst->getRate()." ".$config['currency']."/".$lang['common_hour'].") <b>:</b></td>\n";
      }
      echo "<td class=list align=right><b>".$toolInst->formatCurrency($taskInst->getCosts(true))."</b></td>\n";
      echo "</tr>\n";
    }
    if ($loginInst->hasAccess("task.getCustomerCosts")) {
      echo "<tr>\n";
      if ($taskInst->fixedPrice > "0") {
        echo "<td class=list colspan=".$colspan." align=right><b>".$lang['common_customerCosts']."</b> (".$lang['tasks_fixedPrice'].") <b>:</b></td>\n";
      }
      else {
        echo "<td class=list colspan=".$colspan." align=right><b>".$lang['common_customerCosts']."</b> (".$lang['common_withoutBugsAndTodos'].") <b>:</b></td>\n";
      }
      echo "<td class=list align=right><b>".$toolInst->formatCurrency($taskInst->getCustomerCosts())."</b></td>\n";
      echo "</tr>\n";
    }
  }
  echo "</table>\n";
  echo "</form>\n";
}

?>

</div>
&nbsp;

<?PHP
/***************************************************************************
 * $Log: taskdetails.php,v $
 * Revision 1.12  2005/02/20 23:47:34  matchboy
 * Removed truncated comments. Was set to 100 characters, let's allow this to
 * grow as necessary.
 *
 * Revision 1.11  2005/02/20 20:09:09  genghishack
 * Fixed two bugs:
 *
 * 1) job times on task details page were displaying as 0:00 (being read as private jobs instead of public)
 * 2) task end date was always displaying the same date as the start date when attempting to edit and record end time,
 *    now shows actual end date as well as time when editing a job
 *
 * Revision 1.9  2004/03/17 19:30:52  willuhn
 * @N configurable behavior for task types the customer has to pay for
 *
 * Revision 1.8  2004/02/28 23:03:32  znouza
 *
 * fileget.php
 * @N new file handler - with this you're passing uploaded file to direct download
 *
 * requests.php taskdetails.php tasks.php
 * @C handling uploaded files
 *
 * taskdetails.php
 * @B displaying task (secureGet is case sensitive :( )
 *
 * Revision 1.7  2004/02/28 21:14:21  znouza
 * @B bug #893537 fixed.
 * @N adding handling of non-existent request - even if adding a request, didn't
 *    reload a page and click the same request again.
 *
 * Revision 1.6  2004/02/28 19:51:42  znouza
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
 * Revision 1.5  2003/11/18 02:01:36  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/08/29 07:51:06  arneke
 * suggested date for job completion is now same as entered start date
 *
 * Revision 1.2  2003/08/05 19:43:06  willuhn
 * @B removed small typo in taskdetails
 *
 * Revision 1.1.1.1  2003/07/28 19:22:24  willuhn
 * reimport
 *
 * Revision 1.25  2002/06/26 14:14:29  willuhn
 * @N added form "query task by id"
 *
 * Revision 1.24  2002/05/05 20:12:42  willuhn
 * @N added feature "fixed price" for tasks
 *
 * Revision 1.23  2002/05/02 21:42:40  willuhn
 * @N pretty cool new feature in "task hotlist" -> order by planned time left
 *
 * Revision 1.22  2002/05/02 19:51:32  willuhn
 * @N task->isAvailable() checks if task is "in progress" or "request"
 * @N link to parent task in taskdetails
 * @N link to all child tasks in taskdetails
 * @B in task hotlist
 *
 * Revision 1.21  2002/03/31 16:57:19  willuhn
 * @B task id wasn't set when changing the project
 * @N added getSize() in attachment class
 *
 * Revision 1.20  2002/03/30 19:55:54  willuhn
 * @N deleting of attachments (in taskdetails)
 *
 * Revision 1.19  2002/02/24 22:41:20  willuhn
 * updated content-type in reportviewer
 *
 * Revision 1.18  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 ***************************************************************************/
?>
