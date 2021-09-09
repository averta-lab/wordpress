<?php
namespace Averta\WordPress\Utility;


class Str
{
	/**
	 * Generates and trims a persistent simple hash
	 *
	 * @param string $data   The input string.
	 * @param int    $start  If offset is negative, the returned string will start at the offset'th character from the end of string.
	 * @param int    $length If length is given and is positive, the string returned will contain at most length characters beginning from start.
	 *
	 * @return false|string
	 */
	public static function simpleHash( $data, $start = 0, $length = 10 ){
		return self::hash( $algorithm = 'md5' , $data, $start, $length );
	}

	/**
	 * Generates and trims a hash value
	 *
	 * @param string $algorithm   Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..)
	 * @param string $data        The input string.
	 * @param int    $start       If offset is negative, the returned string will start at the offset'th character from the end of string.
	 * @param int    $length      If length is given and is positive, the string returned will contain at most length characters beginning from start.
	 *
	 * @return false|string
	 */
	public static function hash( $algorithm , $data, $start = 0, $length = 100 ){
		return substr( hash( $algorithm, $data, false ), $start, $length );
	}

	/**
	 * Generates a persistent simple short hash
	 *
	 * @param string $data        The input string.
	 * @param bool   $binary
	 *
	 * @return string
	 */
	public static function shortHash( $data, $binary = false ){
		return hash( 'adler32', $data, $binary );
	}
}
