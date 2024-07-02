<?php

namespace EcabVendasta;

use EcabVendasta\Includes\User\Roles;
use EcabVendasta\Includes\Settings\CabSettings;
use EcabVendasta\Includes\User\MyCab;
use EcabVendasta\Includes\Trip\PostType;
use EcabVendasta\Includes\Trip\Cron;

use EcabVendasta\Includes\Trip\Schedule;

class Plugin {
    /**
     * Constructor.
     *
     * Initializes the plugin by setting up necessary actions and hooks.
     */
    public function __construct() {
        new Roles();
        new CabSettings();
        new MyCab();
        new PostType();
        new Cron();

        new Schedule();
    }
}