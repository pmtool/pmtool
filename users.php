<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/users.php,v $
 * $Revision: 1.4 $
 * $Date: 2003/11/18 02:04:21 $
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
if (!$loginInst->hasAccess("user")) {
  echo $lang['common_accessDenied']."\n";
  exit;
}

?>

<h1><?PHP echo $lang['common_users'];?></h1>

<?PHP
$userInst = new user();

#######################################################################
## perform action

$status = 1;

if (tool::securePost('action') && tool::securePost('action') == "save" && tool::securePost('id') && tool::securePost('id') != "") {
  # fill user with submitted data
  $userInst->id = tool::securePost('id');
  $userInst->fill(tool::securePostAll());
  if (!DEMO_MODE) {
    $status = $userInst->update();
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}
elseif (tool::securePost('action') && tool::securePost('action') == "save") {
  $userInst->fill(tool::securePostAll());
  $status = $userInst->insert();
}

if (tool::securePost('action') && tool::securePost('action') == "delete" && tool::securePost('id') && tool::securePost('id') != "") {
  $userInst->id = tool::securePost('id');
  if (!DEMO_MODE) {
    $userInst->delete();
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}

if (tool::securePost('action') && tool::securePost('action') == "edit" && tool::securePost('id') && tool::securePost('id') != "") {
  $status = 0;
  $userInst->activate(tool::securePost('id'));
}

#######################################################################
## make edit / new form

if (!$status) {
  echo "<h2>".$lang['common_editRecord']." (<a href=\"".$toolInst->encodeUrl("index.php?content=".$content)."\">".$lang['common_newRecord']."</a>)</h2>\n";
}
else {
  $userInst->clear();
  echo "<h2>".$lang['common_newRecord']."</h2>\n";
}
?>

<form method="post" name="form2">
<input type="hidden" name="action" value="save">
<input type="hidden" name="id" value="<?PHP echo $userInst->id;?>">
<table border="0" cellpadding="2" cellspacing="0">
  <tr><td><?PHP echo $lang['common_username'];?>:&nbsp;</td><td><input value="<?PHP echo $userInst->username;?>" type="text" name="username" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_name'];?>:&nbsp;</td><td><input value="<?PHP echo $userInst->name;?>" type="text" name="name" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_email'];?>:&nbsp;</td><td><input value="<?PHP echo $userInst->email;?>" type="text" name="email" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_IP'];?>:&nbsp;</td><td><input value="<?PHP echo $userInst->ip;?>" type="text" name="ip" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr>
    <td><?PHP echo $lang['common_language'];?>:&nbsp;</td>
    <td>
      <select name="language"><option value=""><?PHP echo $lang['common_systemDefault'];?>
        <?PHP
        $dir = $toolInst->getDir("./lang");
        while ($element = current($dir)) {
          $selected = "";
          if (eregi("\.inc\.php",$element)) {
            $element = eregi_replace("\.inc\.php","",$element);
            if ($userInst->language == $element) $selected = "selected";
            echo "<option ".$selected." value=\"".$element."\">".$element."\n";
          }
          next($dir);
        }
        ?>
      </select>
    </td>
  </tr>
  <tr><td><?PHP echo $lang['common_customer'];?>&nbsp;</td><td><select name="customerid"><option value=""><?PHP echo $lang['common_userIsNotACustomer'];?>
  <?PHP 
    $customerInst = new customer();
    $list = $customerInst->getList();
    while ($element = current($list)) {
      $customerInst->activate($element);
      $selected = "";
      if ($customerInst->id == $userInst->customerId) $selected = "selected";
      echo "<option ".$selected." value=\"".$customerInst->id."\">".$customerInst->company."\n";
      next($list);
    }
  ?>
  </select></td></tr>
  <tr><td valign="top"><?PHP echo $lang['common_changePasswordTo'];?> &sup1;:&nbsp;</td><td><input type="password" name="password" size="<?PHP echo $htmlconfig['text_size1'];?>"><div class="small">&sup1;<?PHP echo $lang['common_LeaveBlankNoPassword'];?></div></td></tr>
  <tr><td><?PHP echo $lang['common_rate'];?> (<?PHP echo $config['currency'];?>/<?PHP echo $lang['common_hour'] ;?>):&nbsp;</td><td><input value="<?PHP echo $userInst->rate;?>" maxlength="10" type="text" name="rate" size="10"></td></tr>
  <tr><td colspan="2" class="small">&nbsp;</td></tr>
  <tr><td>&nbsp;</td>
  <?PHP  if (tool::securePost('action') && tool::securePost('action') == "edit") {?>
    <td><input type="submit" value="<?PHP echo $lang['common_save'];?>" onclick="document.form2.action.value='save'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP } else {?>
    <td><input type="submit" value="<?PHP echo $lang['common_insert'];?>" onclick="document.form2.action.value='save'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP }?>
  </tr>
</table>
</form>

<?PHP
#######################################################################
## list existing records

# order
$order = "username";
if (tool::secureGet('order')) {$order = tool::secureGet('order');}
if (tool::secureGet('desc') && tool::secureGet('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}

?>

<br><br>
<h2><?PHP echo $lang['user_available'];?></h2>
<form method="post" name="form1">
<input type=hidden name="id">
<input type=hidden name="action">
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <?PHP 
    echo "<th width=\"20%\"><a href=\"".$toolInst->encodeUrl("index.php?content=users.php&order=username&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['common_username']."\">".$lang['common_username']."</a></th>\n";
    echo "<th width=\"30%\"><a href=\"".$toolInst->encodeUrl("index.php?content=users.php&order=name&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['common_name']."\">".$lang['common_name']."</a></th>\n";
    echo "<th width=\"30%\"><a href=\"".$toolInst->encodeUrl("index.php?content=users.php&order=email&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['common_email']."\">".$lang['common_email']."</a></th>\n";
    echo "<th width=\"20%\"><a href=\"".$toolInst->encodeUrl("index.php?content=users.php&order=ip&desc=".$desc)."\" title=\"".$lang['common_orderBy']." IP\">IP</a></th>\n";
    ?>
    <th colspan=2><?PHP echo $lang['common_action'];?></th>
  </tr>
  <?PHP

  $list = $userInst->getList($order,$desc);
  $style = "light";
  while ($element = current($list)) {
    $userInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    echo "<td>".$userInst->username."</td>\n";
    echo "<td>".$userInst->name."</td>\n";
    echo "<td><a href=\"mailto:".$userInst->email."\">".$userInst->email."</a></td>\n";
    echo "<td>".$userInst->ip."</td>\n";
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
 * $Log: users.php,v $
 * Revision 1.4  2003/11/18 02:04:21  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/09/27 19:15:50  willuhn
 * @B fix on logon page (empty passwords enabled again)
 * @N added DEMO_MODE
 *
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:29  willuhn
 * reimport
 *
 * Revision 1.22  2002/05/23 21:49:16  willuhn
 * @N added some more language stuff
 *
 * Revision 1.21  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.20  2002/04/03 23:19:01  willuhn
 * @N fixed a bug in language changing on user page
 *
 * Revision 1.19  2002/03/31 17:17:52  willuhn
 * @N added some more colors ;)
 *
 * Revision 1.18  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
