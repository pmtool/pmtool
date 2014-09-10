<?php
/****************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/plugins/projects\040status/index.php,v $
 * $Revision: 1.2 $
 * $Date: 2003/11/17 20:41:14 $
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
<?php

// first, you need to check the login again, to deny direct referrers
if (!$loginInst->id || !$loginInst->hasAccess("task.getCustomerCosts")) {
  echo "access denied\n";
  exit;
} ?>

<h1>plugins: <?php echo $pluginconfig['title'];?></h1>
<h2>Project status</h2>
<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">
  <tr>
    <th><?php echo $lang['common_projects'] ?></th>
	  <th><?php echo $lang['project_customerCosts'] ?>&sup1;</th>
	  <th><?php echo $lang['common_status'] ?></th>
  	<th><?php echo $lang['project_paid'] ?></th>
	  <th><?php echo $lang['project_consignment_date'] ?></th>
	  <th><?php echo $lang['project_payment_date'] ?> </th>
  </tr>
<?php

$projectInst = new project();
$order = "paid desc,projectstatus_id";

if (tool::secureGet('order')) {
  $order = tool::secureGet('order');
}
if (tool::secureGet('desc') == "DESC") {
  $desc = "";
}
else {
  $desc = "DESC";
}

$list = $projectInst->getList($order,$desc);
$style = "light";

//projects stats vars declarations
$counter          = 0;
$tentative        = 0; 	//id=1
$inprogress       = 0; 	//id=2
$done             = 0;  //id=3
$paid             = 0;
$notpaid          = 0;
$donenotpaid      = 0;
$tentativenotpaid = 0;
$totalsum         = 0;

while ($element = current($list)) {
	$counter++;
  $projectInst->activate($element);

  $curr = $projectInst->getCustomerCosts();

  switch ($projectInst->statusId) {

    case PROJECT_STATUS_TENTATIVE:
  		$tentative += $curr;
  		if (!$projectInst->paid)
        $tentativenotpaid += $curr;
  		break;

    case PROJECT_STATUS_INPROGRESS:
  		$inprogress += $curr;
  		break;

    case PROJECT_STATUS_DONE:
  		$done += $curr;
  		if (!$projectInst->paid)
        $donenotpaid += $curr;
  		break;
	}

  switch ($projectInst->paid) {

    case true:
  		$paid += $curr;
  		break;

    case false:
  		$notpaid += $curr;
  		break;
	}

  $totalsum += $curr;

  ?>

  <tr class="<?php echo $style;?>" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>
      <?php echo "<a href=\"javascript:openwindow('".tool::encodeUrl("index.php?content=projectdetails.php&view=details&projectid=".$element)."',width='500',height='500')\" title=\"show details for this project\">".$projectInst->name."</a>"?>
    </td>
  	<td align="right">
      <?php echo tool::formatCurrency($curr);?>&nbsp;
    </td>
  	<td align="center">
      <?php echo $projectInst->getStatusName()?>
    </td>
  	<td align="center">
      <?php echo ($projectInst->paid ? $lang['common_yes'] : $lang['common_no']);?>
    </td>
  	<td align="right">
      <?php if ($projectInst->consignment_date=='0000-00-00') { echo 'n.d.';} else { echo $projectInst->consignment_date; }?>&nbsp;
    </td>
  	<td align="right">
      <?php if ($projectInst->payment_date=='0000-00-00') { echo 'n.d.';} else { echo $projectInst->payment_date; }?>&nbsp;
    </td>
  </tr>

  <?php
    next($list);
    if ($style == "light") $style = "dark";
    else $style = "light";
  } ?>
  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td colspan="5" align="right">total projects</td>
    <td align="right"><?php echo $counter;?>
  </tr>
  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td colspan="5" align="right">total sum</td>
    <td align="right"><?php echo tool::formatCurrency($totalsum);?>
  </tr>
</table>
<div class="comment">
&sup1; customer costs do not contain the taskstypes &quot;BUG&quot; and &quot;TODO&quot;.
</div>

<br>
<h2>total</h2>

<table border="0" cellpadding="2" cellspacing="1" width="99%" bgcolor="#ffffff">

  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for tentative projects</td>
    <td align="right"><?php echo tool::formatCurrency($tentative);?></td>
  </tr>
  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for in progress projects</td>
    <td align="right"><?php echo tool::formatCurrency($inprogress);?></td>
  </tr>
  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for done projects</td>
    <td align="right"><?php echo tool::formatCurrency($done);?></td>
  </tr>

  <tr>
    <td colspan="2"><hr size="1"></td>
  </tr>

  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for paid projects</td>
    <td align="right" style="border:1px solid black;background-color:#33ff66;font-weight:bold;"><?php echo tool::formatCurrency($paid);?></td>
  </tr>
  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for not paid projects</td>
    <td align="right" style="border:1px solid black;background-color:yellow;font-weight:bold;"><?php echo tool::formatCurrency($notpaid);?></td>
  </tr>

  <tr>
    <td colspan="2"><hr size="1"></td>
  </tr>

  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for not paid done projects</td>
    <td align="right" style="border:1px solid black;background-color:red;font-weight:bold;"><?php echo tool::formatCurrency($donenotpaid);?></td>
  </tr>

  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td>Subtotal for not paid tentaive projects</td>
    <td align="right" style="border:1px solid black;font-weight:bold;"><?php echo tool::formatCurrency($tentativenotpaid);?></td>
  </tr>

  <tr>
    <td colspan="2"><hr size="1"></td>
  </tr>

  <tr class="light" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
    <td><b>Total sum for projects</b></td>
    <td align="right"><b><?php echo tool::formatCurrency($totalsum);?></b></td>
  </tr>
</table>


<?php
/***************************************************************************
 * $Log: index.php,v $
 * Revision 1.2  2003/11/17 20:41:14  willuhn
 * @N some more fixes at the new project status plugin
 *
 * Revision 1.1  2003/11/17 19:35:17  willuhn
 * @N added Max Nitris changes
 *
 * Revision 1.0  2003/11/14 02:12:00 pzmax
 * First release
 ***************************************************************************/
?>
