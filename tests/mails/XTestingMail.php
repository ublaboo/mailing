<?php

namespace Ublaboo\Mailing\Tests\Mails;

use App,
	Nette,
	Ublaboo\Mailing\Mail,
	Ublaboo\Mailing\IComposableMail;

final class XTestingMail extends Mail implements IComposableMail
{

	public function compose(Nette\Mail\Message $message, $params = NULL)
	{
		$message->setFrom("{$params['name']} <{$params['from']}>");
		$message->addTo($this->mails['recipient']);
	}

}
