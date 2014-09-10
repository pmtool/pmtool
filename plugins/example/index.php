<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/plugins/example/index.php,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:23:13 $
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
// first, you need to check the login again, to deny direct referrers
if (!$loginInst->id) {
  echo "access denied\n";
  exit;
}
?>
<h1>plugins: <?PHP echo $pluginconfig['title'];?></h1>
<form method="post">

<?PHP 
// the following objects automatically exists after loading.
// $loginInst (instance of inc/login.in.php) contains the actual user
// $toolInst (instance of inc/tool.inc.php)
?>

<h2>examples</h2>
<table border="0" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="2">user</th>
  </tr>
  <tr>
    <th>Username</th>
    <td><?PHP echo $loginInst->username;?></td>
  </tr>
  <tr>
    <th>user ID</th>
    <td><?PHP echo $loginInst->id;?></td>
  </tr>
  <tr>
    <th>user is a customer</th>
    <td><?PHP if ($loginInst->isCustomer()) echo "yes"; else echo "no";?></td>
  </tr>
  <tr>
    <th>member of the following groups</th>
    <td>
      <?PHP
        $groups = $loginInst->getGroups();
        while ($group = current($groups)) {
          echo $group."<br>";
          next($groups);
        }
      ?>
    </td>
  </tr>
  <tr>
    <th colspan="2">tools</th>
  </tr>
  <tr>
    <th>currency format</th>
    <td><?PHP echo $toolInst->formatCurrency("0")?></td>
  </tr>
  <tr>
    <th>encoded url</th>
    <td><?PHP echo $toolInst->encodeUrl($PHP_SELF)?></td>
  </tr>
  <tr>
    <th>convert seconds (360 seconds)</th>
    <td><?PHP echo $toolInst->formatTime("360")?></td>
  </tr>
  <tr>
    <th>deductible time (360 seconds)</th>
    <td><?PHP echo $toolInst->formatTime($toolInst->deductibleSeconds("360"))?></td>
  </tr>
  <tr>
    <th colspan="2">customers</th>
  </tr>
  <tr>
    <th valign="top">companies</th>
    <td>
      <?PHP
        $customerInst = new customer();
        $list = $customerInst->getList();
        while ($element = current($list)) {
          $customer = new customer($element);
          echo $customer->company."<br>";
          next($list);
        }
      ?>
    </td>
  </tr>
</table>
</form>
<br>
<h2>Source of this example</h2>
<?PHP highlight_file(__FILE__);?>
<?PHP
/***************************************************************************
 * $Log: index.php,v $
 * Revision 1.1.1.1  2003/07/28 19:23:13  willuhn
 * reimport
 *
 * Revision 1.4  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.3  2002/04/15 22:14:35  willuhn
 * @N added plugin "task hotlist"
 *
 * Revision 1.2  2002/03/30 17:15:52  willuhn
 * @N added plugin "presence list"
 *
 * Revision 1.1  2002/03/30 14:14:39  willuhn
 * @N added plugin loader
 *
 ***************************************************************************/
?>
