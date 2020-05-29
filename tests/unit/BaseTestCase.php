<?php
/*
 * This class caters for the different PHPUnit versions between production (CI)
 * and development.
 * To execute unit tests use :
 * 
 *   php codecept.phar run unit --debug
 */
if (class_exists('\PHPUnit_Framework_TestCase')) {
	// php 5.6, production - CI environment
	class BaseTestCase extends \PHPUnit_Framework_TestCase {}
} else {
	// php 7+, dev environment
	class BaseTestCase extends PHPunit\Framework\TestCase {}
}