<?php
namespace Behat\Console\Command;

use Behat\Behat\ApplicationFactory;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\App;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Behat shell.
 */
class BehatShell extends Shell {

/**
* Behat Application Object
*
* @Object
*/
	public $behatApp;

/**
* Override startup
*
* @return void
*/
	public function startup() {
		$this->out('Cake Behat Shell');
		$this->hr();
	}

/**
* Install method
*
* Don't use this if you're using
*
* @return void
*/
	public function install() {
		// Download all the things
		foreach ($this->storage as $name => $link) {
			$this->__install($name, $link);
		}
		// Setup Behat Console
		$file = new File($this->_getPath() . DS . 'skel' . DS . 'behat');
		$this->out('Copying behat to App/Console...');
		$file->copy($path = APP . 'Console' . DS . 'behat');
		chmod($path, 0755);

		// Setup Behat Config
		$file = new File($this->_getPath() . DS . 'skel' . DS . 'behat.yml');
		$this->out('Copying behat.yml to App/Config...');
		$file->copy(APP . 'Config' . DS . 'behat.yml');
		// Setup features dir
		$folder = new Folder($this->_getPath() . DS . 'skel' . DS . 'Features');
		$this->out('Copying features dir into Application Root...');
		$folder->copy(array('to' => ROOT . 'Features'));
	}

/**
* Override main
*
* @return void
*/
	public function main() {
		// Internal encoding to utf8
		mb_internal_encoding('utf8');
		// Get rid of Cake default args
		$args = $this->_cleanArgs($_SERVER['argv']);
		// Create instance of BehatApplication
		$appFactory = new ApplicationFactory();
		$this->behatApp = $appFactory->createApplication();

		if (!in_array('--config', $args) && !in_array('-c', $args) && !$this->_isCommand($args)) {
			array_push($args, '--config', APP . 'Config' . DS . 'behat.yml');
		}

		$this->out('Now running behat tests...');
		$this->_stop($this->behatApp->run(new ArgvInput($args)));
	}

/**
* get the option parser.
*
* @return BehatConsoleOptionParser
*/
	public function getOptionParser() {
		return new BehatConsoleOptionParser($this->name);
	}

/**
* Arguments cleaning
*
* @param array $args
*
* @return array
*/
	protected function _cleanArgs($args) {
		while ($args[0] != 'Behat.behat') {
			array_shift($args);
		}

		return $args;
	}

/**
* Check if one of the args is a Behat option or shortcut
*
* @param array $args
*
* @return boolean
*/
	protected function _isCommand($args) {
		$definition = $this->behatApp->getDefinition();
		foreach ($args as $arg) {
			$arg = str_replace("-", "", $arg);
			if ($definition->hasOption($arg) || $definition->hasShortcut($arg)) {
				return true;
			}
		}

		return false;
	}

/**
* Return the path used
*
* @return string Path used
*/
	protected function _getPath() {
		return App::pluginPath('Behat');
	}
}

/**
 * BehatConsoleOptionParser
 *
 * Stub to suppress processing of incoming console commands
 */
class BehatConsoleOptionParser extends ConsoleOptionParser {

/**
* @param array       $argv
* @param null|string $command
*
* @return array
*/
	public function parse($argv, $command = null) {
		$params = $args = array();

		return array($params, $args);
	}
}