<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/attachment.inc.php,v $
 * $Revision: 1.4 $
 * $Date: 2004/02/28 23:01:27 $
 * $Author: znouza $
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

class attachment {
  // instance vars
  var $id;
  var $name;
  var $taskId;
  var $created;

  var $logger;

  /*********************************************************************
  ** constructor                                                     **/

  function attachment($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("ATTACHMENT");
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("attachment.activate")) return false;

    $query = "select * from ".$dbInst->config['table_attachment']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id             = $row['id'];
    $this->name           = $row['name'];
    $this->taskId         = $row['task_id'];
    $this->created        = $row['created'];
  }

  function clear() {
    $this->id             = "";
    $this->name           = "";
    $this->taskId         = "";
    $this->created        = "";
  }

  function insert() {
    global $config,$dbInst,$toolInst,$loginInst;

    $this->created = $toolInst->getTime("U");

    // set the name of the attachment
    $this->name = tool::secureFiles('userfile','name');

    if (!$this->check()) return false;

    if (!$loginInst->hasAccess("attachment.insert")) return false;

    if (! @file_exists(tool::secureFiles('userfile','tmp_name'))) {
      $toolInst->errorStatus("file was not successfully uploaded to the server.");
      return false;
    }
	
	
    // create a new directory based on the current time.
    // this is needed to attach multiple files with the same name
    $dir = $config['attach_dir']."/".$this->created;
    mkdir($dir,0755);

    // copy the file from the temporary server location into the pmtool
    // attachment directory
    $file = $dir."/".$this->name;
    if (!@move_uploaded_file(tool::secureFiles('userfile','tmp_name'),$file)) {
      $toolInst->errorStatus("unable to move the file from temporary server location into pmtool dir.");
      return false;
    }
    $query = "insert into ".$dbInst->config['table_attachment']." ".
             "(task_id,name,created) ".
             "values (".
             "'".$this->taskId."',".
             "'".$this->name."',".
             "'".$this->created."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_attachment']);
    $dbInst->status($result[1],"i");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("added attachment ".$file);

      return $id;
    }
    else {
      return false;
    }
  }

  function check() {
    global $toolInst;

    if (! $this->taskId) {
      $toolInst->errorStatus("no task given");
      return false;
    }
    if (! $this->name) {
      $toolInst->errorStatus("no filename given");
      return false;
    }

    return true;
  }

  function update() {
    global $dbInst,$config,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("attachment.update")) return false;

    if (!$this->check()) return false;

    $query = "update ".$dbInst->config['table_attachment']." set ".
             "task_id = '".$this->taskId."',".
             "name = '".$this->name."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("changed attachment ".$this->name);

      return true;
    }
    else {
      return false;
    }
  }

  function delete() {
    global $config,$dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("attachment.delete")) return false;

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    // delete only, if id given
    $this->activate($this->id);

    $dir  = $config['attach_dir']."/".$this->created;
    $file = $dir."/".$this->name;
    if (! unlink($file)) {
      $toolInst->errorStatus("unable to delete attachment");
      return false;
    }
    // it's not bad, if this fails ;)
    rmdir($dir);

    $result = $dbInst->query("delete from ".$dbInst->config['table_attachment']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    // logging
    $this->logger->warn("deleted attachment ".$this->name);

    return true;
  }

  function getSize() {
    global $config,$loginInst;

    if (!$loginInst->hasAccess("attachment.getSize")) return false;

    $file  = $config['attach_dir']."/".$this->created."/".$this->name;
    return (floor(filesize($file) / 1024))." kb";
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: attachment.inc.php,v $
 * Revision 1.4  2004/02/28 23:01:27  znouza
 *
 * tool.inc.php
 * @N secureFiles - handles file uploads more securely
 *
 * attachment, request, task
 * @C handling uploaded files
 *
 * attachment.inc.php
 * @C copy() to move_uploaded_file() = more security
 *
 * Revision 1.3  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.2  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.1.1.1  2003/07/28 19:22:53  willuhn
 * reimport
 *
 * Revision 1.5  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.4  2002/03/31 17:40:10  willuhn
 * @N added right "attachment.update"
 * @N attachments are now also available for requests
 *
 * Revision 1.3  2002/03/31 16:57:19  willuhn
 * @B task id wasn't set when changing the project
 * @N added getSize() in attachment class
 *
 * Revision 1.2  2002/03/30 19:55:54  willuhn
 * @N deleting of attachments (in taskdetails)
 *
 * Revision 1.1  2002/03/30 19:24:12  willuhn
 * @N added attachment code
 *
 ***************************************************************************/

?>
