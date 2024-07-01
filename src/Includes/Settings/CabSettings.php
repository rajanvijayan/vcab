<?php
namespace EcabVendasta\Includes\Settings;

class CabSettings {

    public function __construct() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    public static function enqueue_scripts() {
        wp_enqueue_style('vcab-style', plugin_dir_url( __FILE__ ) . '../../../assets/css/trip.css');
    }

    public static function add_admin_menu() {
        add_options_page(
            'Cab Settings',        // Page title
            'Cab Settings',        // Menu title
            'manage_options',      // Capability
            'cab_settings',        // Menu slug
            [__CLASS__, 'create_admin_page'] // Callback function
        );
    }

    public static function register_settings() {
        register_setting('cab_settings_group', 'vcab_shifts');

        add_settings_section(
            'cab_settings_section', 
            'Cab Shifts Settings', 
            null, 
            'cab_settings'
        );

        add_settings_field(
            'vcab_shifts', 
            'Cab Provided Shifts', 
            [__CLASS__, 'render_shifts_field'], 
            'cab_settings', 
            'cab_settings_section'
        );
    }

    public static function create_admin_page() {
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                settings_fields('cab_settings_group');
                do_settings_sections('cab_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function render_shifts_field() {
        $vcab_shifts = get_option('vcab_shifts', []);
        if (!is_array($vcab_shifts)) {
            $vcab_shifts = [];
        }
        ?>
        <div id="vcab-shifts-container">
            <?php foreach ($vcab_shifts as $index => $shift) { ?>
                <div class="vcab-shift">
                    <input type="time" name="vcab_shifts[<?php echo $index; ?>][time]" value="<?php echo esc_attr($shift['time']); ?>" required>
                    <select name="vcab_shifts[<?php echo $index; ?>][type]" required>
                        <option value="pickup" <?php selected($shift['type'], 'pickup'); ?>>Pickup</option>
                        <option value="drop" <?php selected($shift['type'], 'drop'); ?>>Drop</option>
                    </select>
                    <button type="button" class="button vcab-remove-shift">Remove</button>
                </div>
            <?php } ?>
        </div>
        <button type="button" class="button vcab-add-shift">Add Shift</button>

        <script>
        jQuery(document).ready(function($) {
            var shiftIndex = <?php echo count($vcab_shifts); ?>;

            $('.vcab-add-shift').click(function() {
                $('#vcab-shifts-container').append(`
                    <div class="vcab-shift">
                        <input type="time" name="vcab_shifts[` + shiftIndex + `][time]" required>
                        <select name="vcab_shifts[` + shiftIndex + `][type]" required>
                            <option value="pickup">Pickup</option>
                            <option value="drop">Drop</option>
                        </select>
                        <button type="button" class="button vcab-remove-shift">Remove</button>
                    </div>
                `);
                shiftIndex++;
            });

            $(document).on('click', '.vcab-remove-shift', function() {
                $(this).closest('.vcab-shift').remove();
            });
        });
        </script>
        <?php
    }
}