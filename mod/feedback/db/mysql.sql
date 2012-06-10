
#
# Tabellenstruktur f�r Tabelle `prefix_feedback`
#

CREATE TABLE `prefix_feedback` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `summary` text NOT NULL,
  `anonymous` int(1) NOT NULL default '1',
  `email_notification` int(1) NOT NULL default '1',
  `multiple_submit` int(1) NOT NULL default '0',
  `page_after_submit` text NOT NULL,
  `publish_stats` int(1) NOT NULL default '0',
  `timeopen` int(10) NOT NULL default '0',
  `timeclose` int(10) NOT NULL default '0',
  `timemodified` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`)
) TYPE=MyISAM COMMENT='feedback modul';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_template`
#

CREATE TABLE `prefix_feedback_template` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) NOT NULL default '0',
  `public` int(1) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`)
) TYPE=MyISAM COMMENT='templates of feedbackstructures';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_item`
#

CREATE TABLE `prefix_feedback_item` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `feedback` int(10) NOT NULL default '0',
  `template` int(10) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `presentation` text NOT NULL,
  `typ` varchar(255) NOT NULL default '0',
  `hasvalue` int(1) NOT NULL default '0',
  `position` int(3) NOT NULL default '0',
  `required` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `feedback` (`feedback`),
  KEY `template` (`template`)
) TYPE=MyISAM COMMENT='feedback items';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_completed`
#

CREATE TABLE `prefix_feedback_completed` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `feedback` int(10) NOT NULL default '0',
  `userid` int(10) NOT NULL default '0',
  `timemodified` int(10) NOT NULL default '0',
  `random_response` int(10) NOT NULL,
  `anonymous_response` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `feedback` (`feedback`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='filled out feedbacks';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_value`
#

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_completedtmp`
#

CREATE TABLE `prefix_feedback_completedtmp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `feedback` int(10) NOT NULL default '0',
  `userid` int(10) NOT NULL default '0',
  `guestid` varchar(255) NOT NULL default '',
  `timemodified` int(10) NOT NULL default '0',
  `random_response` int(10) NOT NULL,
  `anonymous_response` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `feedback` (`feedback`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='filled out feedbacks';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_value`
#

CREATE TABLE `prefix_feedback_value` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course_id` int(10) unsigned NOT NULL default '0',
  `item` int(10) NOT NULL default '0',
  `completed` int(10) NOT NULL default '0',
  `tmp_completed` int(10) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `item` (`item`),
  KEY `completed` (`completed`)
) TYPE=MyISAM COMMENT='feedback values';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_value`
#

CREATE TABLE `prefix_feedback_valuetmp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course_id` int(10) unsigned NOT NULL default '0',
  `item` int(10) NOT NULL default '0',
  `completed` int(10) NOT NULL default '0',
  `tmp_completed` int(10) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `item` (`item`),
  KEY `completed` (`completed`)
) TYPE=MyISAM COMMENT='feedback values';

#
# Tabellenstruktur f�r Tabelle `prefix_feedback_tracking`
#

CREATE TABLE `prefix_feedback_tracking` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `feedback` int(10) NOT NULL default '0',
  `completed` int(10) NOT NULL default '0',
  `tmp_completed` int(10) NOT NULL default '0',
  `count` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `feedback` (`feedback`),
  KEY `completed` (`completed`)
) TYPE=MyISAM COMMENT='feedback trackingdata';

CREATE TABLE `prefix_feedback_sitecourse_map` (
   `id` int(10) unsigned NOT NULL auto_increment,
   `feedbackid` int(10) NOT NULL default '0',
   `courseid` int(10) NOT NULL default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY `feebackid_courseid` (`feedbackid`, `courseid`)
) TYPE=MyISAM COMMENT='feedback sitecourse map';

