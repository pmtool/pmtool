<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/requests.php,v $
 * $Revision: 1.5 $
 * $Date: 2004/02/28 23:03:32 $
 * $Author: znouza $
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
  echo $lang['requests_noProjectsAvailable']."\n";
  exit;
}

?>

<h1><?PHP echo $lang['common_requests'];?></h1>

<?PHP
$requestInst = new request();

#######################################################################
## perform action

$recordType = "new";
$action = tool::securePost('action');

if ($action == "update") {
  $requestInst->id = tool::securePost('id');
  $requestInst->fill(tool::securePostAll());
  if ($requestInst->update()) {
    $requestInst->clear();
    $recordType = "new";
  }
  else {
    $recordType = "edit";
  }
}

if ($action == "new") {
  $requestInst->fill(tool::securePostAll());
  if($requestInst->insert()) $requestInst->clear();
}

if ($action == "search") {
  if(tool::securePost('lastrecordtype')) $recordType = tool::securePost('lastrecordtype');
  $requestInst->fill(tool::securePostAll());
}

if ($action == "delete") {
  $requestInst->id = tool::securePost('id');
  $requestInst->delete();
  $requestInst->clear();
}

if ($action == "edit") {
  $recordType = "edit";
  $requestInst->activate(tool::securePost('id'));
}

#######################################################################
## make edit / new form

?>

<form method="post" name="form1" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="<?PHP echo $config['attach_maxfilesize'];?>">
<input type="hidden" name="id" value="<?PHP echo $requestInst->id;?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="order" value="<?PHP echo tool::securePost('order');?>">
<input type="hidden" name="desc" value="<?PHP echo tool::securePost('desc');?>">
<input type="hidden" name="lastrecordtype" value="<?PHP echo $recordType;?>">

