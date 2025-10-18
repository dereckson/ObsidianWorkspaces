<?php

/*  -------------------------------------------------------------
    Bootstrap tests for Obsidian Workspaces
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
    __DIR__ . "/../vendor",

    # Composer packages have been installed at monorepo root level
    __DIR__ . "/../../vendor",

];

function search_vendor_autoload () : string|null {
    foreach (VENDOR_DIR_CANDIDATES as $dir) {
        $autoload_path = $dir . "/autoload.php";

        echo "Candidate: $autoload_path\n";

        if (file_exists($autoload_path)) {
            return $autoload_path;
        }
    }

    return null;
}

/*  -------------------------------------------------------------
    Entry point for tests
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

function run() : void {
    $vendor_autoload = search_vendor_autoload();

    if ($vendor_autoload === null) {
        fwrite(STDERR, "Your first need to install dependencies. Run `composer install` before running tests.");
        die;
    }

    require($vendor_autoload);
}

run();
