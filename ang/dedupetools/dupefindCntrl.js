(function(angular, $, _) {

  // Cache schema metadata
  var schema = [];
  // Cache list of entities
  var entities = [];

  angular.module('dedupetools').config(function($routeProvider) {
      $routeProvider.when('/dupefinder/:api4entity?', {
        controller: 'DedupetoolsdupefindCntrl',
        templateUrl: '~/dedupetools/dupefindCntrl.html',
        title: 'Dedupe url generator',

        // If you need to look up data when opening the page, list it out
        // under "resolve".
        resolve: {
          contactFields: function(crmApi) {
            return crmApi('Contact', 'getfields', {
              action: 'get',
              api_action: 'get',
              options: {'get_options':'all'}
            });
          },
          ruleGroups: function(crmApi) {
            return crmApi('ruleGroup', 'get', {
            });
          }
        }
      });
    }
  );

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   myContact -- The current contact, defined above in config().
  angular.module('dedupetools').controller('DedupetoolsdupefindCntrl', function($scope, $routeParams, $timeout, crmApi, crmStatus, crmUiHelp, crmApi4, contactFields, ruleGroups) {
    // Main angular function.
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('dedupetools');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/dedupetools/dupefindCntrl'}); // See: templates/CRM/dedupetools/dupefindCntrl.hlp
    $scope.operators = arrayToSelect2([
      '=',
        '<=',
        '>=',
        '>',
        '<',
        'LIKE',
        "<>",
        "!=",
        "NOT LIKE",
        'IN',
        'NOT IN',
        'BETWEEN',
        'NOT BETWEEN',
        'IS NOT NULL',
        'IS NULL'
    ]);
    $scope.entities = entities;
    // We have myContact available in JS. We also want to reference it in HTML.
    $scope.contactFields = schema = contactFields['values'];
    var fieldList = [];
    _.each(contactFields['values'], function(spec) {
      fieldList.push({id : [spec.name], text : spec.title});
    });
    $scope.criteria = [];
    $scope.fieldList = fieldList;
    $scope.limit = 1000;
    $scope.newClause = null;
    $scope.ruleGroups = [];
    $scope.mergedCount = 0;
    $scope.skippedCount = 0;
    $scope.foundCount = 0;
    $scope.exceptedCount = 0;
    $scope.duplicatePairs = [];
    $scope.contactsToMerge = [];
    $scope.hasSearched = false;
    $scope.contactURL = CRM.url('civicrm/contact/view', $.param({'reset': 1, 'cid' : ''}));
    $scope.mergeURL = CRM.url('civicrm/contact/merge', $.param({'reset': 1, 'action' : 'update'}));
    $scope.isRowMerging = false;
    // Number of matches to fetch at once..
    // We might expose this.
    $scope.numberMatchesToFetch = 25;

    _.each(ruleGroups['values'], function(spec) {
      $scope.ruleGroups .push({id : spec.id, text : spec.contact_type + ' (' + spec.used + ') ' + spec.title});
      if (spec.contact_type === 'Individual' && spec.used === 'Unsupervised') {
        $scope.ruleGroupID = spec.id;
      }
    });
    $scope.hasMerged = false;
    $scope.isMerging = false;

    $scope.entity = $routeParams.api4entity;

    $scope.$watch('newClause', function(newValue, oldValue) {
      var field = newValue;
      $timeout(function() {
        if (field) {
          $scope.criteria.push([newValue, '=', '', '']);
          $scope.newClause = null;
        }
      });
    });
    var delayInMs = 2000;
    $scope.$watch('criteria', function(values) {
      // Remove empty values
      _.each(values, function(clause, index) {
        if (typeof clause !== 'undefined' && !clause[0]) {
          values.splice(index, 1);
        }
      });
      $timeout.cancel(timeoutPromise);
      timeoutPromise = $timeout(function() {
        $scope.hasMerged = false;
        writeUrl();
      }, delayInMs);
    }, true);

    $scope.$watch('ruleGroupID', function() {
      writeUrl();
    });

    $scope.$watch('limit', function() {
      writeUrl();
    });

    writeUrl();


    /**
     * Format the chosen criteria into a json string.
     *
     * @returns {*}
     */
    function formatCriteria() {
      var contactCriteria = {};
      var startCriteria = {};
      _.each($scope.criteria, function (criterion) {
        if (criterion[1] === '=') {
          contactCriteria[criterion[0]] = criterion[2];
        }
        else if (criterion[1] === 'BETWEEN' || criterion[1] === 'NOT BETWEEN') {
          contactCriteria[criterion[0]] = {};
          contactCriteria[criterion[0]][criterion[1]] = [criterion[2], criterion[3]];
        }
        else {
          contactCriteria[criterion[0]] = {};
          contactCriteria[criterion[0]][criterion[1]] = criterion[2];
        }

      });
      if (JSON.stringify(contactCriteria) === JSON.stringify(startCriteria)) {
        // Stick with an empty array to reflect what would happen on the core dedupe screen. This gets
        // us the same cachekey
        return {};
      }
      return {'contact': contactCriteria};
    }

    function getCachedMergeInfo(contactCriteria) {
      crmApi('Merge', 'getcacheinfo', {
        'rule_group_id': $scope.ruleGroupID,
        'criteria': contactCriteria
      }).then(function (data) {
          var results = data.values[0];
          $scope.duplicatePairs = results.skipped;
          if (results.stats.skipped !== undefined) {
            $scope.skippedCount = results.stats.skipped;
          }
          else {
            $scope.skippedCount = 0;
          }
          $scope.foundCount = parseInt(results.found);
          // We might have just merged, or we might have reloaded earlier results.
          $scope.hasMerged = (data['values'][0]['skipped'].length > 0 || data.values[0].stats.length);
        }
      );
    }

    function getConflicts(to_keep_id, to_remove_id,contactCriteria, pair) {
      crmApi('Merge', 'get_conflicts', {
        'rule_group_id': $scope.ruleGroupID,
        'criteria': contactCriteria,
        'to_remove_id' : to_remove_id,
        'to_keep_id' : to_keep_id
      }).then(function (data) {
          var results = data.values[0];
          pair['conflicts'] = results;
        }
      );
    }

    function updateUrl(contactCriteria) {
      $scope.url = CRM.url('civicrm/contact/dedupefind', $.param({
        'reset': 1,
        'action': 'update',
        'rgid': $scope.ruleGroupID,
        'limit': $scope.limit,
        'context': 'conflicts',
        'criteria': JSON.stringify(contactCriteria)
      }));
    }

    var timeoutPromise;
    function writeUrl() {
      var contactCriteria = formatCriteria();
      $scope.hasSearched = false;
      $scope.exceptedCount = 0;
      // We could do this second but maybe the next bit is slow...
      updateUrl(contactCriteria);
      getCachedMergeInfo(contactCriteria);
    }

    $scope.forceMerge = function (mainID, otherID) {
      merge(mainID, otherID, 'aggressive');
    };
    $scope.retryMerge = function retryMerge(mainID, otherID, pair) {
      merge(mainID, otherID, 'safe', pair);
    };
    $scope.dedupeException = function dedupeException(mainID, otherID) {
      $scope.isRowMerging = true;
      crmApi('Exception', 'create', {
        'contact_id1' : mainID,
        'contact_id2' : otherID
      }).then(function (data) {
        $scope.isRowMerging = false;
          removeMergedMatch(mainID, otherID);
      });
    };

    $scope.notDuplicates = function notDuplicates() {
      $scope.isMerging = true;
      crmApi('Merge', 'mark_duplicate_exception', {
        'rule_group_id': $scope.ruleGroupID,
        'criteria': formatCriteria(),
      }).then (function(result) {
        getCachedMergeInfo(formatCriteria());
        $scope.exceptedCount = result.count;
        $scope.isMerging = false;
      });
    };

    /**
     * Merge two contacts.
     *
     * @param to_keep_id
     *  Contact ID to be kept
     * @param to_remove_id
     *  Contact ID to be deleted by merge.
     * @param mode
     *   safe or aggressive
     * @param pair
     */
    function merge(to_keep_id, to_remove_id, mode, pair) {
      $scope.isRowMerging = true;
      crmApi('Contact', 'merge', {
        'to_keep_id' : to_keep_id,
        'to_remove_id' : to_remove_id,
        'mode' : mode
      }).then(function (data) {
        $scope.isRowMerging = false;
        if (data['values']['merged'].length === 1) {
          removeMergedContact(to_remove_id);
          $scope.mergedCount++;
        }
        else {
          if (typeof pair !== 'undefined') {
            getConflicts(to_keep_id, to_remove_id, formatCriteria(), pair);
          }
        }
      });
    }

    function updateFoundCount() {
      crmApi('Merge', 'getcount', {
        'rule_group_id': $scope.ruleGroupID,
        'criteria': formatCriteria()
      }).then(function (data) {
        $scope.foundCount = data.result;
      });
    }

    function removeMergedContact(id) {
      _.each($scope.duplicatePairs, function(pair, index) {
        if (typeof(pair) !== 'undefined' && (pair['dstID'] === id || pair['srcID'] === id)) {
          $scope.duplicatePairs.splice(index, 1);
        }
      });
      updateFoundCount();
    }

    function removeMergedMatch(id, id2) {
      _.each($scope.duplicatePairs, function(pair, index) {
        if (typeof(pair) !== 'undefined' && pair['dstID'] === id && pair['srcID'] === id2 ) {
          $scope.duplicatePairs.splice(index, 1);
        }
      });
      updateFoundCount();
    }

    $scope.batchMerge = function () {
      $scope.isMerging = true;
      $scope.duplicatePairs = [];
      crmApi('Job', 'process_batch_merge', {
        'rule_group_id' : $scope.ruleGroupID,
        'limit' : $scope.limit,
        'criteria' : formatCriteria()
      }).then(function (data) {
        $scope.isMerging = false;
        getCachedMergeInfo(formatCriteria());
        $scope.mergedCount = data['values']['merged'].length;
        $scope.skippedCount = data['values']['skipped'].length;
        $scope.duplicatePairs = data['values']['skipped'];
        $scope.hasMerged = true;
      });
    };

    $scope.getDuplicates = function () {
      $scope.isSearching = true;
      crmApi('Merge', 'get_duplicates', {
        'rule_group_id' : $scope.ruleGroupID,
        'options': {'limit' : $scope.numberMatchesToFetch},
        'search_limit' : $scope.limit,
        'criteria' : formatCriteria()
      }).then(function (data) {
        $scope.duplicatePairs = data['values'];
        $scope.hasSearched = true;
        $scope.isSearching = false;
        crmApi('Merge', 'getcount', {
          'rule_group_id' : $scope.ruleGroupID,
          'criteria' : formatCriteria()
        }).then(function (data) {
          $scope.foundCount = data.result;
        });
      });
    };

  });


  angular.module('dedupetools').directive('dedupeExpValue', function($routeParams, crmApi4) {
    return {
      scope: {
        data: '=dedupeExpValue'
      },
      link: function (scope, element, attrs) {
        var ts = scope.ts = CRM.ts('api4'),
          entity = $routeParams.api4entity;

        function getField(fieldName) {
          var fieldNames = fieldName.split('.');
          return get(entity, fieldNames);

          function get(entity, fieldNames) {
            if (fieldNames.length === 1) {
              return _.findWhere(schema, {name: fieldNames[0]});
            }
            var comboName = _.findWhere(entityFields(entity), {name: fieldNames[0] + '.' + fieldNames[1]});
            if (comboName) {
              return comboName;
            }
            var linkName = fieldNames.shift(),
              entityLinks = _.findWhere(links, {entity: entity}).links,
              newEntity = _.findWhere(entityLinks, {alias: linkName}).entity;
            return get(newEntity, fieldNames);
          }
        }

        function destroyWidget() {
          var $el = $(element);
          if ($el.is('.crm-form-date-wrapper .crm-hidden-date')) {
            $el.crmDatepicker('destroy');
          }
          if ($el.is('.select2-container + input')) {
            $el.crmEntityRef('destroy');
          }
          $(element).removeData().removeAttr('type').removeAttr('placeholder').show();
        }

        function makeWidget(field, op, isExtra) {
          var $el = $(element),
            dataType = field.data_type;
          if (op === 'IS NULL' || op === 'IS NOT NULL') {
            $el.hide();
            return;
          }
          if (isExtra && op === 'BETWEEN' && op === 'NOT BETWEEN') {
            $el.show();
            return;
          }
          if (dataType === 'Timestamp' || dataType === 'Date') {
            if (_.includes(['=', '!=', '<>', '<', '>=', '<', '<='], op)) {
              $el.crmDatepicker({time: dataType === 'Timestamp'});
            }
          } else if (_.includes(['=', '!=', '<>', 'IN', 'NOT IN'], op)) {
            multi = _.includes(['IN', 'NOT IN'], op);
            if (field.fk_entity) {
              $el.crmEntityRef({entity: field.fk_entity});
            } else if (field.options) {
              $el.addClass('loading').attr('placeholder', ts('- select -')).crmSelect2({allowClear: false, data: [{id: '', text: ''}]});
                var options = [];
                _.each(field.options, function(val, key) {
                  options.push({id: key, text: val});
                $el.removeClass('loading').select2({multiple: multi, data: options});
              });
            } else if (dataType === 'Boolean') {
              $el.attr('placeholder', ts('- select -')).crmSelect2({allowClear: false, placeholder: ts('- select -'), data: [
                {id: '1', text: ts('Yes')},
                {id: '0', text: ts('No')}
              ]});
            }
          }
        }

        scope.$watchCollection('data', function(data) {
          destroyWidget();
          var field = getField(data.field);
          if (field) {
            makeWidget(field, data.op || '=', data.isExtra);
          }
        });
      }
    };
  });

  // Turn a flat array into a select2 array
  function arrayToSelect2(array) {
    var out = [];
    _.each(array, function(item) {
      out.push({id: item, text: item});
    });
    return out;
  }

})(angular, CRM.$, CRM._);
