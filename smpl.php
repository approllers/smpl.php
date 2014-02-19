<?php
session_start();

#######################################################################
##########################                   ##########################
##########################   CONFIGURATION   ##########################
##########################                   ##########################
#######################################################################


// constant variables are used in this framework because functions cannot be called 
// to set default argument values in another function.

# YOU SHOULD EDIT THESE...
define('PATH_APPS', 'apps/');
define('PATH_HOOKS', 'hooks/');
define('PATH_BLOCKS', 'blocks/');
define('PATH_ASSETS', 'assets/');
define('SALT', '8r&lt;Q)i2d4(JO;&gt;Hq~mcqu&lt;,CzR`Ki:#qqZ}8}]l\~#GZZ&lt;&gt;wh8XL3T&lt;ChV\zTW~|sN:fDj89&lt;P&amp;UK#.`_Ui{jcoB&amp;h(x+Goc0msD');

# YOU SHOULD PROBABLY NOT EDIT THESE...
define('PATH_ROOT', dirname(__FILE__) .'/'); // path to document root directory of the simpl framework from system root
define('FULL_PATH_APPS', PATH_ROOT . PATH_APPS);
define('FULL_PATH_BLOCKS', PATH_ROOT . PATH_BLOCKS);
define('FULL_PATH_HOOKS', PATH_ROOT . PATH_HOOKS);



// config function stores site variables
function c(
		$single = '',
		$multi = array(
					'db' => array(
								'host' => 'your-db-host-here',
								'name' => 'your-db-name-here',
								'user' => 'your-db-user-here',
								'pass' => 'your-db-pass-here'
							),
					'protocol' => 'http',
					'subdomain' => '',  // <----- always put a trailing (appended) 'dot' at the end -> EX: 'www.' or EX: 'api.v3.'
					'domain' => 'your-domain-here', // if your domain is mydomain.com you simply put 'mydomain'
					'tld' => '.com',  // <----- always put the prepending dot
					'htaccess' => '',  // <----- use 'index.php/' if you are not using .htaccess rewrite
					'h' => PATH_HOOKS,
					'b' => PATH_BLOCKS,
					'a' => PATH_APPS, // path to apps directory from document root of this installation of simpl
					'm' => PATH_ASSETS, // path to media/assets files document from root of installation of simpl
					'cache' => 'off', //
					'name' => 'Simpl PHP Framework', //
					'path' => PATH_ROOT // dirname(dirname(__FILE__)) path to root directory of the simpl framework
				)
) {
    while (true) {
        if (!$single || $single == '') { break; }
		if (is_string($single)) { $single = array($single); }
        $index = array_shift($single);
        foreach ($multi as $k => $v) {
            if ($k == $index) {
                $multi = $v;
				continue 2;
            }
        }
    }
    return $multi;
}







#######################################################################
##########################                   ##########################
##########################   URL FUNCTIONS   ##########################
##########################                   ##########################
#######################################################################




function uri(
			$format = 'array',
			$lc = true,
			$delimiter = '/',
			$clear_get = false,
			$pipe = 'index.php'
) {
	if ($lc) { $uri = strtolower($_SERVER['REQUEST_URI']); }
	else { $uri = $_SERVER['REQUEST_URI']; }
	$get_vars_string = (strrpos($uri, '?')) ? (substr($uri, strrpos($uri, '?') +1, strlen($uri))) : (false);
	$get_vars_array = array();
	if ($get_vars_string !== false) {
		if (strpos($get_vars_string, '&') !== false) {
			foreach(explode('&', $get_vars_string) as $get_vars_pair) {
				if (strpos($get_vars_pair, '=') !== false) {
					list($key, $val) = explode('=', $get_vars_pair);
					$get_vars_array[$key] = $val;
				}
				else { $get_vars_array[$get_vars_pair] = ''; }
			}
		}
		elseif (strpos($get_vars_string, '=') !== false) {
			list($key, $val) = explode('=', $get_vars_string);
			$get_vars_array[$key] = $val;
		}
		else { $get_vars_array[$get_vars_string] = ''; }
	}
	$uri = (count($get_vars_array) > 0 && strrpos($uri, '?')) ? (substr($uri, 0, strrpos($uri, '?'))) : ($uri);
	$uri = (substr($uri, 0, strlen($delimiter)) == $delimiter) ? (substr($uri, strlen($delimiter), strlen($uri))) : ($uri);
	$uri = (substr($uri, 0, strlen($pipe)) == $pipe) ? (substr($uri, strlen($pipe), strlen($uri))) : ($uri);
	$uri = (substr($uri, 0, strlen($delimiter)) == $delimiter) ? (substr($uri, strlen($delimiter), strlen($uri))) : ($uri);
	$uri = (substr($uri, -strlen($delimiter)) == $delimiter) ? (substr($uri, 0, (strlen($uri) -strlen($delimiter)))) : ($uri);
	//print '$uri current: '. $uri .'<br />'; exit;
	if ($format == 'array') {
		$uri = explode($delimiter, $uri); 
		if (count($get_vars_array) > 0) { $uri['$_GET'] = $get_vars_array; }
		if (count($uri) === 1 && $uri[0] == '') { return array(); } else { return $uri; }
	}
	if ($format == 'string') {
		if ($clear_get) {
			return $uri;
		}
		else { return ($get_vars_string) ? ($uri .'?'. $get_vars_string) : ($uri); }
	}
	if ($format == 'raw') { return $_SERVER['REQUEST_URI']; }
} // end uri() function






function sub($format = 'array', $remove = array()) {
	$sub = strtolower($_SERVER['HTTP_HOST']);
	//print '$sub = '. $sub .'<br />';
	//print '$sub_len = '. $sub_len .'<br />';
	//if (strpos($sub, c('domain') .'.'. c('tld')) === 0) {
	$sub = substr($sub, 0, strrpos($sub, domain() .'.'. domain(true)));
	$sub = (substr($sub, -1) == '.') ? (substr($sub, 0, strlen($sub) -1)) : ($sub);
	$sub = explode('.', $sub);
	//print '<pre>'; print_r($sub); print '</pre>';
	$sub = (count($remove)) ? (array_diff($sub, $remove)) : ($sub); // comment out this line if you want to keep www in the substring
	//print '<pre>'; print_r($sub); print '</pre>';
	if ($format == 'array') { return $sub; }
	if ($format == 'string') { return implode('.', $sub); }
} // end sub() function




