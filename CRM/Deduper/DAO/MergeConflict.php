<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2020
 *
 * Generated from xml/schema/CRM/Deduper/MergeConflict.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:d3f9818087520239bfde39c5ad933b25)
 */

/**
 * Database access object for the MergeConflict entity.
 */
class CRM_Deduper_DAO_MergeConflict extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_mergeconflict';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * Unique MergeConflict ID
   *
   * @var int
   */
  public $id;

  /**
   * FK to Contact
   *
   * @var int
   */
  public $contact_1;

  /**
   * FK to Contact
   *
   * @var int
   */
  public $contact_2;

  /**
   * FK to Group
   *
   * @var int
   */
  public $group_id;

  /**
   * @var string
   */
  public $conflicted_field;

  /**
   * @var string
   */
  public $value_1;

  /**
   * @var string
   */
  public $value_2;

  /**
   * @var string
   */
  public $analysis;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_mergeconflict';
    parent::__construct();
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Deduper_ExtensionUtil::ts('Unique MergeConflict ID'),
          'required' => TRUE,
          'where' => 'civicrm_mergeconflict.id',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'contact_1' => [
          'name' => 'contact_1',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Deduper_ExtensionUtil::ts('Contact 1'),
          'description' => CRM_Deduper_ExtensionUtil::ts('FK to Contact'),
          'where' => 'civicrm_mergeconflict.contact_1',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'contact_2' => [
          'name' => 'contact_2',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Deduper_ExtensionUtil::ts('Contact 2'),
          'description' => CRM_Deduper_ExtensionUtil::ts('FK to Contact'),
          'where' => 'civicrm_mergeconflict.contact_2',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'group_id' => [
          'name' => 'group_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Deduper_ExtensionUtil::ts('FK to Group'),
          'where' => 'civicrm_mergeconflict.group_id',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'conflicted_field' => [
          'name' => 'conflicted_field',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Deduper_ExtensionUtil::ts('Conflicted Field'),
          'size' => CRM_Utils_Type::TWO,
          'where' => 'civicrm_mergeconflict.conflicted_field',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'value_1' => [
          'name' => 'value_1',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Deduper_ExtensionUtil::ts('Value 1'),
          'size' => CRM_Utils_Type::TWO,
          'where' => 'civicrm_mergeconflict.value_1',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'value_2' => [
          'name' => 'value_2',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Deduper_ExtensionUtil::ts('Value 2'),
          'size' => CRM_Utils_Type::TWO,
          'where' => 'civicrm_mergeconflict.value_2',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
        'analysis' => [
          'name' => 'analysis',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Deduper_ExtensionUtil::ts('Analysis'),
          'size' => CRM_Utils_Type::TWO,
          'where' => 'civicrm_mergeconflict.analysis',
          'table_name' => 'civicrm_mergeconflict',
          'entity' => 'MergeConflict',
          'bao' => 'CRM_Deduper_DAO_MergeConflict',
          'localizable' => 0,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mergeconflict', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mergeconflict', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}