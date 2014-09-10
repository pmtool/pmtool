<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/requestdetails.php,v $
 * $Revision: 1.5 $
 * $Date: 2005/02/20 20:46:28 $
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
?>

<?PHP
#######################################################################
## check login again
if (!$loginInst->id) {
  echo $lang['common_accessDenied']."\n";
  exit;
}

$requestInst = new request(tool::secureGet('requestid'));
$projectInst = new project($requestInst->projectId);

// this request doesn't exists
if (!$requestInst->id) {
  echo "<div>".$lang['common_request_not_exists']."</div>";
  exit;
}


#######################################################################
## show request details


#######################################################################
## perform action

if ((tool::securePost('action') == "taskify" && $loginInst->hasAccess("request.assignTo")) || (tool::securePost('action')=="save" && tool::securePost('taskid'))) {
  
  if (!tool::securePost('taskid')) {
	 $taskId = $requestInst->assignTo(tool::securePost('userid'));
  } else {
	  $taskId = tool::securePost('taskid');
  }
  if ($taskId) {
    include("taskdetails.php");
	exit;
  }
}

?>

<form method="post" name="form1">
<input type="hidden" name="action" value="taskify">
<input type="hidden" name="requestid" value="<?PHP echo $requestInst->id;?>">

<h1><?PHP echo $lang['requests_requestDetails'];?></h1>
<div align="center">
<table border="0" cellpadding="2" cellspacing="1" width="96%">
  <tr>
    <td><h2><?PHP echo $requestInst->subject;?></h2></td>
  </tr>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="96%" bgcolor="#ffffff">
  <tr>
    <th><?PHP echo $lang['common_project'];?></th>
    <th><?PHP echo $projectInst->name;?></th>
  </tr><tr>
    <th><?PHP echo $lang['requests_postedBy'];?></th>
    <th>
      <?PHP
      $userInst = new user($requestInst->posterId);
      echo $userInst->name;
      ?>
    </th>
  </tr><tr>
    <td class="list"><?PHP echo $lang['common_priority'];?></td>
    <td class="<?PHP echo $requestInst->getPriorityStyle();?>"><?PHP echo $requestInst->getPriorityName();?></td>
  </tr><tr>
    <td class="list"><?PHP echo $lang['common_type'];?></td>
    <td class="<?PHP echo $requestInst->getTypeStyle();?>"><?PHP echo $requestInst->getTypeName();?></td>
  </tr><tr>
    <td class="list"><?PHP echo $lang['common_posted'];?></td>
    <td class="list"><?PHP echo $toolInst->getTime("",$requestInst->time);?></td>
  </tr>
  <?PHP if ($requestInst->body != "") { ?>
    <tr>
      <td class="list"><?PHP echo $lang['common_body'];?></td>
      <td class="list"><?PHP echo ereg_replace("\n","<br>",$requestInst->body);?></td>
    </tr>
  <?PHP }
  if ($loginInst->hasAccess("request.assignTo")) {
    $userInst = new login();
    $list = $userInst->getList("name");
    if ($list) {
      ?>
      <tr>
        <td colspan="2" class="list">&nbsp;</td>
      </tr><tr>
        <td class="list"><?PHP echo $lang['requests_assignAsTaskTo'];?>:</td>
        <td class="list"><select name="userid">
        <?PHP
          while ($element = current($list)) {
            $userInst->activate($element);
            echo "<option value=\"".$element."\">".$userInst->name."\n";
            next($list);
          }
        ?>
        </select>&nbsp;<input type="submit" value="<?PHP echo $lang['common_save'];?>"></td>
      </tr>
      <?PHP
    }
  }
  ?>
</table>
</form>

</div>
&nbsp;

<?PHP
/***************************************************************************
 * $Log: requestdetails.php,v $
 * Revision 1.5  2005/02/20 20:46:28  matchboy
 * The dropdown is displaying the 'name' field not the username field and the
 * sortorder for the list was ASC on username. Makes more sense to sort by what
 * is displaying to the user.
 *
 * Revision 1.4  2004/02/28 21:14:21  znouza
 * @B bug #893537 fixed.
 * @N adding handling of non-existent request - even if adding a request, didn't
 *    reload a page and click the same request again.
 *
 * Revision 1.3  2003/11/18 01:56:20  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/09/27 18:23:44  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:29  willuhn
 * reimport
 *
 * Revision 1.9  2002/02/09 19:38:27  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
?>
