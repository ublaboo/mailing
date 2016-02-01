<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing\DI;

use Nette,
	Ublaboo\Mailing\Mail;

class MailingExtension extends Nette\DI\CompilerExtension
{

	private $defaults = [
		'do' => Mail::CONFIG_BOTH,
		'log_directory' => '%appDir%/../log/mails',
		'mail_images_base_path' => '%wwwDir%',
		'mails' => []
	];


	public function loadConfiguration()
	{
		$config = $this->_getConfig();

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('mailLogger'))
			->setClass('Ublaboo\Mailing\MailLogger')
			->setArguments([$config['log_directory']]);

		$builder->addDefinition($this->prefix('mailFactory'))
			->setClass('Ublaboo\Mailing\MailFactory')
			->setArguments([$config['do'], $config['mail_images_base_path'], $config['mails']]);
	}


	private function _getConfig()
	{
		$config = $this->validateConfig($this->defaults, $this->config);

		$config['log_directory'] = Nette\DI\Helpers::expand(
			$config['log_directory'],
			$this->getContainerBuilder()->parameters
		);

		$config['mail_images_base_path'] = Nette\DI\Helpers::expand(
			$config['mail_images_base_path'],
			$this->getContainerBuilder()->parameters
		);

		return $config;
	}

}
