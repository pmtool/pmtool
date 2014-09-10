###########################################################################
# $Source: /cvsroot/pmtool/pmtool/sql/table_update_0.9.x.sql,v $
# $Revision: 1.5 $
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

CREATE TABLE groups (
   id int(4) unsigned NOT NULL auto_increment,
   name char(100) NOT NULL,
   PRIMARY KEY (id),
   UNIQUE name (name)
);

INSERT INTO groups (id, name) VALUES ( '995', 'manager');
INSERT INTO groups (id, name) VALUES ( '996', 'developer');
INSERT INTO groups (id, name) VALUES ( '997', 'customer');
INSERT INTO groups (id, name) VALUES ( '998', 'access');
INSERT INTO groups (id, name) VALUES ( '999', 'admin');

CREATE TABLE groups_user (
   id int(4) unsigned NOT NULL auto_increment,
   group_id int(4) DEFAULT '0' NOT NULL,
   user_id int(4) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   UNIQUE group_user (group_id, user_id)
);

INSERT INTO groups_user (id, group_id, user_id) VALUES ( '999', '999', '999');

CREATE TABLE log (
   id int(5) unsigned NOT NULL auto_increment,
   time timestamp(14),
   level varchar(50) NOT NULL,
   user varchar(100) NOT NULL,
   comment text NOT NULL,
   PRIMARY KEY (id)
);

CREATE TABLE report (
   id int(5) unsigned NOT NULL auto_increment,
   subject varchar(255) NOT NULL,
   xml mediumblob NOT NULL,
   user_id int(4) DEFAULT '0' NOT NULL,
   project_id int(4),
   created int(10),
   PRIMARY KEY (id)
);

CREATE TABLE request (
   id int(10) unsigned NOT NULL auto_increment,
   subject varchar(100) NOT NULL,
   body blob NOT NULL,
   time int(10) DEFAULT '0' NOT NULL,
   poster_id int(4) DEFAULT '0' NOT NULL,
   project_id int(4) DEFAULT '0' NOT NULL,
   priority_id int(4) DEFAULT '0' NOT NULL,
   type_id int(4) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
);

CREATE TABLE rights (
   id int(4) unsigned NOT NULL auto_increment,
   name char(100) NOT NULL,
   alert int(1) unsigned,
   PRIMARY KEY (id),
   UNIQUE name (name)
);

