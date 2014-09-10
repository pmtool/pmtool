<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/mail.inc.php,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:22:54 $
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

class mail {

  // instance vars
  var $senderId;
  var $recipientId;
  var $subject;
  var $body;
  var $priorityId;

  /*********************************************************************
  ** constructor                                                     **/

  function mail() {
  }

  /**                                                                 **
  *********************************************************************/
  /*********************************************************************
  ** private methods                                                 **/

  function getHeader() {
    global $dbInst;

    $userInst = new user();
    $userInst->activate($this->senderId);
    $mailheader = "FROM: ".$userInst->name." <".$userInst->email.">\n";
    if ($this->priorityId == TASK_PRIO_HIGH || $this->priorityId == TASK_PRIO_VERYHIGH) $mailheader .= "X-Priority: 1\nX-MSMail-Priority: High\n";
    return $mailheader;
  }

  /**                                                                 **
  *********************************************************************/
  /*********************************************************************
  ** public methods                                                  **/

  function send() {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("mail.send")) return false;

    $userInst = new user();
    $userInst->activate($this->recipientId);

    if ($userInst->email) {
      mail($userInst->name." <".$userInst->email.">",$this->subject,$this->body,$this->getHeader());
      $toolInst->appendStatus(", email sent to ".$userInst->name);
    }
  }

  /**                                                                 **
  *********************************************************************/

}

/***************************************************************************
 * $Log: mail.inc.php,v $
 * Revision 1.1.1.1  2003/07/28 19:22:54  willuhn
 * reimport
 *
 * Revision 1.10  2002/11/07 22:57:21  willuhn
 * @B division by zero in "project roadmap" plugin
 * @B some calculation errors in plugins "task hotlist" and "project roadmap" fixed
 * @N renamed page "password" into "preferences"
 * @N user is now able to change his language settings
 * @N added some constants for task properties
 *
 * Revision 1.9  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
