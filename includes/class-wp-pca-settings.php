<?php

class WP_PCA_Settings {

    public function run() {
        add_action('admin_init', array(&$this, 'wp_pca_debug_info_settings_init'));
    }

    public function wp_pca_debug_info_settings_init() {
        $wp_pca_debug_info_args = array(
            'sanitize_callback' => array( &$this, 'sanitize_debug_info_input_value' ),
            'default' => '0'
		);
	    register_setting(
            'wp_pca_options', 
            'wp_pca_debug_info_option', 
            $wp_pca_debug_info_args
        );
        $settings_section_text = __( 'WP-PCA Settings', 'wp-plugin-compatibility-assistant' );
        add_settings_section(
            'wp_pca_settings_section',
            $settings_section_text, array( &$this, 'wp_pca_settings_section_callback' ),
            'wp-plugin-compatibility-assistant'
        );
        $settings_debug_info_field_text = __( 'Show debug info', 'wp-plugin-compatibility-assistant' );
        add_settings_field(
            'wp_pca_settings_debug_info_field',
            $settings_debug_info_field_text, array( &$this, 'wp_pca_settings_debug_info_field_callback' ),
            'wp-plugin-compatibility-assistant',
            'wp_pca_settings_section'
        );
    }

    public function sanitize_debug_info_input_value( $input ) {
        if ( $input == '1' ) {
            $input = '1';
        } else {
            $input = '0';
        }
        return $input;
    }

    public function wp_pca_settings_section_callback() {
        return;
    }

    public function wp_pca_settings_debug_info_field_callback() {
        $wp_pca_debug_info = $this->get_pca_debug_info_option();
        ?>
            <input type="checkbox" id="wp_pca_debug_info_on" name="wp_pca_debug_info_option" value="1" <?php checked($wp_pca_debug_info) ?>/>
        <?php
    }

    public function get_pca_debug_info_option() {
        $wp_pca_debug_info = get_option( 'wp_pca_debug_info_option', '0' );
        return $wp_pca_debug_info;
    }

    public function wp_pca_settings() {
        ?>
            <form action="options.php" method="post">
                <?php 
                    settings_fields( 'wp_pca_options' );
                    do_settings_sections( 'wp-plugin-compatibility-assistant' );
                    submit_button( __( 'Save settings', 'wp-plugin-compatibility-assistant' ) );
                ?>
            </form>
        <?php
    }

}

?>