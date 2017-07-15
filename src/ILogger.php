<?php

declare(strict_types=1);

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
	 * @param  string             $type
	 * @param  Nette\Mail\Message $mail
	 * @return void
	 */
	public function log($type, Nette\Mail\Message $mail);
}
