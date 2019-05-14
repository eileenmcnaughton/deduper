<?php
/**
 * Merge redo spec
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_merge_getduplicates_spec(&$spec){
  $spec['rule_group_id']['api.required'] = 1;
  $spec['group_id'] = ['title' => ts('CiviCRM Group')];
  $spec['criteria'] = ['title' => ts('Dedupe criteria')];
  $spec['limit'] = ['title' => ts('Number of contacts to find matches for'), 'api.default' => \Civi::settings()->get('dedupe_default_limit')];
}

/**
 * Get duplicates
 *
 * @param array $params
 * @return array API result descriptor
 *
 * @throws API_Exception
 * @throws CiviCRM_API3_Exception
 * @throws CRM_Core_Exception
 */
function civicrm_api3_merge_getduplicates($params) {
  $cacheKeyString = CRM_Dedupe_Merger::getMergeCacheKeyString(
    $params['rule_group_id'],
    CRM_Utils_Array::value('group_id', $params),
    CRM_Utils_Array::value('criteria', $params, []),
    CRM_Utils_Array::value('check_permissions', $params)
  );
  CRM_Core_BAO_PrevNextCache::refillCache(
    $params['rule_group_id'],
    CRM_Utils_Array::value('group_id', $params),
    $cacheKeyString,
    CRM_Utils_Array::value('criteria', $params, []),
    CRM_Utils_Array::value('check_permissions', $params),
    $params['limit']
  );
  return civicrm_api3('Merge', 'getcacheinfo', $params);
}
