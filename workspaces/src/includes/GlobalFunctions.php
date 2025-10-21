<?php

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Information helper functions                                             ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets the resource ID from an identifier
 *
 * @param $resource_type the resource type
 * @param $identifier resource identifier
 * @return mixed the resource ID (as integer), or NULL if unknown
 */
function resolve_resource_id ($resource_type, $identifier) {
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
 * Gets server URL
 * @todo find a way to detect https:// on non standard port
 * @return string the server URL
 */
function get_server_url () {
    if (php_sapi_name() == 'cli') {
        return '';
    }
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
        //We take the end of the URL, ie *FROM* $len position
        return substr(get_server_url() . $_SERVER["REDIRECT_URL"], $len);
    }

    //Last possibility: use REQUEST_URI, but remove QUERY_STRING
    //If you need to edit here, use $_SERVER['REQUEST_URI']
    //but you need to discard $_SERVER['QUERY_STRING']

    //We take the end of the URL, ie *FROM* $len position
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
