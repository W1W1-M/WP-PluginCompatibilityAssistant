<?php

class WP_PCA_Options {

    public function run( $installed_plugins ) {
        add_action('admin_menu', function() use ( $installed_plugins ) {
            $this->page($installed_plugins);
        }); 
    }

    public function page( $installed_plugins ) {
        add_submenu_page(
            'tools.php',
            'WP Plugin Compatibility Assistant',
            'WP Plugin Compatibility Assistant',
            'manage_options',
            'wp-plugin-compatibility-assistant',
            function() use ( $installed_plugins ) {
                $this->page_html($installed_plugins);
            }
        );
    }

    public function page_html( $installed_plugins ) {
        // check user capabilities
        if ( current_user_can( 'manage_options' ) ) {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <?php $this->load_plugin_table( $installed_plugins ) ?>
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

    public function plugin_table_header() {
        ?>
            <tr>
                <th>Plugin</th>
                <th>Path</th>
                <th>Version (current)</th>
                <th>Version (latest)</th>
                <th>WordPress minimum version (required)</th>
                <th>WordPress maximum version (tested)</th>
                <th>PHP minimum version (required)</th>
            </tr>
        <?php
    }

    public function load_plugin_table( $installed_plugins ) {
        ?>
            <table>
        <?php
        $this->plugin_table_header();
        foreach ($installed_plugins as $plugin) {
            ?> 
                <tr>
            <?php
            foreach ($plugin as $metadata_key => $metadata_value) {
                switch ($metadata_key) {
                    case 'Name':
                        ?> 
                            <td> <?php echo $metadata_value; ?> </td>
                        <?php
                        break;
                    case 'Version':
                        ?> 
                            <td> <?php echo $metadata_value; ?> </td>
                        <?php
                        break;
                }
            }
            ?> 
                </tr>
            <?php
        }
        ?>
            </table>
        <?php
    }

}

?>