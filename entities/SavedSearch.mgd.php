<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference

// Install search display if searchkit is installed.
if (!civicrm_api3('Extension', 'getcount', [
  'full_name' => 'org.civicrm.search',
  'status' => 'installed',
])) {
  return [];
}
return [
  [$result =
    'name' => 'Contact Name Pairs Search',
    'entity' => 'SavedSearch',
    'cleanup' => 'never',
    'update' => 'never',
    'params' => [
      'version' => 4,
      'checkPermissions' => FALSE,
      'values' => [
        'name' => 'Equivalent_names',
        'label' => 'Equivalent names',
        'api_entity' => 'ContactNamePair',
        'api_params' => '{"version":4,"select":["id","name_a","name_b","is_name_b_nickname","is_name_b_inferior"],"orderBy":[],"where":[],"groupBy":[],"join":[],"having":[]}'
      ],
      'chain' => [
        'search_display' => ['SearchDisplay', 'create', [
          'values' => [
            'name' => 'Equivalent_names',
            'label' => 'Equivalent names',
            'saved_search_id' => '$id',
            'type' => 'table',
            'settings' => '{"limit":20,"pager":true,"columns":[{"key":"name_a","label":"Name A","dataType":"String","type":"field","editable":{"entity":"ContactNamePair","options":false,"serialize":false,"fk_entity":null,"id":"id","name":"name_a","value":"name_a"}},{"key":"name_b","label":"Name B","dataType":"String","type":"field","editable":{"entity":"ContactNamePair","options":false,"serialize":false,"fk_entity":null,"id":"id","name":"name_b","value":"name_b"}},{"key":"is_name_b_nickname","label":"Is Name B a Nickname of Name A?","dataType":"Boolean","type":"field","editable":{"entity":"ContactNamePair","options":false,"serialize":false,"fk_entity":null,"id":"id","name":"is_name_b_nickname","value":"is_name_b_nickname"}},{"key":"is_name_b_inferior","label":"Is Name B Inferior to Name A?","dataType":"Boolean","type":"field","editable":{"entity":"ContactNamePair","options":false,"serialize":false,"fk_entity":null,"id":"id","name":"is_name_b_inferior","value":"is_name_b_inferior"}}],"actions":true,"sort":[["name_a","ASC"]]}'
          ],
        ]],
      ],
    ],
  ],
];
