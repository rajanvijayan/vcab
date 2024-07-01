<?php

namespace EcabVendasta;

use EcabVendasta\Includes\User\Roles;
use EcabVendasta\Includes\Settings\CabSettings;
use EcabVendasta\Includes\User\MyCab;

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
    }
}