<h2>
<?PHP
if ($recordType == "edit") {
  echo $lang['requests_editRequest'];
  ?>&nbsp;(<a href="javascript:document.form1.submit();">
     <?PHP echo $lang['requests_newRequest'];?></a>)<?PHP
}
else {
  echo $lang['requests_newRequest'];
}
?>
</h2>
<table border="0" cellpadding="2" cellspacing="0">
  <tr>
    <td><?PHP echo $lang['common_project'];?>:&nbsp;</td>
    <td><select name="projectid">
      <?PHP
      $projectInst = new project();
      $list = $projectInst->getList();
      while ($element = current($list)) {
        $projectInst->activate($element);

        $selected = "";
        if ($projectInst->id == $requestInst->projectId) $selected = "selected";
        echo "<option ".$selected." value=\"".$projectInst->id."\">".$projectInst->name."\n";
        next($list);
      }
      ?>
    </select></td>
  </tr><tr>
    <td><?PHP echo $lang['common_subject'];?>:&nbsp;</td>
    <td><input type="text" name="subject" value="<?PHP echo $requestInst->subject;?>" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
  </tr><tr>
    <td valign="top"><?PHP echo $lang['common_body'];?>:&nbsp;</td>
    <td><textarea name="body" rows="<?PHP echo $htmlconfig['textarea_rows'];?>" cols="<?PHP echo $htmlconfig['textarea_cols'];?>"><?PHP echo $requestInst->body;?></textarea></td>
  </tr><tr>
    <td><?PHP echo $lang['common_attachment'];?>:&nbsp;</td>
    <td><input type="file" name="userfile" value="<?PHP echo $taskInst->attachment;?>" size="<?PHP echo $htmlconfig['text_size3'];?>"></td>
  </tr><tr>
    <td><?PHP echo $lang['common_priority'];?>:&nbsp;</td>
    <td><select name="priorityid">
      <?PHP
      $list = $requestInst->getPriorityList();
      while ($element = current($list)) {
        $selected = "";
        if ($element == $requestInst->priorityId) $selected = "selected";
        echo "<option ".$selected." value=\"".$element."\">".$requestInst->getPriorityName($element)."\n";
        next($list);
      }
      ?>
    </select></td>
  </tr><tr>
    <td><?PHP echo $lang['common_type'];?>:&nbsp;</td>
    <td><select name="typeid">
      <?PHP
      $list = $requestInst->getTypeList();
      while ($element = current($list)) {
        $selected = "";
        if ($element == $requestInst->typeId) $selected = "selected";
        echo "<option ".$selected." value=\"".$element."\">".$requestInst->getTypeName($element)."\n";
        next($list);
      }
      ?>
    </select></td>
  </tr>
  <tr><td>&nbsp;</td>
  <?PHP if ($recordType == "edit") {?>
    <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_save'];?>" onclick="document.form1.action.value='update'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP } else {?>
    <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_insert'];?>" onclick="document.form1.action.value='new'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP }?>
  </tr>
</table>

<?PHP

#######################################################################
## list existing records

# order
$order = "project";
if (tool::securePost('order')) {$order = tool::securePost('order');}
if (tool::securePost('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}

$list = $requestInst->getList($order,$desc);

?>

<br><br>
<h2><?PHP echo $lang['requests_available'];?> (<?PHP echo $requestInst->matches;?> <?PHP echo $lang['common_matches'];?>)</h2>
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <th align=left><a href="javascript:document.form1.order.value='project';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByProject'];?>"><?PHP echo $lang['common_project'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='subject';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderBySubject'];?>"><?PHP echo $lang['common_subject'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='body';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByBody'];?>"><?PHP echo $lang['common_body'];?></a></th>
    <th align=left><?PHP echo $lang['common_attachment'];?></th>
    <th align=left><a href="javascript:document.form1.order.value='priority';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByPriority'];?>"><?PHP echo $lang['common_priority'];?></a></th>
    <th align=left><a href="javascript:document.form1.order.value='type';document.form1.desc.value='<?PHP echo $desc;?>';document.form1.submit();" title="<?PHP echo $lang['common_orderByType'];?>"><?PHP echo $lang['common_type'];?></a></th>
    <th colspan=2><?PHP echo $lang['common_action'];?></th>
  </tr>
  <?PHP
  $style = "light";
  while ($element = current($list)) {
    $requestInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    $projectInst = new project($requestInst->projectId);
    echo "<td>".$projectInst->name."</td>\n";
    echo "<td><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=requestdetails.php&view=details&requestid=".$element)."',width='500',height='500')\" title=\"".$lang['common_showDetailsForThisRequest']."\">".$requestInst->subject."</a></td>\n";
    echo "<td>";
    if ($requestInst->body) echo substr($requestInst->body,0,100);
    echo "&nbsp;</td>\n";
    echo "<td>\n";
    while ($r = current($requestInst->attachments)) {
      $attachment = new attachment($r);
      echo "<a href=\"".tool::encodeUrl("fileget.php?created=".$attachment->created."&filename=".$attachment->name)."\" title=\"".$lang['common_open']." ".$attachment->name." ".$lang['common_inANewWindow']."\">".$attachment->name."</a><br>\n";
      next($requestInst->attachments);
    }
    echo "</td>\n";
    echo "<td><nobr class=".$requestInst->getPriorityStyle().">".$requestInst->getPriorityName()."</td>\n";
    echo "<td><nobr class=".$requestInst->getTypeStyle().">".$requestInst->getTypeName()."</td>\n";
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
 * $Log: requests.php,v $
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
 * Revision 1.4  2003/11/18 01:58:07  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.1.1.1  2003/07/28 19:22:26  willuhn
 * reimport
 *
 * Revision 1.7  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.6  2002/03/31 17:40:10  willuhn
 * @N added right "attachment.update"
 * @N attachments are now also available for requests
 *
 * Revision 1.5  2002/03/31 17:17:52  willuhn
 * @N added some more colors ;)
 *
 * Revision 1.4  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
