<?php

namespace Waystone\Workspaces\Engines\Errors;

use Keruald\OmniTools\Debug\Debugger;

use ErrorException;

class ErrorHandling {

    public static function init () : void {
        error_reporting(E_ALL);

        $minorRecoverableErrors =
            E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED;

        set_error_handler([static::class, 'throwExceptionErrorHandler'],
            E_ALL ^ $minorRecoverableErrors);

        Debugger::register();
    }

    /**
     * A callback method for the error handler, which throws exceptions on errors
     *
     * @param int    $errno   the level of the error raised
     * @param string $errstr  the error message
     * @param string $errfile the filename that the error was raised in
     * @param int    $errline the line number the error was raised at
     *
     * @return boolean true when the error has been handled ; otherwise, false,
     *                 to let the normal error handler continues.
     * @throws ErrorException
     */
    public static function throwExceptionErrorHandler (
        int $errno, string $errstr, string $errfile, int $errline
    ) : bool {
        if (error_reporting() === 0) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Prints an error message and dies
     *
     * @param int         $code  A constant identifying the type of error (SQL_ERROR, HACK_ERROR or GENERAL_ERROR)
     * @param string|null $text  the error description
     * @param string|null $title the error title
     * @param int|null    $line  the file line the error have occurred (typically __LINE__)
     * @param string|null $file  the  file the error have occurred (typically __FILE__)
     * @param string|null $sql   the sql query which caused the error
     *
     * @return never
     */
    public static function messageAndDie (
        int $code,
        ?string $text = null,
        ?string $title = null,
        ?int $line = null,
        ?string $file = null,
        ?string $sql = null,
    ) : never {
        //Ensures we've an error text
        $text ??= "An error have occurred";

        //Adds file and line information to error text
        if ($file !== null) {
            $text .= " — $file";
            if ($line !== null) {
                $text .= ", line $line";
            }
        }

        //Ensures we've an error title and adds relevant extra information
        switch ($code) {
            case HACK_ERROR:
                $title ??= "Access non authorized";
                break;

            case SQL_ERROR:
                global $db;
                $title ??= "SQL error";

                //Gets SQL error information
                if ($db !== null) {
                    $sqlError = $db->error();
                    if ($sqlError['message'] != '') {
                        $text .= "<br />Error n° $sqlError[code]: $sqlError[message]";
                    }
                    $text .= '<br />&nbsp;';
                }

                $text .= '<br />Query: ';
                $text .= $sql;

                break;

            default:
                //TODO: here can be added code to handle error error ;-)
                //Falls to GENERAL_ERROR

            case GENERAL_ERROR:
                $title ??= "General error";
                break;
        }

        //HTML output of $title and $text variables
        echo '<div class="FatalError"><p class="FatalErrorTitle">', $title,
        '</p><p>', $text, '</p></div>';

        exit;
    }

}
