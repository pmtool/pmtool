###########################################################################
# $Source: /cvsroot/pmtool/pmtool/sql/table_update_1.2.1.sql,v $
# $Revision: 1.1 $
# $Date: 2004/03/17 20:19:50 $
# $Author: willuhn $
# $Locker:  $
# $State: Exp $
#
# Copyright (c) by willuhn.webdesign
# All rights reserved
#
###########################################################################

use pmtool;

ALTER TABLE project ADD priority_id int(4) NOT NULL default '0';

###########################################################################
# $Log: table_update_1.2.1.sql,v $
# Revision 1.1  2004/03/17 20:19:50  willuhn
# @N added priorities to projects
#
###########################################################################

