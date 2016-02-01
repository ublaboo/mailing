<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing;

use Nette;

interface ILogger
{

	/**
	 * Log mail messages to eml file
	 * @param  string $type
	 * @return void
	 */
	public function log($type, Nette\Mail\Message $mail);

}
