<?php


namespace Civi\Api4\Action\Name;

use Civi;
use Civi\Api4\Generic\DAOUpdateAction;

/**
 * Class flip.
 *
 * Updates a contact, flipping their names.

 * This api extends the Contact.update api but performs a very
 * specific action - swapping first and last names.
 *
 * It uses api updates rather than direct sql as direct sql
 * doesn't cope with us swapping the places of 2 fields from my testing.
 * In addition using the api is appropriate for exposing to end users (e.g
 * in search kit) and it seems that only the rough pass can be done by script after
 * initial analysis (ie. some common Japanese names have less common usage in
 * other languages).
 *
 * Note the overall volume here is not insane so speed is not a huge concern.
 *
 * @method setNameFilter
 * @method getNameFilter
 */
class Flip extends DAOUpdateAction {

  /**
   * @var string
   */
   protected $nameFilter;

  /**
   * Field values to update.
   *
   * CiviCRM uses docblock annotations so we are
   * overriding the required in the docblock
   * since otherwise we inherit that from the parent.
   *
   * @var array
   */
  protected $values = [];

  /**
   * @return array
   * @throws \API_Exception
   */
  protected function getBatchRecords(): array {
    $params = [
      'checkPermissions' => $this->checkPermissions,
      'where' => $this->where,
      'orderBy' => $this->orderBy,
      'limit' => $this->limit,
      'offset' => $this->offset,
      'select' => ['first_name', 'last_name', 'id'],
    ];
    if ($this->getNameFilter()) {
      $params['where'][] [''];
    }

    return (array) civicrm_api4('Contact', 'get', $params, 'id');
  }

  /**
   * @inheritDoc
   */
  protected function writeObjects($items): array {
    foreach ($items as $item) {
      Civi\Api4\Contact::update($this->getCheckPermissions())->setValues([
        'id' => $item['id'],
        'first_name' => $item['last_name'],
        'last_name' => $item['first_name'],
      ])->execute();
    }
    return $items;
  }

}
