<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing;

use Nette;

interface IComposableMail
{

	/**
	 * @param  Nette\Mail\Message $message
	 * @param  mixed              $params
	 * @return mixed
	 */
	public function compose(Nette\Mail\Message $message, $params = null);
}
