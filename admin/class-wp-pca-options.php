<?php

class WP_PCA_Options {

    public function run( $wp_pca_logic ) {
        add_action('admin_menu', function() use ( $wp_pca_logic ) {
            $this->page($wp_pca_logic);
        }); 
    }

    public function page( $wp_pca_logic ) {
        add_submenu_page(
            'tools.php',
            'WP Plugin Compatibility Assistant',
            'WP Plugin Compatibility Assistant',
            'manage_options',
            'wp-plugin-compatibility-assistant',
            function() use ( $wp_pca_logic ) {
                $this->page_html($wp_pca_logic);
            }
        );
    }

    public function page_html( $wp_pca_logic ) {
        // check user capabilities
        if ( current_user_can( 'manage_options' ) ) {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <table class="wp-pca-table">
                    <tr>
                        <th><h2>PHP version</h2></th>
                        <th><h2>WordPress version</h2></th>
                        <th><h2>Plugin path</h2></th>
                    </tr>
                    <tr>
                        <td><h2><?php $wp_pca_logic->print_php_version(); ?></h2></td>
                        <td><h2><?php $wp_pca_logic->print_wordpress_version(); ?></h2></td>
                        <td><h2><?php $wp_pca_logic->print_plugins_url(); ?></h2></td>
                    </tr>
                </table>
                <?php $this->load_plugin_table( $wp_pca_logic ) ?>
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

    public function load_plugin_table( $wp_pca_logic ) {
        ?>
            <table>
        <?php
        $this->plugin_table_header();
        foreach ($wp_pca_logic->get_installed_plugins() as $plugin) {
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