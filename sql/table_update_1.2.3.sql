###########################################################################
# $Source: /cvsroot/pmtool/pmtool/sql/table_update_1.2.3.sql,v $
# $Revision: 1.2 $
# $Date: 2005/02/20 17:45:29 $
# $Author: matchboy $
# $Locker:  $
# $State: Exp $
#
# Copyright (c) by Robby Russell
# All rights reserved
#
###########################################################################

use pmtool;

ALTER TABLE user CHANGE password password CHAR(80);

###########################################################################
# $Log: table_update_1.2.3.sql,v $
# Revision 1.2  2005/02/20 17:45:29  matchboy
# Initial release
#
#
###########################################################################

