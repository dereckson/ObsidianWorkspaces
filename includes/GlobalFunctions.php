<?php

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Information helper functions                                             ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets the username matching specified user id
 *
 * @param string $user_id the user ID
 * @return string the username
 */
function get_username ($user_id) {
	global $db;

	$user_id = $db->sql_escape($user_id);
	$sql = 'SELECT username FROM '. TABLE_USERS . " WHERE user_id = '$userid'";
	return $db->sql_query_express($sql, "Can't get username from specified user id");
}

/**
 * Gets the user id matching specified username
 *
 * @param string $username the username
 * @return string the user ID
 */
function get_userid ($username) {
	global $db;

	$username = $db->sql_escape($username);
	$sql = 'SELECT user_id FROM '. TABLE_USERS . " WHERE username LIKE '$username'";
    return $db->sql_query_express($sql, "Can't get user id from specified username");
}

/**
 * Gets the resource ID from an identifier
 *
 * @param $resource_type the resource type
 * @param $identifier resource identifier
 * @return mixed the resource ID (as integer), or NULL if unknown
 */
function get_resource_id ($resource_type, $identifier) {
    //Trivial cases: already an ID, null or void ID
    if (is_numeric($identifier)) {
        return $identifier;
    }
    if (!$identifier) {
        return NULL;
    }

    //Searches identifier
    switch ($resource_type) {
        case 'U':
            return get_user_id($identifier);

        case 'G':
            $group = UserGroup::fromCode($identifier);
            return $group->id;

        case 'W':
            $workspace = Workspace::fromCode($identifier);
            return $workspace->id;

        default:
            throw new Exception("Unknown resource type: $resource_type", E_USER_ERROR);
    }
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Misc helper functions                                                    ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Plural management

/*
 * Gets a "s" if the specified amount requests the plural
 * @param mixed $amount the quantity (should be numeric)
 * @return string 's' if the amount is greater or equal than 2 ; otherwise, ''
 */
function s ($amount) {
	if ($amount >= 2 || $amount <= -2 ) return 's';
}

/*
 * Prints human-readable information about a variable, wrapped in a <pre> block
 * @param mixed $mixed the variable to dump
 */
function dprint_r ($mixed) {
	echo '<pre>';
    print_r($mixed);
    echo '</pre>';
}

/*
 * Generates a new GUID
 * @return string a guid (without {})
 */
function new_guid () {
	//The guid chars
    $chars = explode(',', 'a,b,c,d,e,f,0,1,2,3,4,5,6,7,8,9');

    //Let's build our 36 characters string
    //e.g. 68ed40c6-f5bb-4a4a-8659-3adf23536b75
	$guid = "";
	for ($i = 0 ; $i < 36 ; $i++) {
        if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
            //Dashes at position 9, 14, 19 and 24
            $guid .= "-";
		} else {
            //0-f hex digit elsewhere
			$guid .= $chars[mt_rand() % sizeof($characters)];
		}
	}
	return $guid;
}

/*
 * Determines if the expression is a valid guid (in uuid notation, without {})
 * @param string $expression the guid to check
 * @return true if the expression is a valid guid ; otherwise, false
 */
function is_guid ($expression) {
    //We avoid regexp to speed up the check
    //A guid is a 36 characters string
    if (strlen($expression) != 36) return false;

    $expression = strtolower($expression);
	for ($i = 0 ; $i < 36 ; $i++) {
		if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
			//with dashes
			if ($expression[$i] != '-') return false;
		} else {
		    //and hex numbers
			if (!is_numeric($expression[$i]) && $expression[$i] != 'a' && $expression[$i] != 'b' && $expression[$i] != 'c' && $expression[$i] != 'd' && $expression[$i] != 'e' && $expression[$i] != 'f' ) return false;
		}
	}
    return true;
}

/**
 * Gets file extension
 * @param string $file the file to get the extension
* @return string the file extension
 */
function get_extension ($file) {
    $dotPosition = strrpos($file, ".");
    return substr($file, $dotPosition + 1);
}

/**
 * Gets file name
 * @param string $file the file to get the extension
 * @return string the file name
 */
function get_filename ($file) {
    //TODO: clear directory
    $dotPosition = strrpos($file, ".");
    return substr($file, 0, $dotPosition);
}

