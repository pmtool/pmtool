###########################################################################
# $Source: /cvsroot/pmtool/pmtool/sql/table_update_1.1x.sql,v $
# $Revision: 1.3 $
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

## was INT before
ALTER TABLE task CHANGE fixedprice fixedprice FLOAT(10) UNSIGNED DEFAULT 0 NOT NULL;

ALTER TABLE project
  ADD paid TINYINT( 1 ) DEFAULT '0',
  ADD payment_date DATE DEFAULT NULL,
  ADD consignment_date DATE DEFAULT NULL,
  ADD priority_id int(4) NOT NULL default '0';


###########################################################################
# $Log: table_update_1.1x.sql,v $
# Revision 1.3  2004/03/17 20:19:50  willuhn
# @N added priorities to projects
#
# Revision 1.2  2003/11/17 19:03:13  willuhn
# @N added payment fields to project table
#
# Revision 1.1  2003/11/05 20:31:34  willuhn
# *** empty log message ***
#
###########################################################################

