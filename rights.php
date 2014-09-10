<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/rights.php,v $
 * $Revision: 1.5 $
 * $Date: 2003/11/18 02:00:00 $
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

<h1><?PHP echo $lang['common_rights'];?></h1>

<?PHP
$accessInst = new access();

#######################################################################
## perform action


if (tool::securePost('id')) {
  $accessInst->activate(tool::securePost('id'));
  if (!DEMO_MODE) {
    if (is_array(tool::securePost('remove'))) $accessInst->removeRights(tool::securePost('remove'));
    if (is_array(tool::securePost('add'))) $accessInst->addRights(tool::securePost('add'));
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}

#######################################################################
## make edit / new form

if (tool::securePost('action') == "edit") { ?>
  <h2><?PHP echo $lang['common_editRecord'];?></h2>
  <form method="post">
  <input type="hidden" name="action" value="edit">
  <input type="hidden" name="id" value="<?PHP echo tool::securePost('id');?>">
  <table border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td><b><?PHP echo $lang['right_available'];?></b></td>
      <td>&nbsp;</td>
      <td><b><?PHP echo $lang['right_included'];?></b></td>
    </tr>
    <tr>
      <td>
        <select name="add[]" size="16" multiple>
        <?PHP
          $list = $accessInst->getNotRights();
          while ($element = current($list)) {
            echo "<option value=\"".$element."\">".$accessInst->getRightName($element)."\n";
            next($list);
          }
        ?>
        </select>
      </td>
      <td align="center">
        <input type="submit" value="&lt; <?PHP echo $lang['common_switch'];?> &gt;">
      </td>
      <td>
        <select name="remove[]" size="16" multiple>
        <?PHP
          $list = $accessInst->getRights();
          while ($element = current($list)) {
            echo "<option value=\"".$element."\">".$accessInst->getRightName($element)."\n";
            next($list);
          }
        ?>
        </select>
      </td>
    </tr>
  </table>
  </form>
<?PHP
}

#######################################################################
## list existing records
?>

<br><br>
<h2><?PHP echo $lang['right_existing'];?></h2>
<form method="post" name="form1">
<input type=hidden name="id">
<input type=hidden name="action">
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <th width="40%"><?PHP echo $lang['group_name'];?></th>
    <th width="50%"><?PHP echo $lang['right_included'];?></th>
    <th><?PHP echo $lang['common_action'];?></th>
  </tr>
  <?PHP

  $list = $accessInst->getList();
  $style = "light";
  while ($element = current($list)) {
    $accessInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    echo "<td>".$accessInst->name."</td>\n";
    echo "<td><table border=0 cellpadding=0 cellspacing=0>";
    $rightList = $accessInst->getRights();
    while ($right = current($rightList)) {
      echo "<tr>\n";
      echo "<td>".$accessInst->getRightName($right)."&nbsp;</td>";
      next($rightList);
      $right = current($rightList);
      echo "<td>".$accessInst->getRightName($right)."</td>";
      next($rightList);
      echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</td>\n";
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
 * $Log: rights.php,v $
 * Revision 1.5  2003/11/18 02:00:00  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/10/09 18:28:10  willuhn
 * @B bug fixed in encodeString and rights page (array were not handled correctly via POST)
 *
 * Revision 1.3  2003/09/27 19:15:50  willuhn
 * @B fix on logon page (empty passwords enabled again)
 * @N added DEMO_MODE
 *
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:21  willuhn
 * reimport
 *
 * Revision 1.6  2002/04/13 20:46:35  willuhn
 * @N added some more multilanguage stuff
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
