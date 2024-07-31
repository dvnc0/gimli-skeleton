<?php
declare(strict_types=1);

namespace App\Core;

use Redis;

class Cache {
	/**
	 * Cache Instance
	 * 
	 * @var Cache
	 */
	protected static Cache $instance;

	/**
	 * Redis Instance
	 * 
	 * @var Redis
	 */
	protected Redis $Redis;

	/**
	 * Connection Error
	 * 
	 * @var bool
	 */
	protected bool $connection_error = FALSE;

	/**
	 * Config Array
	 * 
	 * @var array
	 */
	protected array $config;

	/**
	 * Construct
	 *
	 * @param array $config The settings to apply to our environment
	 */
	public function __construct(array $config) {
		$this->config = $config;
		$this->Redis  = new Redis();
		try {
			$this->Redis->connect($config['host'], (int)$config['port']);
		} catch (RedisException $e) {
			return;
		}
	}

	/**
	 * Get Cache
	 *
	 * @param array $config The settings to apply to our environment
	 * 
	 * @return Cache
	 */
	public static function getCache(array $config) {
		if (!isset(self::$instance)) {
			self::$instance = new Cache($config);
		}

		return self::$instance;
	}

	/**
	 * Ready
	 *
	 * @return bool
	 */
	public function ready() {
		if ($this->connection_error === TRUE) {
			try {
				$this->Redis->connect($this->config['host'], (int)$this->config['port']);
				$this->connection_error = FALSE;
			} catch (RedisException $e) {
				return FALSE;
			}
		}

		if ($this->Redis->isConnected() === FALSE) {
			return;
		}

		if ($this->Redis->ping('hello') !== 'hello') {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Set
	 *
	 * @param string $key   key to set
	 * @param string $value value to set
	 * 
	 * @return void
	 */
	public function set(string $key, string $value) {
		$this->Redis->set($key, $value);
	}

	/**
	 * Get
	 *
	 * @param string $key key to get
	 * 
	 * @return mixed
	 */
	public function get(string $key) {
		return $this->Redis->get($key);
	}

	/**
	 * Delete
	 *
	 * @param string $key key to delete
	 * 
	 * @return void
	 */
	public function delete(string $key) {
		$this->Redis->del($key);
	}

	/**
	 * Close
	 *
	 * @return void
	 */
	public function close() {
		$this->Redis->close();
	}

	/**
	 * Set Expire
	 *
	 * @param string $key     key to set
	 * @param string $value   value to set
	 * @param int    $seconds seconds to expire
	 * 
	 * @return void
	 */
	public function setExpire(string $key, string $value, int $seconds = 3600) {
		$this->Redis->setEx($key, $seconds, $value);
	}

	/**
	 * Key Exists
	 *
	 * @param string $key key to check
	 * 
	 * @return bool
	 */
	public function keyExists(string $key): bool {
		return boolval($this->Redis->exists($key)) ?: FALSE;
	}
}