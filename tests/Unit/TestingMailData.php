<?php

declare(strict_types=1);

namespace Ublaboo\Mailing\Tests\Unit;

use Ublaboo\Mailing\IMailData;

final class TestingMailData implements IMailData
{

	/**
	 * @var string
	 */
	private $foo;


	public function __construct(string $foo)
	{
		$this->foo = $foo;
	}


	public function getFoo(): string
	{
		return $this->foo;
	}
}
