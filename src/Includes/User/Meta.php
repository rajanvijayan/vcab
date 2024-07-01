<?php
namespace EcabVendasta\Includes\User;

class Meta {

    public function __construct() {
        add_action('show_user_profile', [$this, 'add_cab_requirements_meta']);
        add_action('edit_user_profile', [$this, 'add_cab_requirements_meta']);
        add_action('personal_options_update', [$this, 'save_cab_requirements_meta']);
        add_action('edit_user_profile_update', [$this, 'save_cab_requirements_meta']);
    }

    public function add_cab_requirements_meta($user) {
        if (in_array('staff', (array)$user->roles)) {
            ?>
            <h3>Cab Requirements</h3>
            <table class="form-table">
                <tr>
                    <th><label for="cab_requirements">Cab Requirements</label></th>
                    <td>
                        <textarea name="cab_requirements" id="cab_requirements" rows="5" cols="30"><?php echo esc_textarea(get_user_meta($user->ID, 'cab_requirements', true)); ?></textarea>
                        <br>
                        <span class="description">Please enter cab requirements in JSON format.</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="cab_requirement_from">Cab Requirement From Date</label></th>
                    <td>
                        <input type="date" name="cab_requirement_from" id="cab_requirement_from" value="">
                    </td>
                </tr>
                <tr>
                    <th><label for="cab_requirement_to">Cab Requirement To Date</label></th>
                    <td>
                        <input type="date" name="cab_requirement_to" id="cab_requirement_to" value="">
                    </td>
                </tr>
            </table>
            <script>
                jQuery(document).ready(function($) {
                    $('#cab_requirement_from, #cab_requirement_to').datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                });
            </script>
            <?php
        }
    }

    public function save_cab_requirements_meta($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        $cab_requirements = sanitize_text_field($_POST['cab_requirements']);
        update_user_meta($user_id, 'cab_requirements', $cab_requirements);

        $cab_requirement_from = sanitize_text_field($_POST['cab_requirement_from']);
        $cab_requirement_to = sanitize_text_field($_POST['cab_requirement_to']);

        $cab_requirements_array = json_decode(get_user_meta($user_id, 'cab_requirements', true), true);

        if (!is_array($cab_requirements_array)) {
            $cab_requirements_array = [];
        }

        $from_date = strtotime($cab_requirement_from);
        $to_date = strtotime($cab_requirement_to);

        for ($date = $from_date; $date <= $to_date; $date += 86400) {
            $cab_requirements_array[] = [
                'date' => $date,
                'pick_up' => false,
                'drop_off' => false,
                'location' => [
                    'lat' => 0.0,
                    'lng' => 0.0
                ],
                'status' => 'future'
            ];
        }

        update_user_meta($user_id, 'cab_requirements', json_encode($cab_requirements_array));
    }
}