function domain($tld = false) {
	$domain = strtolower($_SERVER['HTTP_HOST']);
	$domain = explode('.', $domain);
	$single_tlds = array(
						'ac', 'academy', 'ad', 'ae', 'aero', 'af', 'ag', 'agency', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bargains', 'bb', 'bd', 'be', 'berlin', 'bf', 'bg', 'bh', 'bi', 'bike', 'biz', 'bj', 'blue', 'bm', 'bn', 'bo', 'boutique', 'br', 'bs', 'bt', 'build', 'builders', 'buzz', 'bv', 'bw', 'by', 'bz', 'ca', 'cab', 'camera', 'camp', 'careers', 'cat', 'catering', 'cc', 'cd', 'center', 'ceo', 'cf', 'cg', 'ch', 'cheap', 'ci', 'ck', 'cl', 'cleaning', 'clothing', 'club', 'cm', 'cn', 'co', 'codes', 'coffee', 'com', 'community', 'company', 'computer', 'construction', 'contractors', 'cool', 'coop', 'cr', 'cruises', 'cu', 'cv', 'cw', 'cx', 'cy', 'cz', 'dance', 'dating', 'de', 'democrat', 'diamonds', 'directory', 'dj', 'dk', 'dm', 'do', 'domains', 'dz', 'ec', 'edu', 'education', 'ee', 'eg', 'email', 'enterprises', 'equipment', 'er', 'es', 'estate', 'et', 'eu', 'events', 'expert', 'exposed', 'farm', 'fi', 'fj', 'fk', 'flights', 'florist', 'fm', 'fo', 'fr', 'ga', 'gallery', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gift', 'gl', 'glass', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'graphics', 'gs', 'gt', 'gu', 'guitars', 'guru', 'gw', 'gy', 'hk', 'hm', 'hn', 'holdings', 'holiday', 'house', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'immobilien', 'in', 'info', 'institute', 'int', 'international', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'kaufen', 'ke', 'kg', 'kh', 'ki', 'kim', 'kitchen', 'kiwi', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'land', 'lb', 'lc', 'li', 'lighting', 'limo', 'link', 'lk', 'lr', 'ls', 'lt', 'lu', 'luxury', 'lv', 'ly', 'ma', 'management', 'marketing', 'mc', 'md', 'me', 'menu', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'moda', 'monash', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nagoya', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'ninja', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'onl', 'org', 'pa', 'partners', 'pe', 'pf', 'pg', 'ph', 'photo', 'photography', 'photos', 'pics', 'pink', 'pk', 'pl', 'plumbing', 'pm', 'pn', 'post', 'pr', 'pro', 'properties', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'recipes', 'red', 'rentals', 'repair', 'report', 'rich', 'ro', 'rs', 'ru', 'ruhr', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sexy', 'sg', 'sh', 'shiksha', 'shoes', 'si', 'singles', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'social', 'solar', 'solutions', 'sr', 'st', 'su', 'support', 'sv', 'sx', 'sy', 'systems', 'sz', 'tattoo', 'tc', 'td', 'technology', 'tel', 'tf', 'tg', 'th', 'tienda', 'tips', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'today', 'tokyo', 'tools', 'tp', 'tr', 'training', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'uno', 'us', 'uy', 'uz', 'va', 'vc', 've', 'ventures', 'vg', 'vi', 'viajes', 'vn', 'voting', 'voyage', 'vu', 'wang', 'watch', 'wed', 'wf', 'wien', 'works', 'ws', 'xxx', 'ye', 'yt', 'za', 'zm', 'zone', 'zw'
					);
	$double_tlds = array(
						'co.uk', 'com.in', 'com.ir'
					);
	if ($tld) {
		if (in_array($domain[count($domain) -1], $single_tlds)) { return $domain[count($domain) -1]; }
		if (in_array($domain[count($domain) -2] .'.'. $domain[count($domain) -1], $double_tlds)) {
			return $domain[count($domain) -2] .'.'. $domain[count($domain) -1];
		}
		else { return false; }
	}
	else {
		if (in_array($domain[count($domain) -1], $single_tlds)) { return $domain[count($domain) -2]; }
		if (in_array($domain[count($domain) -2] .'.'. $domain[count($domain) -1], $double_tlds)) {
			return $domain[count($domain) -3];
		}
	}
	//return current domain in url, use $tld argument to return tld separately
} // end domain() function




// d function
function d($uri = '', $sub = '', $current = true) {
	if ($current) {
		$sub = (!empty($sub)) ? (c('subdomain')) : ('');
		return c('protocol') .'://'. $sub . domain() .'.'. domain(true) .'/'. c('htaccess') . $uri;
	}
	else {
		$sub = (!empty($sub)) ? (c('subdomain')) : ('');
//		$domain = (count($current) > 0) ? () : ();
		return c('protocol') .'://'. $sub . c('domain') . c('tld') .'/'. c('htaccess') . $uri;
		#return c('protocol') .'://'. (c('subdomain') != '') ? (c('subdomain') .'.') : ('') . c('domain') .'.'. c('tld') .'/'. $uri;
	}
	
} // end d() function

// url path to root assets folder function
function p($uri = '', $sub = '', $current = true) {
	if ($current) {
		$sub = (!empty($sub)) ? (c('subdomain')) : ('');
		return c('protocol') .'://'. $sub . domain() .'.'. domain(true) .'/'. PATH_ASSETS . $uri;
	}
	else {
		$sub = (!empty($sub)) ? (c('subdomain')) : ('');
//		$domain = (count($current) > 0) ? () : ();
		return c('protocol') .'://'. $sub . c('domain') . c('tld') .'/'. PATH_ASSETS . $uri;
		#return c('protocol') .'://'. (c('subdomain') != '') ? (c('subdomain') .'.') : ('') . c('domain') .'.'. c('tld') .'/'. $uri;
	}
} // end p() function


