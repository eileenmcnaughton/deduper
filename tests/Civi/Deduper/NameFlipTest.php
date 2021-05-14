<?php


namespace Civi\Deduper;

use Civi\Api4\Contact;
use Civi\Api4\Name;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;

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
class NameFlipTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  /**
   * Setup used when HeadlessInterface is implemented.
   *
   * Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
   *
   * @see See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
   *
   * @return \Civi\Test\CiviEnvBuilder
   *
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * Test flipping name from generic search criteria.
   *
   * @throws \API_Exception
   */
 public function testNameFlipping(): void {
    $contactID = Contact::create(FALSE)->setValues([
      'last_name' => 'Misha',
      'first_name' => 'Sato',
      'preferred_language' => 'ja_JP',
    ])->execute()->first()['id'];
    Name::flip(FALSE)
      ->addWhere('first_name', '=', 'Sato')
      ->addWhere('preferred_language', '=', 'ja_JP')
      // We need values because we are extending another class.
      //->setValues(['flip' => TRUE])
      ->execute()
      ->first();
    $contact = Contact::get(FALSE)
      ->addWhere('id', '=', $contactID)
      ->setSelect(['first_name', 'last_name'])
      ->execute()->first();
    $this->assertEquals('Sato', $contact['last_name']);
    $this->assertEquals('Misha', $contact['first_name']);
 }

  /**
   * Test flipping name where nameFilter .
   *
   * @throws \API_Exception
   */
  public function testNameFlippingWithNameFilter(): void {
    $contactID = Contact::create(FALSE)->setValues([
      'last_name' => 'Misha',
      'first_name' => 'Sato',
      'preferred_language' => 'ja_JP',
    ])->execute()->first()['id'];
    Name::flip(FALSE)
      ->addWhere('first_name', '=', 'Sato')
      ->addWhere('preferred_language', '=', 'ja_JP')
      // We need values because we are extending another class.
      //->setValues(['flip' => TRUE])
      ->execute()
      ->first();
    $contact = Contact::get(FALSE)
      ->addWhere('id', '=', $contactID)
      ->setSelect(['first_name', 'last_name'])
      ->execute()->first();
    $this->assertEquals('Sato', $contact['last_name']);
    $this->assertEquals('Misha', $contact['first_name']);
  }

}
