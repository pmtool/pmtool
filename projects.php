<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/projects.php,v $
 * $Revision: 1.6 $
 * $Date: 2004/03/17 20:19:50 $
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
$customerInst = new customer();
if (!$customerInst->getList()) {
  echo $lang['project_noCustomer1'].
       $toolInst->encodeUrl("index.php?content=customers.php").
       $lang['project_noCustomer2']."\n";
  exit;
}
?>

<h1><?PHP echo $lang['common_projects'];?></h1>

<?PHP
$projectInst = new project();

if (! $loginInst->isCustomer()) {
  #######################################################################
  ## perform action

  $status = 1;

  if (tool::securePost('action') == "save" && tool::securePost('id')) {
    # fill project with submitted data
    $projectInst->id = tool::securePost('id');
    $projectInst->fill(tool::securePostAll());
    $status = $projectInst->update();
  }
  elseif (tool::securePost('action') == "save") {
    $projectInst->fill(tool::securePostAll());
    $status = $projectInst->insert();
  }

  if (tool::securePost('action') == "delete") {
    $projectInst->id = tool::securePost('id');
    $projectInst->delete();
  }

  if (tool::securePost('action') == "edit") {
    $status = 0;
    $projectInst->activate(tool::securePost('id'));
  }

  #######################################################################
  ## make edit / new form

  if (!$status) {
    echo "<h2>".$lang['common_editRecord']." (<a href=\"".$toolInst->encodeUrl("index.php?content=".$content)."\">".$lang['common_newRecord']."</a>)</h2>\n";
  }
  else {
    $projectInst->clear();
    ?><h2><?PHP echo $lang['common_newRecord'];?></h2><?PHP
  }
  ?>

  <form method="post">
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?PHP echo $projectInst->id;?>">
  <table border="0" cellpadding="2" cellspacing="0">
    <tr><td><?PHP echo $lang['common_project'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $projectInst->name;?>" type="text" name="name" size="<?PHP  echo $htmlconfig['text_size1'];?>"></td></tr>
    <tr><td valign="top"><?PHP echo $lang['common_description'];?>:&nbsp;</td><td><textarea name="description" wrap="physical" rows="<?PHP echo $htmlconfig['textarea_rows'];?>" cols="<?PHP echo $htmlconfig['textarea_cols'];?>"><?PHP echo $projectInst->description;?></textarea></td></tr>
    <tr><td><?PHP echo $lang['common_rate'];?> (<?PHP echo $config['currency']?>/<?PHP echo $lang['common_hour'];?>):&nbsp;</td><td><input value="<?PHP echo $projectInst->rate;?>" maxlength="10" type="text" name="rate" size="10"></td></tr>
    <tr><td><?PHP echo $lang['common_budget'];?> (<?PHP echo $config['currency']?>):&nbsp;</td><td><input value="<?PHP echo $projectInst->budget;?>" maxlength="10" type="text" name="budget" size="10"></td></tr>
    <tr>
      <td><?PHP echo $lang['common_customer'];?>:&nbsp;</td>
      <td><select name="customerid">
        <?PHP
        $customerInst = new customer();
        $list = $customerInst->getList();
        while ($element = current($list)) {
          $customerInst->activate($element);
          $selected = "";
          if ($customerInst->id == $projectInst->customerId) $selected = "selected";
          echo "<option ".$selected." value=\"".$customerInst->id."\">".$customerInst->company."\n";
          next($list);
        }
        ?>
      </select></td>
    </tr>
    <tr>
      <td><?PHP echo $lang['common_manager'];?>:&nbsp;</td>
      <td><select name="managerid">
        <?PHP
        $managerInst = new user();
        $list = $managerInst->getList();
        while ($element = current($list)) {
          $managerInst->activate($element);
          $selected = "";
          if ($managerInst->id == $projectInst->managerId) $selected = "selected";
          echo "<option ".$selected." value=\"".$managerInst->id."\">".$managerInst->name."\n";
          next($list);
        }
        ?>
      </select></td>
    </tr>
    <tr>
      <td><?PHP echo $lang['common_status'];?>:&nbsp;</td>
      <td><select name="statusid">
        <?PHP
        $list = $projectInst->getStatusList();
        while ($element = current($list)) {
          $selected = "";
          if ($element == $projectInst->statusId) $selected = "selected";
          echo "<option ".$selected." value=\"".$element."\">".$projectInst->getStatusName($element)."\n";
          next($list);
        }
        ?>
      </select></td>
    </tr>
    <tr>
      <td><?PHP echo $lang['common_priority'];?>:&nbsp;</td>
      <td><select name="priorityid">
        <?PHP
        $list = $projectInst->getPriorityList();
        while ($element = current($list)) {
          $selected = "";
          if ($element == $projectInst->priorityId) $selected = "selected";
          echo "<option ".$selected." value=\"".$element."\">".$projectInst->getPriorityName($element)."\n";
          next($list);
        }
        ?>
      </select></td>
    </tr>
    <tr>
      <td><?php echo $lang['project_paid'];?>:&nbsp;</td>
      <td><input type="checkbox" name="paid" value="1"<?PHP if ($projectInst->paid==1) echo " CHECKED";?>></td>
    </tr>
    <tr>
      <td><?php echo $lang['project_consignment_date'];?>:&nbsp;</td>
      <td><input value="<?PHP echo $projectInst->consignment_date;?>" maxlength="10" type="text" name="consignment_date" size="20"> (format: yyyy-mm-dd)</td>
    </tr>
    <tr>
      <td><?php echo $lang['project_payment_date'];?>:&nbsp;</td>
      <td><input value="<?PHP echo $projectInst->payment_date;?>" maxlength="10" type="text" name="payment_date" size="20"> (format: yyyy-mm-dd)</td>
    </tr>

    <tr><td>&nbsp;</td>
    <?PHP if (tool::securePost('action') == "edit") {?>
      <td colspan="2"><input type="submit" value="save"><input type="reset" value="reset"></td>
    <?PHP } else {?>
      <td colspan="2"><input type="submit" value="insert"><input type="reset" value="reset"></td>
    <?PHP } ?>
    </tr>
  </table>
  </form>

  <?PHP
}

#######################################################################
## list existing records

# order
$order = "name";
if (tool::secureGet('order')) {$order = tool::secureGet('order');}
if (tool::secureGet('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}

?>

<br><br>
<h2><?PHP echo $lang['project_available'];?></h2>
<form method="post" name="form1"">
<input type=hidden name="id">
<input type=hidden name="action">
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <?PHP
    echo "<th width=\"20%\" align=left><a href=\"".$toolInst->encodeUrl("index.php?content=projects.php&order=name&desc=".$desc)."\" title=\"".$lang['common_orderByProject']."\">".$lang['common_project']."</a></th>\n";
    echo "<th width=\"30%\" align=left><a href=\"".$toolInst->encodeUrl("index.php?content=projects.php&order=description&desc=".$desc)."\" title=\"".$lang['project_orderByDescription']."\">".$lang['common_description']."</a></th>\n";
    if (!$loginInst->isCustomer()) echo "<th width=\"20%\" align=left><a href=\"".$toolInst->encodeUrl("index.php?content=projects.php&order=customer&desc=".$desc)."\" title=\"".$lang['project_orderByCustomer']."\">".$lang['common_customer']."</a></th>\n";
    echo "<th width=\"20%\" align=left><a href=\"".$toolInst->encodeUrl("index.php?content=projects.php&order=projectstatus_id&desc=".$desc)."\" title=\"".$lang['common_orderByStatus']."\">".$lang['common_status']."</a></th>\n";
    echo "<th width=\"20%\" align=left><a href=\"".$toolInst->encodeUrl("index.php?content=projects.php&order=priority_id&desc=".$desc)."\" title=\"".$lang['common_orderByPriority']."\">".$lang['common_priority']."</a></th>\n";
    if (! $loginInst->isCustomer()) {
      echo "<th>".$lang['project_paid']."</th>\n";
      echo "<th colspan=2>".$lang['common_action']."</th>\n";
    }
    echo "</tr>\n";

  $list = $projectInst->getList($order,$desc);
  $style = "light";
  while ($element = current($list)) {
    $projectInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    echo "<td><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$element)."',width='500',height='500')\" title=\"".$lang['common_showDetailsForThisProject']."\">".$projectInst->name."</a></td>\n";
    echo "<td>".$projectInst->description."</td>\n";
    $customerInst = new customer($projectInst->customerId);
    if (!$loginInst->isCustomer()) echo "<td><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=customerdetails.php&view=details&customerid=".$customerInst->id)."',width='500',height='500')\" title=\"".$lang['project_showDetailsForThisCustomer']."\">".$customerInst->company."</a></td>\n";
    echo "<td>".$projectInst->getStatusName()."</td>\n";
    echo "<td>".$projectInst->getPriorityName()."</td>\n";
    if (! $loginInst->isCustomer()) {
      echo "<td>".($projectInst->paid ? $lang['common_yes'] : $lang['common_no'])."</td>\n";
      echo "<td align=center><input type=submit value=\"".$lang['common_delete']."\" onclick=\"document.form1.id.value='".$element."';document.form1.action.value='delete';return Check()\"></td>\n";
      echo "<td align=center><input type=submit value=\"".$lang['common_edit']."\" onclick=\"document.form1.id.value='".$element."';document.form1.action.value='edit'\"></td>\n";
    }
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
 * $Log: projects.php,v $
 * Revision 1.6  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 * Revision 1.5  2003/11/18 01:51:27  willuhn
 * *** empty log message ***
 *
 * Revision 1.4  2003/11/17 20:41:13  willuhn
 * @N some more fixes at the new project status plugin
 *
 * Revision 1.3  2003/11/17 19:35:17  willuhn
 * @N added Max Nitris changes
 *
 * Revision 1.4  2003/09/27 18:23:45  willuhn
 * added paid, consignment_date, payment_date fields
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:26  willuhn
 * reimport
 *
 * Revision 1.29  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.28  2002/04/14 18:09:29  willuhn
 * @N splitted db class into a super class (valid for all databases)
 *    and an implementation class
 * @N some more multilanguage stuff
 *
 * Revision 1.27  2002/04/13 20:46:35  willuhn
 * @N added some more multilanguage stuff
 *
 * Revision 1.26  2002/03/31 17:17:52  willuhn
 * @N added some more colors ;)
 *
 * Revision 1.25  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
