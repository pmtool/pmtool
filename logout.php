<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/logout.php,v $
 * $Revision: 1.2 $
 * $Date: 2003/11/18 01:47:07 $
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

if (!file_exists("cfg/config.inc.php")) {echo "panic: cfg/config.inc doesn't exist";exit;} require("cfg/config.inc.php");

session_start();
session_destroy();
if (isset($HTTP_SERVER_VARS["PHP_SELF"])) $url = $HTTP_SERVER_VARS["PHP_SELF"];
if (isset($PHP_SELF)) $url = $PHP_SELF;

$forward_url = ereg_replace("logout.php","index.php",$url);
header("Location: $forward_url");
?>
<?PHP echo $lang['common_ifRedirectDontWork'];?> <a href="<?PHP echo $forward_url;?>"><?PHP echo $lang['common_here'];?></a>
<?PHP
/***************************************************************************
 * $Log: logout.php,v $
 * Revision 1.2  2003/11/18 01:47:07  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:30  willuhn
 * reimport
 *
 * Revision 1.4  2002/06/04 21:24:04  willuhn
 * @N added debug method in logger
 *
 * Revision 1.3  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
