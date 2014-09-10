<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/db.mysql.inc.php,v $
 * $Revision: 1.2 $
 * $Date: 2003/09/27 18:23:45 $
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

class mysql extends db {

  /*********************************************************************
  ** constructor                                                     **/

  function mysql() {
    // call the constructor of the parent class
    $this->db();

    // open new database connection
    if (!$link = mysql_connect($this->config['host'], $this->config['user'], $this->config['pass'])) {
      $this->error("unable to connect to database");
    }
    if (!mysql_select_db($this->config['name'])) {
      $this->error("unable to select database (name: ".$this->config['name'].")");
    }
    $this->dbLink = $link;
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function close() {
    if ($this->$dbLink) {
      return mysql_close() || $this->error("unable to close database handle");
    }
  }

  function query($query) {
    // debug

    $this->logger->debug(__FILE__,__LINE__,$query);

    $result = mysql_query($query);
    if (!$result) {
      $this->error("unable to send query to database", $query);
    }
    $matches = 0;
    if (eregi("^select", $query)) {
      $matches = mysql_num_rows($result);
    }
    else {
      $matches = mysql_affected_rows();
    }
    $this->count++;
    return array ($result,$matches);
  }

  function fetchRow($result) {
    return mysql_fetch_row($result);
  }

  function fetchArray($result) {
    return mysql_fetch_array($result);
  }

  function error($message, $query="") {
    echo "<h2>".$message."</h2>\n";
    echo "<table>\n";
    if ($query != "") {
      echo "<tr><td>Query: </td><td>".$query."</td></tr>\n";
    }
    echo "<tr><td rowspan=2 valign=top>MySQL: </td>\n";
    echo "<td>errorcode: ".mysql_errno()."</td></tr><tr><td>errortext: ".mysql_error()."</td></tr>\n";
    echo "</table></body></html>";
    exit;
  }

  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: db.mysql.inc.php,v $
 * Revision 1.2  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.1.1.1  2003/07/28 19:22:58  willuhn
 * reimport
 *
 * Revision 1.5  2002/09/07 19:23:13  willuhn
 * @N global commit for missing files
 *
 * Revision 1.4  2002/05/05 16:25:18  willuhn
 * @B mysql stuff
 *
 * Revision 1.3  2002/05/02 22:43:49  willuhn
 * @B order
 *
 * Revision 1.2  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.1  2002/04/14 18:09:30  willuhn
 * @N splitted db class into a super class (valid for all databases)
 *    and an implementation class
 * @N some more multilanguage stuff
 *
 ***************************************************************************/

?>
