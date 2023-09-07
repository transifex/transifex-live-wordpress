<?php

/**
 * Common type validators for rewrite object
 * @package TransifexLiveIntegration
 */
/*
 * Validator class, which has a series of boolean static functions
 * 	these functions are used primarily to check params coming from WP filter hooks
 */
class Transifex_Live_Integration_Validators {
	/*
	 * Checks urls coming from WP filter, assumes that it is path url
	 * @param string $link The url
	 * @return bool Returns true if $link is as expected, false if not
	 */

	static function is_hard_link_ok( $link ) {
		if ( !self::is_ok( $link ) ) {
			Plugin_Debug::logTrace( 'failed validator' );
			return false;
		}
		if ( false === stripos( $link, 'http' ) ) {
			Plugin_Debug::logTrace( 'failed validator contains http' );
			return false;
		}
		if ( 3 > substr_count( $link, '/' ) ) {  //Note: this will return for home urls wo the trailing slash
			Plugin_Debug::logTrace( 'failed validator slash count '. $link );
			return false;
		}
		return true;
	}

	/*
	 * Checks rewrite rules coming from WP filter
	 * @param array $rules The list of rules
	 * @return bool Returns true if $rules is as expected, false if not
	 */

	static function is_rules_ok( $rules ) {
		if ( !self::is_ok( $rules ) ) {
			Plugin_Debug::logTrace( 'failed validator' );
			return false;
		}
		if ( !is_array( $rules ) ) {
			Plugin_Debug::logTrace( 'failed validator is_array' );
			return false;
		}
		return true;
	}

	/*
	 * Checks a permalink coming from WP filter
	 * @param string $permalink The permalink
	 * @return bool Returns true if $permalink is as expected, false if not
	 */

	static function is_permalink_ok( $permalink ) {
		return self::is_ok( $permalink );
	}

	/*
	 * Checks WP query object coming from WP filter
	 * @param object $query The query object
	 * @return bool Returns true if $query is as expected, false if not
	 */

	static function is_query_ok( $query ) {
		if ( !self::is_ok( $query ) ) {
			Plugin_Debug::logTrace( 'failed validator' );
			return false;
		}
		$query_vars = (isset( $query->query_vars )) ? $query->query_vars : false;
		if ( !self::is_query_vars_ok( $query_vars ) ) {
			Plugin_Debug::logTrace( 'failed validator query vars' );
			return false;
		}
		return true;
	}

	/*
	 * Checks WP query vars object coming from WP filter
	 * @param object $query_vars The query vars object
	 * @return bool Returns true if $query_vars is as expected, false if not
	 */

	static function is_query_vars_ok( $query_vars ) {
		return self::is_ok( $query_vars );
	}

	/*
	 * Checks a general object for null/empty cases
	 * @param object $o
	 * @return bool Returns true if $o is as expected, false if not
	 */

	static function is_ok( $o ) {
		if ( !$o ) {
			Plugin_Debug::logTrace( 'failed validator is_ok false' );
			return false;
		}
		if ( !isset( $o ) ) {
			Plugin_Debug::logTrace( 'failed validator is_ok not isset' );
			return false;
		}
		if ( empty( $o ) ) {
			Plugin_Debug::logTrace( 'failed validator is_ok empty' );
			return false;
		}
		return true;
	}

}
