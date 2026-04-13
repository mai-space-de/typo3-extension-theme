--
-- ext_tables.sql — mai_theme
-- Inline child-record tables for accordion/FAQ/steps, tabs, slider, timeline.
--

CREATE TABLE tx_maitheme_accordion_item (
    uid         int(11) NOT NULL auto_increment,
    pid         int(11) NOT NULL DEFAULT 0,
    parent_uid  int(11) NOT NULL DEFAULT 0,
    sort        int(11) NOT NULL DEFAULT 0,
    hidden      tinyint(4) NOT NULL DEFAULT 0,
    deleted     tinyint(4) NOT NULL DEFAULT 0,

    sys_language_uid   int(11) NOT NULL DEFAULT 0,
    l10n_parent        int(11) NOT NULL DEFAULT 0,
    l10n_diffsource    mediumblob,

    question    varchar(1024) NOT NULL DEFAULT '',
    answer      mediumtext,

    PRIMARY KEY (uid),
    KEY parent_uid (parent_uid),
    KEY language (l10n_parent, sys_language_uid)
);

CREATE TABLE tx_maitheme_tab_item (
    uid         int(11) NOT NULL auto_increment,
    pid         int(11) NOT NULL DEFAULT 0,
    parent_uid  int(11) NOT NULL DEFAULT 0,
    sort        int(11) NOT NULL DEFAULT 0,
    hidden      tinyint(4) NOT NULL DEFAULT 0,
    deleted     tinyint(4) NOT NULL DEFAULT 0,

    sys_language_uid   int(11) NOT NULL DEFAULT 0,
    l10n_parent        int(11) NOT NULL DEFAULT 0,
    l10n_diffsource    mediumblob,

    tab_title   varchar(512) NOT NULL DEFAULT '',
    content     mediumtext,

    PRIMARY KEY (uid),
    KEY parent_uid (parent_uid),
    KEY language (l10n_parent, sys_language_uid)
);

CREATE TABLE tx_maitheme_slider_item (
    uid         int(11) NOT NULL auto_increment,
    pid         int(11) NOT NULL DEFAULT 0,
    parent_uid  int(11) NOT NULL DEFAULT 0,
    sort        int(11) NOT NULL DEFAULT 0,
    hidden      tinyint(4) NOT NULL DEFAULT 0,
    deleted     tinyint(4) NOT NULL DEFAULT 0,

    sys_language_uid   int(11) NOT NULL DEFAULT 0,
    l10n_parent        int(11) NOT NULL DEFAULT 0,
    l10n_diffsource    mediumblob,

    headline    varchar(1024) NOT NULL DEFAULT '',
    bodytext    mediumtext,
    link        varchar(2048) NOT NULL DEFAULT '',
    image       int(11) NOT NULL DEFAULT 0,

    PRIMARY KEY (uid),
    KEY parent_uid (parent_uid),
    KEY language (l10n_parent, sys_language_uid)
);

CREATE TABLE tx_maitheme_timeline_item (
    uid         int(11) NOT NULL auto_increment,
    pid         int(11) NOT NULL DEFAULT 0,
    parent_uid  int(11) NOT NULL DEFAULT 0,
    sort        int(11) NOT NULL DEFAULT 0,
    hidden      tinyint(4) NOT NULL DEFAULT 0,
    deleted     tinyint(4) NOT NULL DEFAULT 0,

    sys_language_uid   int(11) NOT NULL DEFAULT 0,
    l10n_parent        int(11) NOT NULL DEFAULT 0,
    l10n_diffsource    mediumblob,

    event_date  varchar(255) NOT NULL DEFAULT '',
    title       varchar(1024) NOT NULL DEFAULT '',
    description mediumtext,
    image       int(11) NOT NULL DEFAULT 0,

    PRIMARY KEY (uid),
    KEY parent_uid (parent_uid),
    KEY language (l10n_parent, sys_language_uid)
);
