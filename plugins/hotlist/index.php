<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/plugins/hotlist/index.php,v $
 * $Revision: 1.2 $
 * $Date: 2003/09/27 18:23:45 $
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
?>

<?PHP
if (!$loginInst->id) {
  echo "access denied\n";
  exit;
}
?>

<style type="text/css">
  td.rmred  {
    background-color    : #E46C77;
  }
  td.rmyellow  {
    background-color    : #EEEF7F;
  }
  td.rmgreen  {
    background-color    : #8BDD8B;
  }
</style>

<h1>plugins: <?PHP echo $pluginconfig['title'];?></h1>

<form method="post" name="form1">
create hotlist by:
  <select name="order" onchange="document.form1.submit();">
    <option value="finish">days left
    <option value="plannedhours" <?PHP if (tool::securePost('order') == "plannedhours") echo "selected";?>>planned hours
  </select>
</form>
<br><br>
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <th><nobr>project</nobr></th>
    <?PHP if ($loginInst->hasAccess("task.viewOther")) { ?>
      <th><nobr>user</nobr></th>
    <?PHP } ?>
    <th><nobr>task name</nobr></th>
    <th>prio</th>
    <th>status</th>
    <th>used</th>
    <?PHP if (tool::securePost('order') == "plannedhours") { ?>
      <th>planned</th>
      <th><nobr>time left</nobr></th>
    <?PHP } else { ?>
      <th><nobr>days left</nobr></th>
    <?PHP } ?>
    <th>percentage</th>
    <th>&nbsp;</th>
  </tr>

