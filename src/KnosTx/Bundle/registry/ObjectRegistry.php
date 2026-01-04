<?php

declare(strict_types=1);

namespace KnosTx\Bundle\registry;

use ArgumentCountError;
use Error;
use InvalidArgumentException;

abstract class ObjectRegistry{

	/** @var array<string, object> */
	private static array $members = [];

	private static bool $initialized = false;

	final public static function setup() : void{
		if(self::$initialized){
			return;
		}

		self::$initialized = true;
		self::$members = [];
		static::registerDefaults();
	}

	abstract protected static function registerDefaults() : void;

	final protected static function normalize(string $name) : string{
		if(!preg_match('/^(?!\d)[A-Za-z\d_]+$/u', $name)){
			throw new InvalidArgumentException("Invalid registry name \"$name\"");
		}
		return mb_strtoupper($name);
	}

	final protected static function registerObject(string $name, object $object) : void{
		if(!self::$initialized){
			throw new Error(static::class . "::setup() not called");
		}

		$key = self::normalize($name);

		if(isset(self::$members[$key])){
			throw new InvalidArgumentException("Duplicate registry entry \"$key\"");
		}

		self::$members[$key] = $object;
	}

	final protected static function get(string $name) : object{
		if(!self::$initialized){
			throw new Error(static::class . "::setup() not called");
		}

		$key = mb_strtoupper($name);

		if(!isset(self::$members[$key])){
			throw new InvalidArgumentException(
				"No such registry member: " . static::class . "::$key"
			);
		}

		return static::preprocess(self::$members[$key]);
	}

	protected static function preprocess(object $object) : object{
		return $object;
	}

	final public static function __callStatic($name, $args){
		if(count($args) !== 0){
			throw new ArgumentCountError("Expected 0 arguments");
		}

		try{
			return static::get($name);
		}catch(InvalidArgumentException $e){
			throw new Error($e->getMessage(), 0, $e);
		}
	}

	final public static function all() : array{
		if(!self::$initialized){
			throw new Error(static::class . "::setup() not called");
		}

		return array_map(
			static fn(object $o) => static::preprocess($o),
			self::$members
		);
	}
}
