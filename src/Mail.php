<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing;

use Nette,
	Ublaboo;

abstract class Mail extends Nette\Object
{

	const CONFIG_LOG  = 'log';
	const CONFIG_SEND = 'send';
	const CONFIG_BOTH = 'both';


	/**
	 * @var string
	 */
	private $config;

	/**
	 * @var array
	 */
	private $mails;

	/**
	 * @var Nette\Mail\IMailer
	 */
	protected $mailer;

	/**
	 * @var Nette\Mail\Message
	 */
	protected $message;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var Nette\Application\LinkGenerator
	 */
	protected $linkGenerator;

	/**
     * @var MailLogger
     */
    protected $logger;

	/**
	 * @var Nette\Application\UI\ITemplate
	 */
	protected $template;

	/**
	 * @var stirng
	 */
	protected $underscore_name;

	/**
	 * @var string
	 */
	protected $log_type;

	/**
	 * @var string
	 */
	protected $base_path = NULL;

	/**
	 * @var string
	 */
	protected $template_file;


	public function __construct(
		$config,
		$mails,
		Nette\Mail\IMailer $mailer,
		Nette\Mail\Message $message,
		Nette\Application\LinkGenerator $linkGenerator,
		Nette\Application\UI\ITemplateFactory $templateFactory,
		MailLogger $logger,
		$args
	) {
		$this->config = $config;
		$this->mails = $mails;
		$this->mailer = $mailer;
		$this->message = $message;
		$this->linkGenerator = $linkGenerator;
		$this->logger = $logger;
		$this->args = $args;

		$this->template = $templateFactory->createTemplate();

		/**
		 * Initiate mail composing
		 */
		$this->compose($this->message, $this->args);
	}


	/**
	 * Set template file
	 * @return void
	 */
	public function setTemplateFile($template_file)
	{
		$this->template_file = (string) $template_file;
	}


	/**
	 * Set template variables
	 * @return void
	 */
	protected function setTemplateVariables()
	{
		foreach ($this->args as $key => $value) {
			$this->template->$key = $value;
		}
	}


	/**
	 * Set absolute base path for images
	 * @param string $mail_images_base_path
	 */
	public function setBasePath($mail_images_base_path)
	{
		$this->mail_images_base_path = (string) $mail_images_base_path;

		return $this;
	}


	/**
	 * Stick to convention that Email:
	 * 		?/mailing/Mails/FooMail.php	
	 * 
	 * will have template with path of:
	 * 		?/mailing/Mails/templates/foo_mail.latte
	 * 
	 * @return string
	 */
	protected function getTemplateFile()
	{
		/**
		 * Get child class file path
		 * @var \ReflectionClass
		 */
		$reflection = new \ReflectionClass(get_class($this));
		
		/**
		 * Split path to directory and file
		 */
		$class_path = $reflection->getFileName();
		$class_dir = dirname($class_path);
		$class_name = pathinfo($class_path, PATHINFO_FILENAME);

		/**
         * Convert class name to underscore and set latte file extension
         */
        $this->underscore_name = lcfirst(preg_replace_callback('/(?<=.)([A-Z])/', function ($m) {
            return '_' . strtolower($m[1]);
        }, $class_name));

		$template_name = $this->underscore_name . '.latte';
		$this->log_type = $this->underscore_name;

		return $class_dir . '/templates/' . $template_name;
	}


	/**
	 * Render latte template to string and send (and/or log) mail
	 * @return void
	 */
	public function send()
	{
		/**
		 * Set template file and variables
		 */
		$this->template->setFile($this->template_file ?: $this->getTemplateFile());
		$this->setTemplateVariables();

		/**
		 * Set html body
		 */
		$this->message->setHtmlBody((string) $this->template, $this->base_path);

		/**
		 * In case mail sending in on, send message
		 */
		if ($this->config === self::CONFIG_BOTH || $this->config === self::CONFIG_SEND) {
			$this->mailer->send($this->message);
		}

		/**
		 * In case mail logging in on, log message
		 */
		if ($this->config === self::CONFIG_LOG || $this->config === self::CONFIG_BOTH) {
			$this->logger->log($this->log_type, $this->message);
		}
	}

}
