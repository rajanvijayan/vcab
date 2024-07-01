<?php
namespace EcabVendasta\Includes\User;

class Roles {
    public function __construct() {
        add_role(
            'staff',
            __( 'Staff' ),
            array(
                'read'         => true,  // Allows a user to read posts and pages
                'edit_posts'   => true,  // Allows user to edit their own posts
                'delete_posts' => false, // Doesn't allow user to delete their own posts
            )
        );

        add_role(
            'cab_admin',
            __( 'Cab Admin' ),
            array(
                'read'         => true,
                'edit_posts'   => true,
                'delete_posts' => false,
            )
        );
    }
}
?>
