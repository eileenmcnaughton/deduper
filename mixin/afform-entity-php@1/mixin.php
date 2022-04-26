<?php

/**
 * Empty shim, can be removed when this extension drops support for core < 5.50
 *
 * @mixinName afform-entity-php
 * @mixinVersion 1.0.0
 *
 * @param CRM_Extension_MixInfo $mixInfo
 * @param \CRM_Extension_BootCache $bootCache
 */
return function ($mixInfo, $bootCache) {

  // This shim doesn't actually need to do anything because prior to CiviCRM 5.50,
  // afformEntiteis were loaded automatically without any event.
  // In CiviCRM 5.50+ this shim won't be used; the mixin from core will load the entities.

};
