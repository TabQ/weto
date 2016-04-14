DROP TABLE IF EXISTS weto_site;
CREATE TABLE weto_site (
  id tinyint(1) unsigned NOT NULL DEFAULT 1,
  seotitle varchar(255) NOT NULL DEFAULT '',
  keywords varchar(800) NOT NULL DEFAULT '',
  description varchar(1000) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO weto_site(seotitle, keywords, description) values('微兔社区', '', '');

DROP TABLE IF EXISTS weto_pretops;
CREATE TABLE weto_pretops (
  id tinyint(3) unsigned  NOT NULL AUTO_INCREMENT,
  title char(255) NOT NULL DEFAULT '',
  message mediumtext NOT NULL,
  time int(10) unsigned NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned  NOT NULL DEFAULT 0,
  username char(16) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO weto_pretops(title, message) values('', '');

DROP TABLE IF EXISTS weto_users;
CREATE TABLE weto_users (
  id mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  username char(16) NOT NULL DEFAULT '',
  password char(32) NOT NULL DEFAULT '',
  email char(40) NOT NULL DEFAULT '',
  avatar tinyint(2) NOT NULL DEFAULT 0,
  nickname char(50) NOT NULL DEFAULT '',
  forbidden tinyint(1) NOT NULL DEFAULT 0,
  forbiddentime int(10) unsigned NOT NULL DEFAULT 0,
  gender tinyint(1) NOT NULL DEFAULT 0,
  groupid smallint(5) unsigned NOT NULL DEFAULT 0,
  regip int(11) unsigned NOT NULL DEFAULT 0,
  regdate int(10) unsigned NOT NULL DEFAULT 0,
  lastip int(11) unsigned NOT NULL DEFAULT 0,
  lastvisit int(10) unsigned NOT NULL DEFAULT 0,
  articles mediumint(8) unsigned NOT NULL DEFAULT 0,
  posts mediumint(8) unsigned NOT NULL DEFAULT 0,
  digests smallint(6) unsigned NOT NULL DEFAULT 0,
  credits int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY username (username),
  UNIQUE KEY email(email),
  KEY groupid (groupid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_action;
CREATE TABLE weto_action (
  id mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  type enum('delete', 'top', 'notop', 'digest', 'nodigest') NOT NULL DEFAULT 'delete',
  aid int(10) unsigned NOT NULL DEFAULT 0,
  pid int(10) unsigned NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  time int(10) unsigned NOT NULL DEFAULT 0,
  isadmin tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_blocks;
CREATE TABLE weto_blocks (
  id tinyint(3) unsigned  NOT NULL AUTO_INCREMENT,
  blockname char(16) NOT NULL DEFAULT '',
  rank tinyint(3) unsigned NOT NULL DEFAULT 0,
  seotitle varchar(255) NOT NULL DEFAULT '',
  keywords varchar(800) NOT NULL DEFAULT '',
  description varchar(1000) NOT NULL DEFAULT '',
  closed tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY blockname (blockname)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_preforums;
CREATE TABLE weto_preforums (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  bid tinyint(3) unsigned  NOT NULL DEFAULT 0,
  name char(30) NOT NULL DEFAULT '',
  proposer char(16) NOT NULL DEFAULT '',
  time int(10) unsigned NOT NULL DEFAULT 0,
  seotitle varchar(255) NOT NULL DEFAULT '',
  keywords varchar(800) NOT NULL DEFAULT '',
  description varchar(1000) NOT NULL DEFAULT '',
  overdue tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY name_proposer (name, proposer)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_supporters;
CREATE TABLE weto_supporters (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  pfid smallint(5) unsigned NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  username char(16) NOT NULL DEFAULT '',
  time int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY pfid_uid (pfid, uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_preadmins;
CREATE TABLE weto_preadmins (
  id mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  fid smallint(5) unsigned NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  username char(16) NOT NULL DEFAULT '',
  time int(10) unsigned NOT NULL DEFAULT 0,
  overdue tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY fid_uid (fid, uid),
  UNIQUE KEY fid_username (fid, username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_support_preadmins;
CREATE TABLE weto_support_preadmins (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  paid mediumint(8) unsigned  NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  username char(16) NOT NULL DEFAULT '',
  time int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY paid_uid (paid, uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_forums;
CREATE TABLE weto_forums (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  bid tinyint(3) unsigned  NOT NULL DEFAULT 0,
  forumname char(30) NOT NULL DEFAULT '',
  articles int(10) unsigned NOT NULL DEFAULT 0,
  posts int(10) unsigned NOT NULL DEFAULT 0,
  rank tinyint(3) unsigned NOT NULL DEFAULT 0,
  seotitle varchar(255) NOT NULL DEFAULT '',
  keywords varchar(800) NOT NULL DEFAULT '',
  description varchar(1000) NOT NULL DEFAULT '',
  closed tinyint(1) unsigned NOT NULL DEFAULT 0,
  adminlist char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY forumname (forumname)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_articles;
CREATE TABLE weto_articles (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  fid smallint(5) unsigned NOT NULL DEFAULT 0,
  title char(255) NOT NULL DEFAULT '',
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  author char(15) NOT NULL DEFAULT '',
  views int(10) unsigned NOT NULL DEFAULT 0,
  replies int(10) unsigned NOT NULL DEFAULT 0,
  top tinyint(1) NOT NULL DEFAULT 0,
  digest tinyint(1) NOT NULL DEFAULT 0,
  attachment tinyint(1) NOT NULL DEFAULT 0,
  deleted tinyint(1) NOT NULL DEFAULT 0,
  createtime int(10) unsigned NOT NULL DEFAULT 0,
  edittime int(10) unsigned NOT NULL DEFAULT 0,
  lastpost int(10) unsigned NOT NULL DEFAULT 0,
  lastposter char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  KEY digest (digest),
  KEY top (top)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_posts;
CREATE TABLE weto_posts (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  aid int(10) unsigned NOT NULL DEFAULT 0,
  firstpost tinyint(1) NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  author char(15) NOT NULL DEFAULT '',
  message mediumtext NOT NULL,
  createtime int(10) unsigned NOT NULL DEFAULT 0,
  edittime int(10) unsigned NOT NULL DEFAULT 0,
  deleted tinyint(1) NOT NULL DEFAULT 0,
  attachment tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY uid (uid),
  KEY createtime (createtime),
  KEY deleted (deleted),
  KEY aid_firstpost (aid, firstpost)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_attachments;
CREATE TABLE weto_attachments (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  aid int(10) unsigned NOT NULL DEFAULT 0,
  pid int(10) unsigned NOT NULL DEFAULT 0,
  filename char(100) NOT NULL DEFAULT '',
  path varchar(4096) NOT NULL DEFAULT '',
  description char(100) NOT NULL DEFAULT '',
  filesize int(10) unsigned NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY aid (aid),
  KEY pid (pid, id),
  KEY uid (uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_goods_detail;
CREATE TABLE weto_goods_detail (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  nid tinyint(3) unsigned  NOT NULL DEFAULT 0,
  rid tinyint(3) unsigned  NOT NULL DEFAULT 0,
  unid tinyint(3) unsigned  NOT NULL DEFAULT 0,
  price float(10,2) unsigned NOT NULL DEFAULT 0.00,
  time int(10) unsigned NOT NULL DEFAULT 0,
  uid mediumint(8) unsigned NOT NULL DEFAULT 0,
  username char(16) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_goods_cate;
CREATE TABLE weto_goods_cate (
  id tinyint(3) unsigned  NOT NULL AUTO_INCREMENT,
  name varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO weto_goods_cate(name) values('农副产品');

DROP TABLE IF EXISTS weto_goods_name;
CREATE TABLE weto_goods_name (
  id tinyint(3) unsigned  NOT NULL AUTO_INCREMENT,
  cid tinyint(3) unsigned  NOT NULL DEFAULT 0,
  name varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY cid_name (cid, name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS weto_goods_rate;
CREATE TABLE weto_goods_rate (
  id tinyint(3) unsigned  NOT NULL AUTO_INCREMENT,
  name varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO weto_goods_rate(name) values('新鲜一级');

DROP TABLE IF EXISTS weto_goods_unit;
CREATE TABLE weto_goods_unit (
  id tinyint(3) unsigned  NOT NULL AUTO_INCREMENT,
  name varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO weto_goods_unit(name) values('斤');

DROP TABLE IF EXISTS weto_goods_site;
CREATE TABLE weto_goods_site (
  id mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  name varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;