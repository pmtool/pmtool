<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/tasks.php,v $
 * $Revision: 1.6 $             } kig
 * $Date: 2004/03/17 20:19:50 $ } kig
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
if (!$loginInst->id || $loginInst->isCustomer()) {
  echo $lang['common_accessDenied']."\n";

  exit;
}

# check if projects available
$projectInst = new project();
if (!$projectInst->getList()) {
  echo $lang['tasks_projectsNoMatches']."\n";
  exit;
}

?>

<h1><?PHP echo $lang['common_tasks'];?> </h1>


<?PHP
$taskInst = new task();
// set default search values (only, if no submit was pressed)
if (!tool::securePost('action')) {
  $taskInst->filterStatusId = TASK_STATUS_DONE;
  $taskInst->filterInvertStatus = 1;
}

#######################################################################
## perform action

$recordType = "new";
$action = tool::securePost('action');

if ($action == "update") {
  $taskInst->id = tool::securePost('id');
  $taskInst->fill(tool::securePostAll());
  if ($taskInst->update()) {
    $taskInst->clear();
    $recordType = "new";
  }
  else {
    $recordType = "edit";
  }
}

if ($action == "new") {
  $taskInst->fill(tool::securePostAll());
  if($taskInst->insert()) $taskInst->clear();
}

if ($action == "search") {
  if(tool::securePost('lastrecordtype')) $recordType = tool::securePost('lastrecordtype');
  $taskInst->fill(tool::securePostAll());
  $taskInst->id = tool::securePost('id');
}

if ($action == "delete") {
  $taskInst->id = tool::securePost('id');
  $taskInst->delete();
}

if ($action == "edit") {
  $recordType = "edit";
  $taskInst->activate(tool::securePost('id'));
}

$taskInst->fillFilter(tool::securePostAll());

#######################################################################
## make edit / new form

?>

