<?php

declare(strict_types=1);

namespace Ublaboo\Mailing\Tests\Unit;

use Nette\Mail\Message;
use Ublaboo\Mailing\AbstractMail;
use Ublaboo\Mailing\IComposableMail;
use Ublaboo\Mailing\IMessageData;

final class TestingMail extends AbstractMail implements IComposableMail
{

	/**
	 * @throws \InvalidArgumentException
	 */
	public function compose(Message $message, ?IMessageData $mailData): void
	{
		if (!$mailData instanceof TestingMailData) {
			throw new \InvalidArgumentException;
		}

		$message->setFrom(sprintf("%s <%s>", $mailData->getName(), $mailData->getFrom()));
		$message->addTo($this->mailAddresses['recipient']);
	}
}
