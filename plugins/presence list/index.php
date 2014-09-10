<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/plugins/presence\040list/index.php,v $
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
if (!$loginInst->id) {
  echo "access denied\n";
  exit;
}
?>
<h1>plugins: <?PHP echo $pluginconfig['title'];?></h1>

<h2>currently working users</h2>
<table border="0" cellpadding="2" cellspacing="0" width="100%">
  <tr>
    <th>user</th>
    <th>project</th>
    <th>task</th>
    <th>since</th>
    <th>used time</th>
  </tr>
    <?PHP
      $userInst = new user();
      $list = $userInst->getList();
      while ($element = current($list)) {
        echo "<tr>\n";
        // create a new user object, based on the current element of the list
        $user = new user($element);
        echo "<td>".$user->username."</td>\n";

        // determine the id of the job, the user is currently working at
        $job = new job();
        $jobId = $job->getOpenJob($user->id);
        if ($jobId) {
          $job     = new job($jobId);
          $task    = new task($job->taskId);
          $project = new project($task->projectId);
          echo "<td>".$project->name."</td>\n";
          echo "<td>".$task->subject."</td>\n";
          echo "<td>".$toolInst->getTime("H:i",$job->start)."</td>\n";
          echo "<td>".$toolInst->formatTime($job->getSummary())."</td>\n";
        }
        else {
          echo "<td colspan=\"4\">offline</td>\n";
        }
        next($list);
        echo "</tr>\n";
      }
    ?>
</table>
<?PHP
/***************************************************************************
 * $Log: index.php,v $
 * Revision 1.1.1.1  2003/07/28 19:23:13  willuhn
 * reimport
 *
 * Revision 1.2  2002/04/15 22:14:35  willuhn
 * @N added plugin "task hotlist"
 *
 * Revision 1.1  2002/03/30 17:15:52  willuhn
 * @N added plugin "presence list"
 *
 * Revision 1.1  2002/03/30 14:14:39  willuhn
 * @N added plugin loader
 *
 ***************************************************************************/
?>
