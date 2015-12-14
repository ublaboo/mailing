<?php

namespace Ublaboo\Mailing\Tests\Cases;

use Tester\TestCase,
	Tester\Assert,
	Mockery,
	Ublaboo\Mailing\MailLogger;

require __DIR__ . '/../bootstrap.php'; 

final class MailLoggerTest extends TestCase
{

	public function testLogDirectory()
	{
		$log_dir = 'foo';
		$logger = new MailLogger($log_dir);

		$computed_path = $logger->getLogFile('bar', '2015-12-14 22:11:09');
		Assert::same($computed_path, 'foo/2015/2015-12/2015-12-14/bar.eml');

		@unlink($computed_path);
	}

}


$test_case = new MailLoggerTest;
$test_case->run();
