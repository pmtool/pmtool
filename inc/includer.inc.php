<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/includer.inc.php,v $
 * $Revision: 1.7 $
 * $Date: 2005/02/21 05:36:33 $
 * $Author: matchboy $
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

//error_reporting("E_ALL & ~E_WARNING");
error_reporting("E_ALL");
ini_set('register_globals', 'off');

// List all files to be included here.  Class name is used as key for future functionality.
$include_file_arr = array( 'config'     => 'cfg/config.inc.php',
                           'db'         => 'inc/db.inc.php',
                           'priority'   => 'inc/priority.inc.php',
                           'request'    => 'inc/request.inc.php',
                           'user'       => 'inc/user.inc.php',
                           'job'        => 'inc/job.inc.php',
                           'task'       => 'inc/task.inc.php',
                           'tool'       => 'inc/tool.inc.php',
                           'mail'       => 'inc/mail.inc.php',
                           'login'      => 'inc/login.inc.php',
                           'access'     => 'inc/access.inc.php',
                           'project'    => 'inc/project.inc.php',
                           'customer'   => 'inc/customer.inc.php',
                           'box'        => 'inc/box.inc.php',
                           'report'     => 'inc/report.inc.php',
                           'attachment' => 'inc/attachment.inc.php',
                           'logger'     => 'inc/logger.inc.php',
                           'mysql'      => 'inc/db.mysql.inc.php' );

// Initialize the error array.
$include_error_arr = array();

// Attempt to include each file.  If file doesn't exist, put an error into the error array.
foreach ($include_file_arr as $class => $file) {
    if (!file_exists($file)) {
        $include_error_arr[] = "error: $file doesn't exist";
    }
}

// If there were any errors, display them and exit.
if ( count($include_error_arr) > 0 ) {
    foreach ($include_error_arr as $msg) {
        echo "$msg <br />";
    }
    echo "Please make sure these files are in place, and then run PMTool again.";
    exit;
} else {
    // This is done here rather than in the first foreach loop due to the way PHP handles errors with require.
    foreach ($include_file_arr as $class => $file) {
        require ($file);
    }
}

// create a new instance of the given database type
if (class_exists($dbconfig['type'])) {
    $dbInst = new $dbconfig['type']();
} else {
    print "The class {$dbconfig['type']} does not exist. Please check your configuration.";
    exit;
}

// fallback: if $dbInst doesn't exist, we use the mysql implementation.
//           of course, on top of the browser window there will be shown
//           up an error, but the app itself should work

// CMW - Um, well, actually, no it won't.  Trying to instantiate a non-existent class throws a fatal error.
// So I'm commenting these three lines as they won't get run anyway.  Someone should remove this part.

//if (!isset($dbInst)) {
//  $dbInst = new mysql();
//}

$loginInst = new login();
$toolInst = new tool();

## we start the session
session_start();

/***************************************************************************
 * $Log: includer.inc.php,v $
 * Revision 1.7  2005/02/21 05:36:33  matchboy
 * Added class_exists() check on dbInst for consistancy. (and for pgsql port in
 * future)
 *
 * Revision 1.6  2005/02/21 02:41:07  genghishack
 * rewrote file to be cleaner and more easily maintainable.  Commented fallback database instantiation code as it won't get run anyway.  Functionality should be essentially the same, except now will list all missing files before exiting instead of just the first one it comes to.
 *
 * Revision 1.4  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 * Revision 1.3  2003/10/09 18:28:10  willuhn
 * @B bug fixed in encodeString and rights page (array were not handled correctly via POST)
 *
 * Revision 1.2  2003/08/05 19:43:06  willuhn
 * @B removed small typo in taskdetails
 *
 * Revision 1.1.1.1  2003/07/28 19:22:53  willuhn
 * reimport
 *
 * Revision 1.23  2002/06/04 21:24:04  willuhn
 * @N added debug method in logger
 *
 * Revision 1.22  2002/05/23 21:49:16  willuhn
 * @N added some more language stuff
 *
 * Revision 1.21  2002/05/05 16:25:18  willuhn
 * @B mysql stuff
 *
 * Revision 1.20  2002/05/03 11:17:57  willuhn
 * @B session handling
 *
 * Revision 1.19  2002/04/17 22:26:46  willuhn
 * @N child tasks can only set to "in progress", if parent task is set to "done"
 *
 * Revision 1.18  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.17  2002/04/14 22:03:57  willuhn
 * @N multilanguage
 *
 * Revision 1.16  2002/04/14 18:09:30  willuhn
 * @N splitted db class into a super class (valid for all databases)
 *    and an implementation class
 * @N some more multilanguage stuff
 *
 * Revision 1.15  2002/03/30 19:24:12  willuhn
 * @N added attachment code
 *
 * Revision 1.14  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