########################### render function ######################
	/*
		$default_example = array(
								'header' => 'header.php',
								'nav' => 'navigation.php',
								'primary' => 'home.php',
								'sidebar' => 'sidebar.php',
								'footer' => 'footer.php'
							);
		
		$replace_example = array(
								'primary' => 'somepage.php',
								'sidebar' => 'alternate_sidebar.php',
								'footer'  => ''
							);
		
		$insert_example = array(
								array('before', 'header', array('pre_header', 'pre_header.php')),
								array('after', 'nav', array('second_nav_bar', 'secondary_nav.php')),
								array('before', 'pre_header', array('another_pre_header', 'another_pre_header.php')),
								array('after', 'primary', array('second_content', 'secondary_content.php')),
								array('before', 'footer', array('pre_footer', 'pre_footer.php'))
							);
	*/
	




#######################################################################
##########################                   ##########################
##########################    TEMPLATING     ##########################
##########################                   ##########################
#######################################################################


function render(
				$replace = array(),
				$insert = array(),
				$dir = '',
				$default = array(),
				$path = FULL_PATH_APPS //  c('path') . c('a')  full path from root to the primary app's directory
) {
	# HERE IS WHERE YOU INITIATE YOUR SITE'S GLOBAL VARIABLES
	global $uris, $urisng, $uria, $urir, $suba, $subs, $sub, $dom, $tld, $pdo, $hooks;
	
	$final_template = array_merge($default, $replace);
	
	$current_template = $final_template;
	
	if (!empty($insert)) {
		foreach($insert as $insert_template) {
			$i = 0;
			foreach($current_template as $k => $v) {
				if ($k == $insert_template[1]) {
					if ($insert_template[0] == 'after') { $offset = ($i === 0) ? ($i) : ($i +1); }
					else { $offset = $i; }
					
					$final_template = array_slice($current_template, 0,	$offset, true) +
									  array($insert_template[2][0] => $insert_template[2][1]) +
									  array_slice($current_template, $offset, null, true);
				}
				$i++;
				$current_template = $final_template;
			}
		}
	}
	
	// you should not take away the beginning slash '/' from the $path var
	$path = (substr($path, -strlen('/')) == '/') ? (substr($path, 0, (strlen($path) -strlen('/')))) : ($path);
	$dir = (substr($dir, 0, strlen('/')) == '/') ? (substr($dir, strlen('/'), strlen($dir))) : ($dir);
	$dir = (substr($dir, -strlen('/')) == '/') ? (substr($dir, 0, (strlen($dir) -strlen('/')))) : ($dir);
	foreach ($final_template as $k => $v) {
		if ($v != '') { include $path .'/'. $dir .'/'. $final_template[$k]; }
	}
	#print '<pre>'; print_r($final_template); '</pre>';
	exit;
} // end render() function







	
################# hook functionality ############################
/*
$example_hooks = array(
						'head-js' => array(
											array('notification_js', ''),
											array('twitterfeed_js', 'twitter.php'),
											array('themeswitcher_js', 'themeswitcher.php')
										),
						'head-css' => array(
											array('notification_css', 'notification.php'),
											array('twitterfeed_css', ''),
											array('themeswitcher_css, '')
										),
						'sidebar-top' => array(
											array('newsletter_form', 'newsletter.php')
										)
					);
*/

function hook(
			$action = '',
			$name = '',
			$func = '',
			$file = '',
			$path = FULL_PATH_HOOKS //  c('path') . c('h')
) {
	global $hooks;
	#turn this into a switch-case???
	if ($action == '+') {
		$hooks[$name][] = array($func, $file);
	}
	if ($action == '-') {
		foreach($hooks[$name] as $k => $v) {
			#print '$k => '. $k .' $v => '. $v .'<br />';
			if ($v[0] == $func) { unset($hooks[$name][$k]); }
		}
	}
	if ($action == '!') {
		if ($hooks[$name]) { 
			foreach($hooks as $k => $v) {
				if ($k == $name) {
					#print '<pre>'; print_r($v); print '</pre>';
					foreach($v as $hook) {
						if ($hook[1] != '') {
							require_once($path . $hook[1]);
						}
						if (!function_exists($hook[0])) {
							print $hook[0];
						}
						else { 
							#print $func; // i don't think these lines are needed unless debugging
							call_user_func($hook[0]);
							#print $file; // i don't think these lines are needed unless debugging
						}
					}
				}
			}
		} else { return false; }
	}
	if ($action == '--') { $hooks[$name] = array(); }
	if ($action == '?') { return count($hooks[$name]); }
	if ($action == '') { return $hooks; }
	
} // end hook() function







#######################################################################
##########################                   ##########################
########################## DATABASE LOGGING  ##########################
##########################                   ##########################
#######################################################################