INSERT INTO rights (id, name, alert) VALUES ( '1', 'access.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '2', 'access.addRight', '1');
INSERT INTO rights (id, name, alert) VALUES ( '3', 'access.addUser', '1');
INSERT INTO rights (id, name, alert) VALUES ( '4', 'access.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '5', 'access.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '6', 'access.getNotRights', '0');
INSERT INTO rights (id, name, alert) VALUES ( '7', 'access.getNotUsers', '0');
INSERT INTO rights (id, name, alert) VALUES ( '8', 'access.getRightName', '0');
INSERT INTO rights (id, name, alert) VALUES ( '9', 'access.getRights', '0');
INSERT INTO rights (id, name, alert) VALUES ( '10', 'access.getUsers', '0');
INSERT INTO rights (id, name, alert) VALUES ( '11', 'access.hasRight', '0');
INSERT INTO rights (id, name, alert) VALUES ( '12', 'access.hasUser', '0');
INSERT INTO rights (id, name, alert) VALUES ( '13', 'access.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '14', 'access.removeRight', '1');
INSERT INTO rights (id, name, alert) VALUES ( '15', 'access.removeUser', '1');
INSERT INTO rights (id, name, alert) VALUES ( '16', 'access.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '17', 'customer', '0');
INSERT INTO rights (id, name, alert) VALUES ( '18', 'customer.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '19', 'customer.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '20', 'customer.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '21', 'customer.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '22', 'customer.setFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '23', 'customer.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '24', 'group', '0');
INSERT INTO rights (id, name, alert) VALUES ( '25', 'job', '0');
INSERT INTO rights (id, name, alert) VALUES ( '26', 'job.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '27', 'job.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '28', 'job.deleteByTask', '1');
INSERT INTO rights (id, name, alert) VALUES ( '29', 'job.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '30', 'job.getOpenJob', '0');
INSERT INTO rights (id, name, alert) VALUES ( '31', 'job.getSummary', '0');
INSERT INTO rights (id, name, alert) VALUES ( '32', 'job.isDone', '0');
INSERT INTO rights (id, name, alert) VALUES ( '33', 'job.setFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '34', 'job.start', '1');
INSERT INTO rights (id, name, alert) VALUES ( '35', 'job.stop', '1');
INSERT INTO rights (id, name, alert) VALUES ( '36', 'job.viewOther', '0');
INSERT INTO rights (id, name, alert) VALUES ( '37', 'login.updatePassword', '1');
INSERT INTO rights (id, name, alert) VALUES ( '38', 'mail.send', '0');
INSERT INTO rights (id, name, alert) VALUES ( '39', 'project', '0');
INSERT INTO rights (id, name, alert) VALUES ( '40', 'project.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '41', 'project.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '44', 'project.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '45', 'project.getStatusList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '46', 'project.getStatusName', '0');
INSERT INTO rights (id, name, alert) VALUES ( '48', 'project.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '49', 'project.setFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '50', 'project.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '51', 'report', '0');
INSERT INTO rights (id, name, alert) VALUES ( '52', 'report.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '53', 'report.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '54', 'report.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '55', 'report.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '56', 'report.setFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '57', 'report.viewOther', '0');
INSERT INTO rights (id, name, alert) VALUES ( '58', 'request', '0');
INSERT INTO rights (id, name, alert) VALUES ( '59', 'request.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '60', 'request.assignTo', '0');
INSERT INTO rights (id, name, alert) VALUES ( '61', 'request.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '62', 'request.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '63', 'request.getPriorityList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '64', 'request.getPriorityName', '0');
INSERT INTO rights (id, name, alert) VALUES ( '65', 'request.getPriorityStyle', '0');
INSERT INTO rights (id, name, alert) VALUES ( '66', 'request.getTypeList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '67', 'request.getTypeName', '0');
INSERT INTO rights (id, name, alert) VALUES ( '68', 'request.getTypeStyle', '0');
INSERT INTO rights (id, name, alert) VALUES ( '69', 'request.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '70', 'request.setFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '71', 'request.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '72', 'right', '0');
INSERT INTO rights (id, name, alert) VALUES ( '73', 'task.isDone', '0');
INSERT INTO rights (id, name, alert) VALUES ( '74', 'task', '0');
INSERT INTO rights (id, name, alert) VALUES ( '75', 'task.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '76', 'task.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '77', 'task.fillFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '78', 'task.getCosts', '0');
INSERT INTO rights (id, name, alert) VALUES ( '79', 'task.getCustomerCosts', '0');
INSERT INTO rights (id, name, alert) VALUES ( '80', 'task.getCustomerSummary', '0');
INSERT INTO rights (id, name, alert) VALUES ( '81', 'task.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '82', 'task.getRate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '83', 'task.getStatusList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '84', 'task.getStatusName', '0');
INSERT INTO rights (id, name, alert) VALUES ( '85', 'task.getStatusStyle', '0');
INSERT INTO rights (id, name, alert) VALUES ( '86', 'task.getSummary', '0');
INSERT INTO rights (id, name, alert) VALUES ( '87', 'task.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '88', 'task.setFilter', '0');
INSERT INTO rights (id, name, alert) VALUES ( '89', 'task.start', '0');
INSERT INTO rights (id, name, alert) VALUES ( '90', 'task.stop', '0');
INSERT INTO rights (id, name, alert) VALUES ( '91', 'task.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '92', 'task.viewOther', '0');
INSERT INTO rights (id, name, alert) VALUES ( '93', 'user', '0');
INSERT INTO rights (id, name, alert) VALUES ( '94', 'user.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '95', 'user.getList', '0');
INSERT INTO rights (id, name, alert) VALUES ( '96', 'user.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '97', 'user.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '98', 'attachment.activate', '0');
INSERT INTO rights (id, name, alert) VALUES ( '99', 'attachment.insert', '1');
INSERT INTO rights (id, name, alert) VALUES ( '100', 'attachment.delete', '1');
INSERT INTO rights (id, name, alert) VALUES ( '101', 'attachment.getSize', '0');
INSERT INTO rights (id, name, alert) VALUES ( '102', 'attachment.update', '1');
INSERT INTO rights (id, name, alert) VALUES ( '103', 'task.isAvailable', '0');
INSERT INTO rights (id, name, alert) VALUES ( '104', 'task.fixedPrice', '0');
INSERT INTO rights (id, name, alert) VALUES ( '105', 'task.dumpXml', '0');
INSERT INTO rights (id, name, alert) VALUES ( '106', 'job.dumpXml', '0');

CREATE TABLE rights_groups (
   id int(4) unsigned NOT NULL auto_increment,
   group_id int(4) DEFAULT '0' NOT NULL,
   right_id int(4) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   UNIQUE right_group (right_id, group_id)
);

INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '1', '998', '1');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '2', '998', '2');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '3', '998', '3');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '4', '998', '4');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '5', '998', '5');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '6', '998', '6');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '7', '998', '7');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '8', '998', '8');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '9', '998', '9');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '10', '998', '10');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '11', '998', '11');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '12', '998', '12');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '13', '998', '13');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '14', '998', '14');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '15', '998', '15');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '16', '998', '16');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '17', '998', '20');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '18', '998', '18');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '19', '998', '24');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '20', '998', '72');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '21', '998', '93');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '22', '998', '94');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '23', '998', '95');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '24', '998', '96');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '25', '998', '97');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '26', '999', '1');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '27', '999', '2');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '28', '999', '3');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '29', '999', '4');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '30', '999', '5');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '31', '999', '6');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '32', '999', '7');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '33', '999', '8');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '34', '999', '9');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '35', '999', '10');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '36', '999', '11');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '37', '999', '12');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '38', '999', '13');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '39', '999', '14');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '40', '999', '15');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '41', '999', '16');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '42', '999', '17');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '43', '999', '18');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '44', '999', '19');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '45', '999', '20');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '46', '999', '21');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '47', '999', '22');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '48', '999', '23');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '49', '999', '24');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '50', '999', '25');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '51', '999', '26');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '52', '999', '27');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '53', '999', '28');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '54', '999', '29');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '55', '999', '30');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '56', '999', '31');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '57', '999', '32');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '58', '999', '33');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '59', '999', '34');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '60', '999', '35');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '61', '999', '36');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '62', '999', '37');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '63', '999', '38');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '64', '999', '39');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '65', '999', '40');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '66', '999', '41');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '147', '996', '20');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '146', '996', '18');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '69', '999', '44');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '70', '999', '45');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '71', '999', '46');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '145', '996', '17');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '73', '999', '48');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '74', '999', '49');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '75', '999', '50');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '76', '999', '51');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '77', '999', '52');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '78', '999', '53');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '79', '999', '54');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '80', '999', '55');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '81', '999', '56');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '82', '999', '57');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '83', '999', '58');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '84', '999', '59');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '85', '999', '60');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '86', '999', '61');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '87', '999', '62');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '88', '999', '63');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '89', '999', '64');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '90', '999', '65');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '91', '999', '66');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '92', '999', '67');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '93', '999', '68');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '94', '999', '69');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '95', '999', '70');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '96', '999', '71');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '97', '999', '72');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '98', '999', '73');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '99', '999', '74');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '100', '999', '75');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '101', '999', '76');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '102', '999', '77');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '103', '999', '78');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '104', '999', '79');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '105', '999', '80');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '106', '999', '81');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '107', '999', '82');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '108', '999', '83');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '109', '999', '84');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '110', '999', '85');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '111', '999', '86');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '112', '999', '87');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '113', '999', '88');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '114', '999', '89');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '115', '999', '90');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '116', '999', '91');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '117', '999', '92');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '118', '999', '93');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '119', '999', '94');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '120', '999', '95');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '121', '999', '96');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '122', '999', '97');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '123', '997', '37');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '124', '997', '38');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '125', '997', '39');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '126', '997', '40');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '206', '995', '19');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '128', '997', '44');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '129', '997', '45');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '130', '997', '46');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '148', '996', '22');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '132', '997', '49');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '133', '997', '58');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '134', '997', '59');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '135', '997', '62');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '136', '997', '63');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '137', '997', '64');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '138', '997', '65');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '139', '997', '66');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '140', '997', '67');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '141', '997', '68');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '142', '997', '69');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '143', '997', '70');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '144', '997', '71');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '150', '996', '25');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '151', '996', '26');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '152', '996', '27');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '153', '996', '28');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '154', '996', '29');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '155', '996', '30');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '156', '996', '31');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '157', '996', '32');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '158', '996', '33');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '159', '996', '34');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '160', '996', '35');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '161', '996', '37');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '162', '996', '38');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '163', '996', '39');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '164', '996', '40');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '165', '996', '44');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '166', '996', '45');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '167', '996', '46');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '168', '996', '49');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '169', '996', '51');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '170', '996', '52');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '171', '996', '54');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '172', '996', '55');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '173', '996', '56');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '174', '996', '58');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '175', '996', '59');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '176', '996', '62');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '177', '996', '63');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '178', '996', '64');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '179', '996', '65');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '180', '996', '66');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '181', '996', '67');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '182', '996', '68');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '183', '996', '69');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '184', '996', '70');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '185', '996', '71');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '205', '995', '18');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '187', '996', '74');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '188', '996', '75');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '189', '996', '76');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '190', '996', '77');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '191', '996', '81');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '192', '996', '83');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '193', '996', '84');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '194', '996', '85');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '195', '996', '86');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '196', '996', '87');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '197', '996', '73');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '198', '996', '88');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '199', '996', '89');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '200', '996', '90');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '201', '996', '91');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '204', '995', '17');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '203', '996', '95');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '207', '995', '20');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '208', '995', '21');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '209', '995', '22');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '210', '995', '23');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '211', '995', '25');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '212', '995', '26');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '213', '995', '27');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '214', '995', '28');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '215', '995', '29');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '216', '995', '30');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '217', '995', '31');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '218', '995', '32');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '219', '995', '33');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '220', '995', '34');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '221', '995', '35');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '222', '995', '36');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '223', '995', '37');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '224', '995', '38');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '225', '995', '39');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '226', '995', '40');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '227', '995', '41');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '228', '995', '44');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '229', '995', '45');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '230', '995', '46');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '231', '995', '48');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '232', '995', '49');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '233', '995', '50');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '234', '995', '51');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '235', '995', '52');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '236', '995', '53');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '237', '995', '54');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '238', '995', '55');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '239', '995', '56');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '240', '995', '57');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '241', '995', '58');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '242', '995', '59');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '243', '995', '60');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '244', '995', '61');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '245', '995', '62');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '246', '995', '63');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '247', '995', '64');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '248', '995', '65');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '249', '995', '66');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '250', '995', '67');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '251', '995', '68');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '252', '995', '69');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '253', '995', '70');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '254', '995', '71');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '255', '995', '74');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '256', '995', '75');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '257', '995', '76');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '258', '995', '77');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '259', '995', '78');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '260', '995', '79');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '261', '995', '80');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '262', '995', '81');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '263', '995', '82');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '264', '995', '83');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '265', '995', '84');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '266', '995', '85');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '267', '995', '86');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '268', '995', '87');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '269', '995', '73');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '270', '995', '88');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '271', '995', '89');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '272', '995', '90');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '273', '995', '91');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '274', '995', '92');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '275', '995', '95');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '276', '995', '98');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '277', '995', '99');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '278', '995', '100');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '279', '995', '101');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '280', '995', '102');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '281', '996', '98');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '282', '996', '99');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '283', '996', '100');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '284', '996', '101');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '285', '996', '102');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '286', '997', '98');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '287', '997', '99');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '288', '997', '101');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '289', '999', '98');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '290', '999', '99');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '291', '999', '100');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '292', '999', '101');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '293', '999', '102');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '294', '999', '103');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '295', '997', '103');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '296', '996', '103');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '297', '995', '103');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '298', '999', '104');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '299', '997', '104');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '300', '996', '104');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '301', '995', '104');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '302', '999', '105');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '303', '997', '105');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '304', '996', '105');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '305', '995', '105');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '306', '999', '106');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '307', '997', '106');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '308', '996', '106');
INSERT INTO rights_groups (id, group_id, right_id) VALUES ( '309', '995', '106');


