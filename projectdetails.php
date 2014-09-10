<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/projectdetails.php,v $
 * $Revision: 1.5 $
 * $Date: 2004/03/17 20:19:40 $
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
#######################################################################
## check login again
if (!$loginInst->id) {
  echo $lang['common_accessDenied']."\n";
  exit;
}
if (!$loginInst->hasAccess("project")) {
  echo $lang['common_accessDenied']."\n";
  exit;
}

$projectInst = new project(tool::secureGet('projectid'));

#######################################################################
## show project details

$customerInst              = new customer($projectInst->customerId);
$managerInst               = new user($projectInst->managerId);

$taskInst                  = new task();
$taskInst->filterProjectId = $projectInst->id;

?>
<h1><?PHP echo $lang['common_projectDetails'];?></h1>
<div align="center">
<table border="0" cellpadding="2" cellspacing="1" width="96%">
  <tr>
    <td><h2><?PHP echo $projectInst->name;?></h2></td>
  </tr>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
  <tr>
    <th><?PHP echo $lang['common_description'];?></th>
    <th><?PHP echo $projectInst->description;?></a></th>
  </tr><tr>
    <th><?PHP echo $lang['common_customer'];?></th>
    <?PHP if ($customerInst->email) { ?>
      <th><a href="mailto:<?PHP echo $customerInst->email;?>"><?PHP echo $customerInst->company;?></a></th>
    <?PHP } else { ?>
      <th><?PHP echo $customerInst->company;?></a></th>
    <?PHP } ?>
  </tr><tr>
    <th><?PHP echo $lang['common_manager'];?></th>
    <?PHP if ($customerInst->email) { ?>
      <th><a href="mailto:<?PHP echo $managerInst->email;?>"><?PHP echo $managerInst->name;?></a></th>
    <?PHP } else { ?>
      <th><?PHP echo $managerInst->name;?></a></th>
    <?PHP } ?>
  </tr><tr>
    <th><?PHP echo $lang['common_rate'];?></th>
    <?PHP if ($projectInst->rate) { ?>
      <th><?PHP echo $toolInst->formatCurrency($projectInst->rate);?>/hour</th>
    <?PHP } else { ?>
      <th>no</th>
    <?PHP } ?>
  </tr><tr>
    <th><?PHP echo $lang['common_budget'];?></th>
    <?PHP if ($projectInst->budget) { ?>
      <th><?PHP echo $toolInst->formatCurrency($projectInst->budget);?></th>
    <?PHP } else { ?>
      <th>no</th>
    <?PHP } ?>
  </tr><tr>
    <td class="list"><?PHP echo $lang['common_priority'];?></td>
    <td class="list"><span class="<?PHP echo $projectInst->getPriorityStyle();?>"><?PHP echo $projectInst->getPriorityName();?></span></td>
  </tr><tr>
    <td class="list"><?PHP echo $lang['common_status'];?></td>
    <td class="list"><?PHP echo $projectInst->getStatusName();?></td>
  </tr>
</table>

