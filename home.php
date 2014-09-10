
<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/home.php,v $
 * $Revision: 1.5 $
 * $Date: 2005/02/20 18:14:56 $
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
?>

<h1><?PHP echo $lang['common_home'];?></h1>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td valign="top">
      <?PHP
        // create box with today's jobs until now
        if ($loginInst->hasAccess("job")) {
          $boxInst = new box();
          $dayToShow = $toolInst->getTime("U");
          if (tool::secureGet('day') && $toolInst->checkInt(tool::secureGet('day'))) $dayToShow = tool::secureGet('day');
          $boxInst->setTitle($lang['home_myWork']. " ".$toolInst->getTime("Y-m-d",$dayToShow));
          $boxInst->setWidth("530");
          $boxInst->setBgColor("#f8f8f8");

          $jobInst = new job();
          $jobInst->filterUserId = $loginInst->id;
          $jobInst->filterStartTime = $toolInst->timestampToSec($toolInst->getTime("Y",$dayToShow),
                                                                $toolInst->getTime("m",$dayToShow),
                                                                $toolInst->getTime("d",$dayToShow),0,0);
          $jobInst->filterStopTime  = $toolInst->timestampToSec($toolInst->getTime("Y",$dayToShow),
                                                                $toolInst->getTime("m",$dayToShow),
                                                                $toolInst->getTime("d",$dayToShow),23,59);
          // show currently running job only today
          if ($toolInst->getTime("Y",$dayToShow) != $toolInst->getTime("Y")) $jobInst->filterCurrentlyRunning = "1";
          if ($toolInst->getTime("m",$dayToShow) != $toolInst->getTime("m")) $jobInst->filterCurrentlyRunning = "1";
          if ($toolInst->getTime("d",$dayToShow) != $toolInst->getTime("d")) $jobInst->filterCurrentlyRunning = "1";

          $list = $jobInst->getList();
          if ($jobInst->matches > 0) {
            $boxInst->addContent("<table border=0 cellpadding=2 cellspacing=0 width=100%>");
            $boxInst->addContent("<tr><th>".$lang['common_task']."</th><th>".$lang['common_comment']."</th><th class=right>".$lang['common_start']."</th><th class=right>".$lang['common_stop']."</th><th class=right>".$lang['common_used']."</th></tr>");

            $sum = 0;
            while ($element = current($list)) {
              $jobInst->activate($element);
              $taskInst = new task($jobInst->taskId);
              $projectInst = new project($taskInst->projectId);
              $boxInst->addContent("<tr><td class=list><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$taskInst->id)."',width='500',height='500')\" title=\"".$lang['common_showTaskdetails']."\">".substr($projectInst->name.": ".$taskInst->subject,0,40)."...</a></td>");
              $boxInst->addContent("<td class=list>".substr($jobInst->comment,0,50)."...</td>");
              $boxInst->addContent("<td class=list align=right>".$toolInst->getTime("H:i",$jobInst->start)."</td>");
              if ($jobInst->id == $jobInst->getOpenJob()) $boxInst->addContent("<td class=list align=right>running</td>");
              else $boxInst->addContent("<td class=list align=right>".$toolInst->getTime("H:i",$jobInst->stop)."</td>");


              $boxInst->addContent("<td class=list align=right>".$toolInst->formatTime($jobInst->getSummary())."</td></tr>");
              $sum += $jobInst->getSummary();
              next($list);
            }
            $boxInst->addContent("<tr><td colspan=4 class=list align=right><b>".$lang['common_summary']."&nbsp;</b></td><td class=list align=right><b>".$toolInst->formatTime($sum)."</b></td></tr>");
            $boxInst->addContent("</table>");
          }
          else {
            $boxInst->addContent("&nbsp;".$lang['home_myWorkTodayNoMatches']."<br>&nbsp;");
          }
          $dayBefore = $dayToShow - (60 * 60 * 24);
          $dayAfter  = $dayToShow + (60 * 60 * 24);
          $boxInst->addContent("<table border=0 cellpadding=2 cellspacing=0 width=100%>");
          $boxInst->addContent("<tr><td class=list>[<a href=\"".$toolInst->encodeUrl("index.php?content=home.php&day=".$dayBefore)."\">&laquo; ".$toolInst->getTime("Y-m-d",$dayBefore)."</a>]</td>");
          // show forward link only, if not current day (huuuaa, what a date compare ;))
          if ($toolInst->timestampToSec($toolInst->getTime("Y",$dayToShow),
                                   $toolInst->getTime("m",$dayToShow),
                                   $toolInst->getTime("d",$dayToShow),0,0) <
              $toolInst->timestampToSec($toolInst->getTime("Y"),
                                   $toolInst->getTime("m"),
                                   $toolInst->getTime("d"),0,0)) {
            $boxInst->addContent("<td class=list align=right>[<a href=\"".$toolInst->encodeUrl("index.php?content=home.php&day=".$dayAfter)."\">".$toolInst->getTime("Y-m-d",$dayAfter)." &raquo;</a>]</td>");
          }
          $boxInst->addContent("</table>");
          $boxInst->get();
        }
      ?>
    &nbsp;</td>
    <td valign="top" align="right" rowspan="2">
      <?PHP
        // box to query tasks by id
        if ($loginInst->hasAccess("task")) {
          $boxInst = new box();
          $boxInst->setTitle($lang['home_queryTask']);
          $boxInst->setBgColor("#f8f8f8");
          $boxInst->addContent("<form name=\"form3\" onsubmit=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid='+document.form3.taskid.value+'")."','500','500')\">");
          $boxInst->addContent("&nbsp;".$lang['common_ID']." ".$lang['common_task'].": <input type=\"text\" name=\"taskid\" size=\"".$htmlconfig['text_size4']."\">");
          $boxInst->addContent("<input type=\"button\" value=\"".$lang['common_search']."\" onclick=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid='+document.form3.taskid.value+'")."','500','500')\">");
          $boxInst->addContent("</form>");
          $boxInst->get();
        }

        if ($loginInst->hasAccess("task")) {
          // create box with open tasks
          $taskInst = new task();
          $taskInst->filterStatusId = TASK_STATUS_DONE;
          $taskInst->filterInvertStatus = 1;
          $taskInst->filterUserId = $loginInst->id;
          $list = $taskInst->getList("priority","DESC");

          $boxInst = new box();
          $boxInst->setTitle($lang['home_myOpenTasks']);
          $boxInst->setBgColor("#f8f8f8");

          if ($taskInst->matches > 0) {
            $boxInst->addContent("<table border=0 cellpadding=2 cellspacing=0 width=100%>");
            $boxInst->addContent("<tr><th>".$lang['common_priority']."</th><th>".$lang['common_type']."</th><th>".$lang['common_subject']."</th></tr>");
            while ($element = current($list)) {
              $taskInst->activate($element);
              $projectInst = new project($taskInst->projectId);
              if ($projectInst->isAvailable()) {
                $projectInst = new project($taskInst->projectId);
                $boxInst->addContent("<tr><td valign=top class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</td>");
                $boxInst->addContent("<td valign=top class=".$taskInst->getTypeStyle().">".$taskInst->getTypeName()."</td>");
                $boxInst->addContent("<td class=list><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$element)."',width='500',height='500')\" title=\"".$lang['common_showTaskdetails']."\">");
                $boxInst->addContent(substr($projectInst->name.": ".$taskInst->subject,0,50));
                $boxInst->addContent("...</a></td></tr>");
              }
              next($list);
            }
            $boxInst->addContent("</table>");
          }
          else {
            $boxInst->addContent("<b>".$lang['home_myOpenTasksNoMatches']."<br>&nbsp;");
          }
          $boxInst->get();
        }
      ?>
      <br>

      <?PHP
        // create box with requests
        if ($loginInst->hasAccess("request")) {
          $requestInst = new request();
          $list = $requestInst->getList("priority","DESC");
          if ($requestInst->matches > 0) {
            $count = 0;
            $boxInst = new box();
            $boxInst->setTitle($lang['home_unassignedRequests']);
            $boxInst->setWidth("300");
            $boxInst->setBgColor("#f8f8f8");
            $boxInst->addContent("<table border=0 cellpadding=2 cellspacing=0 width=100%>");
            $boxInst->addContent("<tr><th>".$lang['common_priority']."</th><th>".$lang['common_type']."</th><th>".$lang['common_subject']."</th></tr>");

            while ($element = current($list)) {
              $requestInst->activate($element);
              $projectInst = new project($requestInst->projectId);
              if ($loginInst->id == $projectInst->managerId) {
                $boxInst->addContent("<tr><td valign=top class=".$requestInst->getPriorityStyle().">".$requestInst->getPriorityName()."</td>");
                $boxInst->addContent("<td valign=top class=".$requestInst->getTypeStyle().">".$requestInst->getTypeName()."</td>");
                $boxInst->addContent("<td class=list><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=requestdetails.php&view=details&requestid=".$element)."',width='500',height='500')\" title=\"".$lang['common_showDetailsForThisRequest']."\">");
                $boxInst->addContent(substr($projectInst->name.": ".$requestInst->subject,0,50));
                $boxInst->addContent("...</a></td></tr>");
                $count++;
              }
              next($list);
            }
            $boxInst->addContent("</table>");
            if ($count == 0) {
              $boxInst->clearContent();
              $boxInst->addContent($lang['home_unassignedRequestsNoMatches']);
            }
            $boxInst->get();
          }
        }
      ?>
    </td>
  </tr>
  <tr>
    <td valign="bottom">
      <h2><?PHP echo $lang['home_interested_in_pmtool'];?></h2>
      <?PHP echo $lang['home_interested_in_pmtool_text'];?> <a href="http://www.pmtool.org/" target="_new">http://www.pmtool.org/</a>
    </td>
  </tr>
</table>

<?PHP
/***************************************************************************
 * $Log: home.php,v $
 * Revision 1.5  2005/02/20 18:14:56  matchboy
 * Changed URL to download pmtool to the new homepage.
 *
 * Revision 1.4  2003/11/18 16:31:20  willuhn
 * @N updated italian language file
 *
 * Revision 1.3  2003/11/18 01:45:58  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:26  willuhn
 * reimport
 *
 * Revision 1.18  2002/11/07 22:57:20  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.17  2002/06/26 14:14:28  willuhn
 * @N added form "query task by id"
 *
 * Revision 1.16  2002/05/06 21:02:23  willuhn
 * @N home: links to browse within the jobs of the last days
 *
 * Revision 1.15  2002/04/08 22:30:02  willuhn
 * @C project availability check on home page
 *
 * Revision 1.14  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.13  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
