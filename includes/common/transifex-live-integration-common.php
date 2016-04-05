<?php

/**
 * Common libraries
 * @package TransifexLiveIntegration
 */

/**
 * Common Libraries, including PHP polyfills
 */
class Transifex_Live_Integration_Common {

	/**
	 * A static function that generates a map by locale for each language
	 * 
	 * @param string $raw_url The url to generate the map for
	 * @param string $tokenized_url The site_url that includes a language placeholder, 
	 * 		generally this should be from settings
	 * @param type $language_map This gives a map of Transifex Locale -> custom code,
	 * 		generally this should be from settings 
	 * @return array A list of key value where Locale->localized url
	 */
	static function generate_language_url_map( $raw_url, $tokenized_url,
			$language_map
	) {
		Plugin_Debug::logTrace();
		$trimmed_tokenized_url = rtrim( $tokenized_url, '/' );
		$trimmed_url = ltrim( $raw_url, '/' );
		$ret = [ ];
		foreach ($language_map as $k => $v) {
			$unslashed_url = str_replace( '%lang%', $v, $trimmed_tokenized_url ) . '/' . $trimmed_url;
			$ret[$k] = rtrim( $unslashed_url, '/' ) . '/';
		}

		return $ret;
	}

	/**
	 * This file is part of the array_column library
	 *
	 * For the full copyright and license information, please view the LICENSE
	 * file that was distributed with this source code.
	 *
	 * @copyright Copyright (c) Ben Ramsey (http://benramsey.com)
	 * @license   http://opensource.org/licenses/MIT MIT
	 */

	/**
	 * Returns the values from a single column of the input array, identified by
	 * the $columnKey.
	 *
	 * Optionally, you may provide an $indexKey to index the values in the returned
	 * array by the values from the $indexKey column in the input array.
	 *
	 * @param  array $input     A multi-dimensional array (record set) from which to pull a column of values.
	 *                     a column of values.
	 * @param  mixed $columnKey The column of values to return. This value may be the
	 *                         integer key of the column you wish to retrieve, or it
	 *                         may be the string key name for an associative array.
	 * @param  mixed $indexKey  (Optional.) The column to use as the index/keys for the returned array. This value may be the integer key of the column, or it may be the string key name.
	 *                        the returned array. This value may be the integer key
	 *                        of the column, or it may be the string key name.
	 * @return array
	 */
	static public function array_column( $input = null, $columnKey = null,
			$indexKey = null
	) {
		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc = func_num_args();
		$params = func_get_args();

		if ( $argc < 2 ) {
			trigger_error( "array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING );
			return null;
		}

		if ( !is_array( $params[0] ) ) {
			trigger_error(
					'array_column() expects parameter 1 to be array, ' . gettype( $params[0] ) . ' given', E_USER_WARNING
			);
			return null;
		}

		if ( !is_int( $params[1] ) && !is_float( $params[1] ) && !is_string( $params[1] ) && $params[1] !== null && !(is_object( $params[1] ) && method_exists( $params[1], '__toString' ))
		) {
			trigger_error( 'array_column(): The column key should be either a string or an integer', E_USER_WARNING );
			return false;
		}

		if ( isset( $params[2] ) && !is_int( $params[2] ) && !is_float( $params[2] ) && !is_string( $params[2] ) && !(is_object( $params[2] ) && method_exists( $params[2], '__toString' ))
		) {
			trigger_error( 'array_column(): The index key should be either a string or an integer', E_USER_WARNING );
			return false;
		}

		$paramsInput = $params[0];
		$paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

		$paramsIndexKey = null;
		if ( isset( $params[2] ) ) {
			if ( is_float( $params[2] ) || is_int( $params[2] ) ) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}

		$resultArray = array();

		foreach ($paramsInput as $row) {
			$key = $value = null;
			$keySet = $valueSet = false;

			if ( $paramsIndexKey !== null && array_key_exists( $paramsIndexKey, $row ) ) {
				$keySet = true;
				$key = (string) $row[$paramsIndexKey];
			}

			if ( $paramsColumnKey === null ) {
				$valueSet = true;
				$value = $row;
			} elseif ( is_array( $row ) && array_key_exists( $paramsColumnKey, $row ) ) {
				$valueSet = true;
				$value = $row[$paramsColumnKey];
			}

			if ( $valueSet ) {
				if ( $keySet ) {
					$resultArray[$key] = $value;
				} else {
					$resultArray[] = $value;
				}
			}
		}

		return $resultArray;
	}

}