ALTER TABLE customer CHANGE zip zip CHAR (7) not null;
ALTER TABLE time CHANGE id id INT (10) UNSIGNED not null AUTO_INCREMENT;
ALTER TABLE time RENAME job;
ALTER TABLE project ADD rate CHAR (10) not null AFTER description;
ALTER TABLE project ADD budget CHAR (10) not null AFTER rate;
ALTER TABLE project ADD manager_id INT (4) not null AFTER customer_id;
ALTER TABLE project ADD UNIQUE(name);
ALTER TABLE task CHANGE taskpriority_id priority_id INT (1) DEFAULT '0' not null;
ALTER TABLE task CHANGE tasktype_id type_id INT (4) DEFAULT '0' not null;
ALTER TABLE task CHANGE taskstatus_id status_id INT (4) DEFAULT '0' not null;
ALTER TABLE task ADD mount_id INT (5) UNSIGNED DEFAULT '0' not null AFTER project_id;
ALTER TABLE task ADD finish INT (10) not null AFTER time;
ALTER TABLE task ADD plannedhours INT (5) UNSIGNED DEFAULT '0' not null AFTER finish;
ALTER TABLE task ADD fixedprice float(10) UNSIGNED DEFAULT '0' NOT NULL AFTER plannedhours;
ALTER TABLE tasktype CHANGE style style ENUM ('red','yellow','green','black','gray') DEFAULT 'black' not null;
ALTER TABLE user ADD rate CHAR (10) not null AFTER ip;
ALTER TABLE user DROP access;
ALTER TABLE user ADD customer_id CHAR (4) not null AFTER rate;
ALTER TABLE user ADD lang CHAR (5) not null AFTER ip;
INSERT INTO tasktype VALUES ( '4', 'todo', 'gray');
INSERT INTO user (id, username, password, name, email, ip, rate, customer_id) VALUES ( '999', 'newadmin', '', 'Administrator v1.0', 'root@localhost', '', '', '');