function logs(
			$type,
			$status,
			$description = '',
			$user_id = ''
) {
	$sql = '
			INSERT INTO
				`logs` (
					`user_id`,
					`type`,
					`status`,
					`description`,
					`ip`
				)
			VALUES (
				'. $user_id .',
				"'. $type .'",
				"'. $status .'",
				"'. $description .'",
				"'. $_SERVER['REMOTE_ADDR'] .'"
			)
	';
	try { return $pdo->query($sql); }
	catch (PDOException $e) {
		die('Error SQL query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
	}

} // end logs() function



	/*
	 * $table = the db table
	 * db_select -> $condition = array('`session_id` = "'. $post_array['some_id'] .'"', '`property` != "'. $post_array['some_property'] .'"');
	 * $set = array('`property_name`' => '"'. $post_array['property_name'] .'"', '`property_name`' => '"'. $post_array['property_name'] .'"');
	 */





#######################################################################
##########################                   ##########################
##########################  CRUD FUNCTIONS   ##########################
##########################                   ##########################
#######################################################################



function db_conn($type = '', $args = array()) {
	switch($type) {
		case 'mysql':
			try {
				return new PDO('mysql:host='. $args['host'] .';dbname='. $args['name'], $args['user'], $args['pass']);
			} catch (PDOException $e) {
				die('Error connecting to the database: '. $e->getMessage() .' Arguments provided db_conn(): ');
			}
			break;
		case 'pgsql':
			// do stuff here
			break;
		case 'mssql':
			// do stuff here
			break;
		case 'sqlite':
			try {
				return new PDO('sqlite:'. $args['name']);
			} catch (PDOException $e) {
				die('Error connecting to the database: '. $e->getMessage());
			}
			break;
		case 'firebird':
			try {
				return new PDO('firebird:dbname='. $args['host'] .':'. $args['name'], $args['user'], $args['pass']);
			} catch (PDOException $e) {
				die('Error connecting to the database: '. $e->getMessage());
			}
			break;
		case 'informix':
			// do stuff here
			break;
		case 'oracle':
			// do stuff here
			break;
		case 'dblib':
			// do stuff here
			break;
		case 'ibm':
			// do stuff here
			break;
	}
}



// CRUD - READ
function db_r(
			$table,
			$condition = array(),
			$limit = '',
			$order = array(),
			$group = array(),
			$return_query_string = false
) {
	global $pdo;
	$sql = '
		SELECT
			*
		FROM
			`'. $table .'`
	';
	if (is_array($condition)) {
		if (count($condition) > 1) {
			$sql .= '
					WHERE
				';
			$sql .= implode(' AND ', $condition);
		}
		elseif (count($condition) > 0) { $sql .= ' WHERE '. $condition[0] .' '; }
	}
	elseif (!empty($condition) && is_string($condition)) { $sql .= ' WHERE '. $condition .' '; }
	
	if (!empty($group)) {
		$sql .= '
				GROUP BY
			';
		$sql .= implode(', ', $group);
	}
	if (!empty($order)) {
		$sql .= '
				ORDER BY
			';
		$sql .= implode(', ', $order);
	}
	if ($limit != '') {
		$sql .= '
				LIMIT
					'. $limit .'
			';
	}
	//print '<pre>'. $sql .'</pre>';
	//$query = mysql_query($sql) OR die(mysql_error() .'<p><pre>'. $sql .'</pre></p>');
	//if ($return_query_string) { return $sql; }
	//else { return $query; }
	
	//print '<pre>'. $sql .'</pre>';
	//$query = mysql_query($sql) OR die(mysql_error() .'<p><pre>'. $sql .'</pre></p>');
	//print '$sql: '. $sql .'<br />';
	if ($return_query_string) { return $sql; }
	else {
		try { return $pdo->query($sql); }
		catch (PDOException $e) {
			die('Error select query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
		}
	}
	
	
	
} // end db_s() function




// CRUD - UPDATE
function db_u($table, $set = array(), $condition = array(), $group = '') {
	global $pdo;
	$sql = '
		UPDATE
			`'. $table .'`
		SET
	';
	$sets = array();
	foreach($set as $key => $val) {
		$sets[] = '`'. $key .'` = "'. $val .'"';
	}
	$sql .= implode(', ', $sets);
	
	if (!empty($condition)) {
		$sql .= '
				WHERE
			';
		$sql .= implode(' AND ', $condition);
	}
	try { return $pdo->exec($sql); }
	catch (PDOException $e) {
		die('Error update query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
	}
	//return mysql_query($sql) OR die(mysql_error() .'<p><pre>'. $sql .'</pre></p>');
} // end function db_u()




// CRUD - DELETE	
function db_d($table, $condition = array()) {
	global $pdo;
	if (!empty($condition)) {
		$sql = '
			DELETE FROM
				`'. $table .'`
			WHERE
		';
		$sql .= '
				WHERE
			';
		$sql .= implode(' AND ', $condition);
		//return mysql_query($sql) OR die(mysql_error() .'<p><pre>'. $sql .'</pre></p>');
		try { return $pdo->exec($sql); }
		catch (PDOException $e) {
			die('Error delete query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
		}
	}
	else { return false; }
} // end function db_d()


// CRUD - CREATE
function db_c($table, $insert = array()) {
	global $pdo;
	if (!empty($insert)) {
		$sql = '
			INSERT INTO
				`'. $table .'`
			(
		';
		$keys = array();
		foreach ($insert as $key => $val) {
			$keys[] = '`'. $key .'`';
		}
		$sql .= implode(', ', $keys);
		$sql .= '
			)
		';
		$sql .= '
			VALUES (
		';
		$vals = array();
		foreach ($insert as $key => $val) {
			$vals[] = (is_int($val)) ? ($val) : ('"'. $val .'"');
		}
		$sql .= implode(', ', $vals);
		$sql .= '
			)
		';
		
		//return mysql_query($sql) OR die(mysql_error() .'<p><pre>'. $sql .'</pre></p>');
		try { return $pdo->exec($sql); }
		catch (PDOException $e) {
			die('Error insert query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
		}
	}
	else { return false; }
} // end function db_i()


// CRUD - EXECUTE CUSTOM SQL QUERY
function db_exec($sql) {
	global $pdo;
	try { return $pdo->exec($sql); }
	catch (PDOException $e) {
		die('Error custom query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
	}
}



// CRUD - FETCH ALL
function db_fa($stmt) {
	global $pdo;
	//return $stmt->fetch(PDO::FETCH_ASSOC);
	try { return $stmt->fetch(PDO::FETCH_ASSOC); }
	catch (PDOException $e) {
		die('Error select query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
	}
}






#######################################################################
##########################                      #######################
######################### SESSION NOTIFICATIONS  ######################
##########################                      #######################
#######################################################################

function n($action, $page, $input = '', $type = '', $message = '') {
	switch($action) {
		case '+':
			$_SESSION['notifications'][$page][$input][$type] = $message;
			return true;
			break;
		case '-':
			$_SESSION['notifications'][$page] = array();
			return true;
			break;
		case '?':
			if (
				isset($_SESSION['notifications'][$page][$input][$type]) &&
				$_SESSION['notifications'][$page][$input][$type] !== '' &&
				!empty($_SESSION['notifications'][$page][$input][$type])
			) {
				return true;
			}
			else { return false; }
			break;
		case '|':
			if (
				isset($_SESSION['notifications'][$page][$input][$type]) &&
				$_SESSION['notifications'][$page][$input][$type] !== '' &&
				!empty($_SESSION['notifications'][$page][$input][$type])
			) {
				return $_SESSION['notifications'][$page][$input][$type];
			}
			break;
		case ':':
			if (
				isset($_SESSION['notifications'][$page][$input][$type]) &&
				$_SESSION['notifications'][$page][$input][$type] !== ''
			) {
				return $_SESSION['notifications'][$page][$input][$type];
			}
			else { return false; }
			break;
			
	}
} // end function n()
	
	




#######################################################################
##########################                      #######################
##########################     SESSION FORMS    #######################
##########################                      #######################
#######################################################################


function i($action, $page, $form = '', $input = '') {
	switch($action) {
		case '+':
			clean($input);
			$_SESSION['form'][$page][$form] = $input;
			return true;
			break;
		case '+@':
			clean($input);
			foreach ($input as $key => $val) {
				$_SESSION['form'][$page][$form][$key] = $val;
			}
			return true;
			break;
		case '-@':
			/*foreach ($input as $key => $val) {
				$_SESSION['form'][$page][$form][$val] = array();
			}
			return true;
			break;*/
			$_SESSION['form'][$page][$form] = array();
			return true;
			break;
		case '@':
			if (isset($_SESSION['form'][$page][$form])) {
				return $_SESSION['form'][$page][$form];
			}
			else { return false; }
			break;
		case '-':
			$_SESSION['form'][$page][$form] = array();
			return true;
			break;
		case '|':
			if (isset($_SESSION['form'][$page][$form][$input])) {
				return $_SESSION['form'][$page][$form][$input];
			}
			else { return false; }
			break;
		case '?':
			if (isset($_SESSION['form'][$page][$form][$input])) {
				return true;
			}
			else { return false; }
			break;
	}
} // end function i()
	
	


#################### clean form input #####################
function clean($input) {
	if (is_array($input)) {
		foreach ($input as $key => $val) {
			$input[$key] = mysql_real_escape_string($val);
			$input[$key] = trim($input[$key]);
			$input[$key] = rtrim($input[$key]);
		}
	}
	else {
		$input = mysql_real_escape_string($input);
		$input[$key] = trim($input[$key]);
		$input[$key] = rtrim($input[$key]);
	}
} // end function clean()
	
	







#######################################################################
##########################                      #######################
##########################   FORM VALIDATION    #######################
##########################                      #######################
#######################################################################

	function valid($type, $input, $min = 1, $max = 999999999, $allowed_extensions = 'all') {
		if ($type == 'email') {
			// eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email_string)
			// eregi("^[a-z0-9\._-]+@([a-z0-9][a-z0-9-]*[a-z0-9]\.)+([a-z]+\.)?([a-z]+)$", $email_string)
			if (!preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/i', $input)) {
				if(preg_match('/^.+\@(\[?)[-a-zA-Z0-9\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/i', $input)) {
					return true;
				} else { return false; }
			} else { return false; }
		}
		
		if ($type == 'phone') {
			/* valid format: (232) 555-5555 */
			if (preg_match('/^\(?[0-9]{3}\)?|[0-9]{3}[-. ]? [0-9]{3}[-. ]?[0-9]{4}$/', $input)) {
				return true;
			}
			else { return false; }
		}
		
		if ($type == 'text') {
			if (strlen($input) >= $min && strlen($input) <= $max) {
				return true;
			}
			else { return false; }
		}
		
		if ($type == 'username') {
			if (strlen($input) >= $min && strlen($input) <= $max) {
				if (preg_match('/^[a-zA-Z0-9._-]+/', $input)) {
					return true;
				}
				else { return false; }
			}
			else { return false; }
		}
		
		if ($type == 'int') {
			if (preg_match('/[0-9]{'. $min .', '. $max .'}/', $input)) {
				return true;
			}
			else { return false; }
		}
		
		if ($type == 'ss') {
			if (preg_match('%^(?!000)([0-6]\d{2}|7([0-6]\d|7[012]))([ -]?)(?!00)\d\d\3(?!0000)\d{4}$%', $input)) {
				return true;
			}
			else { return false; }
		}
		if ($type == 'dl') { //http://diogenesllc.com/stdlformats.html
			$min = strtolower($min);
			if ($min == 'alabama' || $min == 'al') {
				if (preg_match('%^[0-9]{7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'alaska' || $min == 'ak') {
				if (preg_match('%^[0-9]{1, 7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'arizona' || $min == 'az') {
				if (preg_match('%^[a-zA-Z][0-9]{8}$%', $input) || preg_match('^%[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'arkansas' || $min == 'ar') {
				if (preg_match('%^[0-9]{8, 9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'california' || $min == 'ca') {
				if (preg_match('%^[a-zA-Z][0-9]{7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'colorado' || $min == 'co') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'connecticut' || $min == 'ct') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'delaware' || $min == 'de') {
				if (preg_match('%^[0-9]{1, 7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'washington d.c.' || $min == 'dc') {
				if (preg_match('%^[0-9]{7}$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'florida' || $min == 'fl') {
				if (preg_match('%^[a-zA-Z][0-9]{12}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'georgia' || $min == 'ga') {
				if (preg_match('%^[0-9]{7, 9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'hawaii' || $min == 'hi') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'idaho' || $min == 'id') {
				if (preg_match('%^[a-zA-Z]{2}[0-9]{6}[a-zA-Z]$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'illinois' || $min == 'il') {
				if (preg_match('%^[a-zA-Z][0-9]{11}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'indiana' || $min == 'in') {
				if (preg_match('%^[0-9]{9, 10}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'iowa' || $min == 'ia') {
				if (preg_match('%^[0-9]{3}[a-zA-Z]{2}[0-9]{4}$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'kansas' || $min == 'ks') {
				if (preg_match('%^[a-zA-Z][0-9]{8}$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'kentucky' || $min == 'ky') {
				if (preg_match('%^[a-zA-Z][0-9]{8}$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'louisiana' || $min == 'la') {
				if (preg_match('%^0{2}[0-9]{7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'maine' || $min == 'me') {
				if (preg_match('%^[0-9]{7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'maryland' || $min == 'md') {
				if (preg_match('%^[a-zA-Z][0-9]{12}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'massachusetts' || $min == 'ma') {
				if (preg_match('%^[a-zA-Z][0-9]{8}$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'michigan' || $min == 'mi') {
				if (preg_match('%^[a-zA-Z][0-9]{12}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'minessota' || $min == 'mn') {
				if (preg_match('%^[a-zA-Z][0-9]{12}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'mississippi' || $min == 'ms') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'missouri' || $min == 'mo') {
				if (preg_match('%^[0-9]{9}$%', $input) || preg_match('%^[a-zA-Z][0-9]{5, 9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'montana' || $min == 'mt') {
				if (preg_match('%^[0-9]{9}$%', $input) || preg_match('%^[a-zA-Z0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'nebraska' || $min == 'ne') {
				if (preg_match('%^[a-zA-Z][0-9]{3, 8}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'nevada' || $min == 'nv') {
				if (preg_match('%^[0-9]{10}$%', $input) || preg_match('%^[0-9]{12}$%', $input) || preg_match('%^[xX][0-9]{8}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'new hampshire' || $min == 'nh') {
				if (preg_match('%^[0-9]{2}[a-zA-Z]{3}[0-9]{5}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'new jersey' || $min == 'nj') {
				if (preg_match('%^[a-zA-Z][0-9]{14}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'new york' || $min == 'ny') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'new mexico' || $min == 'nm') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'north carolina' || $min == 'nc') {
				if (preg_match('%^[0-9]{1, 8}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'north dakota' || $min == 'nd') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'ohio' || $min == 'oh') {
				if (preg_match('%^[a-zA-Z]{2}[0-9]{6}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'oklahoma' || $min == 'ok') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'oregon' || $min == 'or') {
				if (preg_match('%^[0-9]{1, 7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'pennsylvania' || $min == 'pa') {
				if (preg_match('%^[0-9]{8}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'rhode island' || $min == 'ri') {
				if (preg_match('%^[0-9]{7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'south carolina' || $min == 'sc') {
				if (preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'south dakota' || $min == 'sd') {
				if (preg_match('%^[0-9]{8, 9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'tennessee' || $min == 'tn') {
				if (preg_match('%^[0-9]{7, 9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'texas' || $min == 'tx') {
				if (preg_match('%^[0-9]{8}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'utah' || $min == 'ut') {
				if (preg_match('%^[0-9]{4, 10}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'vermont' || $min == 'vt') {
				if (preg_match('%^[0-9]{7}[a-zA-Z]$%', $input) || preg_match('%^[0-9]{8}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'virginia' || $min == 'va') { // 9 Numeric (SSN); or 1 Alpha (R Or T) 8 Numeric
				if (preg_match('%^([rR]|[tT])[0-9]{8}$%', $input) || preg_match('%^[0-9]{9}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'washington' || $min == 'wa') {
				if (preg_match('%^[a-zA-Z0-9]{12}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'west virginia' || $min == 'wv') {
				if (preg_match('%^[a-zA-Z][0-9]{6}$%', $input) || preg_match('%^[0-9]{7}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'wisconsin' || $min == 'wi') {
				if (preg_match('%^[a-zA-Z][0-9]{13}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			if ($min == 'wyoming' || $min == 'wy') {
				if (preg_match('%^[0-9]{9, 10}$%', $input)) {
					return true;
				}
				else { return false; }
			}
			
		}
		
		if ($type == 'ip') {
		
		}
		
		if ($type == 'date') {
			if (preg_match('%^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$%', $input)) {
				return true;
			}
			else { return false; }
		}
		
		if ($type == 'regex') {
			if (preg_match($min, $input)) {
				return true;
			}
			else { return false; }
		}
		
		if ($type == 'password') {
			$sql = '
				SELECT
					*
				FROM
					`users`
				WHERE
					`session_id` = "'. $min .'"
				LIMIT
					1
			';
			 $query = mysql_query($sql) OR die(mysql_error());
			 $result = mysql_fetch_assoc($query);
			 //print '<pre>'; print_r($result); print '</pre>';
			 if ($result['password'] == md5($input)) {
				return true;
			 } else { return false; }
		}
		
		/* IF FILE UPLOAD */
		if (substr($type, 0, 4) == 'file') {
			// $input is not the input name but the file handler name
			// $min is what you want to rename the uploaded file to
			// $max is 'yes' if you want to overwrite the file if it exists already
			// example of $type 'file|logos/' (logos/) would be appended to the uploads/ path
			// $input here is not the input's $_POST value, but the actual INPUT NAME 
			// $min is bytes, $max is megabytes 
			
			$append = str_replace('file', '', $type);
			$append = str_replace('|', '', $append);
			$target = 'uploads/'. $append;
			//print 'target: '. $target .'<br /><br />';
			$file_name = $_FILES[$input]['name'];
			$file_size = $_FILES[$input]['size'];
			$file_type = $_FILES[$input]['type'];
			$file_tmp_name = $_FILES[$input]['tmp_name'];
			
			$valid = true;
			
			if ($valid == true) {
				if ($allowed_extensions == 'all') {
					//print 'extensions allowed = all<br /><br />';
					$allowed_extensions = array(
												'txt',
												'csv',
												'htm',
												'html',
												'xml',
												'css',
												'doc',
												'docx',
												'odt',
												'xls',
												'rtf',
												'ppt',
												'pdf',
												'swf',
												'flv',
												'avi',
												'wmv',
												'mov',
												'jpg',
												'jpeg',
												'gif',
												'png',
											);
				}
				elseif (!is_array($allowed_extensions)) {
					$allowed_extensions = array($allowed_extensions);
				}
				
				if (!in_array(strtolower(end(explode('.', strtolower($file_name)))), $allowed_extensions)) {
					$valid = false;
				}
			}

			//set the name for the uploaded file
			$file_name = ($min != '') ? ($min .'.'. strtolower(end(explode('.', strtolower($file_name))))) : ($file_name);
			if ($valid == true) {
				if (file_exists($target . $file_name) && $max == 'no') {
					$valid = false;
				}
			}
			
			if ($valid == true) {
				if (!move_uploaded_file($file_tmp_name, $target . $file_name)) {
					$valid = false;
				}
			}
			
			return $valid;
		} // end if $type is 'file'
		
		if ($type == 'regex') {
			if (preg_match($min, $input)) {
				return true;
			}
			else { return false; }
		}
		
	} // end validate functions
	
	
	





#######################################################################
##########################                      #######################
########################## USER AUTHENTICATION  #######################
##########################                      #######################
#######################################################################

function auth(
			$action = '',
			$user_id = '',
			$var = '',
			$val = ''
) {
	global $pdo;
	if ($action == '+') {
		// add user
		// $user_id should be an associative array with all the data in it
		//EXAMPLE:
		// $addinfo = array('regdate' => timestamp(), 'ip' => $_SERVER['REMOTE_ADDR'], 'active' => 1);
		// auth('+', $_POST, $addinfo);
		// 
		
		if (is_array($var)) { array_merge($user_id, $var); }
		
		$sql = '
			INSERT INTO
				`users` (
					';
		$cols = array_keys($user_id);
		$cols = implode('`, `', $cols);
		$sql .= '`'. $cols .'`
				)
			VALUES (
				';
		
		$vals = array_values($user_id);
		
		foreach($vals as $k => $v) { if (is_int($v)) {} else { $vals[$k] = '"'. $v .'"'; } }
		$vals = implode(', ', $vals);
		$sql .= '
				'. $vals .'
				)
		';
		//mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
		//print '<pre>'; print_r($sql); print '</pre>';
		try { return $pdo->exec($sql); }
		catch (PDOException $e) {
			die('Error ADD USER query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
		}
		
	}
	
	if ($action == '?') {
		// returns true/false if $val is given, returns value if no $val is given for checking
		//auth('?', 4151, 'email', 'someaddy@gmail.org'); >> returns true if user 4151 has the email "someaddy@gmail.org"
		//auth('?', 4151, 'email'); >> returns 'someaddy@gmail.org' if user 4151 has the email "someaddy@gmail.org"
		//if (array_keys($var) !== range(0, count($var) -1)) {}
		
		if (!$user_id) { return false; }
		elseif (is_array($user_id)) {
			$sql = '
					SELECT
				';
			$cols = array_keys($user_id);
			$cols = implode('`, `', $cols);
			$sql .= '`'. $cols .'`
					FROM
						`users`
					WHERE
				';
			$where = array();
			foreach($user_id as $k => $v) {
				$str = '`'. $k .'` = ';
				if (is_int($v)) {} else { $str .='"'; }
				$str .= $v;
				if (is_int($v)) {} else { $str .='"'; }
				$where[] = $str;
			}
			$sql .= implode(' AND ', $where);
			$sql .= '
					AND `active` = 1
					LIMIT
						1
			';
			//print '<pre>'; print_r($sql); print '</pre>';
			//$query = mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
			//return mysql_num_rows($query);
			try {
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				return ($stmt->fetch(PDO::FETCH_NUM) > 0) ? (true) : (false);
				#if (count($pdo->query($sql)->fetchAll())) { return true; }
				#else { return false; }
				
			}
			catch (PDOException $e) {
				die('Error user-check query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
			}
		}
		else {
			if ($val == '') {
				$sql = '
					SELECT
						`'. $var .'`
					FROM
						`users`
					WHERE
						`id` = '. $user_id .'
				';
				#$query = mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
				#$row = mysql_fetch_assoc($query);
				#return $row[$var];
				try {
					$stmt = $pdo->query($sql);
					return $stmt[$var];
				}
				catch (PDOException $e) {
					die('Error user-check query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
				}
			}
			else {
				$sql = '
						SELECT
							`'. $var .'`
						FROM
							`users`
						WHERE
							`'. $var .'` = ';
				if (is_int($val)) {} else { $sql .='"'; }
				$sql .= $val;
				if (is_int($val)) {} else { $sql .='"'; }
				$sql .= '
						AND `active` = 1
						LIMIT
							1
				';
				//print '<pre>'; print_r($sql); print '</pre>';
				//$query = mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
				//return mysql_num_rows($query);
				try {
					$stmt = $pdo->query($sql);
					return ($stmt->rowCount() > 0) ? (true) : (false);
				}
				catch (PDOException $e) {
					die('Error user-check query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
				}
			}
		}
		// if $var is array, prepare sql query to do WHERE `jkdsf` = "ljkfdsf" AND `kfjf = "kdljg", etc
		// check userid 142 logged in >> auth('?', 142, 'session_id', session_id())
		// check userid 71 type >> returns actual type (admin, subscriber, etc)
		// check userid 71 type is admin >> returns true/false
	}
	
	
	if ($action == '@') {
		// return array of userid requested data like username, first/last name, session id, password hash, group_id, last timestamp, etc, if user is not logged in
		//EXAMPLE $user = auth('@', 'id', 5145); returns array of data related to that specific user id
		$sql = '
				SELECT
					*
				FROM
					`users`
				WHERE
					`'. $user_id .'` = ';
		if (is_int($var)) {} else { $sql .='"'; }
		$sql .= $var;
		if (is_int($var)) {} else { $sql .='"'; }
		$sql .= '
				AND `active` = 1
				LIMIT
					1
		';
		//$query = mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
		//print '<pre>'; print_r($sql); print '</pre>';
		//return mysql_fetch_assoc($query);
		
		try { return $pdo->exec($sql); }
		catch (PDOException $e) {
			die('Error @user query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
		}
	}
	
	if ($action == '^') {
		// update userid 5124 active = 1 auth('^', 5124, 'active', 1)
		// update userid 62332 sessionid = 2jf9fjf207aAKg97g6t2BFh293fHF37gAjb83iw
		// update userid 277 password to Bingo5! >>> auth('^', 277, 'password', md5('Bingo5!'));
		if (is_array($var)) {
			$sql = '
				UPDATE
					`users`
				SET
			';
			$set = array();
			foreach ($var as $k => $v) {
				$str = '`'. $k .'` = ';
				if (is_int($v)) {} else { $str .= '"'; }
				$str .= $v;
				if (is_int($v)) {} else { $str .= '"'; }
				$set[] = $str;
			}
			$sql .= implode(', ', $set);
			$sql .= '
				WHERE
					`id` = '. $user_id .'
			';
			//mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
			//print '<pre>'; print_r($sql); print '</pre>';
			try { return $pdo->exec($sql); }
			catch (PDOException $e) {
				die('Error update user query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
			}
		}
		else {
			$sql = '
				UPDATE
					`users`
				SET
					`'. $var .'` = ';
			if (is_string($val)) { $sql .= '"'; }
			$sql .= $val;
			if (is_string($val)) { $sql .= '"'; }
			$sql .= '
				WHERE
					`id` = '. $user_id .'
			';
			//mysql_query($sql) OR die(mysql_error() .'<pre>'. $sql .'</pre>');
			//print '<pre>'; print_r($sql); print '</pre>';
			try { return $pdo->exec($sql); }
			catch (PDOException $e) {
				die('Error update user query: '. $e->getMessage() .'<br /><pre>'. $sql .'</pre>');
			}
		}
	}
}







#######################################################################
##########################                      #######################
##########################    IP/GEO LOCATOR    #######################
##########################                      #######################
#######################################################################

function ipgeo(
				$ip,
				$format = 'array', // ('json', 'xml', 'string', 'array')
				$accuracy = 'city', // (city, 'country')
				$apikey = 'your-api-key-from-ipinfodb.com'
) {
	$request = 'http://api.ipinfodb.com/v3/ip-'. $accuracy .'/?key='. $apikey .'&format='. $format .'&ip='. $ip;
	$location = file_get_contents($request);
	
	if ($format == 'json') {
		return get_object_vars(json_decode($location));
	}
	if ($format == 'xml') {
		return json_decode(json_encode(simplexml_load_string($location)), true);
	}
	
	if ($format == 'string') {
		return $location;
	}
	if ($format == 'array') {
		$a = array(
					'statusCode',
					'statusMessage',
					'ipAddress',
					'countryCode',
					'countryName',
					'regionName',
					'cityName',
					'zipCode',
					'latitude',
					'longitude',
					'timeZone'
				);
		$o = explode(';', $location);
		$n = array();
		
		for ($i = 0; $i < count($a); $i++) {
			$n[$a[$i]] = $o[$i];
		}
		return $n;
	}
} // end ipgeo() function






#######################################################################
##########################                      #######################
##########################    SALT GENERATOR    #######################
##########################                      #######################
#######################################################################

function chaos(
			$type = 'all',
			$len = 35,
			$add = '',
			$unique = false
) {
	$num = '0123456789';
	$alphalow = 'abcdefghijklmnopqrstuvwxyz';
	$alphacap = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$special = '~`!@#$%^&*()-_+={}[]|/\?<>,.:;'; // does NOT include quotation marks (') or (")
	switch($type) {
		case 'num':
			$chars = $num . $add;
			break;
		case 'alpha':
			$chars = $alphalow . $alphacap . $add;
			break;
		case 'alphalow':
			$chars = $alphalow . $add;
			break;
		case 'alphacap':
			$chars = $alphacap . $add;
			break;
		case 'alphanum':
			$chars = $num . $alphalow . $alphacap . $add;
			break;
		case 'url':
			$chars = $num . $alphalow . $alphacap .'-_'. $add;
			break;
		case 'all':
			$chars = $num . $alphalow . $alphacap . $special . $add;
			break;
	}
	
	$str = '';
	for($i = 0; $i < $len; $i++) {
		// ensure all characters are unique
		if ($unique) {
			/*if (!strstr($str, $new_char)) {
				$str .= $new_char;
			} else { $i--; }*/
			$chars_a = str_split($chars);
			shuffle($chars_a);
			$str = implode('', $chars_a);
		}
		else {
			// get a new character
			$new_char = $chars[rand(0, strlen($chars) -1)];
			$str .= $new_char;
		}
	}
	return htmlspecialchars($str);
}


// format input/output (for easier debugging of arrays)
function fio($input = array(), $title = '') {
	print '<pre>';
	if ($title) { print '<b>'. $title .': </b>'; }
	print_r($input);
	print '</pre>';
} // end fio()




#######################################################################
##########################                   ##########################
########################## DATABASE  CONNECT ##########################
##########################                   ##########################
#######################################################################

// connect to your database here (PDO setup)
$args = array(
			'host' => 'localhost',
			'name' => 'armin_kdc',
			'user' => 'armin_kdc',
			'pass' => 'yG}m7,fD[hue'
);
$pdo = db_conn('mysql', $args);






#######################################################################
##########################                   ##########################
##########################       BLOCKS      ##########################
##########################                   ##########################
#######################################################################

// put your default code blocks here (they will be loaded at runtime)
// require_once('blocks/auth.php'); $user = auth();






#######################################################################
##########################                   ##########################
##########################       HOOKS       ##########################
##########################                   ##########################
#######################################################################
	




	







#######################################################################
##########################                   ##########################
##########################        INIT       ##########################
##########################                   ##########################
#######################################################################

// anything you add here you will probably want to add inside the render()
// function as a global variable also, so you can use these variables with page
// templates that are loaded with the render() function
$uria = uri('array'); $uris = uri('string'); $urisng = uri('string', true, '/', true); $urir = uri('raw');
$suba = sub('array'); $subs = sub('string');
$dom = domain(); $tld = domain(true);



?>
