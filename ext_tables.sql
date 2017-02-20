#
# Table structure for table 'tx_wepublication_docs_authors_mm'
# ## WOP:[tables][1][fields][15][conf_relations_mm]
#
CREATE TABLE tx_wepublication_docs_authors_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_wepublication_docs_author_grp_external_mm'
# ## WOP:[tables][1][fields][12][conf_relations_mm]
#
CREATE TABLE tx_wepublication_docs_author_grp_external_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_wepublication_docs_author_grp_internal_mm'
# ## WOP:[tables][1][fields][13][conf_relations_mm]
#
CREATE TABLE tx_wepublication_docs_author_grp_internal_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_wepublication_docs'
#
CREATE TABLE tx_wepublication_docs (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	magazine tinytext NOT NULL,
	magazine2 int(11) DEFAULT '0' NOT NULL,
	year int(11) DEFAULT '0' NOT NULL,
	status int(11) DEFAULT '0' NOT NULL,
	issue varchar(10) DEFAULT '' NOT NULL,
	page_start varchar(255) DEFAULT '' NOT NULL,
	page_end varchar(255) DEFAULT '' NOT NULL,
	abstract text NOT NULL,
	cover blob NOT NULL,
	url_href text NOT NULL,
	url_title varchar(255) DEFAULT '' NOT NULL,
	doi varchar(55) DEFAULT '' NOT NULL,
	pacs varchar(255) DEFAULT '' NOT NULL,
	document_1_file blob NOT NULL,
	document_1_title varchar(255) DEFAULT '' NOT NULL,
	document_2_file blob NOT NULL,
	document_2_title varchar(255) DEFAULT '' NOT NULL,
	document_3_file blob NOT NULL,
	document_3_title varchar(255) DEFAULT '' NOT NULL,
	authors int(11) DEFAULT '0' NOT NULL,
	author_grp_external int(11) DEFAULT '0' NOT NULL,
	author_grp_internal int(11) DEFAULT '0' NOT NULL,
	comment text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_wepublication_magazine'
#
CREATE TABLE tx_wepublication_magazine (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    sorting int(10) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);