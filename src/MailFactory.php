<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing;

use Nette;
use Nette\Mail\Message;
use Ublaboo;
use Ublaboo\Mailing\Exception\MailingMailCreationException;

class MailFactory
{
	use Nette\SmartObject;

	/**
	 * @var string
	 */
	private $config;

	/**
	 * @var Nette\Mail\IMailer
	 */
	private $mailer;

	/**
	 * @var Message
	 */
	private $message;

	/**
	 * @var array
	 */
	private $mails;

	/**
	 * @var Nette\Application\UI\ITemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var Nette\Application\LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @var ILogger
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $mail_images_base_path;


	/**
	 * @param string                                $config
	 * @param string                                $mail_images_base_path
	 * @param array                                 $mails
	 * @param Nette\Mail\IMailer                    $mailer
	 * @param Nette\Application\LinkGenerator       $linkGenerator
	 * @param Nette\Application\UI\ITemplateFactory $templateFactory
	 * @param ILogger                               $logger
	 */
	public function __construct(
		$config,
		$mail_images_base_path,
		$mails,
		Nette\Mail\IMailer $mailer,
		Nette\Application\LinkGenerator $linkGenerator,
		Nette\Application\UI\ITemplateFactory $templateFactory,
		ILogger $logger
	) {
		$this->config = $config;
		$this->mail_images_base_path = $mail_images_base_path;
		$this->mailer = $mailer;
		$this->mails = $mails;
		$this->linkGenerator = $linkGenerator;
		$this->templateFactory = $templateFactory;
		$this->logger = $logger;
	}


	/**
	 * Create email by given type
	 * @param  string     $type
	 * @param  mixed|null $args
	 * @return Ublaboo\Mailing\Mail
	 * @throws MailingMailCreationException
	 */
	public function createByType($type, $args = null)
	{
		$this->message = new Message;

		if (class_exists($type)) {
			$mail = new $type(
				$this->config,
				$this->mails,
				$this->mailer,
				$this->message,
				$this->linkGenerator,
				$this->templateFactory,
				$this->logger,
				$args
			);

			$mail->setBasePath($this->mail_images_base_path);

			return $mail;
		}

		throw new MailingMailCreationException("Email [$type] does not exist");
	}
}
