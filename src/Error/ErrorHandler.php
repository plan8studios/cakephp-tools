<?php

namespace Tools\Error;

use Cake\Error\ErrorHandler as CoreErrorHandler;
use Cake\Log\Log;
use Exception;
use Tools\Error\Middleware\ErrorHandlerMiddleware;

/**
 * Custom ErrorHandler to not mix the 404 exceptions with the rest of "real" errors in the error.log file.
 *
 * All you need to do is:
 * - switch `use Cake\Error\ErrorHandler;` with `use Tools\Error\ErrorHandler;` in your bootstrap
 * - Make sure you got the 404 log defined either in your app.php or as Log::config() call.
 *
 * Example config as scoped one:
 * - 'className' => 'Cake\Log\Engine\FileLog',
 * - 'path' => LOGS,
 * - 'file'=> '404',
 * - 'levels' => ['error'],
 * - 'scopes' => ['404']
 *
 * If you don't want the errors to also show up in the debug and error log, make sure you set
 * `'scopes' => false` for those two in your app.php file.
 *
 * In case you need custom 404 mappings for some additional custom exceptions, make use of `log404` option.
 * It will overwrite the current defaults completely.
 *
 * @deprecated As of CakePHP 3.3+. Use Tools\Error\Middleware\ErrorHandlerMiddleware now.
 */
class ErrorHandler extends CoreErrorHandler {

	/**
	 * Handles exception logging
	 *
	 * @param \Exception $exception Exception instance.
	 * @return bool
	 */
	protected function _logException(Exception $exception) {
		$blacklist = ErrorHandlerMiddleware::$blacklist;
		if (isset($this->_options['log404'])) {
			$blacklist = $this->_options['log404'];
		}
		if ($blacklist && in_array(get_class($exception), (array)$blacklist)) {
			$level = LOG_ERR;
			Log::write($level, $this->_getMessage($exception), ['404']);
			return false;
		}
		return parent::_logException($exception);
	}

}
