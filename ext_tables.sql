#
# Table structure for table 'tx_auxnewsmailer_newscat_fe_groups_mm'
# 
#
CREATE TABLE tx_auxnewsmailer_newscat_fe_groups_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'fe_groups'
#
CREATE TABLE fe_groups (
	tx_auxnewsmailer_newscat int(11) unsigned DEFAULT '0' NOT NULL
);


#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_auxnewsmailer_newsletter int(11) unsigned DEFAULT '0' NOT NULL
	tx_auxnewsmailer_html int(11) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tt_news'
#
CREATE TABLE tt_news (
	tx_auxnewsmailer_scanstate int(11) unsigned DEFAULT '0' NOT NULL
);


#
# Table structure for table 'tx_auxnewsmailer_usercat'
#
CREATE TABLE tx_auxnewsmailer_usercat (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	iduser int(11) unsigned DEFAULT '0' NOT NULL,
	mailcat int(11) unsigned DEFAULT '0' NOT NULL,
	domail tinyint(3) unsigned DEFAULT '0' NOT NULL,
	dosms tinyint(3) unsigned DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_auxnewsmailer_msglist'
#
CREATE TABLE tx_auxnewsmailer_msglist (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	msgtype int(11) DEFAULT '0' NOT NULL,
	idctrl int(11) DEFAULT '0' NOT NULL,
	plaintext mediumtext NOT NULL,
	htmltext mediumtext NOT NULL,
	resources mediumtext NOT NULL,
	image varchar(128) DEFAULT '' NOT NULL,
	msgsignature varchar(128) DEFAULT '' NOT NULL,
	state int(11) DEFAULT '0' NOT NULL,	
	PRIMARY KEY (uid),
	
);

#
# Table structure for table 'tx_auxnewsmailer_usrmsg'
#
CREATE TABLE tx_auxnewsmailer_usrmsg (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	iduser int(11) unsigned DEFAULT '0' NOT NULL,
	idmsg int(11) unsigned DEFAULT '0' NOT NULL,
	state int(11) DEFAULT '0' NOT NULL,	
	PRIMARY KEY (uid),
);

#
# Table structure for table 'tx_auxnewsmailer_maillist'
#
CREATE TABLE tx_auxnewsmailer_maillist (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	iduser int(11) unsigned DEFAULT '0' NOT NULL,
	idnews int(11) unsigned DEFAULT '0' NOT NULL,
	idctrl int(11) unsigned DEFAULT '0' NOT NULL,
	state int(11) DEFAULT '0' NOT NULL,
	msgtype int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_auxnewsmailer_control'
#
CREATE TABLE tx_auxnewsmailer_control (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	template blob NOT NULL,
	stylesheet blob NOT NULL,
	userpage blob NOT NULL,
	organisation tinytext NOT NULL,
	name tinytext NOT NULL,
	subject tinytext NOT NULL,
	returnmail tinytext NOT NULL,
	sendtime int(11) DEFAULT '0' NOT NULL,
	duration blob NOT NULL,
	folders blob NOT NULL,
	lasttime int(11) DEFAULT '0' NOT NULL,
	usecat tinyint(4) DEFAULT '0' NOT NULL,
	userpid int(11) DEFAULT '0' NOT NULL,
	image tinytext NOT NULL,
	imagew int(11) DEFAULT '0' NOT NULL,
	imageh int(11) DEFAULT '0' NOT NULL,
	listimagew int(11) DEFAULT '0' NOT NULL,
	listimageh int(11) DEFAULT '0' NOT NULL,
	autoscan tinyint(4) DEFAULT '0' NOT NULL,
	pretext blob NOT NULL,
	posttext blob NOT NULL,
	dateformat tinytext NOT NULL,
	timeformat tinytext NOT NULL,
	newspage blob NOT NULL,
	showitems blob NOT NULL,
	lang tinytext NOT NULL,
	orgdomain tinytext NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);