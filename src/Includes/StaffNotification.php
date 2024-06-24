<?php
namespace EcabVendasta\Includes;

class StaffNotification {
    public function __construct() {
        add_action('show_user_profile', [__CLASS__, 'add_staff_notification_fields']);
        add_action('edit_user_profile', [__CLASS__, 'add_staff_notification_fields']);
        add_action('personal_options_update', [__CLASS__, 'save_staff_notification_fields']);
        add_action('edit_user_profile_update', [__CLASS__, 'save_staff_notification_fields']);
    }

    public static function add_staff_notification_fields($user) {
        ?>
        <h3><?php _e('Staff Cab Requirement', 'textdomain'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="cab_pickup_required"><?php _e('Cab Pickup Required Today?', 'textdomain'); ?></label></th>
                <td>
                    <select name="cab_pickup_required" id="cab_pickup_required">
                        <option value="no" <?php selected(get_user_meta($user->ID, 'cab_pickup_required', true), 'no'); ?>><?php _e('No', 'textdomain'); ?></option>
                        <option value="yes" <?php selected(get_user_meta($user->ID, 'cab_pickup_required', true), 'yes'); ?>><?php _e('Yes', 'textdomain'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="cab_drop_required"><?php _e('Cab Drop Required Today?', 'textdomain'); ?></label></th>
                <td>
                    <select name="cab_drop_required" id="cab_drop_required">
                        <option value="no" <?php selected(get_user_meta($user->ID, 'cab_drop_required', true), 'no'); ?>><?php _e('No', 'textdomain'); ?></option>
                        <option value="yes" <?php selected(get_user_meta($user->ID, 'cab_drop_required', true), 'yes'); ?>><?php _e('Yes', 'textdomain'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_staff_notification_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        update_user_meta($user_id, 'cab_pickup_required', $_POST['cab_pickup_required']);
        update_user_meta($user_id, 'cab_drop_required', $_POST['cab_drop_required']);
    }
}
