<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/reportviewer.php,v $
 * $Revision: 1.3 $
 * $Date: 2003/11/18 01:55:26 $
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

#######################################################################
## check login again
if (!file_exists("inc/includer.inc.php")) {echo "panic: inc/includer.inc.php doesn't exist";exit;} require("inc/includer.inc.php");

session_start();
if (! session_is_registered("loginid")) {
  echo "you are not logged in\n";
  exit;
}
// activate user
$loginInst->activate($_SESSION["loginid"]);

if (!$loginInst->id) {
  echo "access denied\n";
  exit;
}
if (!$loginInst->hasAccess("report")) {
  echo "access denied\n";
  exit;
}
$reportInst = new report(tool::secureGet('reportid'));

header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
echo "\n";
echo "<?xml-stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" type=\"text/xsl\" href=\"templates/report/".tool::secureGet('template')."/template.xsl\"?>\n";

echo $reportInst->xml;

/***************************************************************************
 * $Log: reportviewer.php,v $
 * Revision 1.3  2003/11/18 01:55:26  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:22  willuhn
 * reimport
 *
 * Revision 1.7  2002/09/07 19:23:12  willuhn
 * @N global commit for missing files
 *
 * Revision 1.6  2002/07/11 16:15:10  willuhn
 * @XML seems to work now in mozilla
 *
 * Revision 1.5  2002/06/26 14:14:29  willuhn
 * @N added form "query task by id"
 *
 * Revision 1.4  2002/04/29 20:29:50  willuhn
 * @C session handling
 *
 * Revision 1.3  2002/02/24 22:41:20  willuhn
 * updated content-type in reportviewer
 *
 * Revision 1.2  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
