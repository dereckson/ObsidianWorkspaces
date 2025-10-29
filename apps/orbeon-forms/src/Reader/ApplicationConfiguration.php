<?php

namespace Waystone\Apps\OrbeonForms\Reader;

use Waystone\Workspaces\Engines\Apps\ApplicationConfiguration as BaseApplicationConfiguration;

class ApplicationConfiguration extends BaseApplicationConfiguration {

    /**
     * @var array Configuration for Orbeon Forms
     */
    public array $orbeonDatabase = [];

    /**
     * @var array Configuration for Orbeon forms
     */
    public array $forms = [];

}
