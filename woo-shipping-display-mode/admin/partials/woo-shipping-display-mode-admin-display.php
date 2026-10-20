<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/admin/partials
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Settings_Shipping_Display_Mode_Methods {

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->woo_shipping_hooks();
    }

    /**
     * Class hooks.
     *
     * @since 1.0.0
     */
    public function woo_shipping_hooks() {
        // Add WC settings tab
        add_filter('woocommerce_settings_tabs_array', array($this, 'woo_shipping_settings_tab'), 70);

        // Settings page contents
        add_action('woocommerce_settings_tabs_shipping_mode', array($this, 'woo_shipping_settings_page'));

        // Save settings page
        add_action('woocommerce_update_options_shipping_mode', array($this, 'woo_shipping_update_options'));
    }

    /**
     * Settings tab.
     *
     * Add a WooCommerce settings tab for the Receiptful settings page.
     *
     * @since 1.0.0
     *
     * @param    array $tabs Array of default tabs used in WC.
     * @return    array            All WC settings tabs including newly added.
     */
    public function woo_shipping_settings_tab($tabs) {

        $tabs['shipping_mode'] = __('Shipping Method Display Mode', 'woo-shipping-display-mode');

        return $tabs;
    }

    /**
     * Settings page content.
     *
     * @since 1.0.0
     */
    public function woo_shipping_settings_page() {
        if (!defined('ABSPATH')) {
            exit;
        }
        ?>
        <?php
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
	    do_action('woocommerce_update_options_shipping_mode');
        $shiiping_method_get_value = get_option('woocommerce_shipping_method_format');
        $wsdm_add_placeholder      = get_option( 'wsdm_add_shipping_placeholder', 'no' );
        $wsdm_placeholder_text     = get_option( 'wsdm_shipping_placeholder_text', '' );
        if ( ! is_string( $wsdm_placeholder_text ) || '' === trim( $wsdm_placeholder_text ) ) {
            $wsdm_placeholder_text = __( 'Select Shipping', 'woo-shipping-display-mode' );
        }
        ?>
        <h3><?php esc_html_e('Shipping Method Display Mode', 'woo-shipping-display-mode'); ?></h3>
        <table class="form-table" id="shipping_display_table">
            <?php wp_nonce_field(basename(__FILE__), 'woo_shipping_display_mode'); ?>
            <tr>
                <th scope="row" class="titledesc">
                    <label for="woocommerce_shipping_method_format"><?php esc_html_e('Select Display Mode', 'woo-shipping-display-mode'); ?><span class="woocommerce-help-tip" data-tip="<?php echo esc_attr('Display shipping methods with Radio buttons and Dropdowns', 'woo-shipping-display-mode'); ?>"></span></label>
                </th>
                <td class="forminp forminp-radio">
                    <fieldset>
                        <ul>
                            <li>
                                <label><input name="woocommerce_shipping_method_format" value="radio" type="radio" style="" class="" <?php
                                    if ('radio' === $shiiping_method_get_value) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                    <span><?php esc_html_e('Radio Button (Default)', 'woo-shipping-display-mode'); ?></span>
                                </label>
                            </li>
                            <li>
                                <label><input name="woocommerce_shipping_method_format" value="select" type="radio" style="" class="" <?php
                                    if ('select' === $shiiping_method_get_value) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                    <span><?php esc_html_e('Dropdown', 'woo-shipping-display-mode'); ?></span>
                                </label>
                                <div class="wsdm-placeholder-setting<?php echo ( 'select' === $shiiping_method_get_value ) ? '' : ' is-hidden'; ?>">
                                    <label for="wsdm_add_shipping_placeholder">
                                        <input type="checkbox" id="wsdm_add_shipping_placeholder" name="wsdm_add_shipping_placeholder" value="yes" <?php checked( 'yes', $wsdm_add_placeholder ); ?> />
                                        <?php esc_html_e( 'Add placeholder', 'woo-shipping-display-mode' ); ?>
                                    </label>
                                    <input type="text" class="wsdm-placeholder-text" id="wsdm_shipping_placeholder_text" name="wsdm_shipping_placeholder_text" value="<?php echo esc_attr( $wsdm_placeholder_text ); ?>" placeholder="<?php esc_attr_e( 'Select Shipping', 'woo-shipping-display-mode' ); ?>" aria-label="<?php esc_attr_e( 'Placeholder text', 'woo-shipping-display-mode' ); ?>" />
                                </div>
                            </li>
                        </ul>
                    </fieldset>
                </td>
            </tr>
        </table>
        <div class="shipping_display_table_note">
            <?php echo sprintf( wp_kses( __( '<p><span>Note: </span>If there is only one shipping method available in your cart then it will showcase shipping method as title without radio button or dropdown. <a href="%1$s" target="_blank">Click Here</a> for detail.</span><p>', 'woo-shipping-display-mode' ), array(
                    'p'    => array(),
                    'span' => array(),
                    'a'    => array( 
                                'href'   => array(),
                                'target' => array()
                            ),
                ) ), esc_url( 'https://docs.thedotstore.com/article/421-no-plugin-working-once-there-is-one-shipping-method' ) ); ?>
        </div>
        <?php
    }

    /**
     * Save settings.
     *
     * Save settings based on WooCommerce save_fields() method.
     *
     * @since 1.0.0
     */
    public function woo_shipping_update_options() {

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

	    $is_submitted  = filter_input( INPUT_POST, 'save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        if ( !empty( $is_submitted ) ) {
            // verify nonce
	        $woo_shipping_display_mode  = filter_input( INPUT_POST, 'woo_shipping_display_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            if (!isset($woo_shipping_display_mode) || !wp_verify_nonce($woo_shipping_display_mode, basename(__FILE__))) {
                die('Failed security check');
            }
	        $woocommerce_shipping_method_format  = filter_input( INPUT_POST, 'woocommerce_shipping_method_format', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

            if (!empty($woocommerce_shipping_method_format)) {
                $display_mode = sanitize_text_field($woocommerce_shipping_method_format);

                if ( ! in_array( $display_mode, array( 'radio', 'select' ), true ) ) {
                    return;
                }

                update_option( 'woocommerce_shipping_method_format', $display_mode, 'yes' );

                $placeholder_enabled = filter_input( INPUT_POST, 'wsdm_add_shipping_placeholder', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                update_option( 'wsdm_add_shipping_placeholder', ( 'yes' === $placeholder_enabled ) ? 'yes' : 'no', 'yes' );

                $placeholder_text = filter_input( INPUT_POST, 'wsdm_shipping_placeholder_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $placeholder_text = is_string( $placeholder_text ) ? sanitize_text_field( $placeholder_text ) : '';
                if ( '' === $placeholder_text ) {
                    $placeholder_text = __( 'Select Shipping', 'woo-shipping-display-mode' );
                }
                update_option( 'wsdm_shipping_placeholder_text', $placeholder_text, 'yes' );
            }
        }
    }
    
}
