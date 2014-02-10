<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Events helper methods class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

 /**
  * Events
  */
class Events {
    /**
     * Determines whether the specified variable is callable.
     *
     * @return boolean If the specified variable is callable, true ; otherwise, false.
     */
    public static function isCallableType ($callable) {
        if (is_string($callable)) {
            return true;
        }

        if ($callable instanceof Closure) {
            return true;
        }

        if (is_array($callable)) {
            return
                count($callable) == 2
            &&
                (is_array($callable[0]) || is_object($callable[0]))
            &&
                is_string($callable[1])
            ;
        }
    }

    /**
     * Grabs the first exception among specified items.
     *
     * @param Travesable $items The items to check
     * @return Exception|null If an exception has been found, the first encountered : otherwise, null.
     */
    public static function grabException (Traversable $items) {
        foreach ($items as $item) {
            if ($item instanceof Exception) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Calls a set of functions with the specified parameters.
     * This is intended for callback purpose.
     *
     * @param array The functions to call, each item a callable
     * @param array The parameters to pass to the functions [optional]
     */
    public static function call ($callables, $parameters = []) {
        foreach ($callables as $callable) {
            if (!isCallableType($callable)) {
                $ex = static::grabException($parameters);
                throw new InvalidArgumentException("Callback for this method.", 0, $previousEx);
            }
            call_user_func_array($callable, $parameters);
        }
    }

    /**
     * Calls a set of functions with the specified parameters.
     * If no function is present, throws an exception.
     *
     * @param array The functions to call, each item a callable
     * @param array The parameters to pass to the functions [optional]
     * @param Exception The exception to throw if no callback is provided
     */
    public static function callOrThrow ($callables, $parameters = [], $exception = null) {
        if (!count($callables)) {
            //Throws an exception
            if ($exception === null) {
                $exception = static::grabException($parameters);
            }
            if ($exception === null) {
                $exception = new RuntimeException();
            }
            throw $exception;
        }

        static::call($callables, $parameters);
    }
}
