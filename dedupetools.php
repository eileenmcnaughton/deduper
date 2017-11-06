<?php

require_once 'dedupetools.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dedupetools_civicrm_config(&$config) {
  _dedupetools_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function dedupetools_civicrm_xmlMenu(&$files) {
  _dedupetools_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dedupetools_civicrm_install() {
  _dedupetools_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function dedupetools_civicrm_postInstall() {
  _dedupetools_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dedupetools_civicrm_uninstall() {
  _dedupetools_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dedupetools_civicrm_enable() {
  _dedupetools_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dedupetools_civicrm_disable() {
  _dedupetools_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function dedupetools_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dedupetools_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function dedupetools_civicrm_managed(&$entities) {
  _dedupetools_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dedupetools_civicrm_caseTypes(&$caseTypes) {
  _dedupetools_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function dedupetools_civicrm_angularModules(&$angularModules) {
  _dedupetools_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function dedupetools_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dedupetools_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * @param string $op Operation
 *   Some examples of the operation are below:
 *     - create.new.shortcuts - shortcuts available from the 'create new' button.
 *     - view.contact.activity - shortcuts available from actions tab.
 *     - view.contact.userDashBoard - tbc
 *     - pdfFormat.manage.action - tbc
 *
 *     - Search results rows eg.
 *       - note.selector.row
 *       - survey.dashboard.row
 *
 * @param string $objectName (e.g. Contact for view.contact.activity)
 * @param int $objectId
 * @param array $links
 * @param int $mask
 * @param array $values
 */
function dedupetools_civicrm_links($op, $objectName, $objectId, &$links, &$mask, &$values) {
 $b = $links;
 if ($objectName !== 'Contact' || $op !== 'view.contact.activity') {
   return;
 }
 try {
  $ruleGroups = civicrm_api3('RuleGroup', 'get', array(
    'contact_type' => civicrm_api3('Contact', 'getvalue' , array('id' => $objectId, 'return' => 'contact_type')),
  ));

  $contactIDS = array($objectId);
  foreach ($ruleGroups['values'] as $ruleGroup) {
    $links[] = array(
      'title' => ts('Find matches using Rule : %1', array(1 => $ruleGroup['title'])),
      'name' => ts('Find matches using Rule : %1', array(1 => $ruleGroup['title'])),
      'url' => CRM_Utils_System::url('civicrm/contact/dedupefind', array(
        'reset' => 1,
        'action' => 'update',
        'rgid' => $ruleGroup['id'],
        'criteria' => json_encode(array('contact' => array('id' => array('IN' => $contactIDS)))),
        'limit' => count($contactIDS),
      )),

    );
  }
 }
 catch (CiviCRM_API3_Exception $e) {
   // This would most likely happen if viewing a deleted contact since we are not forcing
   // them to be returned. Keep calm & carry on.
 }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function dedupetools_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function dedupetools_civicrm_navigationMenu(&$menu) {
  _dedupetools_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'org.wikimedia.dedupetools')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _dedupetools_civix_navigationMenu($menu);
} // */
