<?php

namespace Tontonsb\Sonar\Services;

class Config
{
	protected $config = [];

	public function set($key, $value): void
	{
		$this->config[$key] = $value;
	}

	public function get($key)
	{
		return $this->config[$key] ?? null;
	}
}
