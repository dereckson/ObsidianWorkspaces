<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Error handling
 *
 * There are 3 standard error types:
 *  - SQL_ERROR         error during a sql query
 *  - HACK_ERROR        error trying to access a protected resource
 *  - GENERAL_ERROR     miscelleanous error
 *
 * The message_die/SQL_ERROR idea were found in phpBB 2 code.
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

//Error code constants
define ("SQL_ERROR",      65);
define ("HACK_ERROR",     99);
define ("GENERAL_ERROR", 117);

/**
 * Prints human-readable information about a variable
 * wrapped in a general error and dies
 *
 * @param mixed $mixed the variable to dump
 */
function dieprint_r ($var, $title = '') {
    if (!$title) $title = 'Debug';

    //GENERAL_ERROR with print_r call as message
    message_die(GENERAL_ERROR, '<pre>' . print_r($var, true) .'</pre>', $title);
}

/**
 * A callback method for the error handler, which throws exceptions on errors
 *
 * @param int $errno the level of the error raised
 * @param string $errstr the error message
 * @param string $errfile the filename that the error was raised in
 * @param int $errline the line number the error was raised at
 * @param string $errcontext an array that points to the active symbol table at the point the error occurred
 * @return boolean true when the error has been handled ; otherwise, false, to let the normal error handler continues.
 */
function throwExceptionErrorHandler ($errno, $errstr, $errfile, $errline, array $errcontext) {
    if (error_reporting() === 0) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

/**
 * Prints an error message and dies
 *
 * @param int $code A constant identifying the type of error (SQL_ERROR, HACK_ERROR or GENERAL_ERROR)
 * @param string $text the error description
 * @param string $text the error title
 * @param int $line the file line the error have occured (typically __LINE__)
 * @param string $file  the  file the error have occured (typically __FILE__)
 * @param string $sql the sql query which caused the error
 */
function message_die ($code, $text = '', $title = '', $line = '', $file = '', $sql = '') {
    //Ensures we've an error text
    $text = $text ? $text : "An error have occured";

    //Adds file and line information to error text
    if ($file) {
        $text .= " — $file";
        if ($line) {
            $text .= ", line $line";
        }
    }

    //Ensures we've an error title and adds relevant extra information
    switch ($code) {
        case HACK_ERROR:
            $title = $title ? $title : "Access non authorized";
            break;

        case SQL_ERROR:
            global $db;
            $title = $title ? $title : "SQL error";

            //Gets SQL error information
            $sqlError = $db->sql_error();
            if ($sqlError['message'] != '') {
                $text .= "<br />Error n° $sqlError[code]: $sqlError[message]";
            }
            $text .= '<br />&nbsp;<br />Query: ';
            $text .= $sql;

            break;

        default:
            //TODO: here can be added code to handle error error ;-)
            //Falls to GENERAL_ERROR

        case GENERAL_ERROR:
            $title = $title ? $title : "General error";
            break;
    }

    //HTML output of $title and $text variables
    echo '<div class="FatalError"><p class="FatalErrorTitle">', $title,
         '</p><p>', $text, '</p></div>';

    exit;
}
