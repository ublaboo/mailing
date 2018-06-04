<?php

declare(strict_types=1);

namespace Ublaboo\Mailing\Tests\Unit;

use Ublaboo\Mailing\IMessageData;

final class TestingMailData implements IMessageData
{

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $from;


	public function __construct(string $name, string $from)
	{
		$this->name = $name;
		$this->from = $from;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getFrom(): string
	{
		return $this->from;
	}
}
