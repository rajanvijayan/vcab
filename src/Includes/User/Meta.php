<?php
namespace EcabVendasta\Includes\User;

class Meta {
    public function __construct() {
        add_action('show_user_profile', [__CLASS__, 'add_user_meta_fields']);
        add_action('edit_user_profile', [__CLASS__, 'add_user_meta_fields']);
        add_action('personal_options_update', [__CLASS__, 'save_user_meta_fields']);
        add_action('edit_user_profile_update', [__CLASS__, 'save_user_meta_fields']);
    }

    public static function add_user_meta_fields($user) {
        $role = reset($user->roles); // Get the role of the user (assuming one role per user)

        if ($role === 'staff') {
            ?>
            <h3>Staff Information</h3>
            <table class="form-table">
                <tr>
                    <th><label for="address">Address</label></th>
                    <td><input type="text" name="address" id="address" value="<?php echo esc_attr(get_user_meta($user->ID, 'address', true)); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="checkin_time">Check-In Time</label></th>
                    <td><input type="time" name="checkin_time" id="checkin_time" value="<?php echo esc_attr(get_user_meta($user->ID, 'checkin_time', true)); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="checkout_time">Check-Out Time</label></th>
                    <td><input type="time" name="checkout_time" id="checkout_time" value="<?php echo esc_attr(get_user_meta($user->ID, 'checkout_time', true)); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="phone_number">Phone Number</label></th>
                    <td><input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone_number', true)); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php
        } elseif ($role === 'driver') {
            ?>
            <h3>Driver Information</h3>
            <table class="form-table">
                <tr>
                    <th><label for="licence_number">Licence Number</label></th>
                    <td><input type="text" name="licence_number" id="licence_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'licence_number', true)); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="phone_number">Phone Number</label></th>
                    <td><input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone_number', true)); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php
        }
    }

    public static function save_user_meta_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        $role = reset(get_userdata($user_id)->roles); // Get the role of the user (assuming one role per user)

        if ($role === 'staff') {
            update_user_meta($user_id, 'address', sanitize_text_field($_POST['address']));
            update_user_meta($user_id, 'checkin_time', sanitize_text_field($_POST['checkin_time']));
            update_user_meta($user_id, 'checkout_time', sanitize_text_field($_POST['checkout_time']));
            update_user_meta($user_id, 'phone_number', sanitize_text_field($_POST['phone_number']));
        } elseif ($role === 'driver') {
            update_user_meta($user_id, 'licence_number', sanitize_text_field($_POST['licence_number']));
            update_user_meta($user_id, 'phone_number', sanitize_text_field($_POST['phone_number']));
        }
    }
}
?>
