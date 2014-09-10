###########################################################################
# $Source: /cvsroot/pmtool/pmtool/sql/table_update_1.2x.sql,v $
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
ALTER TABLE job ADD flags INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE project   ADD priority_id int(4) NOT NULL default '0';

###########################################################################
# $Log: table_update_1.2x.sql,v $
# Revision 1.3  2004/03/17 20:19:50  willuhn
# @N added priorities to projects
#
# Revision 1.2  2004/02/29 16:55:21  willuhn
# @B missing semicolon at the and of the line
#
# Revision 1.1  2004/02/28 19:46:42  znouza
# @N adding 'flags' type INT to table 'job' for new feature 'private jobs'
#
#
###########################################################################

