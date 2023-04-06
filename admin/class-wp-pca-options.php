<?php

class WP_PCA_Options {
    public function run() {
        add_action('admin_menu', array($this, 'page')); 
    }
    public function page() {
        add_submenu_page(
            'tools.php',
            'WP Plugin Compatibility Assistant',
            'WP Plugin Compatibility Assistant',
            'manage_options',
            'wp-plugin-compatibility-assistant',
            array($this, 'page_html')
        );
    }
    public function page_html() {
        // check user capabilities
        if ( current_user_can( 'manage_options' ) ) {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            </div>
            <?php
        } else {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <h3>You are not authorised to manage these settings. Please contact your WordPress administrator.</h3>
            </div>
            <?php
        }
    }
}

?>