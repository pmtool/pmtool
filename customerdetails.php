<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/customerdetails.php,v $
 * $Revision: 1.3 $
 * $Date: 2003/11/18 00:55:15 $
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
  echo $lang['common_accessDenied']."\n";
  exit;
}

$customerInst = new customer(tool::secureGet('customerid'));

#######################################################################
## show customer details

$projectInst = new project();
$projectInst->filterCustomerId = $customerInst->id;

?>
<h1><?PHP echo $lang['common_customerDetails'];?></h1>
<div align="center">
<table border="0" cellpadding="2" cellspacing="1" width="96%">
  <tr>
    <td><h2><?PHP echo $customerInst->company;?></h2></td>
  </tr>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
  <?PHP if ($customerInst->customerNumber) { ?>
    <tr>
      <th><?PHP echo $lang['customer_number'];?></th>
      <th><?PHP echo $customerInst->customerNumber;?></a></th>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->title) { ?>
    <tr>
      <th><?PHP echo $lang['common_title'];?></th>
      <th><?PHP echo $customerInst->title;?></a></th>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->firstname) { ?>
    <tr>
      <th><?PHP echo $lang['common_firstname'];?></th>
      <th><?PHP echo $customerInst->firstname;?></a></th>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->lastname) { ?>
    <tr>
      <th><?PHP echo $lang['common_lastname'];?></th>
      <th><?PHP echo $customerInst->lastname;?></a></th>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->email) { ?>
    <tr>
      <td class=list><?PHP echo $lang['common_email'];?></td>
      <td class=list><a href="mailto:<?PHP echo $customerInst->email;?>"><?PHP echo $customerInst->email;?></a></td>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->street) { ?>
    <tr>
      <td class=list><?PHP echo $lang['common_street'];?></td>
      <td class=list><?PHP echo $customerInst->street;?></a></td>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->zip || $customerInst->city) { ?>
    <tr>
      <td class=list><?PHP echo $lang['common_zip'];?> / <?PHP echo $lang['common_city'];?></td>
      <td class=list><?PHP echo $customerInst->zip." ".$customerInst->city;?></a></td>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->phone) { ?>
    <tr>
      <td class=list><?PHP echo $lang['common_phone'];?></td>
      <td class=list><?PHP echo $customerInst->phone;?></a></td>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->fax) { ?>
    <tr>
      <td class=list><?PHP echo $lang['common_fax'];?></td>
      <td class=list><?PHP echo $customerInst->fax;?></a></td>
    </tr>
  <?PHP } ?>
  <?PHP if ($customerInst->cellphone) { ?>
    <tr>
      <td class=list><?PHP echo $lang['common_cellphone'];?></td>
      <td class=list><?PHP echo $customerInst->cellphone;?></a></td>
    </tr>
  <?PHP } ?>
</table>

<?PHP
# order
$order = "name";
if (tool::secureGet('order')) {$order = tool::secureGet('order');}
if (tool::secureGet('desc') == "DESC") {$desc = "";}
else {$desc = "DESC";}
$list = $projectInst->getList($order,$desc);

if ($projectInst->matches > 0) {
  #######################################################################
  ## show existing projects
  ?>
    <br>
    <table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
      <tr>
        <th colspan="6"><?PHP echo $projectInst->matches;?> <?PHP echo $lang['customer_projectsUntilNow'];?></th>
      </tr><tr>
        <?PHP
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=customerdetails.php&view=details&customerid=".$customerInst->id."&order=name&desc=".$desc)."\" title=\"".$lang['customer_orderByName']."\">".$lang['common_name']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=customerdetails.php&view=details&customerid=".$customerInst->id."&order=rate&desc=".$desc)."\" title=\"".$lang['customer_orderByRate']."\">".$lang['common_rate']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=customerdetails.php&view=details&customerid=".$customerInst->id."&order=manager&desc=".$desc)."\" title=\"".$lang['customer_orderByManager']."\">".$lang['common_manager']."</a></th>\n";
          echo "<th><a href=\"".$toolInst->encodeUrl("index.php?content=customerdetails.php&view=details&customerid=".$customerInst->id."&order=status_id&desc=".$desc)."\" title=\"".$lang['common_orderByStatus']."\">".$lang['common_status']."</a></th>\n";

          while ($element = current($list)) {
            $projectInst->activate($element);
            echo "<tr><td class=list><nobr><a href=\"".$toolInst->encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$projectInst->id)."\" title=\"".$lang['common_showDetailsForThisProject']."\">".substr($projectInst->name,0,40)."...</a></nobr></td>\n";
            if ($projectInst->rate) {
              echo "<td class=list>".$toolInst->formatCurrency($projectInst->rate)."</td>\n";
            }
            else {
              echo "<td class=list>&nbsp;</td>\n";
            }
            if ($projectInst->managerId) {
              $userInst = new user($projectInst->managerId);
              if ($userInst->email) {
                echo "<td class=list><a href=\"mailto:".$userInst->email."\">".$userInst->name."</a></td>\n";
              }
              else {
                echo "<td class=list>".$userInst->name."</td>\n";
              }
            }
            else {
              echo "<td class=list>&nbsp;</td>\n";
            }
            echo "<td class=list>".$projectInst->getStatusName()."</td></tr>\n";
            next($list);
          }
  echo "</table>\n";
}
?>
</div>
&nbsp;

<?PHP
/***************************************************************************
 * $Log: customerdetails.php,v $
 * Revision 1.3  2003/11/18 00:55:15  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:21  willuhn
 * reimport
 *
 * Revision 1.6  2002/05/02 22:20:18  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.5  2002/04/01 23:17:21  willuhn
 * @N added some language stuff
 *
 * Revision 1.4  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
