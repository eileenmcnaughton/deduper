<?php

use CRM_Dedupetools_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Civi\Test\Api3TestTrait;

require_once __DIR__  .'/../DedupeBaseTestClass.php';
/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CRM_DedupeTools_BAO_MergeConflictTest extends DedupeBaseTestClass {

  use Api3TestTrait;

  /**
   * Test the boolean resolver works.
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testGetBooleanFields() {
    $fields = CRM_Dedupetools_BAO_MergeConflict::getBooleanFields();
    $this->assertTrue(isset($fields['do_not_mail'], $fields['on_hold']));
    $this->assertFalse(isset($fields['contact_type']));
    $this->assertFalse(isset($fields['is_deleted']));
  }

  /**
   * Test that a boolean field is resolved if set.
   */
  public function testResolveBooleanFields() {
    $this->ids['Contact'][0] = $this->callAPISuccess('Contact', 'create', ['first_name' => 'bob', 'do_not_mail' => 0, 'contact_type' => 'Individual'])['id'];
    $this->ids['Contact'][1] = $this->callAPISuccess('Contact', 'create', ['first_name' => 'bob', 'do_not_mail' => 1, 'contact_type' => 'Individual'])['id'];
    $this->callAPISuccess('Contact', 'merge', ['to_keep_id' => $this->ids['Contact'][0], 'to_remove_id' => $this->ids['Contact'][1]]);
    $mergedContacts = $this->callAPISuccess('Contact', 'get', ['id' => ['IN' => $this->ids['Contact']]])['values'];

    $this->assertEquals(1, $mergedContacts[$this->ids['Contact'][1]]['contact_is_deleted']);
    $this->assertEquals(0, $mergedContacts[$this->ids['Contact'][0]]['contact_is_deleted']);
    $this->assertEquals(1, $mergedContacts[$this->ids['Contact'][0]]['do_not_mail']);

    // Now try merging a contact with 0 in that field into our retained contact.
    $this->ids['Contact'][2] = $this->callAPISuccess('Contact', 'create', ['first_name' => 'bob', 'do_not_mail' => 0, 'contact_type' => 'Individual'])['id'];
    $this->callAPISuccess('Contact', 'merge', ['to_keep_id' => $this->ids['Contact'][0], 'to_remove_id' => $this->ids['Contact'][2]]);
    $mergedContacts = $this->callAPISuccess('Contact', 'get', ['id' => ['IN' => $this->ids['Contact'], 'is_deleted' => 0]])['values'];

    $this->assertEquals(1, $mergedContacts[$this->ids['Contact'][0]]['do_not_mail']);

    $this->assertEquals(1, $mergedContacts[$this->ids['Contact'][2]]['contact_is_deleted']);
    $this->assertEquals(0, $mergedContacts[$this->ids['Contact'][0]]['contact_is_deleted']);
  }

}
