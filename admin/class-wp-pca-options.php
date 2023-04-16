<?php

class WP_PCA_Options {

    public function run( $wp_pca_logic ) {
        add_action('admin_menu', function() use ( $wp_pca_logic ) {
            $this->page($wp_pca_logic);
        }); 
    }

    public function page( $wp_pca_logic ) {
        add_submenu_page(
            'plugins.php',
            'WP Plugin Compatibility Assistant',
            'Plugin Compatibility',
            'manage_options',
            'wp-plugin-compatibility-assistant',
            function() use ( $wp_pca_logic ) {
                $this->page_html($wp_pca_logic);
                $this->dump_plugin_metadata_debug_info($wp_pca_logic);
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
                        <th><h2>Plugins path</h2></th>
                    </tr>
                    <tr>
                        <td><h4><?php $wp_pca_logic->print_php_version(); ?></h4></td>
                        <td><h4><?php $wp_pca_logic->print_wordpress_version(); ?></h4></td>
                        <td><h4><?php $wp_pca_logic->print_plugins_url(); ?></h4></td>
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
                <th>PHP minimum version (required)</th>
                <th>WordPress minimum version (required)</th>
                <th>WordPress maximum version (tested)</th>
            </tr>
        <?php
    }

    public function load_plugin_table( $wp_pca_logic ) {
        ?>
            <table class="wp-pca-table">
        <?php
        $this->plugin_table_header();
        foreach ($wp_pca_logic->get_installed_plugins_metadata() as $plugin) {
            ?> 
                <tr>
                    <td><?php echo $plugin['Name']?></td>
                    <td><?php echo $plugin['path']?></td>
                    <td><?php echo $plugin['Version']?></td>
                    <td><?php echo $plugin['version']?></td>
                    <td><?php echo $plugin['RequiresPHP']?></td>
                    <td><?php echo $plugin['RequiresWP']?></td>
                    <td><?php echo $plugin['tested']?></td>
                </tr>
            <?php
        }
        ?>
            </table>
        <?php
    }

    public function dump_plugin_metadata_debug_info( $wp_pca_logic ) {
        foreach ($wp_pca_logic->get_installed_plugins_metadata() as $plugin) {
            ?><p>Plugin metadata : </p><?php
            var_dump($plugin);
        }
    }
}

?>