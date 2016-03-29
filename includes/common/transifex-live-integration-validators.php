<?php

/**
 * Common type validators for rewrite object
 * @package TransifexLiveIntegration
 */
class Transifex_Live_Integration_Validators
{

    static function is_hard_link_ok( $link ) 
    {
        if (!self::is_ok($link) ) {
            Plugin_Debug::logTrace('failed validator');
            return false;
        }
        if (false === stripos($link, 'http') ) {
            Plugin_Debug::logTrace('failed validator contains http');
            return false;
        }
        if (3 > substr_count($link, '/') ) {  //Note: this will return for home urls wo the trailing slash
            Plugin_Debug::logTrace('failed validator slash count');
            return false;
        }
        return true;
    }

    static function is_rules_ok( $rules ) 
    {
        if (!self::is_ok($rules) ) {
            Plugin_Debug::logTrace('failed validator');
            return false;
        }
        if (!is_array($rules) ) {
            Plugin_Debug::logTrace('failed validator is_array');
            return false;
        }
        return true;
    }

    static function is_permalink_ok( $permalink ) 
    {
        return self::is_ok($permalink);
    }

    static function is_query_ok( $query ) 
    {
        if (!self::is_ok($query) ) {
            Plugin_Debug::logTrace('failed validator');
            return false;
        }
        $query_vars = (isset($query->query_vars)) ? $query->query_vars : false;
        if (!self::is_query_vars_ok($query_vars) ) {
            Plugin_Debug::logTrace('failed validator query vars');
            return false;
        }
        return true;
    }

    static function is_query_vars_ok( $query_vars ) 
    {
        return self::is_ok($query_vars);
    }

    static function is_ok( $o ) 
    {
        if (!$o ) {
            Plugin_Debug::logTrace('failed validator is_ok false');
            return false;
        }
        if (!isset($o) ) {
            Plugin_Debug::logTrace('failed validator is_ok not isset');
            return false;
        }
        if (empty($o) ) {
            Plugin_Debug::logTrace('failed validator is_ok empty');
            return false;
        }
        return true;
    }

}
