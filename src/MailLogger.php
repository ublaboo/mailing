<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\Mailing;

use Nette;

class MailLogger extends Nette\Object implements ILogger
{

	CONST LOG_EXTENSION = '.eml';

	/**
	 * @var string
	 */
	protected $log_directory;


	public function __construct($log_directory)
	{
		$this->log_directory = $log_directory;
	}


	/**
	 * Log mail messages to eml file
	 * @param  string             $type
	 * @param  Nette\Mail\Message $mail
	 * @return void
	 */
	public function log($type, Nette\Mail\Message $mail)
	{
		$timestamp = date('Y-m-d H:i:s');
		$type .= '.' . time();
		$file = $this->getLogFile($type, $timestamp);

		if (file_exists($file) && filesize($file)) {
			$file = str_replace(static::LOG_EXTENSION, '.' . uniqid() . static::LOG_EXTENSION, $file);
		}

		file_put_contents($file, $mail->generateMessage());
	}


	/**
	 * If not already created, creat edirectory path that stickes to standard described above
	 * @param  string $type
	 * @param  string $timestamp
	 * @return string
	 */
	public function getLogFile($type, $timestamp)
	{
		preg_match('/^((([0-9]{4})-[0-9]{2})-[0-9]{2}).*/', $timestamp, $fragments);

		$year_dir  = $this->log_directory . '/' . $fragments[3];
		$month_dir = $year_dir . '/' . $fragments[2];
		$day_dir   = $month_dir . '/' . $fragments[1];
		$file      = $day_dir . '/' . $type . static::LOG_EXTENSION;

		if (!file_exists($day_dir)) {
			mkdir($day_dir, 0777, TRUE);
		}

		if (!file_exists($file)) {
			touch($file);
		}

		return $file;
	}

}
