<?php

/*
 * Class Name: Plugin Debug
 * Description: An plugin/application error/trace log handler.  It *only* traps info from the plugin, and the display is controllable from "Settings" page
 * Author: Matthew Jackowski
 * Version: 0.2.1
 * Author URI: https://github.com/matthewjackowski/
 * License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
 */

class Plugin_Debug {

	private static $calls; //array of debug output messages
	private static $debug_mode;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		self::$debug_mode = true;
		$this->logTrace();

		// Check to see if plugin is in debug mode
		// If not, skip all display handlers and custom error handling
		if ( self::$debug_mode ) {
			set_error_handler( array( 'Plugin_Debug', 'logError' ) );
			// Check for admin level if not surpress all debug output hooks
			//TODO: Reimplement admin check
			if ( true ) {
				add_action( 'wp_footer', array( 'Plugin_Debug', 'printLog' ) );
				add_action( 'admin_footer', array( 'Plugin_Debug', 'printLog' ) );
			} // End if Wordpress user admin check
		} // End if debug mode check
	}

	public static function logTrace( $message = null ) {
		if ( self::$debug_mode ) {
			if ( !is_array( self::$calls ) ){
				self::$calls = array();
			}
			$call = debug_backtrace( false );
			$call = (isset( $call[1] )) ? $call[1] : $call[0];
			$call['message'] = $message;
			array_push( self::$calls, $call );
		}
	}

	public static function logError( $severity, $message, $filename, $lineno ) {
		if ( self::$debug_mode ) {
			if ( !is_array( self::$calls ) ){
				self::$calls = array();
			}
			if ( strpos( $filename, 'transifex-live-integration' ) ) {
				$call = debug_backtrace( false );
				$call = (isset( $call[2] )) ? $call[2] : $call[1];
				$call['message'] = 'Severity: '.$severity. ' File: ' . basename( $filename ) . ' Line: ' . $lineno . ': ' . $message;
				array_push( self::$calls, $call );
			}
		}
	}

	public function printLog() {
		if ( self::$debug_mode ) {
			echo ('<div id="miw_debug" class="transparent" style="width:90%;margin: 1em auto;padding: 10px 160px;text-align: left;z-index: 999;">' . "\n");
			echo ('<h3>Plugin: Plugin Debug Mode Output</h3>' . "\n");
			array_walk( self::$calls, array( 'Plugin_Debug', 'printLogCallback' ) );
			echo "</div>";
		}
	}

	static function printLogCallback( $value, $key ) {
		if ( self::$debug_mode ) {
			echo "*<br/>";
			if ( array_key_exists( 'file', $value ) )
				echo ("<b>File: " . basename( $value['file'] ) . "</b> - ");
			if ( array_key_exists( 'line', $value ) )
				echo ('<font color="green">Line #: ' . $value['line'] . '</font>');
			echo "<br/>";
			if ( array_key_exists( 'class', $value ) )
				echo ("<b>Class: " . $value['class'] . "</b> - ");
			if ( array_key_exists( 'function', $value ) )
				echo ('<font color="green">Function: ' . $value['function'] . '</font>');
			echo "<br/>";
			if ( array_key_exists( 'type', $value ) ) {
				echo ("<b>Type: ");
				switch ($value['type']) {
					case "::":
						echo ("static method call");
						break;
					case "->" :
						echo ("method call");
						break;
					default :
						echo ("function call");
				}
				echo("</b> - ");
			}
			if ( array_key_exists( 'args', $value ) ) {
				echo ('<font color="green">Parameters: ');
				print_r( $value['args'] );
				echo ('</font>');
			}
			echo "<br/>";
			if ( array_key_exists( 'message', $value ) && $value['message'] != null ){
				echo ('<font color="red">');
			}
			print_r( $value['message'] );
			echo ('</font>');
			echo "<br/>*";
		}
	}
	
/**
 * View any string as a hexdump.
 *
 * This is most commonly used to view binary data from streams
 * or sockets while debugging, but can be used to view any string
 * with non-viewable characters.
 *
 * @version     1.3.2
 * @author      Aidan Lister <aidan@php.net>
 * @author      Peter Waller <iridum@php.net>
 * @link        http://aidanlister.com/2004/04/viewing-binary-data-as-a-hexdump-in-php/
 * @param       string  $data        The string to be dumped
 * @param       bool    $htmloutput  Set to false for non-HTML output
 * @param       bool    $uppercase   Set to true for uppercase hex
 * @param       bool    $return      Set to true to return the dump
 */
static function hexdump ($data, $htmloutput = true, $uppercase = false, $return = false)
{
    // Init
    $hexi   = '';
    $ascii  = '';
    $dump   = ($htmloutput === true) ? '<pre>' : '';
    $offset = 0;
    $len    = strlen($data);
 
    // Upper or lower case hexadecimal
    $x = ($uppercase === false) ? 'x' : 'X';
 
    // Iterate string
    for ($i = $j = 0; $i < $len; $i++)
    {
        // Convert to hexidecimal
        $hexi .= sprintf("%02$x ", ord($data[$i]));
 
        // Replace non-viewable bytes with '.'
        if (ord($data[$i]) >= 32) {
            $ascii .= ($htmloutput === true) ?
                            htmlentities($data[$i]) :
                            $data[$i];
        } else {
            $ascii .= '.';
        }
 
        // Add extra column spacing
        if ($j === 7) {
            $hexi  .= ' ';
            $ascii .= ' ';
        }
 
        // Add row
        if (++$j === 16 || $i === $len - 1) {
            // Join the hexi / ascii output
            $dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);
            
            // Reset vars
            $hexi   = $ascii = '';
            $offset += 16;
            $j      = 0;
            
            // Add newline            
            if ($i !== $len - 1) {
                $dump .= "\n";
            }
        }
    }
 
    // Finish dump
    $dump .= $htmloutput === true ?
                '</pre>' :
                '';
    $dump .= "\n";
 
    // Output method
    if ($return === false) {
        echo $dump;
    } else {
        return $dump;
    }
}


}