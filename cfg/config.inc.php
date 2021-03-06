<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/cfg/config.inc.php,v $
<<<<<<< config.inc.php
 * $Revision: 1.7 $
 * $Date: 2005/02/25 17:23:01 $
 * $Author: matchboy $
=======
 * $Revision: 1.7 $
 * $Date: 2005/02/25 17:23:01 $
 * $Author: matchboy $
>>>>>>> 1.6
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

## URL config
## insert here the URL of your pmtool install
## ! whitout trailing slash !
$config['root_url']              = "http://www.pmtool.org";

## send automatically generated mails to task recipients [0|1]
$config['automail']              = "0";

## language (see folder "lang" for available languages
$config['language']              = "en";

## default start page after successful login
$config['defaultpage']           = "home.php";

## Currency
$config['currency']              = "USD";

## tax rate
$config['taxrate']               = "16";

# round style (for calculating of costs)
# 0    : do not round
# 1    : ever round up
# 2    : ever round off
$config['roundstyle']            = 1;

## round used time (will only be used, if $config['roundstyle'] is NOT "0")
# 0.25 : round to 1/4 hour
# 0.5  : round to 1/2 hour
# 1    : round to full hours
$config['roundrate']             = 0.25;

## per default show tasks posted within the last x days
$config['defaulttasktimelimit']  = "30";

## enable database logging ? [0|1]
## all database changes will be logged into table "log"
$config['enable_log']            = "1";


## directory for attachments
## ATTENTION: The webserver needs write permissions in this
##            directory, if you want to upload files!!

$config['attach_url']            = "run/attach";

## Constants
define("TASK_STATUS_REQUEST","1");
define("TASK_STATUS_INPROGRESS","2");
define("TASK_STATUS_DONE","3");
define("TASK_STATUS_WAITING","4");

define("TASK_PRIO_LOW","1");
define("TASK_PRIO_MEDIUM","2");
define("TASK_PRIO_HIGH","3");
define("TASK_PRIO_VERYHIGH","4");

define("TASK_TYPE_BUG","1");
define("TASK_TYPE_NEW","2");
define("TASK_TYPE_CHANGE","3");
define("TASK_TYPE_TODO","4");

## types of tasks the customer has to pay for
$config['paytasks'] = array(TASK_TYPE_NEW,TASK_TYPE_CHANGE);

## Constants
define("TASK_STATUS_REQUEST","1");
define("TASK_STATUS_INPROGRESS","2");
define("TASK_STATUS_DONE","3");
define("TASK_STATUS_WAITING","4");

define("TASK_PRIO_LOW","1");
define("TASK_PRIO_MEDIUM","2");
define("TASK_PRIO_HIGH","3");
define("TASK_PRIO_VERYHIGH","4");

define("TASK_TYPE_BUG","1");
define("TASK_TYPE_NEW","2");
define("TASK_TYPE_CHANGE","3");
define("TASK_TYPE_TODO","4");

## types of tasks the customer has to pay for
$config['paytasks'] = array(TASK_TYPE_NEW,TASK_TYPE_CHANGE);


## absolute path to directory for attachments
## the next 2 lines try to detect this automatically
## If this fails, change the third line
if (isset($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) && $HTTP_SERVER_VARS["SCRIPT_FILENAME"] != "") $_script = $HTTP_SERVER_VARS["SCRIPT_FILENAME"];
elseif (isset($HTTP_SERVER_VARS["PATH_TRANSLATED"]) && $HTTP_SERVER_VARS["PATH_TRANSLATED"] != "") $_script = $HTTP_SERVER_VARS["PATH_TRANSLATED"];
else $_script                    = "/path/to/pmtool";

$config['attach_dir']            = dirname($_script)."/".$config['attach_url'];

## max filesize for attachments (in bytes!)
$config['attach_maxfilesize']    = 2 * 1024 * 1024;  // 2 megabytes

