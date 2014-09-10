<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/customer.inc.php,v $
 * $Revision: 1.3 $
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

class customer {
  // instance vars
  var $id;
  var $customerNumber;
  var $title;
  var $firstname;
  var $lastname;
  var $company;
  var $street;
  var $zip;
  var $city;
  var $phone;
  var $fax;
  var $cellphone;
  var $email;

  var $logger;
  /*********************************************************************
  ** constructor                                                     **/

  function customer($id="-1") {
    global $loginInst;

    if ($id != "-1") {
      $this->activate($id);
    }
    $this->logger = new logger();
    $this->logger->setUser($loginInst);
    $this->logger->setToken("CUSTOMER");
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function activate($id) {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("customer.activate")) return false;

    $query = "select * from ".$dbInst->config['table_customer']." where id = '".$id."'";
    $result = $dbInst->query($query);
    $row = $dbInst->fetchArray($result[0]);
    $this->id             = $row['id'];
    $this->customerNumber = $row['customer_number'];
    $this->title          = $row['title'];
    $this->firstname      = $row['firstname'];
    $this->lastname       = $row['lastname'];
    $this->company        = $row['company'];
    $this->street         = $row['street'];
    $this->zip            = $row['zip'];
    $this->city           = $row['city'];
    $this->phone          = $row['phone'];
    $this->fax            = $row['fax'];
    $this->cellphone      = $row['cellphone'];
    $this->email          = $row['email'];
  }

  function fill($array) {

    $this->customerNumber = $array['customer_number'];
    $this->title          = $array['title'];
    $this->firstname      = $array['firstname'];
    $this->lastname       = $array['lastname'];
    $this->company        = $array['company'];
    $this->street         = $array['street'];
    $this->zip            = $array['zip'];
    $this->city           = $array['city'];
    $this->phone          = $array['phone'];
    $this->fax            = $array['fax'];
    $this->cellphone      = $array['cellphone'];
    $this->email          = $array['email'];
  }

  function clear() {
    $this->id             = "";
    $this->customerNumber = "";
    $this->title          = "";
    $this->firstname      = "";
    $this->lastname       = "";
    $this->company        = "";
    $this->street         = "";
    $this->zip            = "";
    $this->city           = "";
    $this->phone          = "";
    $this->fax            = "";
    $this->cellphone      = "";
    $this->email          = "";
  }

  function setFilter() {
    global $dbInst,$loginInst;

    if (!$loginInst->hasAccess("customer.setFilter")) return false;

    // this is a dummy filter
    $filter = "where id like '%%' ";
    if ($loginInst->isCustomer()) $filter .= "and ".$dbInst->config['table_customer'].".id = '".$loginInst->customerId."'";
    return $filter;
  }

  function getList($order="company",$desc="ASC") {
    global $dbInst,$toolInst,$loginInst;

    if (!$loginInst->hasAccess("customer.getList")) return false;

    $array = array();
    $query = "select id from ".$dbInst->config['table_customer']." ".$this->setFilter()." order by ".$order." ".$desc;
    $result = $dbInst->query($query);
    while($row = $dbInst->fetchArray($result[0])) {
      $array[] = $row['id'];
    }
    return $array;
  }

  function insert() {
    global $dbInst,$toolInst,$loginInst;

    if (!$this->check()) return false;

    if (!$loginInst->hasAccess("customer.insert")) return false;

    $query = "insert into ".$dbInst->config['table_customer']." ".
             "(customer_number,title,firstname,lastname,company,street,zip,city,phone,fax,cellphone,email) ".
             "values (".
             "'".$this->customerNumber."',".
             "'".$this->title."',".
             "'".$this->firstname."',".
             "'".$this->lastname."',".
             "'".$this->company."',".
             "'".$this->street."',".
             "'".$this->zip."',".
             "'".$this->city."',".
             "'".$this->phone."',".
             "'".$this->fax."',".
             "'".$this->cellphone."',".
             "'".$this->email."')";

    $result = $dbInst->query($query);
    $id = $dbInst->getValue("select distinct last_insert_id() from ".$dbInst->config['table_customer']);
    $dbInst->status($result[1],"i");
    if ($result[1] == 1 || $result[1] == 0) {

      // logging
      $this->logger->info("added customer ".$this->company);

      return $id;
    }
    else {
      return false;
    }
  }

  function update() {
    global $dbInst,$toolInst,$loginInst;

    if (!$this->check()) return false;

    if (!$loginInst->hasAccess("customer.update")) return false;

    $query = "update ".$dbInst->config['table_customer']." set ".
             "customer_number = '".$this->customerNumber."',".
             "title = '".$this->title."',".
             "firstname = '".$this->firstname."',".
             "lastname = '".$this->lastname."',".
             "company = '".$this->company."',".
             "street = '".$this->street."',".
             "zip = '".$this->zip."',".
             "city = '".$this->city."',".
             "phone = '".$this->phone."',".
             "fax = '".$this->fax."',".
             "cellphone = '".$this->cellphone."',".
             "email = '".$this->email."' where id = '".$this->id."'";

    $result = $dbInst->query($query);
    $dbInst->status($result[1],"u");
    if ($result[1] == 1 || $result[1] == 0) {
      // logging
      $this->logger->info("changed customer ".$this->company);

      return true;
    }
    else {
      return false;
    }
  }

  function check() {
    global $toolInst,$loginInst;

    if (! $this->company) {
      $toolInst->errorStatus("no company name given");
      return false;
    }
    return true;
  }

  function delete() {
    global $dbInst,$loginInst,$toolInst;

    if (!$loginInst->hasAccess("customer.delete")) return false;

    # check dependencies
    if ($dbInst->getValue("select id from ".$dbInst->config['table_project']." where customer_id = '".$this->id."'")) {
      $toolInst->errorStatus("dependency check failed: there are existing projects assigned to this customer");
      return false;
    }

    if (! $this->id) {
      $toolInst->errorStatus("no record selected");
      return false;
    }

    # delete only, if id given
    $result = $dbInst->query("delete from ".$dbInst->config['table_customer']." where id = '".$this->id."'");
    $dbInst->status($result[1],"d");

    $this->activate($this->id);
    // logging
    $this->logger->warn("deleted customer ".$this->company);
    return true;
  }
  /**                                                                 **
  *********************************************************************/
}

/***************************************************************************
 * $Log: customer.inc.php,v $
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
 * Revision 1.21  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.20  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
