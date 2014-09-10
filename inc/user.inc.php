<?PHP

/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/user.inc.php,v $
 * $Revision: 1.6 $
 * $Date: 2005/02/21 16:21:34 $
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

 if (!class_exists("user")) {
    
    /**
    * represents a user in pmtool.
    */
    class user {
    
        // instance vars
        
        /**
        * id of user.
        * @private
        */
        var $id;
        
        /**
        * username.
        * @private
        */
        var $username;
        
        /**
        * password.
        * @private
        */
        var $password;
        
        /**
        * name.
        * @private
        */
        var $name;
        
        /**
        * email.
        * @private
        */
        var $email;
        
        /**
        * client ip.
        * @private
        */
        var $ip;
        
        /**
        * language code.
        * @private
        */
        var $language;
        
        /**
        * rate per hour.
        * @private
        */
        var $rate;
        
        /**
        * customer id if user is assigned to one.
        * @private
        */
        var $customerId;
        
        /**
        * performance speedup - this array contains a list of all
        * rights, the user inherits. will be initialized on activation.
        * @private
        */
        var $rights = array();
        
        /**
        * instance of system logger.
        * @private
        */
        var $logger;
    
        /**
        * creates a new user object.
        * @public
        * @return new user object.
        * @param id String id of this user.
        */
        function user($id="-1")
        {
            global $loginInst;
    
            if ($id != "-1") {
                $this->activate($id);
            }
    
            $this->logger = new logger();
    
            $this->logger->setUser($loginInst);
    
            $this->logger->setToken("USER");
            
        }   
        
        /**
        * load user from database.
        * @public
        * @return void
        * @param id String id of this user.
        */
        function activate($id)
        {
            global $dbInst, $loginInst;
        
            // no right check here: this function is also needed for login
        
            $query = "SELECT * FROM " . $dbInst->config['table_user'] . " WHERE id = '" . $id . "'";
            
            $result = $dbInst->query($query);
            
            $row = $dbInst->fetchArray($result[0]);
            
            $this->id         = $row['id'];
            $this->username   = $row['username'];
            $this->name       = $row['name'];
            $this->email      = $row['email'];
            $this->ip         = $row['ip'];
            $this->language   = $row['lang'];
            $this->rate       = $row['rate'];
            $this->customerId = $row['customer_id'];
            
        }
    
        /**
        * loads the users rights into an internal array.
        * @private
        * @return void.
        */
        function loadRights()
        {
            // initialize only once
            if (sizeof($this->rights) < 2) {
            
                global $dbInst;
                
                $query = "SELECT DISTINCT " . $dbInst->config['table_rights'] . ".name AS name FROM ".
                        $dbInst->config['table_user'] . ",".
                        $dbInst->config['table_groups'] . ",".
                        $dbInst->config['table_rights'] . ",".
                        $dbInst->config['table_groups_user'] . ",".
                        $dbInst->config['table_rights_groups'] . " WHERE ".
                        $dbInst->config['table_user'] . ".id = " . $dbInst->config['table_groups_user'] . ".user_id AND ".
                        $dbInst->config['table_groups'] . ".id = " . $dbInst->config['table_groups_user'] . ".group_id AND ".
                        $dbInst->config['table_groups'] . ".id = " . $dbInst->config['table_rights_groups'] . ".group_id AND ".
                        $dbInst->config['table_rights'] . ".id = " . $dbInst->config['table_rights_groups'] . ".right_id AND ".
                        $dbInst->config['table_user'] . ".id = '" . $this->id . "'";
            
                $result = $dbInst->query($query);
    
                while($element = $dbInst->fetchArray($result[0])) {
                    $this->rights[] = $element['name'];
                }
                
            }
            
        }
    
        /**
        * checks if the user has the given right.
        * @public
        * @return boolean tru if the user has the right. Otherwise false.
        * @param string $right name of the right.
        */
        function hasAccess($right)
        {
            global $dbInst, $toolInst;
        
            while ($element = current($this->rights)) {
    
                if ($element == $right) {
                    reset($this->rights);
                    return true;
                }
                
                next($this->rights);
                
            }
    
            $alertQuery = "SELECT alert FROM " . $dbInst->config['table_rights'] . " WHERE name = '" . $right . "'"; 
    
            if ($dbInst->getValue($alertQuery) == 1) {
                $toolInst->errorStatus("acces failed in right &quot;" . $right . "&quot;. Ask your administrator");
            }
    
            reset($this->rights);
            
            return false;
            
        }
    
        /**
        * set all members en bloc with the given array.
        * @public
        * @return void.
        * @param array with the same keys as the member names.
        */
        function fill($array)
        {
        
            $this->username       = $array['username'];
            $this->password       = $array['password'];
            $this->name           = $array['name'];
            $this->email          = $array['email'];
            $this->ip             = $array['ip'];
            $this->language       = $array['language'];
            $this->rate           = $array['rate'];
            $this->customerId     = $array['customerid'];
        }
    
        /**
        * makes all members of this object empty.
        * @public
        * @return void.
        */
        function clear()
        {
            $this->id             = "";
            $this->username       = "";
            $this->password       = "";
            $this->name           = "";
            $this->email          = "";
            $this->ip             = "";
            $this->ip             = "";
            $this->rate           = "";
            $this->customerId     = "";
            $this->language       = "";
        }
    
        /**
        * returns an array containing the found user ids from the database.
        * @public
        * @return array containing user ids.
        * @param order field to order by.
        * @param desc "ASC" for ascending order or "DESC".
        */
        function getList($order="username", $desc="ASC")
        {
            global $dbInst, $loginInst;
        
            if (!$loginInst->hasAccess("user.getList")) return false;
        
            $array = array();
    
            $query = "SELECT id FROM " . $dbInst->config['table_user'] . " ORDER BY " . $order . " " . $desc;
        
            $result = $dbInst->query($query);
            
            while($row = $dbInst->fetchArray($result[0])) {
                $array[] = $row['id'];
            }
            
            return $array;
        }
    
        /**
        * checks if the user is assigned to a customer.
        * @public
        * @return true if the user is assigned to a customer.
        */
        function isCustomer()
        {
            global $dbInst;
            
            if ($dbInst->getValue("SELECT customer_id
                                    FROM " . $dbInst->config['table_user'] . "
                                    WHERE id = '" . $this->id . "'")) {
                return true;
            } else {
                return false;
            }
        }
    
        /**
        * stores the current user as a new record in the database.
        * @public
        * @return id if insert was successful, otherwise false.
        */
        function insert()
        {
            global $dbInst, $loginInst;
        
            if (!$loginInst->hasAccess("user.insert")) return false;
        
            if (!$this->check()) return false;
        
            $query = "INSERT INTO " . $dbInst->config['table_user'] . " " .
                        "(username, password, name, email, ip, lang, rate, customer_id) " .
                        "VALUES (".
                            "'" . $this->username."', " .
                            "PASSWORD('" . $this->password . "'), " .
                            "'" . $this->name . "', " .
                            "'" . $this->email . "', " .
                            "'" . $this->ip . "', " .
                            "'" . $this->language . "', " .
                            "'" . $this->rate . "'," .
                            "'" . $this->customerId . "')";
        
            $result = $dbInst->query($query);
            
            $id = $dbInst->getValue("SELECT DISTINCT last_insert_id() FROM " . $dbInst->config['table_user']);
    
            $dbInst->status($result[1], "i");
            
            if ($result[1] == 1 || $result[1] == 0) {
                $this->logger->info("added user " . $this->username);
                return $id;
            } else {
                return false;
            }
        }
    
        /**
        * updates the current user into the database.
        * @public
        * @return true on success.
        */
        function update()
        {
            global $dbInst, $loginInst;
        
            if (!$loginInst->hasAccess("user.update")) return false;
        
            if (!$this->check()) return false;
        
            $query = "UPDATE " . $dbInst->config['table_user'] . " SET ".
                    "username = '" . $this->username . "'," .
                    "name = '" . $this->name . "'," .
                    "email = '" . $this->email . "'," .
                    "ip = '" . $this->ip . "'," .
                    "lang = '" . $this->language . "'," .
                    "rate = '" . $this->rate . "'," .
                    "customer_id = '" . $this->customerId . "'";
        
            // change password only, if submitted
            if ($this->password) {
                $query .= ", password = PASSWORD('" . $this->password . "')";
            }
    
            $query .= " WHERE id = '" . $this->id . "'";
        
            $result = $dbInst->query($query);
            
            $dbInst->status($result[1], "u");
    
            if ($result[1] == 1 || $result[1] == 0) {
                $this->logger->info("changed user " . $this->username);        
                return true;
            } else {
                return false;
            }
            
        }
    
        /**
        * checks if the user can be stored in the database.
        * @public
        * @return true if the user can be stored.
        */
        function check()
        {
            global $dbInst, $toolInst;
        
            if (! $this->username) {
                $toolInst->errorStatus("no username name given");
                return false;
            }
            
            if ($this->rate && !$toolInst->checkFloat($this->rate)) {
                $toolInst->errorStatus("please enter a valid number as rate");
                return false;
            }
            
            if (! $this->name) {
                $toolInst->errorStatus("no name given");
                return false;
            }
            
            if ($config['automail'] && !$this->email) {
                $toolInst->errorStatus("you need to set an email address if &quot;automail&quot; (cfg/config.inc.php) is activated");
                return false;
            }
            
            if (!$toolInst->checkInt($this->customerId)) {
                $this->customerId = "";
            }
            
            return true;
        }
    
        /**
        * deletes the current user from the database.
        * @public
        * @return true on success.
        */
        function delete()
        {
            global $dbInst, $loginInst, $toolInst;
            
            if (!$loginInst->hasAccess("user.delete")) return false;
            
            $strError = "dependency check failed:";
            
            # check dependencies
            if ($dbInst->getValue("SELECT id
                                    FROM " . $dbInst->config['table_task'] . "
                                    WHERE user_id = '" . $this->id . "'")) {
            
                $toolInst->errorStatus($strError . " there are existing tasks assigned to this user");
                return false;
            }
            
            if ($dbInst->getValue("SELECT id
                                    FROM " . $dbInst->config['table_project'] . "
                                    WHERE manager_id = '" . $this->id . "'")) {
            
                $toolInst->errorStatus($strError . " there are existing projects where this user is manager");
                return false;
            }
            
            if ($dbInst->getValue("SELECT id
                                    FROM " . $dbInst->config['table_groups_user'] . "
                                    WHERE user_id = '" . $this->id . "'")) {
            
                $toolInst->errorStatus($strError . "there are existing groups where this user is a member of");
                return false;
            }
            
            if (!$this->id) {
                $toolInst->errorStatus("no record selected");
                return false;
            } else {
                # delete only, if id given
                $this->activate($this->id);
    
                $query = "DELETE FROM " . $dbInst->config['table_user'] . " WHERE id = '" . $this->id . "'";
                
                $result = $dbInst->query($query);
                                            
                $dbInst->status($result[1], "d");
    
                $this->logger->warn("deleted user " . $this->username);
                
                return true;
            }
            
        }
    
        /**
        * returns an array containing the name of the groups, where the user is a member of.
        * @public
        * @return array of group names.
        * @param order field to order by.
        * @param desc "ASC" for ascending order or "DESC".
        */
        function getGroups($order="name", $desc="ASC")
        {
            global $dbInst,$loginInst;
            
            $query = "SELECT DISTINCT " .
                        $dbInst->config['table_groups'] . ".name AS name
                    FROM " .
                        $dbInst->config['table_user'] . ", " .
                        $dbInst->config['table_groups'] . ", " .
                        $dbInst->config['table_groups_user'] . "
                    WHERE " .
                        $dbInst->config['table_groups_user'] . ".user_id = " . $dbInst->config['table_user'] . ".id AND " .
                        $dbInst->config['table_groups_user'] . ".group_id = " . $dbInst->config['table_groups'] . ".id and " .
                        $dbInst->config['table_user'] . ".id = '" . $this->id . "'
                    ORDER BY " .
                        $dbInst->config['table_groups'] . ".name " . $desc;
            
            $result = $dbInst->query($query);
    
            $array = array();
    
            while($row = $dbInst->fetchArray($result[0])) {
                $array[] = $row['name'];
            }
            
            return $array;
        }
    
    }

}

/***************************************************************************
 * $Log: user.inc.php,v $
 * Revision 1.6  2005/02/21 16:21:34  matchboy
 * Added class_exists before class declaration.
 *
 * Revision 1.5  2005/02/21 15:34:39  matchboy
 * Initial cleanup of file. Haven't begun documenting, but cleaning up the
 * queries, so that they are easier to read and trying to reduce the length of
 * some of the lines of code. (less wrapping)
 *
 * Revision 1.4  2003/09/27 18:23:45  willuhn
 * *** empty log message ***
 *
 * Revision 1.3  2003/08/10 15:44:57  willuhn
 * @B fixed SF bug 602176
 * @D some api doc
 *
 * Revision 1.2  2003/07/28 21:05:37  willuhn
 * @N added some comments
 * @N added doxyfile
 *
 * Revision 1.1.1.1  2003/07/28 19:23:00  willuhn
 * reimport
 *
 * Revision 1.34  2002/11/04 19:23:11  willuhn
 * @N added string escaping for sql statements
 *
 * Revision 1.33  2002/06/04 21:24:04  willuhn
 * @N added debug method in logger
 *
 * Revision 1.32  2002/05/02 22:20:19  willuhn
 * @B order
 * @B array of rights was loaded everytime a user object was instanciated
 *
 * Revision 1.31  2002/04/03 23:19:02  willuhn
 * @N fixed a bug in language changing on user page
 *
 * Revision 1.30  2002/03/29 02:09:45  willuhn
 * @B bug in loading of rights
 *
 * Revision 1.29  2002/03/29 01:50:24  willuhn
 * @N merged template bill and joblist
 * @N performance speedups by caching frequently used values (rights,prios,types...)
 *
 * Revision 1.28  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
