<?php
namespace Averta\WordPress\Cache;

use Psr\SimpleCache\CacheInterface;

class WPCache implements CacheInterface{

	/**
	 * The list of transoent keys
	 *
	 * @var array
	 */
	private $inUseKeys = [];

	/**
	 *  A prefix for all transient keys
	 *
	 * @var string
	 */
	protected $keyPrefix = '';


	/**
	 * Cache constructor.
	 *
	 * @param null $cachePrefix
	 */
	public function __construct( $cachePrefix = null )
	{
		if( ! is_null( $cachePrefix ) ){
			$this->keyPrefix = $cachePrefix;
		}
	}

	/**
	 * Get the value of a transient.
	 *
	 * If the transient does not exist, does not have a value, or has expired,
	 * then the return value will be false.
	 *
	 * @param string $key  Cache key. Expected to not be SQL-escaped.
	 *
	 * @return mixed Value of transient.
	 */
	public function get( $key, $default = false ) {
		$key = $this->validateKey( $key );

		$value = get_transient( $key );
		if ( false === $value ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Set/update the value of a transient.
	 *
	 * You do not need to serialize values. If the value needs to be serialized, then
	 * it will be serialized before it is set.
	 *
	 *
	 * @param string $key  		 Cache key. Expected to not be SQL-escaped. Must be
	 *                           172 characters or fewer in length.
	 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
	 *                           Expected to not be SQL-escaped.
	 * @param int    $ttl        Optional. Time until expiration in seconds. Default 0 (no expiration).
	 *
	 * @return bool False if value was not set and true if value was set.
	 */
	public function set( $key, $value, $ttl = null ): bool {
		$key = $this->validateKey( $key );
		$this->addToKeysList( $key );

		if ( $ttl instanceof DateInterval ) {
			$ttl = $this->convertDateIntervalToInteger( $ttl );
		}

		return set_transient( $key, $value, intval($ttl) );
	}

	/**
	 * Delete a transient.
	 *
	 * @param string $key  Cache key. Expected to not be SQL-escaped.
	 *
	 * @return bool true if successful, false otherwise
	 */
	public function delete( $key ): bool {
		$key = $this->validateKey( $key );
		$this->deleteFromKeyList( $key );

		return delete_transient( $key );
	}

	/**
	 * @inheritDoc
	 */
	public function has( $key ): bool {
		return $this->get( $key, false ) !== false;
	}

	/**
	 * @inheritDoc
	 */
	public function getMultiple( $keys, $default = null ) {
		$result = [];

		foreach ( $keys as $key ) {
			$result[ $key ] = $this->get( $key, $default );
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function setMultiple( $values, $ttl = null ): bool {
		foreach ( $values as $key => $value ) {
			if ( $this->set( $key, $value, $ttl ) ) {
				continue;
			}
			return false;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function deleteMultiple( $keys ): bool {

		foreach ( $keys as $key ) {
			if ( $this->delete( $key ) ) {
				continue;
			}
			return false;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function clear(): bool {

		if( ! empty( $this->keyPrefix ) ) {
			global $wpdb;
			$wpdb->query($wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				"_transient_{$this->keyPrefix}%",
				"_transient_timeout_{$this->keyPrefix}%"
			));
		}

		return $this->deleteMultiple( $this->inUseKeys() );
	}

	/**
	 * @param DateInterval $ttl
	 *
	 * @return int
	 */
	private function convertDateIntervalToInteger( DateInterval $ttl ) : int {

		return ( new DateTime() )
			->setTimestamp(0)
			->add( $ttl )
			->getTimestamp();
	}

	/**
	 * Adds a key to cache key list
	 *
	 * @param string $key
	 */
	private function addToKeysList( $key ): void {
		$this->inUseKeys[ $key ] = $key;
	}

	/**
	 * Removes a key from cache key list
	 *
	 * @param string $key
	 */
	private function deleteFromKeyList( $key ): void {
		unset( $this->inUseKeys[ $key ] );
	}

	/**
	 * @return array
	 */
	private function inUseKeys(): array {
		return $this->inUseKeys;
	}

	private function validateKey( $key ){
		return $this->keyPrefix . ltrim( $key, $this->keyPrefix );
	}


	public function prevent(){
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}

		if ( ! defined( 'DONOTMINIFY' ) ) {
			define( 'DONOTMINIFY', true );
		}

		if ( ! defined( 'DONOTCDN' ) ) {
			define( 'DONOTCDN', true );
		}

		if ( ! defined( 'DONOTCACHCEOBJECT' ) ) {
			define( 'DONOTCACHCEOBJECT', true );
		}

		// prevent caching.
		nocache_headers();
	}

}
