<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/plugins/roadmap/index.php,v $
 * $Revision: 1.3 $
 * $Date: 2003/11/21 05:33:37 $
 * $Author: arneke $
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
<form method="post">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
  <tr>
    <td valign="top">choose a project to visualize:&nbsp;</td>
    <td><select name="id"><option value="">choose...
      <?PHP 
      $projectInst = new project();
      if(tool::securePost('list_done_projects') == ""){
         $projectInst->filterStatusId = PROJECT_STATUS_DONE;
         $projectInst->filterInvertStatus = 1;
      }
      $list = $projectInst->getList();
      while ($element = current($list)) {
        $projectInst->activate($element);
        $selected = "";
        if ($projectInst->id == tool::securePost('id')) $selected = "selected";
        echo "<option ".$selected." value=\"".$projectInst->id."\">".$projectInst->name."\n";
        next($list);
      }
      ?>
    </select>
    <br><input type="checkbox" name="list_done_projects"
    <?PHP
    if( tool::securePost('list_done_projects') != "") {
      echo ' checked>';
    }else{
      echo '>';
    }
    ?>
    List completed projects&nbsp;&nbsp;
    <input type="submit" value="Submit">
    </td>
  </tr>
</table>

<?PHP if (tool::securePost('id')) {

    // first we walk through all tasks to find the deepest mountpoint
    $taskInst = new task();
    $taskInst->filterProjectId = tool::securePost('id');
    $list = $taskInst->getList();
    $max = 1;
    $nodes = array();
    while ($element = current($list)) {
      $taskInst->activate($element);
      $count = count($taskInst->treeId());
      if ($count > $max) $max = $count;
      $nodes[$element] = $count;
      next($list);
    }

    $projectInst = new project(tool::securePost('id'));
  ?>

  <br><br>
  <?PHP echo "<h2>project roadmap for <a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id)."',width='500',height='500')\" title=\"show details for this project\">".$projectInst->name."</a></h2>\n";?>
  <table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
    <tr>
      <th colspan="<?PHP echo $max;?>"><nobr>task name</nobr></th>
      <th>prio</th>
      <th>status</th>
      <th>used</th>
      <th><nobr>planned</nobr></th>
      <th><nobr>days left</nobr></th>
      <th>percentage</th>
      <th>&nbsp;</th>
    </tr>
  <?PHP

    $sumPercentage = 0;
    $rows = 0;

    function processTask($taskDepth,$taskList) {
      global $taskInst,$max,$toolInst,$sumPercentage,$rows;

      while ($element = current($taskList)) {
        $taskInst->activate($element);
        ?><tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
        for ($i=1;$i<$taskDepth;$i++) {
          echo "<td>&nbsp;&nbsp;&nbsp;</td>";
        }
        $colspan = $max-$taskDepth+1;
        echo "<td colspan=\"".$colspan."\"><nobr><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$element)."',width='500',height='500')\" title=\"show details for this task\">".$taskInst->subject."</a></nobr></td>\n";
        echo "<td><nobr class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</nobr></td>\n";
        echo "<td><nobr class=".$taskInst->getStatusStyle().">".$taskInst->getStatusName()."</nobr></td>\n";
        echo "<td align=right><nobr>".$toolInst->formatTime($taskInst->getSummary())."</nobr></td>\n";
        echo "<td align=right><nobr>".$toolInst->getTime("Y-m-d",$taskInst->time)." - ".$toolInst->getTime("Y-m-d",$taskInst->finish)."</nobr></td>\n";

        if (!$taskInst->isDone() && $taskInst->statusId != TASK_STATUS_WAITING && $taskInst->finish != 0) {
          $days = $toolInst->numberRound((($taskInst->finish - $toolInst->getTime("U")) / 60 / 60 / 24),2);

          $percent = $toolInst->numberRound((100 * ($toolInst->getTime("U") - $taskInst->finish)) / ($taskInst->finish - $taskInst->time),2);
          if ($taskInst->finish < $toolInst->getTime("U")) $percent += 100;

          $sumPercentage += $percent;
          $rows++;
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
          echo "<td colspan=\"2\" class=\"".$taskInst->getStatusStyle()."\">".$taskInst->getStatusName()."</td>\n";
        }
        echo "<td width=\"100%\">&nbsp;</td>\n";
        echo "</tr>\n";
        next($taskList);
        $count = $taskDepth;
        processTask(++$count,$taskInst->childs());
      }
    }

    // now we create a tasklist, containing only the root tasks
    $childs = array();

    $taskInst = new task();
    $taskInst->filterProjectId = tool::securePost('id');
    $taskInst->filterMountId = 0;
    processTask(1,$taskInst->getList());


  ?>
  <tr class="dark">
    <td align="right" colspan="<?PHP echo $max+5;?>"><b>average</b></td>
    <?PHP
    $percent = 100;
    if ($rows > 0) {
      $percent = $toolInst->numberRound(($sumPercentage/$rows),2);
    }
    if ($percent > 100) {
      ?><td align="right" class="rmred"><?PHP
    }
    elseif ($percent > 80) {
      ?><td align="right" class="rmyellow"><?PHP
    }
    else {
      ?><td align="right" class="rmgreen"><?PHP
    }
    ?>
    <nobr><b><?PHP echo $percent;?> %</b></nobr></td>
    <td>&nbsp;</td>
  </tr>
  </table>

<?PHP } ?>

</form>
<?PHP
/***************************************************************************
 * $Log: index.php,v $
 * Revision 1.3  2003/11/21 05:33:37  arneke
 * Added statusId filtering to project and roadmap plugin
 *
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:23:13  willuhn
 * reimport
 *
 * Revision 1.8  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.7  2002/05/05 20:12:42  willuhn
 * @N added feature "fixed price" for tasks
 *
 * Revision 1.6  2002/04/15 22:14:35  willuhn
 * @N added plugin "task hotlist"
 *
 * Revision 1.5  2002/04/10 23:00:13  willuhn
 * @misc
 *
 * Revision 1.4  2002/04/09 22:53:00  willuhn
 * @N added first grafx
 *
 * Revision 1.3  2002/04/09 22:37:28  willuhn
 * @N first tree works ;)
 *
 * Revision 1.2  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.1  2002/03/30 20:30:30  willuhn
 * @N new plugin: "project tree"
 *
 * Revision 1.1  2002/03/30 14:14:39  willuhn
 * @N added plugin loader
 *
 ***************************************************************************/
?>
