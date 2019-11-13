<?php

/**
 * CRM_Dedupetools_BAO_Resolver_EquivalentNameResolver
 */
class CRM_Dedupetools_BAO_Resolver_EquivalentNameResolver extends CRM_Dedupetools_BAO_Resolver {

  /**
   * Potential alternatives for name.
   *
   * @var array
   */
  protected $alternatives = [];

  /**
   * Setting for name handling.
   *
   * @var string
   */
  protected $nameHandlingSetting;

  /**
   * Should the nick name be retained.
   *
   * @var string
   */
  protected $isKeepNickName;

  /**
   * Is the nick name preferred.
   *
   * @var bool
   */
  protected $isPreferNickName;

  /**
   * Is the other name preferred.
   *
   * @var bool
   */
  protected $isPreferOtherName;

  /**
   * Should the preferred contact's value be the resolution.
   *
   * @var bool
   */
  protected $isResolveByPreferredContact;

  /**
   * Resolve conflicts where we have a record in the contact_name_pairs table telling us the names are equivalent.
   *
   * @throws \API_Exception
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public function resolveConflicts() {
    if (!$this->hasIndividualNameFieldConflict()) {
      return;
    }
    $this->interpretSetting();

    foreach ([TRUE, FALSE] as $isContactToKeep) {
      $contact1 = $this->getIndividualNameFieldValues($isContactToKeep);

      foreach ($contact1 as $fieldName => $value) {
        if ($this->isFieldInConflict($fieldName)) {

          $otherContactValue = $this->getIndividualNameFieldValues(!$isContactToKeep)[$fieldName];
          $this->loadAlternatives($value);
          $this->loadAlternatives($otherContactValue);

          if ($this->isInferior($value)) {
            $this->resolveInferiorValue($value, $fieldName, $isContactToKeep, $otherContactValue);
          }
          if ( $this->nameHandlingSetting  && $this->hasAlternatives($value)) {
            if ($this->isNickNameOf($value, $otherContactValue)) {
              if ($this->isKeepNickName) {
                $this->setContactValue('nick_name', $value, $isContactToKeep);
              }
              if ($this->isPreferNickName) {
                $this->setResolvedValue($fieldName, $value);
              }
              if ($this->isPreferOtherName) {
                $this->setResolvedValue($fieldName, $otherContactValue);
              }
            }
            if ($this->isResolveByPreferredContact) {
              $this->setResolvedValue($fieldName, $this->getPreferredContactValue($fieldName));
            }
          }
        }
      }
    }
  }

  /**
   * Load alternative variants of the given name.
   *
   * @param string $value
   *
   * @throws \Civi\API\Exception\UnauthorizedException
   * @throws \API_Exception
   */
  public function loadAlternatives($value) {
    if (isset($this->alternatives[$value])) {
      return;
    }
    if (!\Civi::cache('dedupe_pairs')->has('name_alternatives_' . $value)) {
      // There is something funky about including api v4 at the moment.
      // It's in core in 5.19 & I figure if it's still playing up for us once we
      // are on that I'll dig further.
      require_once(__DIR__ . '/../../../../Civi/Api4/ContactNamePair.php');
      $namePair = \Civi\Api4\ContactNamePair::get()
        ->addWhere('name_b', '=', $value)
        ->addClause('OR', ['name_b', '=', $value], ['name_b', '=', $value])
        ->setCheckPermissions(FALSE)
        ->execute();
      $alternatives = ['inferior_version_of' => [], 'nick_name_of' => []];
      foreach ($namePair as $pair) {
        if ($pair['name_b'] === $value) {
          if ($pair['is_name_b_inferior']) {
            $alternatives['inferior_version_of'][] = $pair['name_a'];
          }
          else {
            $alternatives['alternative_of'][] = $pair['name_a'];
          }
          if ($pair['is_name_b_nickname']) {
            $alternatives['nick_name_of'][] = $pair['name_a'];
          }
        }
        else {
          $alternatives['alternative_of'][] = $pair['name_b'];
        }
      }
      \Civi::cache('dedupe_pairs')->set('name_alternatives_' . $value, $alternatives);
    }
    $this->alternatives[$value] = \Civi::cache('dedupe_pairs')->get('name_alternatives_' . $value);
  }

  /**
   * Is this a known misspelling / less preferred version.
   *
   * We plan to expose saving these in the deduper.
   *
   * @param string $value
   *
   * @return bool
   */
  protected function isInferior($value): bool {
    if (is_numeric($value)) {
      return TRUE;
    }
    return !empty($this->alternatives[$value]['inferior_version_of']);
  }

  /**
   * Resolve a misspelling / less preferred version.
   *
   * If there is only one potential alternative we choose it. If there is more than one we
   * look for one which would resolve the conflict.
   *
   * @param string $value
   * @param string $fieldName
   * @param bool $isContactToKeep
   * @param string $otherContactValue
   */
  protected function resolveInferiorValue($value, $fieldName, $isContactToKeep, string $otherContactValue) {
    $inferiorVersionOf = $this->alternatives[$value]['inferior_version_of'];
    if (count($inferiorVersionOf) === 1) {
      $this->setContactValue($fieldName, $inferiorVersionOf[0], $isContactToKeep);
    }
    elseif (in_array($otherContactValue, $inferiorVersionOf, TRUE)) {
      $this->setContactValue($fieldName, $otherContactValue, $isContactToKeep);
    }
  }

  /**
   * Are there alternatives to consider using instead.
   *
   * @return bool
   */
  protected function hasAlternatives($value): bool {
    if (empty($this->alternatives[$value]['alternative_of'])) {
      return FALSE;
    }
    $viableAlternatives = array_intersect_key($this->alternatives, array_fill_keys($this->alternatives[$value]['alternative_of'], 1));
    return !empty($viableAlternatives);
  }

  /**
   * Is the given value the nickname of the other contact's name
   *
   * @param string $value
   * @param string $otherValue
   *
   * @return bool
   */
  protected function isNickNameOf($value, $otherValue):bool {
    if (empty($this->alternatives[$value]['nick_name_of'])) {
      return FALSE;
    }
    return in_array($otherValue, $this->alternatives[$value]['nick_name_of'], TRUE);
  }

  /**
   * Interpret the setting into it's components.
   */
  protected function interpretSetting() {
    $this->nameHandlingSetting = $this->getSetting('deduper_equivalent_name_handling');
    if (in_array($this->nameHandlingSetting, ['prefer_non_nick_name_keep_nick_name', 'prefer_preferred_contact_value_keep_nick_name'], TRUE)) {
      $this->isKeepNickName = TRUE;
    }
    if ($this->nameHandlingSetting === 'prefer_nick_name') {
      $this->isPreferNickName = TRUE;
    }
    if (in_array($this->nameHandlingSetting, ['prefer_non_nick_name', 'prefer_non_nick_name_keep_nick_name'], TRUE)) {
      $this->isPreferOtherName = TRUE;
    }
    if (in_array($this->nameHandlingSetting, ['prefer_preferred_contact_value', 'prefer_preferred_contact_value_keep_nick_name'], TRUE)) {
      $this->isResolveByPreferredContact = TRUE;
    }
  }

}
