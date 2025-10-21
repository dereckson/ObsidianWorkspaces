<?php

namespace Waystone\Workspaces\Engines\Framework;

use Dotenv\Dotenv;

/**
 * Interact with the environment
 */
class Environment {

    /**
     * Path to
     */
    const string ROOT_DIR = __DIR__ . "/../../..";

    const array ENV_DIR_CANDIDATES = [
        # Framework installed from workspaces/
        self::ROOT_DIR,

        # Monorepo installation
        self::ROOT_DIR . "/..",
    ];

    /**
     * Reads and loads .env environment file into environment
     */
    public static function init() : void {
        $dotenv = Dotenv::createImmutable(self::ENV_DIR_CANDIDATES);
        $dotenv->safeLoad();
    }
}
