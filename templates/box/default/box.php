<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/templates/box/default/box.php,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:23:06 $
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
<table border="0" cellpadding="0" cellspacing="0" align="<?PHP echo $this->align;?>">
  <tr>
    <td rowspan="3"><img src="templates/box/default/box_tl.gif" width="6" height="31" border="0"></td>
    <td background="templates/box/default/box_t.gif"><img src="grafx/dummy.gif" width="<?PHP echo $this->width;?>" height="5" border="0"></td>
    <td rowspan="3"><img src="templates/box/default/box_tr.gif" width="13" height="31" border="0"></td>
  </tr><tr>
    <td background="templates/box/default/fade.gif"><table border="0" cellpadding="0" cellspacing="0"><tr><td><img src="templates/box/default/box_arrow.gif" width="11" height="18" border="0"></td><td><b>&nbsp;<?PHP echo $this->title;?></b></td></tr></table></td>
  </tr><tr>
    <td background="templates/box/default/box_m.gif"><img src="grafx/dummy.gif" width="1" height="8" border="0"></td>
  </tr><tr>
    <td background="templates/box/default/box_ml.gif"><img src="grafx/dummy.gif" width="6" height="1" border="0"></td>
    <td valign="top">
    <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
      <?PHP
        if ($this->bgColor) echo "<td bgcolor=".$this->bgColor.">";
        else echo "<td>";
        echo $this->contents;
      ?>
    </td></tr></table>
    </td>
    <td background="templates/box/default/box_mr.gif"><img src="grafx/dummy.gif" width="13" height="1" border="0"></td>
  </tr><tr>
    <td><img src="templates/box/default/box_bl.gif" width="6" height="13" border="0"></td>
    <td background="templates/box/default/box_b.gif"><img src="grafx/dummy.gif" width="1" height="13" border="0"></td>
    <td><img src="templates/box/default/box_br.gif" width="13" height="13" border="0"></td>
  </tr>
</table>

<?PHP
/***************************************************************************
 * $Log: box.php,v $
 * Revision 1.1.1.1  2003/07/28 19:23:06  willuhn
 * reimport
 *
 * Revision 1.4  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.3  2002/04/15 20:41:40  willuhn
 * @N pluginLoader
 *
 * Revision 1.2  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
