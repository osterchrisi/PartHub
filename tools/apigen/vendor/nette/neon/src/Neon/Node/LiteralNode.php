<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Neon\Node;

use Nette\Neon\Node;


/** @internal */
final class LiteralNode extends Node
{
	private const SimpleTypes = [
		'true' => true, 'True' => true, 'TRUE' => true, 'yes' => true, 'Yes' => true, 'YES' => true,
		'false' => false, 'False' => false, 'FALSE' => false, 'no' => false, 'No' => false, 'NO' => false,
		'null' => null, 'Null' => null, 'NULL' => null,
	];

	private const PatternDatetime = '#\d\d\d\d-\d\d?-\d\d?(?:(?:[Tt]| ++)\d\d?:\d\d:\d\d(?:\.\d*+)? *+(?:Z|[-+]\d\d?(?::?\d\d)?)?)?$#DA';
	private const PatternHex = '#0x[0-9a-fA-F]++$#DA';
	private const PatternOctal = '#0o[0-7]++$#DA';
	private const PatternBinary = '#0b[0-1]++$#DA';


	public function __construct(
		public mixed $value,
	) {
	}


	public function toValue(): mixed
	{
		return $this->value;
	}


	public static function parse(string $value, bool $isKey = false): mixed
	{
		if (!$isKey && array_key_exists($value, self::SimpleTypes)) {
			return self::SimpleTypes[$value];

		} elseif (is_numeric($value)) {
			return $value * 1;

		} elseif (preg_match(self::PatternHex, $value)) {
			return hexdec($value);

		} elseif (preg_match(self::PatternOctal, $value)) {
			return octdec($value);

		} elseif (preg_match(self::PatternBinary, $value)) {
			return bindec($value);

		} elseif (!$isKey && preg_match(self::PatternDatetime, $value)) {
			return new \DateTimeImmutable($value);

		} else {
			return $value;
		}
	}


	public function toString(): string
	{
		if ($this->value instanceof \DateTimeInterface) {
			return $this->value->format('Y-m-d H:i:s O');

		} elseif (is_string($this->value)) {
			return $this->value;

		} elseif (is_float($this->value)) {
			$res = json_encode($this->value);
			return str_contains($res, '.') ? $res : $res . '.0';

		} elseif (is_int($this->value) || is_bool($this->value) || $this->value === null) {
			return json_encode($this->value);

		} else {
			throw new \LogicException;
		}
	}
}
