<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/taskbar.php,v $
 * $Revision: 1.6 $
 * $Date: 2003/12/07 19:21:16 $
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

if (!file_exists("inc/includer.inc.php")) {echo "panic: inc/includer.inc.php doesn't exist";exit;} require("inc/includer.inc.php");

if (tool::securePost('action') && tool::securePost('action') == "logout") {
  session_start();
  session_destroy();
//  header("Location: ".$HTTP_SERVER_VARS['PHP_SELF']);
}


?>
<html>
<head>
  <title>PMtool</title>
  <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=<?php echo $lang['charset']?>" />
  <link rel=stylesheet type="text/css" href="styles.css" />
  <script language="JavaScript" src="js/common.js" type="text/javascript"></script>
</head>
<body class="taskbar">
<div>
<form method="post" name="form1" class="taskbar" target="_self">
<?PHP

if (! isset($HTTP_SESSION_VARS["loginid"]) || $HTTP_SESSION_VARS["loginid"] == "") {
  // session is not set -> authenticate

  // try to authenticate by IP
  if ($loginInst->authByIp()) {
    $HTTP_SESSION_VARS['loginid'] = $loginInst->authByIp();
  }
  // try to authenticate by username/password
  elseif (tool::securePost('loginname') && tool::securePost('password') && $loginInst->authByPassword(tool::securePost('loginname'),tool::securePost('password'))) {
    $HTTP_SESSION_VARS['loginid'] = $loginInst->authByPassword(tool::securePost('loginname'),tool::securePost('password'));
  }

  if (isset($HTTP_SESSION_VARS['loginid']) && $HTTP_SESSION_VARS['loginid'] != "" && ! session_is_registered("loginid")) {
    $loginid = $HTTP_SESSION_VARS['loginid'];
    if (! session_register("loginid")) {
      echo "<b>".$lang['common_unableToSaveLoginInSession']."</b><br>";
      // could not save session -> give up
      exit;
    }
  }

  elseif (tool::securePost('loginname') || tool::securePost('password')) {
    // show error message only, if username/password was submitted
    $toolInst->errorStatus($lang['common_userUnknownOrPasswordWrong']);
  }
}


///// Language settings
$lang = array();
$importLang = "en";
$langFound = 0;

// check if language default is set
if (file_exists("lang/".$config['language'].".inc.php")) {
  $langFound = 1;
}


if (session_is_registered("loginid")) {
  if (! isset($HTTP_SESSION_VARS['loginid']) || $HTTP_SESSION_VARS['loginid'] == "") {
    echo "<b>".$lang['common_unableToFindloginInSession']."</b><br>";
    // could not save session -> give up
    exit;
  }

  // activate user
  $loginInst->activate($HTTP_SESSION_VARS['loginid']);

  // check existence of user specific language
  if (isset($loginInst->language) &&
      $loginInst->language != "" &&
      file_exists("lang/".$loginInst->language.".inc.php"))
  {
    $importLang = $loginInst->language;
    $langFound = 1;
  }

  if ($langFound == 1) {
    require("lang/".$importLang.".inc.php");
  }
  else {
    echo "panic, no language file found.\n";
    exit;
  }

}
else {

  if ($langFound == 1) {
    require("lang/".$importLang.".inc.php");
  }
  else {
    echo "panic, no language file found.\n";
    exit;
  }


  ?>
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td>
        <b><?PHP echo $lang['common_login'];?>:</b>
        <?PHP echo $lang['common_name'];?>:
        <?PHP
          $selected = "";
          if (tool::securePost('loginname') && tool::securePost('loginname') != "") {
            $selected = tool::securePost('loginname');
          }
          $dbInst->selectbox($dbInst->config['table_user'],"name","username","loginname",$selected);
        ?>
        &nbsp;<?PHP echo $lang['common_password'];?>: <input type="password" name="password" size="<?PHP echo $htmlconfig['text_size3'];?>">
        <input type="submit" value="<?PHP echo $lang['common_login'];?>">
      </td>
      <td align="right">
        <?PHP if (isset($global_status)) {
          echo $global_status;
        }?>
      </td>
    </tr>
  </table>
  </form>
  </body>
  </html>
  <?PHP
  exit;
}


