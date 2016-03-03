<?php

namespace Ublaboo\Mailing\Tests\Cases;

use Tester\TestCase;
use Tester\Assert;
use Mockery;
use Ublaboo\Mailing\Mail;
use Ublaboo\Mailing\MailFactory;
use Ublaboo\Mailing\Exception\MailingException;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../mails/XTestingMail.php';

final class MailTest extends TestCase
{

	public function createMail($config)
	{
		$mail_images_base_path = NULL;
		$mails = ['recipient' => 'mail@ma.il'];

		$mailer          = Mockery::mock('Nette\Mail\IMailer');
		$linkGenerator   = Mockery::mock('Nette\Application\LinkGenerator');
		$templateFactory = Mockery::mock('Nette\Application\UI\ITemplateFactory');
		$logger          = Mockery::mock('Ublaboo\Mailing\ILogger');
		$template        = Mockery::mock('Nette\Application\UI\ITemplate');

		$templateFactory->shouldReceive('createTemplate')->andReturn($template);

		$template->file = FALSE;
		$template->shouldReceive('setFile')->andSet('file', TRUE);

		$mailer->sent = FALSE;
		$mailer->shouldReceive('send')->andSet('sent', TRUE);

		$logger->logged = FALSE;
		$logger->shouldReceive('log')->andSet('logged', TRUE);

		$mailFactory = new MailFactory(
			$config,
			$mail_images_base_path,
			$mails,
			$mailer,
			$linkGenerator,
			$templateFactory,
			$logger
		);

		$mail = $mailFactory->createByType(
			'Ublaboo\Mailing\Tests\Mails\XTestingMail',
			['name' => 'Name', 'from' => 'recipient@recipi.ent']
		);

		return [$mail, $template, $logger, $mailer];
	}


	public function testDoBoth()
	{
		list($mail, $template, $logger, $mailer) = $this->createMail(Mail::CONFIG_BOTH);

		Assert::exception([$mail, 'getTemplateFile'], MailingException::class);

		$mail->setTemplateFile($this->getTmpTemplateFile('XTestingMail'));
		$mail->send();
		$this->destroyTmpTemplateFile('XTestingMail');

		Assert::true($template->file, 'Mail template has file set (do = both)');
		Assert::true($logger->logged, 'Mail logged (do = both)');
		Assert::true($mailer->sent, 'Mail sent (do = both)');
	}


	public function testDoSend()
	{
		list($mail, $template, $logger, $mailer) = $this->createMail(Mail::CONFIG_SEND);

		Assert::exception([$mail, 'getTemplateFile'], MailingException::class);

		$mail->setTemplateFile($this->getTmpTemplateFile('XTestingMail'));
		$mail->send();
		$this->destroyTmpTemplateFile('XTestingMail');

		Assert::true($template->file, 'Mail template has file set (do = send)');
		Assert::false($logger->logged, 'Mail logged (do = send)');
		Assert::true($mailer->sent, 'Mail sent (do = send)');
	}


	public function testDoLog()
	{
		list($mail, $template, $logger, $mailer) = $this->createMail(Mail::CONFIG_LOG);

		Assert::exception([$mail, 'getTemplateFile'], MailingException::class);

		$mail->setTemplateFile($this->getTmpTemplateFile('XTestingMail'));
		$mail->send();
		$this->destroyTmpTemplateFile('XTestingMail');

		Assert::true($template->file, 'Mail template has file set (do = log)');
		Assert::true($logger->logged, 'Mail logged (do = log)');
		Assert::false($mailer->sent, 'Mail sent (do = log)');
	}


	public function testSetBody()
	{
		list($mail, $template, $logger, $mailer) = $this->createMail(Mail::CONFIG_BOTH);

		Assert::exception([$mail, 'getTemplateFile'], MailingException::class);

		$mail->send();

		Assert::false($template->file, 'Mail template has file set');
		Assert::true($logger->logged, 'Mail logged');
		Assert::true($mailer->sent, 'Mail sent');
	}


	private function getTmpTemplateFile($name)
	{
		$name = lcfirst(preg_replace_callback('/(?<=.)([A-Z])/', function ($m) {
			return '_' . strtolower($m[1]);
		}, $name));

		$path = __DIR__ . "/../templates/$name.latte";

		@mkdir(dirname($path));
		$fh = fopen($path, 'w+');
		fwrite($fh, '<!DOCTYPE html><html><head><title>Contact</title></head><body>{$name}{$email}</body></html>');
		fclose($fh);

		return $path;
	}


	private function destroyTmpTemplateFile($name)
	{
		$name = lcfirst(preg_replace_callback('/(?<=.)([A-Z])/', function ($m) {
			return '_' . strtolower($m[1]);
		}, $name));

		unlink(__DIR__ . "/../templates/$name.latte");
		rmdir(__DIR__ . '/../templates');
	}

}


$test_case = new MailTest;
$test_case->run();
