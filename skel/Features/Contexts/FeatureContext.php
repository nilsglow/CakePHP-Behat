<?php
namespace App\Features\Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * FeatureContext
 */
class FeatureContext extends MinkContext implements Context {

	public function getPathTo($path) {
		switch ($path) {
			default:
				return $path;
		}
	}

    /**
     * @param string $path
     * @return mixed
     */
    public function locatePath($path) {
        return parent::locatePath($this->getPathTo($path));
    }

    /**
     * @param $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, array $args) {
        if (isset($this->$name) && is_callable($this->$name)) {
            return call_user_func_array($this->$name, $args);
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Call to undefined method ' . get_class($this) . '::' . $name .
                    ' in ' . $trace[0]['file'] .
                    ' on line ' . $trace[0]['line'],
                E_USER_ERROR
            );
        }
    }

/**
 * Return Table Object
 *
 * @param string $name
 *
 * @return object
 */
	public function getTable($name) {
		$table = new Table([
			'alias' => $name,
			'connection' => ConnectionManager::get('test')
		]);

		return $table;
	}

/**
 * Truncate Test table
 *
 * @param \Cake\ORM\Table $table
 *
 * @return void
 */
	public function truncateTable(Table $table) {
		$connection = $table->connection();
		$schemaTable = new \Cake\Database\Schema\Table($table->table());
		$sql = $schemaTable->truncateSql($connection);
		foreach ($sql as $stmt) {
			$connection->execute($stmt)->closeCursor();
		}
	}
}