<form method="post" name="form1" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="<?PHP echo $config['attach_maxfilesize'];?>">
<input type="hidden" name="id" value="<?PHP echo $taskInst->id;?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="order" value="<?PHP echo tool::securePost('order');?>">
<input type="hidden" name="desc" value="<?PHP echo tool::securePost('desc');?>">
<input type="hidden" name="lastrecordtype" value="<?PHP echo $recordType;?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">
      <h2>
      <?PHP
        if ($recordType == "edit") {
      echo $lang['tasks_editTask'];
          ?>&nbsp;(<a href="javascript:document.form1.submit();"><?PHP echo $lang['tasks_newTask'];?></a>)<?PHP
        }
    else {
      echo $lang['tasks_newTask'];
    }
      ?>
      </h2>
      <table border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td><?PHP echo $lang['common_user'];?>:&nbsp;</td>
          <td>
            <?PHP
              if (!$loginInst->hasAccess("task.viewOther")) {
                 ?><input type="hidden" name="userid" value="<?PHP echo $loginInst->id;?>"><?PHP
                 echo $loginInst->name;
               }
               else {?>
                 <select name="userid">
                   <?PHP
                   $userInst = new user();
                   $list = $userInst->getList();
                   $found = "0";
                   while ($element = current($list)) {
                     $selected = "";
                     $userInst->activate($element);
                     if ($userInst->id == $taskInst->userId && $recordType == "edit" && $found == "0") {$selected = "selected"; $found = "1";}
                     elseif ($recordType == "new" && $found == "0" && ($userInst->id == $loginInst->id || $userInst->id == $taskInst->userId)) {$selected = "selected"; $found = "1";}
                     echo "<option ".$selected." value=\"".$userInst->id."\">".$userInst->name."\n";
                     next($list);
                   }
                   ?>
                 </select>
            <?PHP } ?>
          </td>
        </tr><tr>
          <td><?PHP echo $lang['common_project'];?>:&nbsp;</td>
          <td><select name="projectid" onchange="javascript:document.form1.action.value='search';document.form1.submit()">
            <?PHP
            $projectInst = new project();
            $list = $projectInst->getList();
            $projectMount = "";
            while ($element = current($list)) {
              $projectInst->activate($element);
              $selected = "";
              if ($projectInst->isAvailable() || $recordType == "edit") {
                if (! $projectMount) $projectMount = $element;
                if ($projectInst->id == $taskInst->projectId) {
                  $selected = "selected";
                  $projectMount = $element;
                }
                echo "<option ".$selected." value=\"".$projectInst->id."\">".$projectInst->name."\n";
              }
              next($list);
            }
            ?>
          </select></td>
        </tr><tr>
          <td><?PHP echo $lang['tasks_depends'];?>:&nbsp;</td>
          <td><select name="mountid">
          <?PHP
            if ($recordType == "edit") {
              $seen = array();
              $taskInst2 = new task();
              $taskInst2->filterProjectId = $projectMount;
              $list = $taskInst2->getList();
              while ($element = current($list)) {
                $taskInst2->activate($element);
                if ($element != $taskInst->mountId && $taskInst->id != $taskInst->mountId) {
                  if ($taskInst->id == $element) {
                    $seen[] = $taskInst2->mountId;
                    echo "<option selected value=\"".$taskInst2->mountId."\">/ ".implode(" / ",$taskInst->treeName($taskInst2->mountId))."\n";
                  }
                  else {
                    $seen[] = $element;
                    echo "<option value=\"".$element."\">/ ".implode(" / ",$taskInst->treeName($element))."\n";
                  }
                }
                next($list);
              }
              if (!in_array("0",$seen)) {
                echo "<option value=\"0\"> /\n";
              }
            }
            else {
              ?><option value="0">/<?PHP
              $taskInst2 = new task();
              $taskInst2->filterProjectId = $projectMount;
              $list = $taskInst2->getList();
              while ($element = current($list)) {
                echo "<option value=\"".$element."\">/ ".implode(" / ",$taskInst2->treeName($element))."\n";
                next($list);
              }
            }
          ?>
          </select>
          </td>
        </tr><tr>
          <td><?PHP echo $lang['common_subject'];?>:&nbsp;</td>
          <td><input type="text" name="subject" value="<?PHP echo $taskInst->subject;?>" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
        </tr><tr>
          <td valign="top"><?PHP echo $lang['common_body'];?>:&nbsp;</td>
          <td><textarea name="body" rows="<?PHP echo $htmlconfig['textarea_rows'];?>" cols="<?PHP echo $htmlconfig['textarea_cols'];?>"><?PHP echo $taskInst->body;?></textarea></td>
        </tr><tr>
          <td><?PHP echo $lang['common_attachment'];?>:&nbsp;</td>
          <td><input type="file" name="userfile" value="<?PHP echo $taskInst->attachment;?>" size="<?PHP echo $htmlconfig['text_size3'];?>"></td>
        </tr><tr>
          <td valign="top"><?PHP echo $lang['tasks_plannedFinish'];?>:&nbsp;</td>
          <td>
            <?PHP
              // if finish is not set, we give him 1 week
              if (! $taskInst->finish) $taskInst->finish = $toolInst->getTime("U") + (7*24*60*60);
            ?>
            <nobr>
            <input type="text" name="finishyear" value="<?PHP echo $toolInst->getTime("Y",$taskInst->finish);?>" size="4">-
            <input type="text" name="finishmonth" value="<?PHP echo $toolInst->getTime("m",$taskInst->finish);?>" size="2">-
            <input type="text" name="finishday" value="<?PHP echo $toolInst->getTime("d",$taskInst->finish);?>" size="2">,&nbsp;
            <input type="text" name="finishhour" value="<?PHP echo $toolInst->getTime("H",$taskInst->finish);?>" size="2">:
            <input type="text" name="finishminute" value="<?PHP echo $toolInst->getTime("i",$taskInst->finish);?>" size="2">
            </nobr>
          </td>
        </tr><tr>
          <td><?PHP echo $lang['tasks_plannedHours'];?>:&nbsp;</td>
          <td><input type="text" name="plannedhours" value="<?PHP echo $taskInst->plannedHours;?>" size="<?PHP echo $htmlconfig['text_size4'];?>"></td>
        </tr>
        <?PHP if ($loginInst->hasAccess("task.fixedPrice")) { ?>
          <tr>
            <td valign="top"><?PHP echo $lang['tasks_fixedPrice'];?>:&nbsp;</td>
            <td><input type="text" name="fixedprice" value="<?PHP echo $taskInst->fixedPrice;?>" size="<?PHP echo $htmlconfig['text_size4'];?>"> <?PHP echo $config['currency'];?><span class="comment"><br><?PHP echo $lang['tasks_leaveBlankIfNoFixedPriceTask'];?></span></td>
          </tr>
        <?PHP } ?>
        <tr>
          <td><?PHP echo $lang['common_priority'];?>:&nbsp;</td>
          <td><select name="priorityid">
            <?PHP
            $list = $taskInst->getPriorityList();
            while ($element = current($list)) {
              $selected = "";
              if ($element == $taskInst->priorityId) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getPriorityName($element)."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr>
        <tr>
          <td><?PHP echo $lang['common_type'];?>:&nbsp;</td>
          <td><select name="typeid">
            <?PHP
            $list = $taskInst->getTypeList();
            while ($element = current($list)) {
              $selected = "";
              if ($element == $taskInst->typeId) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getTypeName($element)."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr><tr>
          <td><?PHP echo $lang['common_status'];?>:&nbsp;</td>
          <td><select name="statusid">
            <?PHP
            $list = $taskInst->getStatusList();
            while ($element = current($list)) {
              $selected = "";
              if ($element == $taskInst->statusId) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getStatusName($element)."\n";
              next($list);
            }
            ?>
          </select></td>
        </tr>
        <tr><td>&nbsp;</td>
        <?PHP if ($recordType == "edit") {?>
          <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_save'];?>" onclick="document.form1.action.value='update'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
        <?PHP } else {?>
          <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_insert'];?>" onclick="document.form1.action.value='new'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"</td>
        <?PHP }?>
        </tr>
      </table>
    </td>
    <td><img src="grafx/dummy.gif" width="10" height="1" border="0"></td>
    <td bgcolor="#909090"><img src="grafx/dummy.gif" width="1" height="1" border="0"></td>
    <td><img src="grafx/dummy.gif" width="10" height="1" border="0"></td>
    <td valign="top">
      <h2><?PHP echo $lang['tasks_queryRecords'];?></h2>
      <table border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td><?PHP echo $lang['common_user'];?>:&nbsp;</td>
          <td>
            <?PHP
              if (!$loginInst->hasAccess("task.viewOther")) {
                 ?><input type="hidden" name="filteruserid" value="<?PHP echo $loginInst->id;?>"><?PHP
                 echo $loginInst->name;
               }
               else {?>
                 <select name="filteruserid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
                   <?PHP
                   $userInst = new user();
                   $list = $userInst->getList();
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
          <td><?PHP echo $lang['common_project'];?>:&nbsp;</td>
          <td><select name="filterprojectid"><option value=""><?PHP echo $lang['common_notSpecified'];?>
            <?PHP
            $projectInst = new project();
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
          <td><?PHP echo $lang['tasks_taskPosted'];?>:<br>(yyyy-mm-dd, hh:mm)&nbsp;</td>
          <td>
            <select name="filtertimebase">
            <?PHP
              if (tool::securePost('filtertimebase') == "<" || $taskInst->filterTimeBase == "<") {
                ?><option value=""><?PHP echo $lang['common_notSpecified'];?>
          <option value="<" selected>before
          <option value=">">after<?PHP
              }
              elseif (tool::securePost('filtertimebase') == ">" || $taskInst->filterTimeBase == ">") {
                ?><option value=""><?PHP echo $lang['common_notSpecified'];?>
          <option value="<">before
          <option value=">" selected>after<?PHP
              }
              else {
                ?><option value="" selected><?PHP echo $lang['common_notSpecified'];?>
          <option value="<">before
          <option value=">">after<?PHP
              }
            ?>
            </select>
            <br>
            <nobr>
            <input type="text" name="filtertimeyear" value="<?PHP echo $toolInst->getTime("Y",$taskInst->filterTime);?>" size="4">-
            <input type="text" name="filtertimemonth" value="<?PHP echo $toolInst->getTime("m",$taskInst->filterTime);?>" size="2">-
            <input type="text" name="filtertimeday" value="<?PHP echo $toolInst->getTime("d",$taskInst->filterTime);?>" size="2">,&nbsp;
            <input type="text" name="filtertimehour" value="<?PHP echo $toolInst->getTime("H",$taskInst->filterTime);?>" size="2">:
            <input type="text" name="filtertimemin" value="<?PHP echo $toolInst->getTime("i",$taskInst->filterTime);?>" size="2">
            </nobr>
          </td>
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
              if ($element == $taskInst->filterStatusId) $selected = "selected";
              echo "<option ".$selected." value=\"".$element."\">".$taskInst->getStatusName($element)."\n";
              next($list);
            }
            ?>
          </select>
          <?PHP
            $checked = "";
            if ($taskInst->filterInvertStatus == 1) $checked = "checked";
          ?>
          <input type="checkbox" name="filterinvertstatus" value="1" <?PHP echo $checked;?> class="checkbox"> <?PHP echo $lang['common_invert'];?>
        </tr>
        <tr><td>&nbsp;</td><td colspan="2"><input type="submit" onclick="document.form1.action.value='search'" value="<?PHP echo $lang['common_search'];?>"></td></tr>
      </table>
    </td>
  </tr>
</table>

<?PHP

#######################################################################
## list existing records

# order
$order = "time";
if (tool::securePost('order')) {$order = tool::securePost('order');}
if (tool::securePost('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}

$list = $taskInst->getList($order,$desc);

?>

<br><br>
<h2><?PHP echo $lang['tasks_available'];?> (<?PHP echo $taskInst->matches;?> <?PHP echo $lang['common_matches'];?>)</h2>
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <th align=left><a href="javascript:document.form1.order.value='id';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['tasks_orderById'];?>"><?PHP echo $lang['common_ID'];?></a></th>
    <?PHP
      if ($loginInst->hasAccess("task.viewOther")) { ?>
      <th align=left><a href="javascript:document.form1.order.value='username';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['tasks_orderByUser'];?>"><?PHP echo $lang['common_user'];?></a></th>
    <?PHP } ?>
    <th align=left><a href="javascript:document.form1.order.value='project';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByProject'];?>"><?PHP echo $lang['common_project'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='subject';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderBySubject'];?>"><?PHP echo $lang['common_subject'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='body';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByBody'];?>"><?PHP echo $lang['common_body'];?></a></th>
    <th align=left><?PHP echo $lang['common_attachment'];?></th>
    <th align=left><a href="javascript:document.form1.order.value='time';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByPostedTime'];?>"><?PHP echo $lang['common_posted'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='finish';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['tasks_orderByPlannedFinish'];?>"><?PHP echo $lang['tasks_plannedFinish'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='priority';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByPriority'];?>"><?PHP echo $lang['common_priority'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='type';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByType'];?>"><?PHP echo $lang['common_type'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='status';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByStatus'];?>"><?PHP echo $lang['common_status'];?></a></th>
    <th colspan=2><?PHP echo $lang['common_action'];?></th>
  </tr>
  <?PHP
  $style = "light";
  while ($element = current($list)) {
    $taskInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    echo "<td align=\"right\">".$taskInst->id."</td>\n";
    if ($loginInst->hasAccess("task.viewOther")) {
      $userInst = new user($taskInst->userId);
      echo "<td>".$userInst->username."</td>\n";
    }
    $projectInst = new project($taskInst->projectId);
    echo "<td><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id)."','500','500')\" title=\"".$lang['common_showDetailsForThisProject']."\">".$projectInst->name."</a></td>\n";
    echo "<td><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=taskdetails.php&view=details&taskid=".$element)."','500','500')\" title=\"".$lang['common_showTaskdetails']."\">".$taskInst->subject."</a></td>\n";
    echo "<td>";
    if ($taskInst->body) echo substr($taskInst->body,0,100);
    echo "&nbsp;</td>\n";
    echo "<td>\n";
    while ($a = current($taskInst->attachments)) {
      $attachment = new attachment($a);
      echo "<a href=\"".tool::encodeUrl("fileget.php?created=".$attachment->created."&filename=".$attachment->name)."\" title=\"".$lang['common_open']." ".$attachment->name." ".$lang['common_inANewWindow']."\">".$attachment->name."</a><br>\n";
      next($taskInst->attachments);
    }
    echo "</td>\n";
    echo "<td><nobr>".$toolInst->getTime("d.m.Y, H:i",$taskInst->time)."</nobr></td>\n";
    $finish = "";
    if ($taskInst->finish != 0) {
      $finish = $toolInst->getTime("d.m.Y, H:i",$taskInst->finish);
    }
    echo "<td><nobr>".$finish."</nobr></td>\n";
    echo "<td><nobr class=".$taskInst->getPriorityStyle().">".$taskInst->getPriorityName()."</nobr></td>\n";
    echo "<td><nobr class=".$taskInst->getTypeStyle().">".$taskInst->getTypeName()."</nobr></td>\n";
    echo "<td><nobr class=".$taskInst->getStatusStyle().">".$taskInst->getStatusName()."</nobr></td>\n";
    echo "<td align=center><input type=submit value=".$lang['common_delete']." onclick=\"document.form1.id.value='".$element."';document.form1.action.value='delete';return Check()\"></td>\n";
    echo "<td align=center><input type=submit value=".$lang['common_edit']." onclick=\"document.form1.id.value='".$element."';document.form1.action.value='edit'\"></td>\n";
    echo "</tr>\n";
    next($list);
    if ($style == "light") $style = "dark";
    else $style = "light";
  }
?>

</table>
</form>

<?PHP
/***************************************************************************
 * $Log: tasks.php,v $
 * Revision 1.6  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 * Revision 1.5  2004/02/28 23:03:32  znouza
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
 * Revision 1.4  2003/11/18 02:03:46  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/11/17 20:41:13  willuhn
 * @N some more fixes at the new project status plugin
 *
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:30  willuhn
 * reimport
 *
 * Revision 1.41  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.40  2002/06/26 14:14:29  willuhn
 * @N added form "query task by id"
 *
 * Revision 1.39  2002/05/05 20:12:42  willuhn
 * @N added feature "fixed price" for tasks
 *
 * Revision 1.38  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.37  2002/05/02 21:42:40  willuhn
 * @N pretty cool new feature in "task hotlist" -> order by planned time left
 *
 * Revision 1.36  2002/05/02 19:51:32  willuhn
 * @N task->isAvailable() checks if task is "in progress" or "request"
 * @N link to parent task in taskdetails
 * @N link to all child tasks in taskdetails
 * @B in task hotlist
 *
 * Revision 1.35  2002/03/31 16:57:19  willuhn
 * @B task id wasn't set when changing the project
 * @N added getSize() in attachment class
 *
 * Revision 1.34  2002/03/30 19:55:54  willuhn
 * @N deleting of attachments (in taskdetails)
 *
 * Revision 1.33  2002/03/30 19:24:12  willuhn
 * @N added attachment code
 *
 * Revision 1.32  2002/02/27 22:37:43  willuhn
 * @C some styling in css
 *
 * Revision 1.31  2002/02/27 19:41:24  willuhn
 * @N added attachment field
 *
 * Revision 1.30  2002/02/24 22:41:20  willuhn
 * updated content-type in reportviewer
 *
 * Revision 1.29  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 ***************************************************************************/
?>
