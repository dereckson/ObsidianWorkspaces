<?php

/*  -------------------------------------------------------------
    Obsidian Workspaces :: Autoloader from vendor/
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Project:        Nasqueron
    Product:        Obsidian Workspaces
    License:        Trivial work, not eligible to copyright
    -------------------------------------------------------------    */

/*  -------------------------------------------------------------
    Search relevant vendor/ directory
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

const VENDOR_DIR_CANDIDATES = [
    # Composer packages have been installed directly in workspaces/
    __DIR__ . "/../../vendor",

    # Composer packages have been installed at monorepo root level
    __DIR__ . "/../../../vendor",
];

function search_vendor_autoload () : string|null {
    foreach (VENDOR_DIR_CANDIDATES as $dir) {
        $autoload_path = $dir . "/autoload.php";

        if (file_exists($autoload_path)) {
            return $autoload_path;
        }
    }

    return null;
}

/*  -------------------------------------------------------------
    Loader
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

function require_vendor_autoload() : void {
    $vendor_autoload = search_vendor_autoload();

    if ($vendor_autoload === null) {
        fwrite(STDERR, "You first need to install dependencies. Run `composer install`.");
        die;
    }

    require($vendor_autoload);
}

require_vendor_autoload();
