<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/customers.php,v $
 * $Revision: 1.3 $
 * $Date: 2003/11/18 00:59:04 $
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

if (!$loginInst->hasAccess("customer")) {
  echo $global_status;
  exit;
}

?>

<h1><?PHP echo $lang['common_customers'];?></h1>

<?PHP
$customerInst = new customer();

#######################################################################
## perform action

$status = 1;

if (tool::securePost('action') == "save" && tool::securePost('id')) {
  # fill customer with submitted data
  $customerInst->id = tool::securePost('id');
  $customerInst->fill(tool::securePostAll());
  $status = $customerInst->update();
}
elseif (tool::securePost('action') == "save") {
  $customerInst->fill(tool::securePostAll());
  $status = $customerInst->insert();
}

if (tool::securePost('action') == "delete") {
  $customerInst->id = tool::securePost('id');
  $customerInst->delete();
}

if (tool::securePost('action') == "edit") {
  $status = 0;
  $customerInst->activate(tool::securePost('id'));
}

#######################################################################
## make edit / new form

if (!$status) {
  echo "<h2>".$lang['common_editRecord']." (<a href=\"".$toolInst->encodeUrl("index.php?content=".$content)."\">".$lang['common_newRecord']."</a>)</h2>\n";
}
else {
  $customerInst->clear();
  echo "<h2>".$lang['common_newRecord']."</h2>\n";
}
?>

<form method="post" name="form2">
<input type="hidden" name="action" value="save">
<input type="hidden" name="id" value="<?PHP echo $customerInst->id;?>">
<table border="0" cellpadding="2" cellspacing="0">
  <tr><td><?PHP echo $lang['customer_number'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->customerNumber;?>" type="text" name="customer_number" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_title'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->title;?>" type="text" name="title" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_firstname'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->firstname;?>" type="text" name="firstname" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_lastname'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP  echo $customerInst->lastname;?>" type="text" name="lastname" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_company'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->company;?>" type="text" name="company" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_street'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->street;?>" type="text" name="street" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_zip'];?> / <?PHP echo $lang['common_city'];?>:&nbsp;</td><td><input value="<?PHP echo $customerInst->zip;?>" type="text" name="zip" size="7"></td><td align="right"><input value="<?PHP echo $customerInst->city;?>" type="text" name="city" size="<?PHP echo $htmlconfig['text_size2'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_phone'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->phone;?>" type="text" name="phone" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_fax'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->fax;?>" type="text" name="fax" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_cellphone'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->cellphone;?>" type="text" name="cellphone" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td><?PHP echo $lang['common_email'];?>:&nbsp;</td><td colspan="2"><input value="<?PHP echo $customerInst->email;?>" type="text" name="email" size="<?PHP echo $htmlconfig['text_size1'];?>"></td></tr>
  <tr><td colspan="2" class="small">&nbsp;</td></tr>
  <tr><td>&nbsp;</td>
  <?PHP if (tool::securePost('action') == "edit") {?>
    <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_save']?>" onclick="document.form2.action.value='save'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP } else {?>
    <td colspan="2"><input type="submit" value="<?PHP echo $lang['common_insert']?>" onclick="document.form2.action.value='save'"><input type="reset" value="<?PHP echo $lang['common_reset'];?>"></td>
  <?PHP }?>
  </tr>
</table>
</form>

<?PHP

#######################################################################
## list existing records

# order
$order = "company";
if (tool::secureGet('order')) {$order = tool::secureGet('order');}
if (tool::secureGet('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}

?>

<br><br>
<h2><?PHP echo $lang['customer_available'];?></h2>
<form method="post" name="form1">
<input type=hidden name="id">
<input type=hidden name="action">
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <?PHP
    echo "<th width=\"20%\"><a href=\"".$toolInst->encodeUrl("index.php?content=customers.php&order=customer_number&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['customer_number']."\">".$lang['customer_number']."</a></th>\n";
    echo "<th width=\"30%\"><a href=\"".$toolInst->encodeUrl("index.php?content=customers.php&order=firstname&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['common_name']."\">".$lang['common_name']."</a></th>\n";
    echo "<th width=\"30%\"><a href=\"".$toolInst->encodeUrl("index.php?content=customers.php&&order=company&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['common_company']."\">".$lang['common_company']."</a></th>\n";
    echo "<th width=\"20%\"><a href=\"".$toolInst->encodeUrl("index.php?content=customers.php&&order=email&desc=".$desc)."\" title=\"".$lang['common_orderBy']." ".$lang['common_email']."\">".$lang['common_email']."</a></th>\n";
    ?>
    <th colspan=2><?PHP echo $lang['common_action'];?></th>
  </tr>
  <?PHP

  $list = $customerInst->getList($order,$desc);
  $style = "light";
  while ($element = current($list)) {
    $customerInst->activate($element);
    ?><tr class="<?PHP echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''"><?PHP
    echo "<td>".$customerInst->customerNumber."</td>\n";
    echo "<td>".$customerInst->firstname." ".$customerInst->lastname."</td>\n";
    echo "<td><a href=\"javascript:openwindow('".$toolInst->encodeUrl("index.php?content=customerdetails.php&view=details&customerid=".$element)."',width='500',height='500')\" title=\"".$lang['customer_showDetails']."\">".$customerInst->company."</a></td>\n";
    echo "<td><a href=\"mailto:".$customerInst->email."\">".$customerInst->email."</a></td>\n";
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
 * $Log: customers.php,v $
 * Revision 1.3  2003/11/18 00:59:04  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:33  willuhn
 * reimport
 *
 * Revision 1.18  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.17  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.16  2002/03/31 17:17:52  willuhn
 * @N added some more colors ;)
 *
 * Revision 1.15  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
