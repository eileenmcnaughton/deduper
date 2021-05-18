-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_mergeconflict`;
DROP TABLE IF EXISTS `civicrm_contact_name_pair_family`;
DROP TABLE IF EXISTS `civicrm_contact_name_pair`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_contact_name_pair
-- *
-- * Pairs of names which are equivalent
-- *
-- *******************************************************/
CREATE TABLE `civicrm_contact_name_pair` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ContactNamePair ID',
  `name_a` varchar(64) COMMENT 'First name (this is the master, if that matters)',
  `name_b` varchar(64) COMMENT 'Second name (if one name is a nickname or a mis-spelling it will be this one)',
  `is_name_b_nickname` tinyint DEFAULT 0,
  `is_name_b_inferior` tinyint DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `name_a`(name_a),
  INDEX `name_b`(name_b),
  INDEX `is_name_b_nickname`(is_name_b_nickname),
  INDEX `is_name_b_inferior`(is_name_b_inferior)
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * civicrm_contact_name_pair_family
-- *
-- * Pairs of family names which are equivalent
-- *
-- *******************************************************/
CREATE TABLE `civicrm_contact_name_pair_family` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ContactNamePair ID',
  `name_a` varchar(64) COMMENT 'Family name (generally the anglicised options)',
  `name_b` varchar(64) COMMENT 'Alternate name',
  PRIMARY KEY (`id`),
  INDEX `name_a`(name_a),
  INDEX `name_b`(name_b)
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * civicrm_mergeconflict
-- *
-- * Table for tracking and viewing merge conflicts.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mergeconflict` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique MergeConflict ID',
  `contact_1` int unsigned COMMENT 'FK to Contact',
  `contact_2` int unsigned COMMENT 'FK to Contact',
  `group_id` int unsigned COMMENT 'FK to Group',
  `conflicted_field` varchar(0),
  `value_1` varchar(0),
  `value_2` varchar(0),
  `analysis` varchar(0),
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB;