<?PHP

  $taskInst = new task();
  $order = "finish";
  if (tool::securePost('order')) $order=tool::securePost('order');
  $list = $taskInst->getList($order);

  if ($order == "plannedhours") {
    $listByHour = array();
    while ($element = current($list)) {
      $taskInst->activate($element);
      if (!$taskInst->isDone() && $taskInst->statusId != TASK_STATUS_WAITING && $taskInst->plannedHours && $taskInst->plannedHours != "0") {
        $diff = ($taskInst->plannedHours * 60 * 60) - $taskInst->getSummary();
        $listByHour[$diff] = $taskInst->id;
      }
      next($list);
    }
    ksort($listByHour);
    while (list($diff,$id) = each($listByHour)) {
      $taskInst->activate($id);
      ?><tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
      $projectInst = new project($taskInst->projectId);
      echo "<td><nobr><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id)."',width='500',height='500')\" title=\"show details for this project\">".$projectInst->name."</a></nobr></td>\n";
      if ($loginInst->hasAccess("task.viewOther")) {
        $userInst = new user($taskInst->userId);
        echo "<td><nobr>".$userInst->username."</nobr></td>\n";
      }
      echo "<td><nobr><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$id)."',width='500',height='500')\" title=\"show details for this task\">".$taskInst->subject."</a></nobr></td>\n";
      echo "<td><nobr class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</nobr></td>\n";
      echo "<td><nobr class=".$taskInst->getStatusStyle().">".$taskInst->getStatusName()."</nobr></td>\n";
      echo "<td align=\"right\"><nobr>".$toolInst->formatTime($taskInst->getSummary())."</nobr></td>\n";
      echo "<td align=\"right\"><nobr>".$taskInst->plannedHours." h</nobr></td>\n";

      if (!$taskInst->isDone()) {
        $percent = $toolInst->numberRound((($taskInst->getSummary() * 100) / ($taskInst->plannedHours * 60 * 60)),2);
        $diffHours = $toolInst->formatTime($diff);
        if ($percent > 100) {
          // alert: we've exceeded the scheduled planned hours
          echo "<td align=\"right\" class=\"rmred\">".$diffHours."</td>\n";
          echo "<td align=\"right\" class=\"rmred\">".$percent." %</td>\n";
        }
        elseif ($percent > 80) {
          // warn: less than 3 hours left
          echo "<td align=\"right\" class=\"rmyellow\">".$diffHours."</td>\n";
          echo "<td align=\"right\" class=\"rmyellow\">".$percent." %</td>\n";
        }
        else {
          echo "<td align=\"right\" class=\"rmgreen\">".$diffHours."</td>\n";
          echo "<td align=\"right\" class=\"rmgreen\">".$percent." %</td>\n";
        }
      }
      else {
        echo "<td colspan=\"2\">&nbsp;</td>\n";
      }
      echo "<td width=\"100%\">&nbsp;</td>\n";
      echo "</tr>\n";
    }
  }
  else {
    while ($element = current($list)) {
      $taskInst->activate($element);
      if ($taskInst->finish && $taskInst->finish != "0" && !$taskInst->isDone() && $taskInst->statusId != TASK_STATUS_WAITING) {
        ?><tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
        $projectInst = new project($taskInst->projectId);
        echo "<td><nobr><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id)."',width='500',height='500')\" title=\"show details for this project\">".$projectInst->name."</a></nobr></td>\n";
        if ($loginInst->hasAccess("task.viewOther")) {
          $userInst = new user($taskInst->userId);
          echo "<td><nobr>".$userInst->username."</nobr></td>\n";
        }
        echo "<td><nobr><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$element)."',width='500',height='500')\" title=\"show details for this task\">".$taskInst->subject."</a></nobr></td>\n";
        echo "<td><nobr class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</nobr></td>\n";
        echo "<td><nobr class=".$taskInst->getStatusStyle().">".$taskInst->getStatusName()."</nobr></td>\n";
        echo "<td><nobr>".$toolInst->formatTime($taskInst->getSummary())."</nobr></td>\n";

        if (!$taskInst->isDone() && $taskInst->finish != 0) {
          $days = $toolInst->numberRound((($taskInst->finish - $toolInst->getTime("U")) / 60 / 60 / 24),2);
          $percent = $toolInst->numberRound((100 * ($toolInst->getTime("U") - $taskInst->finish)) / ($taskInst->finish - $taskInst->time),2);
          if ($taskInst->finish < $toolInst->getTime("U")) $percent += 100;

          if ($percent > 100) {
            // alert: we've exceeded the scheduled finishing time
            echo "<td align=\"right\" class=\"rmred\">".$days."</td>\n";
            echo "<td align=\"right\" class=\"rmred\">".$percent." %</td>\n";
          }
          elseif ($percent > 80) {
            // warn: less than 3 days left
            echo "<td align=\"right\" class=\"rmyellow\">".$days."</td>\n";
            echo "<td align=\"right\" class=\"rmyellow\">".$percent." %</td>\n";
          }
          else {
            echo "<td align=\"right\" class=\"rmgreen\">".$days."</td>\n";
            echo "<td align=\"right\" class=\"rmgreen\">".$percent." %</td>\n";
          }
        }
        else {
          echo "<td colspan=\"2\">&nbsp;</td>\n";
        }
        echo "<td width=\"100%\">&nbsp;</td>\n";
        echo "</tr>\n";
      }
      next($list);
    }
  }
?>
</table>
<br>
<div class="small">all tasks without given planned finish or planned hours will be ignored.</div>
</form>
<?PHP
/***************************************************************************
 * $Log: index.php,v $
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:23:11  willuhn
 * reimport
 *
 * Revision 1.7  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.6  2002/05/05 20:12:42  willuhn
 * @N added feature "fixed price" for tasks
 *
 * Revision 1.5  2002/05/05 17:16:00  willuhn
 * @C plugin "task hotlist" -> shown in percentage
 *
 * Revision 1.4  2002/05/02 22:19:36  willuhn
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.3  2002/05/02 21:42:40  willuhn
 * @N pretty cool new feature in "task hotlist" -> order by planned time left
 *
 * Revision 1.2  2002/05/02 19:51:32  willuhn
 * @N task->isAvailable() checks if task is "in progress" or "request"
 * @N link to parent task in taskdetails
 * @N link to all child tasks in taskdetails
 * @B in task hotlist
 *
 * Revision 1.1  2002/04/15 22:14:35  willuhn
 * @N added plugin "task hotlist"
 *
 ***************************************************************************/
?>
