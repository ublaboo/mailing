<?php

declare(strict_types=1);

namespace Ublaboo\Mailing\Tests\Unit;

use Nette\Mail\Message;
use Ublaboo\Mailing\AbstractMail;
use Ublaboo\Mailing\IComposableMail;

final class TestingMail extends AbstractMail implements IComposableMail
{

	/**
	 * @throws \InvalidArgumentException
	 */
	public function compose(Message $message, ?IMailData $mailData)
	{
		if (!$mailData instanceof TestingMailData) {
			throw new \InvalidArgumentException;
		}

		$message->setFrom("{$params['name']} <{$params['from']}>");
		$message->addTo($this->mails['recipient']);
	}
}
