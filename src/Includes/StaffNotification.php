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
        $checkin_time_saved = get_user_meta($user->ID, 'checkin_time', true);
        $checkout_time_saved = get_user_meta($user->ID, 'checkout_time', true);
    
        // Fetch date and time options from wp_options
        $checkin_times_option = get_option('checkin_times', array());
        $checkout_times_option = get_option('checkout_times', array());
    
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
            <tr>
                <th><label for="checkin_time"><?php _e('Check-In Time', 'textdomain'); ?></label></th>
                <td>
                    <select name="checkin_time" id="checkin_time">
                        <option value="">---</option>
                        <?php
                        foreach ($checkin_times_option as $time) {
                            printf('<option value="%s" %s>%s</option>',
                                esc_attr($time),
                                selected($checkin_time_saved, $time, false),
                                esc_html($time)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="checkout_time"><?php _e('Check-Out Time', 'textdomain'); ?></label></th>
                <td>
                    <select name="checkout_time" id="checkout_time">
                        <option value="">---</option>
                        <?php
                        foreach ($checkout_times_option as $time) {
                            printf('<option value="%s" %s>%s</option>',
                                esc_attr($time),
                                selected($checkout_time_saved, $time, false),
                                esc_html($time)
                            );
                        }
                        ?>
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