## database config
## AT THIS TIME ONLY "mysql" IS SUPPORTED
$dbconfig['type']                = "mysql";         // database type (mysql)
$dbconfig['host']                = "localhost";     // hostname[:port]
$dbconfig['user']                = "username";        // database username
$dbconfig['pass']                = "password";        // database password
$dbconfig['name']                = "pmtool";        // databasename

$dbconfig['table_task']          = "task";
$dbconfig['table_job']           = "job";
$dbconfig['table_report']        = "report";
$dbconfig['table_project']       = "project";
$dbconfig['table_request']       = "request";
$dbconfig['table_tasktype']      = "tasktype";
$dbconfig['table_customer']      = "customer";
$dbconfig['table_attachment']    = "attachment";
$dbconfig['table_taskstatus']    = "taskstatus";
$dbconfig['table_taskpriority']  = "taskpriority";
$dbconfig['table_projectstatus'] = "projectstatus";

$dbconfig['table_log']           = "log";
$dbconfig['table_user']          = "user";
$dbconfig['table_groups']        = "groups";
$dbconfig['table_groups_user']   = "groups_user";
$dbconfig['table_rights']        = "rights";
$dbconfig['table_rights_groups'] = "rights_groups";

## html
if (! isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']) || ! eregi("MSIE", $HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
  ## use these fieldsizes if browser is NOT MS Internet Explorer
  $htmlconfig['textarea_cols']   = "33";
  $htmlconfig['textarea_rows']   = "5";
  $htmlconfig['text_size1']      = "38";
  $htmlconfig['text_size2']      = "26";
  $htmlconfig['text_size3']      = "17";
  $htmlconfig['text_size4']      = "7";
}
else {
  $htmlconfig['textarea_cols']   = "51";
  $htmlconfig['textarea_rows']   = "5";
  $htmlconfig['text_size1']      = "52";
  $htmlconfig['text_size2']      = "41";
  $htmlconfig['text_size3']      = "20";
  $htmlconfig['text_size4']      = "8";
}

// settings for debug mode
define("DEBUG",false);
$config['debug_target']          = "/tmp/pmtool_debug.log";
#$config['debug_target']          = "/var/log/httpd/error_log";


/***************************************************************************
 * $Log: config.inc.php,v $
 * Revision 1.7  2005/02/25 17:23:01  matchboy
 * removing my test db info. oops.
 *
<<<<<<< config.inc.php
 * Revision 1.4  2004/03/17 20:19:50  willuhn
 * @N added priorities to projects
 *
 * Revision 1.3  2004/03/17 19:30:52  willuhn
 * @N configurable behavior for task types the customer has to pay for
=======
 * Revision 1.6  2005/02/21 07:58:07  genghishack
 * added constants... they're in here in the release version, but in CVS they were in includes.inc.php.
 *
 * Revision 1.5  2004/04/01 20:02:53  znouza
 * @N config.inc.php - added $config['paytasks'] option
 * @N include.inc.php - added define of constants (previously in requests.inc.php)
>>>>>>> 1.6
 *
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:38  willuhn
 * reimport
 *
 * Revision 1.43  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.42  2002/09/07 19:23:13  willuhn
 * @N global commit for missing files
 *
 * Revision 1.41  2002/06/04 21:24:04  willuhn
 * @N added debug method in logger
 *
 * Revision 1.40  2002/05/02 21:42:40  willuhn
 * @N pretty cool new feature in "task hotlist" -> order by planned time left
 *
 * Revision 1.39  2002/04/29 23:04:04  willuhn
 * @N planned time in task (only reminder - needs to implement ;)
 *
 * Revision 1.38  2002/04/17 22:26:46  willuhn
 * @N child tasks can only set to "in progress", if parent task is set to "done"
 *
 * Revision 1.37  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.36  2002/04/03 23:19:01  willuhn
 * @N fixed a bug in language changing on user page
 *
 * Revision 1.35  2002/04/01 23:17:22  willuhn
 * @N added some language stuff
 *
 * Revision 1.34  2002/03/30 19:24:12  willuhn
 * @N added attachment code
 *
 * Revision 1.33  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.32  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
