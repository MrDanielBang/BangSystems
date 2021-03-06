<?php
namespace Bang;

class Config {
	protected static
		$data;
	function __construct(string $configFile) {
		#self::init();
		self::$data = (object) [];
		return self::install($configFile);
	}
	static function init() {
		#if (!is_object(self::$data)) self::$data = (object) [];
	}
	static function install(string $configFile) {
		try {
			echo 'Config::install('.$configFile.')'.PHP_EOL;
			if (!file_exists($configFile)) throw new \Error(
				sprintf('missing config %s', $configFile),
				10002
			);
			$config = (object) include $configFile;
			foreach ($config as $k => $v) {
				switch ($k) {
					case 'domain':
						Core::setupDomain($v);
					break;
					case 'host':
						Core::setupHost($v);
					break;
				}
				self::set($k, $v);
			}
		}
		catch (Error $e) {
			throw new Error($e);
		}
	}
	# Config::get('pdo', 'username')
	static function get(string $x, string $y = null) {
		if (!empty($y))
			return self::has($x, $y) ? self::$data->{$x}->{$y} : null;
		return self::has($x) ? self::$data->{$x} : null;
	}
	static function set(string $x, $y, string $z = null) {
		if (!is_null($z))
			return self::$data->{$x}->{$y} = $z;
		return self::$data->{$x} = $y;
	}
	static function has(string $x, string $y = null) {
		if (!is_null($y))
			return isset(self::$data->{$x}->{$y});
		return isset(self::$data->{$x});
	}
	static function isset(string $x, string $y = null) {
		return self::has($x, $y);
	}
	static function empty(string $x, string $y = null) {
		return !(self::has($x, $y) && self::get($x, $y));
	}
	static function unset(string $x, string $y = null) {
		if (!is_null($y) && isset(self::$data->{$x}->{$y}))
			unset(self::$data->{$x}->{$y});
			return;
		unset(self::$data->{$x});
	}
	static function pair(string $x, &$y, string &$z = null) {
		if (!is_null($y))
			return self::$data->{$x}->{$y} = &$z;
		return self::$data->{$x} = &$z;
	}
	static function debug() {
		return self::$data;
	}

	function __set(string $x, $y) {
		return self::set($x, $y);
	}
	function __get(string $x) {
		return self::get($x);
	}
	function __isset(string $x) {
		return self::isset($x);
	}
	function __unset(string $x) {
		return self::unset($x);
	}

	function __debugInfo() {
		return self::$data;
	}
	function __toString() {
		echo json_encode($this->__debugInfo());
	}
}
