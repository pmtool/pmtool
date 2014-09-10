###########################################################################
# $Source: /cvsroot/pmtool/pmtool/sql/table_update_1.0.x.sql,v $
# $Revision: 1.4 $
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

ALTER TABLE request ADD poster_id INT (4) not null AFTER body;
ALTER TABLE request ADD time INT (10) not null AFTER body;
ALTER TABLE user ADD lang CHAR (5) not null AFTER ip;
ALTER TABLE project ADD budget CHAR (10) not null AFTER rate;
ALTER TABLE task ADD mount_id INT (5) UNSIGNED DEFAULT '0' not null AFTER project_id;
ALTER TABLE task ADD finish float(10) not null AFTER time;
ALTER TABLE task ADD plannedhours INT (5) UNSIGNED DEFAULT '0' not null AFTER finish;
ALTER TABLE task ADD fixedprice INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER plannedhours;
UPDATE projectstatus set name='tentative' where name='akquise';

INSERT INTO rights (id, name, alert) VALUES ( '98', 'attachment.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '99', 'attachment.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '100', 'attachment.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '101', 'attachment.getSize', '0');
INSERT INTO rights (id, name, alert) VALUES ( '102', 'attachment.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '103', 'task.isAvailable', '0');
INSERT INTO rights (id, name, alert) VALUES ( '104', 'task.fixedPrice', '0');
INSERT INTO rights (id, name, alert) VALUES ( '105', 'task.dumpXml', '0');
INSERT INTO rights (id, name, alert) VALUES ( '106', 'job.dumpXml', '0');

CREATE TABLE attachment (
   id int(10) unsigned NOT NULL auto_increment,
   name TEXT NOT NULL,
   task_id int(5) NOT NULL,
   created int(10),
   PRIMARY KEY (id)
);

ALTER TABLE project
  ADD paid TINYINT( 1 ) DEFAULT '0',
  ADD payment_date DATE DEFAULT NULL,
  ADD consignment_date DATE DEFAULT NULL,
  ADD priority_id int(4) NOT NULL default '0';


###########################################################################
# $Log: table_update_1.0.x.sql,v $
# Revision 1.4  2004/03/17 20:19:50  willuhn
# @N added priorities to projects
#
# Revision 1.3  2003/11/17 19:03:13  willuhn
# @N added payment fields to project table
#
# Revision 1.2  2003/11/05 20:31:34  willuhn
# *** empty log message ***
#
# Revision 1.1.1.1  2003/07/28 19:23:02  willuhn
# reimport
#
# Revision 1.16  2002/09/07 19:23:14  willuhn
# @N global commit for missing files
#
# Revision 1.15  2002/05/05 20:12:43  willuhn
# @N added feature "fixed price" for tasks
#
# Revision 1.14  2002/05/02 19:57:26  willuhn
# @N added field in task table "plannedhours"
#
# Revision 1.13  2002/05/02 19:51:32  willuhn
# @N task->isAvailable() checks if task is "in progress" or "request"
# @N link to parent task in taskdetails
# @N link to all child tasks in taskdetails
# @B in task hotlist
#
# Revision 1.12  2002/03/31 17:40:11  willuhn
# @N added right "attachment.update"
# @N attachments are now also available for requests
#
# Revision 1.11  2002/03/31 15:24:15  willuhn
# @N added permissions for attachments
#
# Revision 1.10  2002/03/30 19:24:12  willuhn
# @N added attachment code
#
# Revision 1.9  2002/02/09 19:38:28  willuhn
# @N added CVS log
# @N added french language file
#
#
###########################################################################

