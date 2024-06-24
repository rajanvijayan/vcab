<?php
namespace EcabVendasta\Includes\Setting;

class Setting {
    public function __construct() {
        add_action('admin_menu', [__CLASS__, 'add_settings_page']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function add_settings_page() {
        add_options_page(
            __('Trip Settings', 'textdomain'),
            __('Trip Settings', 'textdomain'),
            'manage_options',
            'trip-settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    public static function register_settings() {
        register_setting('trip_settings_group', 'checkin_times', [
            'type' => 'array',
            'sanitize_callback' => [__CLASS__, 'sanitize_times']
        ]);
        register_setting('trip_settings_group', 'checkout_times', [
            'type' => 'array',
            'sanitize_callback' => [__CLASS__, 'sanitize_times']
        ]);
        register_setting('trip_settings_group', 'staff_notification_time', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        add_settings_section(
            'trip_settings_section',
            __('Trip Settings', 'textdomain'),
            '__return_false',
            'trip-settings'
        );

        add_settings_field(
            'checkin_times',
            __('Check-in Times', 'textdomain'),
            [__CLASS__, 'render_checkin_times_field'],
            'trip-settings',
            'trip_settings_section'
        );

        add_settings_field(
            'checkout_times',
            __('Check-out Times', 'textdomain'),
            [__CLASS__, 'render_checkout_times_field'],
            'trip-settings',
            'trip_settings_section'
        );

        add_settings_field(
            'staff_notification_time',
            __('Staff Notification Time', 'textdomain'),
            [__CLASS__, 'render_notification_time_field'],
            'trip-settings',
            'trip_settings_section'
        );
    }

    public static function sanitize_times($times) {
        return array_map('sanitize_text_field', (array)$times);
    }

    public static function render_checkin_times_field() {
        $checkin_times = get_option('checkin_times', []);
        ?>
        <div id="checkin-times-container">
            <?php foreach ($checkin_times as $index => $time): ?>
                <div class="time-entry">
                    <input type="time" name="checkin_times[]" value="<?php echo esc_attr($time); ?>">
                    <button type="button" class="button remove-time"><?php _e('Remove', 'textdomain'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-checkin_times" class="button button-primary"><?php _e('Add Check-in Time', 'textdomain'); ?></button>
        <?php
        self::add_times_script('checkin-times-container', 'checkin_times');
    }

    public static function render_checkout_times_field() {
        $checkout_times = get_option('checkout_times', []);
        ?>
        <div id="checkout-times-container">
            <?php foreach ($checkout_times as $index => $time): ?>
                <div class="time-entry">
                    <input type="time" name="checkout_times[]" value="<?php echo esc_attr($time); ?>">
                    <button type="button" class="button remove-time"><?php _e('Remove', 'textdomain'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-checkout_times" class="button button-primary"><?php _e('Add Check-out Time', 'textdomain'); ?></button>
        <?php
        self::add_times_script('checkout-times-container', 'checkout_times');
    }

    public static function render_notification_time_field() {
        $notification_time = get_option('staff_notification_time', '1');
        ?>
        <select name="staff_notification_time" id="staff_notification_time">
            <option value="1" <?php selected($notification_time, '1'); ?>><?php _e('Before 1 hour', 'textdomain'); ?></option>
            <option value="2" <?php selected($notification_time, '2'); ?>><?php _e('Before 2 hours', 'textdomain'); ?></option>
            <option value="3" <?php selected($notification_time, '3'); ?>><?php _e('Before 3 hours', 'textdomain'); ?></option>
        </select>
        <?php
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <form action="options.php" method="post">
                <?php
                settings_fields('trip_settings_group');
                do_settings_sections('trip-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function add_times_script($container_id, $input_name) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#add-<?php echo $input_name; ?>').click(function() {
                    var container = $('#<?php echo $container_id; ?>');
                    var timeEntry = $('<div class="time-entry"></div>');
                    timeEntry.append('<input type="time" name="<?php echo $input_name; ?>[]">');
                    timeEntry.append('<button type="button" class="button remove-time"><?php _e('Remove', 'textdomain'); ?></button>');
                    container.append(timeEntry);
                });

                $(document).on('click', '.remove-time', function() {
                    $(this).parent('.time-entry').remove();
                });
            });
        </script>
        <?php
    }
}

new Setting();
?>
