<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/password.php,v $
 * $Revision: 1.4 $
 * $Date: 2003/11/18 01:47:41 $
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
if (!$loginInst->id) {
  echo $lang['common_accessDenied']."\n";
  exit;
}
?>

<h1><?PHP echo $lang['password_change'];?></h1>
<form method="post">

<?PHP
if (tool::securePost('action') == "update") {
  if (!DEMO_MODE) {
    $loginInst->changeLanguage(tool::securePost('language'));
    if (tool::securePost('new_password') && tool::securePost('new_password') != "") {
      $loginInst->updatePassword(tool::securePost('old_password'),tool::securePost('new_password'),tool::securePost('new_password_2'));
    }
  }
  else {
    $toolInst->errorStatus("not allowed in this demo. Sorry ;)");
  }
}

?>
<br />
<br />
<h2><?PHP echo $lang['password_change'];?></h2>
<input type="hidden" name="action" value="update">
<table border="0" cellpadding="2" cellspacing="0">
  <tr>
    <td><?PHP echo $lang['password_old'];?>:&nbsp;</td>
    <td><input type="password" name="old_password" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
  </tr><tr>
    <td><?PHP echo $lang['password_new'];?>:&nbsp;</td>
    <td><input type="password" name="new_password" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
  </tr><tr>
    <td><?PHP echo $lang['password_confirm'];?>:&nbsp;</td>
    <td><input type="password" name="new_password_2" size="<?PHP echo $htmlconfig['text_size1'];?>"></td>
  </tr><tr>
    <td colspan="2"><br><h2><?PHP echo $lang['password_change_language'];?></h2></td>
  </tr><tr>
    <td><?PHP echo $lang['common_language'];?>:&nbsp;</td>
    <td>
      <select name="language"><option value=""><?PHP echo $lang['common_systemDefault'];?>
        <?PHP
        $dir = $toolInst->getDir("./lang");
        while ($element = current($dir)) {
          $selected = "";
          if (eregi("\.inc\.php",$element)) {
            $element = eregi_replace("\.inc\.php","",$element);
            if ($loginInst->language == $element) $selected = "selected";
            echo "<option ".$selected." value=\"".$element."\">".$element."\n";
          }
          next($dir);
        }
        ?>
      </select>
    </td>
  </tr><tr>
    <td colspan="2" class="small">&nbsp;</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td><input type="submit" value="<?PHP echo $lang['common_save'];?>"></td>
  </tr>
</table>
</form>

<?PHP
/***************************************************************************
 * $Log: password.php,v $
 * Revision 1.4  2003/11/18 01:47:41  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/09/27 19:15:50  willuhn
 * @B fix on logon page (empty passwords enabled again)
 * @N added DEMO_MODE
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:22  willuhn
 * reimport
 *
 * Revision 1.8  2002/11/07 22:57:20  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.7  2002/04/13 20:46:35  willuhn
 * @N added some more multilanguage stuff
 *
 * Revision 1.6  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.5  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