/*
 * Determines if a string starts with specified substring
 * @param string $haystack the string to check
 * @param string $needle the substring to determines if it's the start
 * @param boolean $case_sensitive determines if the search must be case sensitive
 * @return boolean true if $haystack starts with $needle ; otherwise, false.
 */
function string_starts_with ($haystack, $needle, $case_sensitive = true) {
    if (!$case_sensitive) {
        $haystack = strtoupper($haystack);
        $needle = strtoupper($needle);
    }
    if ($haystack == $needle) return true;
    return strpos($haystack, $needle) === 0;
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Localization (l10n)                                                      ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Defines the LANG constant, to lang to print
 *
 * This information is contained in the session, or if not yet defined,
 * it's to determine according the user's browser preferences.
 * @see find_lang
 */
function initialize_lang () {
    //If $_SESSION['lang'] doesn't exist yet, find a common language
    if (!array_key_exists('lang', $_SESSION)) {
        $lang = find_lang();
        $_SESSION['lang'] = $lang ? $lang : '-';
    }

    if ($_SESSION['lang'] != '-')
        define('LANG', $_SESSION['lang']);
}

/**
 * Gets a common lang spoken by the site and the user's browser
 * @see get_http_accept_languages
 *
 * @return string the language
 */
function find_lang () {
    if (file_exists('lang') && is_dir('lang')) {
        //Gets lang/ subdirectories: this is the list of available languages
        $handle = opendir('lang');
        while ($file = readdir($handle)) {
            if ($file != '.' && $file != '..' && is_dir("lang/$file")) {
                $langs[] = $file;
            }
        }

        //The array $langs contains now the language available.
        //Gets the langs the user should want:
        if (!$userlangs = get_http_accept_languages())
            return;

        //Gets the intersection between the both languages arrays
        //If it matches, returns first result
        $intersect = array_intersect($userlangs, $langs);
        if (count($intersect)) {
            return $intersect[0];
        }

        //Now it's okay with Opera and Firefox but Internet Explorer will
        //by default return en-US and not en or fr-BE and not fr, so second pass
        foreach ($userlangs as $userlang) {
            $lang = explode('-', $userlang);
            if (count($lang) > 1)
                $userlangs2[] = $lang[0];
        }
        $intersect = array_intersect($userlangs2, $langs);
        if (count($intersect)) {
            return $intersect[0];
        }
    }
}

/**
 * Gets the languages accepted by the browser, by order of priority.
 *
 * This will read the HTTP_ACCEPT_LANGUAGE variable sent by the browser in the
 * HTTP request.
 *
 * @return Array an array of string, each item a language accepted by browser
 */
function get_http_accept_languages () {
    //What language to print is sent by browser in HTTP_ACCEPT_LANGUAGE var.
    //This will be something like en,fr;q=0.8,fr-fr;q=0.5,en-us;q=0.3

    if (!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
        return null;
    }

    $http_accept_language = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    foreach ($http_accept_language as $language) {
        $userlang = explode(';q=', $language);
        if (count($userlang) == 1) {
            $userlangs[] = array(1, $language);
        } else {
            $userlangs[] = array($userlang[1], $userlang[0]);
        }
    }
    rsort($userlangs);
    foreach ($userlangs as $userlang) {
        $result[] = $userlang[1];
    }
    return $result;
}

/**
 * Loads specified language Smarty configuration file
 *
 * @param string $file the file to load
 * @param mixed $sections array of section names, single section or null
 */
function lang_load ($file, $sections = null) {
    global $smarty;

    //Loads English file as fallback if some parameters are missing
    if (file_exists("lang/en/$file"))
        $smarty->configLoad("lang/en/$file", $sections);

    //Loads wanted file (if it exists and a language have been defined)
    if (defined('LANG') && LANG != 'en' && file_exists('lang/' . LANG . '/' . $file))
        $smarty->configLoad('lang/' . LANG . '/' . $file, $sections);
}

/**
 * Gets a specified language expression defined in configuration file
 *
 * @param string $key the configuration key matching the value to get
 * @return string The value in the configuration file
 */
function lang_get ($key) {
    global $smarty;

    $smartyConfValue = $smarty->config_vars[$key];
    return $smartyConfValue ? $smartyConfValue : "#$key#";
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL helpers functions                                                    ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets URL
 * @return string URL
 */
function get_url () {
    global $Config;
    if (func_num_args() > 0) {
        $pieces = func_get_args();
        return $Config['BaseURL'] . '/' . implode('/', $pieces);
    } elseif ($Config['BaseURL'] == "" || $Config['BaseURL'] == "/index.php") {
        return "/";
    } else {
        return $Config['BaseURL'];
    }
}

/*
 * Gets page URL
 * @return string URL
 */
function get_page_url () {
    $url = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
    if (substr($url, -10) == "/index.php") {
        return substr($url, 0, -9);
    }
    return $url;
}

/*
 * Gets server URL
 * @todo find a way to detect https:// on non standard port
 * @return string the server URL
 */
function get_server_url () {
	switch ($port = $_SERVER['SERVER_PORT']) {
		case '80':
            return "http://$_SERVER[SERVER_NAME]";

        case '443':
            return "https://$_SERVER[SERVER_NAME]";

        default:
            return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]";
	}
}

/*
 * Gets $_SERVER['PATH_INFO'] or computes the equivalent if not defined.
 * @return string the relevant URL part
 */
function get_current_url () {
    global $Config;

    //Gets relevant URL part from relevant $_SERVER variables
    if (array_key_exists('PATH_INFO', $_SERVER)) {
        //Without mod_rewrite, and url like /index.php/controller
        //we use PATH_INFO. It's the easiest case.
        return $_SERVER["PATH_INFO"];
    }

    //In other cases, we'll need to get the relevant part of the URL
    $current_url = get_server_url() . $_SERVER['REQUEST_URI'];

    //Relevant URL part starts after the site URL
    $len = strlen($Config['SiteURL']);

    //We need to assert it's the correct site
    if (substr($current_url, 0, $len) != $Config['SiteURL']) {
        dieprint_r(GENERAL_ERROR, "Edit includes/config.php and specify the correct site URL<br /><strong>Current value:</strong> $Config[SiteURL]<br /><strong>Expected value:</strong> a string starting by " . get_server_url(), "Setup");
    }

    if (array_key_exists('REDIRECT_URL', $_SERVER)) {
        //With mod_rewrite, we can use REDIRECT_URL
        //We takes the end of the URL, ie *FROM* $len position
        return substr(get_server_url() . $_SERVER["REDIRECT_URL"], $len);
    }

    //Last possibility: use REQUEST_URI, but remove QUERY_STRING
    //If you need to edit here, use $_SERVER['REQUEST_URI']
    //but you need to discard $_SERVER['QUERY_STRING']

    //We takes the end of the URL, ie *FROM* $len position
    $url = substr(get_server_url() . $_SERVER["REQUEST_URI"], $len);

    //But if there are a query string (?action=... we need to discard it)
    if ($_SERVER['QUERY_STRING']) {
        return substr($url, 0, strlen($url) - strlen($_SERVER['QUERY_STRING']) - 1);
    }

    return $url;
}

/*
 * Gets an array of url fragments to be processed by controller
 * @return array an array containing URL fragments
 */
function get_current_url_fragments () {
    $url_source = get_current_url();
    if ($url_source == '/index.php') return array();
    return explode('/', substr($url_source, 1));
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL xmlHttpRequest helpers functions                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets an hash value to check the integrity of URLs in /do.php calls
 * @param Array $args the args to compute the hash
 * @return the hash paramater for your xmlHttpRequest url
 */
function get_xhr_hash ($args) {
    global $Config;

    array_shift($args);
    return md5($_SESSION['ID'] . $Config['SecretKey'] . implode('', $args));
}

/*
 * Gets the URL to call do.php, the xmlHttpRequest controller
 * @return string the xmlHttpRequest url, with an integrity hash
 */
function get_xhr_hashed_url () {
    global $Config;

    $args = func_get_args();
    $args[] = get_xhr_hash($args);
    return $Config['DoURL'] . '/' . implode('/', $args);
}

/*
 * Gets the URL to call do.php, the xmlHttpRequest controller
 * @return string the xmlHttpRequest url
 */
function get_xhr_url () {
    global $Config;

    $args = func_get_args();
    return $Config['DoURL'] . '/' .implode('/', $args);
}