UPDATE projectstatus set name='tentative' where name='akquise';

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
# $Log: table_update_0.9.x.sql,v $
# Revision 1.5  2004/03/17 20:19:50  willuhn
# @N added priorities to projects
#
# Revision 1.4  2003/11/17 19:03:13  willuhn
# @N added payment fields to project table
#
# Revision 1.3  2003/11/05 20:31:34  willuhn
# *** empty log message ***
#
# Revision 1.2  2003/08/29 18:11:02  willuhn
# @C changed xml column in report table from blob to mediumblob
#
# Revision 1.1.1.1  2003/07/28 19:23:06  willuhn
# reimport
#
# Revision 1.20  2002/09/07 19:23:14  willuhn
# @N global commit for missing files
#
# Revision 1.19  2002/05/05 20:12:43  willuhn
# @N added feature "fixed price" for tasks
#
# Revision 1.18  2002/05/02 19:57:26  willuhn
# @N added field in task table "plannedhours"
#
# Revision 1.17  2002/05/02 19:51:32  willuhn
# @N task->isAvailable() checks if task is "in progress" or "request"
# @N link to parent task in taskdetails
# @N link to all child tasks in taskdetails
# @B in task hotlist
#
# Revision 1.16  2002/03/31 17:40:11  willuhn
# @N added right "attachment.update"
# @N attachments are now also available for requests
#
# Revision 1.15  2002/03/31 15:24:14  willuhn
# @N added permissions for attachments
#
# Revision 1.14  2002/03/30 19:24:12  willuhn
# @N added attachment code
#
# Revision 1.13  2002/02/09 19:38:28  willuhn
# @N added CVS log
# @N added french language file
#
#
###########################################################################

