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
                <?php $this->plugin_table() ?>
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
    public function plugin_table() {
        ?>
            <table>
                <tr>
                    <th>Plugin</th>
                    <th>Path</th>
                    <th>Version (current)</th>
                    <th>Version (latest)</th>
                    <th>WordPress minimum version (required)</th>
                    <th>WordPress maximum version (tested)</th>
                    <th>PHP minimum version (required)</th>
                </tr>
            </table>
        <?php
    }
}

?>