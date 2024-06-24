<?php

namespace EcabVendasta;

use EcabVendasta\Includes\User\Roles;
use EcabVendasta\Includes\User\Meta;
use EcabVendasta\Includes\Trip\Trip;
use EcabVendasta\Includes\Setting\Setting;
use EcabVendasta\Includes\StaffNotification;
Use EcabVendasta\Includes\Cron\Cron;

class Plugin {
    /**
     * Constructor.
     *
     * Initializes the plugin by setting up necessary actions and hooks.
     */
    public function __construct() {
        new Roles();
        new Meta();
        new Trip();
        new Setting();
        new StaffNotification();
        new Cron();
    }
}