<?PHP
# order
$order = "priority";
if (tool::secureGet('order')) {$order = tool::secureGet('order');}
if (tool::secureGet('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}
$list = $taskInst->getList($order,$desc);

if ($taskInst->matches > 0) {
  #######################################################################
  ## show existing tasks
  ?>
    <br>
    <table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
      <tr>
        <th colspan="6"><?PHP echo $taskInst->matches;?> <?PHP echo $lang['project_tasksUntilNow'];?></th>
      </tr><tr>
        <?PHP
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id."&order=subject&desc=".$desc)."\" title=\"".$lang['common_orderBySubject']."\">".$lang['common_subject']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id."&order=time&desc=".$desc)."\" title=\"".$lang['common_orderByPostedTime']."\">".$lang['common_posted']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id."&order=priority&desc=".$desc)."\" title=\"".$lang['common_orderByPriority']."\">".$lang['common_priority']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id."&order=type&desc=".$desc)."\" title=\"".$lang['common_orderByType']."\">".$lang['common_type']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id."&order=status&desc=".$desc)."\" title=\"".$lang['common_orderByStatus']."\">".$lang['common_status']."</a></th>\n";
          echo "<th>".$lang['common_usedTime']."</th>\n";

          $sum = 0;
          $sum_private = 0;
          $costs = 0;
          $costs_private = 0;
          $customerSum = 0;
          $customerCosts = 0;
          while ($element = current($list)) {
            $taskInst->activate($element);
            echo "<tr class=\"light\"><td class=list><nobr><a href=\"".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$taskInst->id)."\" title=\"".$lang['common_showTaskdetails']."\">".substr($taskInst->subject,0,15)."</a></nobr></td>\n";
            echo "<td class=list><nobr>".$toolInst->getTime("d.m.Y, H:i",$taskInst->time)."</nobr></td>\n";
            echo "<td><nobr class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</nobr></td>\n";
            echo "<td><nobr class=".$taskInst->getTypeStyle().">".$taskInst->getTypeName()."</nobr></td>\n";
            echo "<td><nobr class=".$taskInst->getStatusStyle().">".$taskInst->getStatusName()."</nobr></td>\n";
            echo "<td class=list align=right><nobr>".$toolInst->formatTime($taskInst->getSummary())."</nobr></td></tr>\n";
            $sum += $taskInst->getSummary();
            $sum_private += $taskInst->getSummary(true);
            $costs += $taskInst->getCosts();
            $costs_private += $taskInst->getCosts(true);
            $customerCosts += $taskInst->getCustomerCosts();
            $customerSum += $taskInst->getCustomerSummary();
            next($list);

          }
          echo "<tr><td class=list colspan=6>&nbsp;</td></tr>\n";
          echo "<tr>\n";
          echo "<td class=list colspan=4 align=right><b>".$lang['common_summaryTime']." : </b></td>\n";
          echo "<td class=list colspan=2 align=right><nobr><b>".$toolInst->formatTime($sum)."</b></nobr></td>\n";
          echo "</tr>\n";

      echo "<tr>\n";
        echo "<td class=list_private colspan=4 align=right><b>".$lang['common_private_jobs']." - ".$lang['common_summaryTime']." : </b></td>\n";
        echo "<td class=list_private colspan=2 align=right><b>".$toolInst->formatTime($sum_private)."</b></td>\n";
        echo "</tr>\n";

          if ($loginInst->hasAccess("task.getSummary")) {
            echo "<tr>\n";
            echo "<td class=list colspan=4 align=right><b>".$lang['common_roundedSummaryTime']." : </b></td>\n";
            echo "<td class=list colspan=2 align=right><b>".$toolInst->formatTime($toolInst->deductibleSeconds($sum))."</b></td>\n";
            echo "</tr>\n";
          }
          if ($loginInst->hasAccess("task.getCosts")) {
            echo "<tr>\n";
            echo "<td class=list colspan=4 align=right><b>".$lang['common_summaryCosts']." :</b></td>\n";
            echo "<td class=list colspan=2 align=right><nobr><b>".$toolInst->formatCurrency($costs+$costs_private)."</b></nobr></td>\n";
            echo "</tr>\n";
          }
          if ($loginInst->hasAccess("task.getCustomerCosts")) {
            echo "<tr>\n";
            echo "<td class=list colspan=4 align=right><b>".$lang['common_customerCosts']."</b> (".$lang['common_withoutBugsAndTodos'].") (".$toolInst->formatTime($toolInst->deductibleSeconds($customerSum)).") <b>:</b></td>\n";
            echo "<td class=list colspan=2 align=right><nobr><b>".$toolInst->formatCurrency($customerCosts)."</b></nobr></td>\n";
            echo "</tr>\n";
          }
          if ($loginInst->hasAccess("task.getCustomerCosts") && $projectInst->budget) {
            $percent = 100 - (($customerCosts * 100) / $projectInst->budget);
            echo "<tr>\n";
            echo "<td class=list colspan=4 align=right><b>budget avaliable:</b></td>\n";
            echo "<td class=list colspan=2 align=right><nobr><b>".$toolInst->numberRound($percent,2)." %</b></nobr></td>\n";
            echo "</tr>\n";
          }
  echo "</table>\n";
}
?>
</div>
&nbsp;

<?PHP
/***************************************************************************
 * $Log: projectdetails.php,v $
 * Revision 1.5  2004/03/17 20:19:40  willuhn
 * @N added priorities to projects
 *
 * Revision 1.4  2004/02/28 19:51:42  znouza
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
 * Revision 1.3  2003/11/18 01:48:43  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:22  willuhn
 * reimport
 *
 * Revision 1.12  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.11  2002/04/14 22:03:57  willuhn
 * @N multilanguage
 *
 * Revision 1.10  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.9  2002/02/27 22:37:43  willuhn
 * @C some styling in css
 *
 * Revision 1.8  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
