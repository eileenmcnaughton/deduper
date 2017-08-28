<?php
use CRM_Dedupetools_ExtensionUtil as E;

class CRM_Dedupetools_Form_Report_MergeConflict extends CRM_Report_Form {

  protected $_addressField = FALSE;

  protected $_emailField = FALSE;

  protected $_summary = NULL;

  protected $_customGroupExtends = array('Membership');
  protected $_customGroupGroupBy = FALSE; function __construct() {
    $this->_columns = array(
      'civicrm_merge_conflict' => array(
        'fields' => array(
          'value_1' => array(
            'title' => E::ts('Value 1'),
            'default' => TRUE,
            /*
            'statistics' => array(
              'count'  => ts('Count')
            ),*/
          ),
          'value_2' => array(
            'title' => E::ts('Value 2'),
            'default' => TRUE,
            /*
            'statistics' => array(
              'count'  => ts('Count')
            ),*/
          ),
          'conflicted_field' => array(
            'title' => E::ts('Conflicted Field'),
            'default' => TRUE,
          ),
          'analysis' => array(
            'title' => E::ts('Analysis'),
            'default' => TRUE,
          ),
        ),
        'filters' => array(
          'value_1' => array(
            'title' => E::ts('Value 1'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'value_2' => array(
            'title' => E::ts('Value 2'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'conflicted_field' => array(
            'title' => E::ts('Conflicted Field'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'analysis' => array(
            'title' => E::ts('Analysis'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
        ),
        'order_bys' => array(
          'value_1' => array(
            'title' => E::ts('Value 1'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'value_2' => array(
            'title' => E::ts('Value 2'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'conflicted_field' => array(
            'title' => E::ts('Conflicted Field'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'analysis' => array(
            'title' => E::ts('Analysis'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
        ),
        'group_bys' => array(
          'value_1' => array(
            'title' => E::ts('Value 1'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'value_2' => array(
            'title' => E::ts('Value 2'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'conflicted_field' => array(
            'title' => E::ts('Conflicted Field'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'analysis' => array(
            'title' => E::ts('Analysis'),
            'type' => CRM_Utils_Type::T_STRING,
          ),
        ),
      ),
    );
    parent::__construct();
  }

  function from() {
    $this->_from = 'FROM civicrm_merge_conflict ' . $this->_aliases['civicrm_merge_conflict'];
  }

}
