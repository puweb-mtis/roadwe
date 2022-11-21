<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * firephp
 *
 * @param    $var array, string, integer
 * @type    string : 'log', 'warn', 'error'
 */
if (!function_exists('firephp')) {
	function firephp($value, $type = 'log')
	{
		if ($type != 'log' and $type != 'warn' and $type != 'error') {
			$type = 'warn';
		}
		$CI = &get_instance();
		$CI->firephp->{$type}($value);
	}
}

//------------------------------------------------------------------------------

/**
 * firephp
 *
 * @type    string : log, warn, error
 */
if (!function_exists('firephp_last_query')) {
	function firephp_last_query($type = 'log')
	{
		if ($type != 'log' and $type != 'warn' and $type != 'error') {
			$type = 'warn';
		}
		$CI = &get_instance();
		$CI->firephp->{$type}($CI->db->last_query());
	}
}

//------------------------------------------------------------------------------

/**
 * Outputs the query result
 *
 * @type    string : log, warn, error
 */
if (!function_exists('firephp_session')) {
	function firephp_session($type = 'log')
	{
		if ($type != 'log' and $type != 'warn' and $type != 'error') {
			$type = 'warn';
		}
		$CI = &get_instance();
		$CI->firephp->{$type}($CI->session->all_userdata());
	}
}

//------------------------------------------------------------------------------
/**
 * Outputs an array or variable
 *
 * @param    $var array, string, integer
 * @return    string
 */
if (!function_exists('debug_var')) {
	function debug_var($var = '', $flag = FALSE)
	{
		echo _before();
		if (is_array($var)) {
			print_r($var);
		} elseif (is_object($var)) {
			print_r($var);
		} else {
			echo $var;
		}
		echo _after();
		if ($flag) die;
	}
}

//------------------------------------------------------------------------------

/**
 * Outputs the last query
 *
 * @return    string
 */
if (!function_exists('debug_last_query')) {
	function debug_last_query($flag = FALSE)
	{
		$CI = &get_instance();
		echo _before();
		echo $CI->db->last_query();
		echo _after();
		if ($flag) die;
	}
}
//------------------------------------------------------------------------------

/**
 * Outputs the query result
 *
 * @param    $query object
 * @return    string
 */
if (!function_exists('debug_query_result')) {
	function debug_query_result($query = null)
	{
		echo _before();
		print_r($query->result_array());
		echo _after();
	}
}

//------------------------------------------------------------------------------

/**
 * Outputs all session data
 *
 * @return    string
 */
if (!function_exists('debug_session')) {
	function debug_session()
	{
		$CI = &get_instance();
		echo _before();
		print_r($CI->session->all_userdata());
		echo _after();
	}
}

//------------------------------------------------------------------------------

/**
 * Logs a message or var
 *
 * @param    $message array, string, integer
 * @return    string
 */
if (!function_exists('debug_log')) {
	function debug_log($message = '')
	{
		is_array($message) ? log_message('debug', print_r($message)) : log_message('debug', $message);
	}
}

//------------------------------------------------------------------------------

/**
 * _before
 *
 * @return    string
 */

function _before()
{
	$before = '<div style=\'padding:10px 20px 10px 20px; background-color:#fbe6f2; border:1px solid #d893a1; color: #000; font-size: 12px;\'>' . PHP_EOL;
	$before .= '<h5 style=\'font-family:verdana,sans-serif; font-weight:bold; font-size:18px;\'>Debug Helper Output</h5>' . PHP_EOL;
	$before .= '<pre>' . PHP_EOL;
	return $before;
}

//------------------------------------------------------------------------------

/**
 * _after
 *
 * @return    string
 */

function _after()
{
	$after = '</pre>' . PHP_EOL;
	$after .= '</div>' . PHP_EOL;
	return $after;
}

//------------------------------------------------------------------------------

/**
 * dg_var
 *
 * @param string $data
 * @param string $data_name
 */
if (!function_exists('dg_var')) {
	function dg_var($data = 'usage: fred(\'any type of data\')', $data_name = 'NULL')
	{
		$data_type = '';
		// $data objects do not display as an array so...
		if (is_object($data)) {
			$data = get_object_vars($data); // returns with $data = array();
		}

		// maybe find the $data type
		if (empty($data)) {
			$data_type     = 'empty()';
		} else {
			switch ($data) {
				case ('' == $data):
					$data_type	= 'empty()';
					break;
				case is_array($data):
					$data_count	= count($data);
					$data_type	= 'array($data_count)';
					break;
				case is_bool($data):
					$data_type	= 'boolean';
					$data		= $data ? 'TRUE' : 'FALSE';
					break;
				case is_numeric($data):
					$data_type	= 'numeric';
					break;
				case is_object($data):
					$data_type	= 'object';
					$value = isset($my_class) ? $my_class : '';
					$data		= get_object_vars($value);
					break;
				case is_resource($data):
					$data_type	= 'resource';
					$data_count	= mysqli_num_rows($data);
					$tmp		= array();
					while ($row = mysqli_fetch_assoc($data)) {
						$tmp[] = $row;
					}
					$data = $tmp;
					break;
				case is_string($data):
					$data_type	= 'string';
					$data_count	= strlen($data);
					break;
				default:
					$data_type	= 'oddball';
					$data_value	= $data;
			} //end switch
		} //endif

		// $data must now be an array or a string, numeric, or...
		$style = 'width:96%; margin:1em; overflow:auto;text-align:left; font-family:Courier; font-size:0.86em; background:#efe none; color:#000; text-align:left; border:solid 1px;padding:0.42em';
		echo '<fieldset style=\'' . $style . '\'>';
		echo    '<br /><b style=\'color:#f00\'>Name &nbsp; ==> '    . $data_name . '</b>';
		echo    '<br /><b>Type &nbsp; ==> </b>'        . $data_type;
		if (isset($data_count)) {
			echo    '<br /><b>Count&nbsp; ==> </b>'        . $data_count;
		}
		echo    '<br /><b>Value &nbsp;==> </b>';
		echo    '<pre style=\'width:58.88%; margin:-1.2em 0 1em 9.0em;overflow:auto\'>';
		print_r($data);
	}
}

//------------------------------------------------------------------------------



/* End of file debug_helper.php */
/* Location: ./intranetapp/helpers/debug_helper.php */
