<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/groups.php,v $
 * $Revision: 1.4 $
 * $Date: 2003/11/18 01:00:36 $
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
?>

<h1><?PHP echo $lang['common_groups'];?></h1>

<?PHP
$recordType = "new";
$accessInst = new access();

#######################################################################
## perform action

if (tool::securePost('id')) {
  $accessInst->activate(tool::securePost('id'));
  if (!DEMO_MODE) {
    if (tool::securePost('remove')) $accessInst->removeUser(tool::securePost('remove'));
    if (tool::securePost('add')) $accessInst->addUser(tool::securePost('add'));
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}
if (tool::securePost('action') == "update") {
  $recordType = "edit";
  $accessInst->fill(tool::securePostAll());
  if (!DEMO_MODE) {
    if ($accessInst->update()) {
      $accessInst->clear();
      $recordType = "new";
    }
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}
if (tool::securePost('action') == "new") {
  $accessInst->fill(tool::securePostAll());
  if ($accessInst->insert()) {
    $accessInst->clear();
    $recordType = "new";
  }
  else {
    $recordType = "edit";
  }
}
if (tool::securePost('action') == "delete") {
  $accessInst->id = tool::securePost('id');
  if (!DEMO_MODE) {
    $accessInst->delete();
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}
if (tool::securePost('action') == "edit") {
  $recordType = "edit";
}

#######################################################################
## make edit / new form

?>

<h2><?PHP echo $recordType;?> <?PHP echo $lang['common_group'];?>
  <?PHP
    if ($recordType == "edit") {
      ?>(<a href="javascript:document.form1.submit();"><?PHP echo $lang['common_newGroup'];?></a>)<?PHP
    }
  ?>
</h2>
<form method="post" name="form2">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?PHP echo tool::securePost('id');?>">
<table border="0" cellpadding="2" cellspacing="0">
  <tr>
    <td><?PHP echo $lang['group_name'];?>:&nbsp;</td>
    <td colspan="2"><input type="text" name="name" value="<?PHP echo $accessInst->name;?>" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
  </tr>
  <tr><td>&nbsp;</td>
  <?PHP if ($recordType == "edit" || $recordType == "update") {?>
    <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_save'];?>" onclick="document.form2.action.value='update'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP } else {?>
    <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_insert'];?>" onclick="document.form2.action.value='new'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP }?>
  </tr>
  <tr>
    <td><b><?PHP echo $lang['common_users'];?></b></td>
    <td>&nbsp;</td>
    <td><b><?PHP echo $lang['common_members'];?></b></td>
  </tr>
  <tr>
    <td>
      <select name="add" size="10">
      <?PHP
        $list = $accessInst->getNotUsers();
        while ($element = current($list)) {
          $userInst = new user($element);
          echo "<option value=\"".$userInst->id."\">".$userInst->name."\n";
          next($list);
        }
      ?>
      </select>
    </td>
    <td align="center">
      <input type="submit" value="&lt; <?PHP echo $lang['common_switch'];?> &gt;">
    </td>
    <td>
      <select name="remove" size="10">
      <?PHP
        $list = $accessInst->getUsers();
        while ($element = current($list)) {
          $userInst = new user($element);
          echo "<option value=\"".$userInst->id."\">".$userInst->name."\n";
          next($list);
        }
      ?>
      </select>
    </td>
  </tr>
</table>
</form>

<?PHP

#######################################################################
## list existing records
?>

<br><br>
<h2><?PHP echo $lang['group_available'];?></h2>
<form method="post" name="form1">
<input type=hidden name="id">
<input type=hidden name="action">
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <th width="40%"><?PHP echo $lang['group_name'];?></th>
    <th width="50%"><?PHP echo $lang['common_members'];?></th>
    <th colspan=2><?PHP echo $lang['common_action'];?></th>
  </tr>
  <?PHP

  $list = $accessInst->getList();
  $style = "light";
  while ($element = current($list)) {
    $accessInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    echo "<td>".$accessInst->name."</td>\n";
    echo "<td>";
    $userList = $accessInst->getUsers();
    while ($userElement = current($userList)) {
      $userInst = new user($userElement);
      echo $userInst->name."<br>";
      next($userList);
    }
    echo "</td>\n";
    echo "<td align=center><input type=submit value=\"".$lang['common_delete']."\" onclick=\"document.form1.id.value='".$element."';document.form1.action.value='delete';return Check()\"></td>\n";
    echo "<td align=center><input type=submit value=\"".$lang['common_edit']."\" onclick=\"document.form1.id.value='".$element."';document.form1.action.value='edit'\"></td>\n";
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
 * $Log: groups.php,v $
 * Revision 1.4  2003/11/18 01:00:36  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/09/27 19:15:50  willuhn
 * @B fix on logon page (empty passwords enabled again)
 * @N added DEMO_MODE
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:29  willuhn
 * reimport
 *
 * Revision 1.5  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.4  2002/03/31 17:17:52  willuhn
 * @N added some more colors ;)
 *
 * Revision 1.3  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