if (tool::securePost('action')) {
  if (tool::securePost('action') == "start") {
    $jobInst = new job();
    $jobInst->taskId = tool::securePost('taskid');
    $jobInst->userId = $loginInst->id;
    $jobInst->comment = tool::securePost('comment');
    $jobInst->start();

  }
  elseif (tool::securePost('action') == "stop") {
    $jobInst = new job(tool::securePost('jobid'));
    $jobInst->comment = tool::securePost('comment');
    $jobInst->stop();
    if (tool::securePost('taskdone') && tool::securePost('taskdone') == "1") {
      $taskInst = new task($jobInst->taskId);
      $taskInst->stop();
      $taskInst->update();
    }
  }
}

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td>
      <?PHP

      $jobInst = new job();

      // check, if there's an open job
      $jobId = $jobInst->getOpenJob();
      if (isset($jobId) && $toolInst->checkInt($jobId)) {
        $jobInst->activate($jobId);
        $taskInst = new task($jobInst->taskId);
        $projectInst = new project($taskInst->projectId);
        ?>
        <input type="hidden" name="jobid" value="<?PHP echo $jobInst->id;?>">
        <input type="hidden" name="taskid" value="<?PHP echo $jobInst->taskId;?>">
        <input type="hidden" name="action" value="stop">
        <a href="javascript:document.form1.action.value='logout';document.form1.submit()" target="_self"><?PHP echo $lang['common_logout'];?></a>
        &nbsp;[<a target="_self" title = "<?PHP echo $lang['common_klickToRefreshThisTimeRunningSince'];?> <?PHP echo $toolInst->getTime("Y-m-d, H:i",$jobInst->start);?>)" href="<?PHP echo $toolInst->encodeUrl("taskbar.php");?>">time</a>: <input readonly="" style="width:54px;font-size:8pt;font-family:Verdana, Helvetica, Arial;text-align:right;border:0;background:transparent" size="5" name="servertime" value="<?PHP echo $toolInst->formatTime($jobInst->getSummary());?>">]&nbsp;<b><a target="_new" title="klick to view tasklist in browser" href="<?PHP echo $toolInst->encodeUrl("index.php?content=tasks.php");?>">task</a>:</b>&nbsp;<span title="<?PHP echo htmlspecialchars($taskInst->body);?>" onclick="javascript:openwindow('<?PHP echo $toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$taskInst->id);?>',width='500',height='500')"><?PHP echo htmlspecialchars(substr("[".$taskInst->id."] ".$projectInst->name.": ".$taskInst->subject,0,28))?></span>&nbsp;
        <b><?PHP echo $lang['common_comment'];?>:</b>&nbsp;<input value="<?PHP echo $jobInst->comment;?>" type="text" name="comment" size="<?PHP echo $htmlconfig['text_size3'];?>"><input title="<?PHP echo $lang['common_closeTask'];?>" type="checkbox" class="taskbar" name="taskdone" value="1"><input type="submit" value="<?PHP echo $lang['common_stop'];?>">
        <script language="JavaScript" type="text/javascript">
          <!--//
          <?PHP
            $min_counter = explode(":",preg_replace("/ h/","",$toolInst->formatTime($jobInst->getSummary())));
          ?>
          var sec = 0;
          var minute='<?PHP echo $min_counter[1];?>';
          var hour='<?PHP echo $min_counter[0];?>';

          function calctime(sec2,minute2,hour2){
            if (sec2>59) {
              sec2-=60;
              minute2++;
            }
            if (minute2>59) {
              minute2-=60;
              hour2++;
            }
            if (hour2>23) {
              hour2-=24;
            }
            var nowtime = new Date();
            var d="";
            if(hour2<10) d="0";
            d += hour2+":";
            if(minute2<10) d+="0";
            d+=minute2+":";
            if(sec2<10) d+="0";
            d+=sec2;

            form1.servertime.value=d;
            setTimeout('calctime('+(sec2+1)+','+minute2+','+hour2+')',1000);
            return true;
          }
          calctime(sec+1,minute,hour);
          //-->
        </script>
        <?PHP
      }

      // else: show task list
      else {
        ?>
        <input type="hidden" name="action" value="start">
        <a href="javascript:document.form1.action.value='logout';document.form1.submit()" target="_self"><?PHP echo $lang['common_logout'];?></a>
        &nbsp;[<a target="_self" title="<?PHP echo $lang['common_klickToRefreshTasklist'];?>" href="<?PHP echo $toolInst->encodeUrl("taskbar.php");?>"><?PHP echo $lang['common_reload'];?></a>]
        <b><a title="<?PHP echo ;?>" href="<?PHP echo $toolInst->encodeUrl("index.php?content=tasks.php");?>" target="_new"><?PHP echo $lang['common_task'];?></a>:</b>&nbsp;
        <?PHP
        $taskInst = new task();
        $taskInst->filterUserId = $loginInst->id;
        $taskInst->filterStatusId = TASK_STATUS_DONE;
        $taskInst->filterInvertStatus = "1";
        $list = $taskInst->getList("project");
        if (! $list) {
          echo $lang['common_noOpenTasksAvailable'];
        }
        else {
          echo "<select name=\"taskid\">\n";
          while ($element = current($list)) {
            $taskInst->activate($element);
            if ($taskInst->isAvailable()) {
              $projectInst = new project($taskInst->projectId);
              $selected = "";
              if (tool::securePost('taskid') && $taskInst->id == tool::securePost('taskid')) $selected = "selected";
              echo "<option ".$selected." value=".$taskInst->id.">[".$taskInst->id."] ".substr($projectInst->name.":&nbsp;".$taskInst->subject,0,55)."...\n";
            }
            next($list);
          }
          echo "</select>&nbsp;\n";
          ?>
          <b><?PHP echo $lang['common_comment'];?>:</b>&nbsp;
          <input type="text" name="comment" size="<?PHP echo $htmlconfig['text_size3'];?>"><input type="submit" value="<?PHP echo $lang['common_start'];?>">
        <?PHP } ?>
      <?PHP } ?>
    </td>
    <td align="right">
      <?PHP
        if (isset($global_status)) {
          echo $global_status;
        }
      ?>
    </td>
  </tr>
</table>
</form>
</div>
</body>
</html>

<?PHP
/***************************************************************************
 * $Log: taskbar.php,v $
 * Revision 1.6  2003/12/07 19:21:16  willuhn
 * @C language
 * @B
 *
 * Revision 1.5  2003/11/18 02:00:51  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/10/07 20:35:18  willuhn
 * @B fixed bug (sourceforge bug id: 815180)
 *
 * Revision 1.3  2003/09/27 18:28:16  willuhn
 * @B fixed typo in taskbar.php
 *
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:29  willuhn
 * reimport
 *
 * Revision 1.21  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.20  2002/06/26 14:14:29  willuhn
 * @N added form "query task by id"
 *
 * Revision 1.19  2002/05/03 11:31:53  willuhn
 * @B session handling
 *
 * Revision 1.18  2002/05/02 19:51:32  willuhn
 * @N task->isAvailable() checks if task is "in progress" or "request"
 * @N link to parent task in taskdetails
 * @N link to all child tasks in taskdetails
 * @B in task hotlist
 *
 * Revision 1.17  2002/04/17 22:26:46  willuhn
 * @N child tasks can only set to "in progress", if parent task is set to "done"
 *
 * Revision 1.16  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.15  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.14  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
