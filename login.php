<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/login.php,v $
 * $Revision: 1.4 $
 * $Date: 2003/11/18 01:46:54 $
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
if (!isset($loginInst->id) || $loginInst->id == "") {
  session_destroy();
}
?>
<div>
<h1><?PHP echo $lang['common_login'];?></h1>
<form method="post" name="form1" action="<?PHP echo $toolInst->encodeUrl("index.php");?>">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td><img src="grafx/dummy.gif" width="1" height="300" border="0"></td>
    <td>
      <table border="0" cellpadding="1" cellspacing="2" width="100%">
        <tr>
          <td align="right"><?PHP echo $lang['common_username'];?>:&nbsp;</td>
          <td><?PHP
          $selected = "";
          if (tool::securePost('loginname') && tool::securePost('loginname') != "") $selected = tool::securePost('loginname');
          ?>
          <input type="text" name="loginname" value="<?PHP echo $selected?>" size="<?PHP echo $htmlconfig['text_size3'];?>"></td>
          </td>
        </tr><tr>
          <td align="right"><?PHP echo $lang['common_password'];?>:&nbsp;</td>
          <td><input type="password" name="password" size="<?PHP echo $htmlconfig['text_size3'];?>"></td>
        </tr><tr>
          <td>&nbsp;</td>
          <td><input type="submit" class="submit" value="<?PHP echo $lang['common_login'];?>"></td>
        </tr>
        <?php if (DEMO_MODE) { ?>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">
              You can use the following accounts to
              log into the demo. Leave the password field blank.
              <br><b>Please note:</b>
              The database will be resetted to the&quot;Factory defaults&quot; once a week (on monday).
              <br><br>
            </td>
          </tr>
          <tr>
            <th>username</th><th>description</th>
          </tr>
          <tr>
            <td>customer</td><td>Customer: is allowed to create new requests.</td>
          </tr>
          <tr>
            <td>user</td><td>User: can create new tasks and post jobs.</td>
          </tr>
          <tr>
            <td>manager</td><td>Manager: can create new projects and assigns requests to the users.</td>
          </tr>
          <tr>
            <td>admin</td><td>Administrator: Can do everything ;)</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        <?php } ?>
      </table>
    </td>
  </tr>
</table>
</div>
<script language="javascript">
  <!--
    <?PHP if ($selected) {?>
      document.form1.password.focus();
    <?PHP } else { ?>
      document.form1.loginname.focus();
    <?PHP } ?>
  //-->
</script>

<?PHP
/***************************************************************************
 * $Log: login.php,v $
 * Revision 1.4  2003/11/18 01:46:54  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/09/27 19:15:50  willuhn
 * @B fix on logon page (empty passwords enabled again)
 * @N added DEMO_MODE
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:30  willuhn
 * reimport
 *
 * Revision 1.13  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.12  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.11  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
