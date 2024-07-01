<?php

namespace EcabVendasta;

use EcabVendasta\Includes\User\Roles;
use EcabVendasta\Includes\User\Meta;
use EcabVendasta\Includes\Trip\Trip;
use EcabVendasta\Includes\Setting\Setting;
Use EcabVendasta\Includes\Cron\Cron;
use EcabVendasta\Includes\Trip\Route;
use EcabVendasta\Includes\API\GoogleAI;

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
        new Cron(); // Daily cron job to create parent trip
        new Route(); // Create shift based cron job
        new GoogleAI();
    }
}