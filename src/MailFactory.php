<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing;

use Nette,
	Nette\Mail\Message,
	Ublaboo;

class MailFactory extends Nette\Object
{

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
		$this->mailer = $mailer;
		$this->mails  = $mails;
		$this->linkGenerator = $linkGenerator;
		$this->templateFactory = $templateFactory;
		$this->logger = $logger;
	}


	/**
	 * Create email by given type
	 * @param  string $type
	 * @return Ublaboo\Mailing\Mail
	 * @throws MailCreationException
	 */
	public function createByType($type, $args)
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

		throw new MailCreationException("Error creating mail service [$type]");
	}

}


class MailCreationException extends \Exception
{
}
