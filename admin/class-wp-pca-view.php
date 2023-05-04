<?php

/**
 * WP-PCA view class
 * 
 * Contains logic for adding plugins submenu item, options page, plugins table & debug information.
 */
class WP_PCA_View {

    public function setup( $wp_pca_logic, $wp_pca_settings ): void {
        add_action('admin_menu', function() use ( $wp_pca_logic, $wp_pca_settings ) {
            $this->setup_submenu_with_page( $wp_pca_logic, $wp_pca_settings );
        });
    }

    public function setup_submenu_with_page( $wp_pca_logic, $wp_pca_settings ): void {
        $submenu_text = __('Plugin Compatibility', 'wp-plugin-compatibility-assistant');
        add_submenu_page(
            'plugins.php',
            'WP Plugin Compatibility Assistant',
            $submenu_text,
            'manage_options',
            'wp-plugin-compatibility-assistant',
            function() use ( $wp_pca_logic, $wp_pca_settings ) {
                $this->setup_page( $wp_pca_logic, $wp_pca_settings );
            }
        );
    }

    public function setup_page( $wp_pca_logic, $wp_pca_settings ): void {
        // check user capabilities
        if ( current_user_can( 'manage_options' ) ) {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <h2><?php _e('Your WP site', 'wp-plugin-compatibility-assistant'); ?></h2>
                <table class="wp-pca-table">
                    <tr>
                        <th><h2><?php _e('PHP version', 'wp-plugin-compatibility-assistant'); ?></h2></th>
                        <th><h2><?php _e('WordPress version', 'wp-plugin-compatibility-assistant'); ?></h2></th>
                        <th><h2><?php _e('Plugins path', 'wp-plugin-compatibility-assistant'); ?></h2></th>
                    </tr>
                    <tr>
                        <td><h4><?php $wp_pca_logic->print_php_version(); ?></h4></td>
                        <td><h4><?php $wp_pca_logic->print_wordpress_version(); ?></h4></td>
                        <td><h4><?php $wp_pca_logic->print_plugins_url(); ?></h4></td>
                    </tr>
                </table>
                <h2><?php _e('Your WP plugins', 'wp-plugin-compatibility-assistant'); ?></h2>
                <?php 
                    $this->setup_plugin_table( $wp_pca_logic );
                    $wp_pca_settings->wp_pca_settings();
                    if ( $wp_pca_settings->get_pca_debug_info_option() == true ) {
                        $this->debug_dump_plugins_metadata( $wp_pca_logic );
                    }
                ?>
            </div>
            <?php
        } else {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <h3><?php _e('You are not authorised to manage these settings. Please contact your WordPress administrator.', 'wp-plugin-compatibility-assistant'); ?></h3>
            </div>
            <?php
        }
    }

    public function setup_plugin_table_header(): void {
        ?>
            <tr>
                <th><?php _e('Plugin', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('Path', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('Author', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('Version (installed)', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('Version (latest)', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('PHP minimum version (required)', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('WordPress minimum version (required)', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('WordPress maximum version (tested)', 'wp-plugin-compatibility-assistant'); ?></th>
                <th><?php _e('Status (active)', 'wp-plugin-compatibility-assistant'); ?></th>
            </tr>
        <?php
    }

    public function setup_plugin_table( $wp_pca_logic ): void {
        ?>
            <table class="wp-pca-table">
        <?php
        $this->setup_plugin_table_header();
        foreach ($wp_pca_logic->get_installed_plugins_metadata() as $plugin_path=>$plugin) {
            ?> 
                <tr>
                    <td class="wp-pca-table-light"><a href="<?php echo $plugin['PluginURI']?>" target="_blank"><?php echo $plugin['Name']?></a></td>
                    <td class="wp-pca-table-light"><a href="<?php echo $wp_pca_logic->get_plugin_editor_url($plugin_path)?>" target="_blank"><?php echo $plugin['path']?></a></td>
                    <td class="wp-pca-table-light"><a href="<?php echo $plugin['AuthorURI']?>" target="_blank"><?php echo $plugin['Author']?></a></td>
                    <td <?php echo $wp_pca_logic->plugin_up_to_date($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-warning"' ?>><i><?php echo $plugin['Version']?></i></td>
                    <td class="wp-pca-table-light"><i><?php echo $plugin['version']?></i><br/><?php echo " (" . $plugin['last_updated'] . ")"?></td>
                    <td <?php echo $wp_pca_logic->min_php_plugin_require($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-warning"' ?>><i><?php echo $plugin['RequiresPHP']?></i></td>
                    <td <?php echo $wp_pca_logic->min_wp_plugin_require($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-warning"' ?>><i><?php echo $plugin['RequiresWP']?></i></td>
                    <td <?php echo $wp_pca_logic->max_wp_plugin_tested($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-warning"' ?>><i><?php echo $plugin['tested']?></i></td>
                    <td class="wp-pca-table-light"><input type="checkbox" <?php checked($plugin['status']) ?> disabled="disabled"/></td>
                </tr>
            <?php
        }
        ?>
            </table>
        <?php
    }

    public function debug_dump_plugins_metadata( $wp_pca_logic ): void {
        ?><h2>Debug info</h2><?php
        foreach ($wp_pca_logic->get_installed_plugins_metadata() as $plugin) {
            ?><h3>Plugin metadata : <?php echo $plugin['Name']?> </h3><?php
            foreach ($plugin as $key=>$value) {
                ?> <p class="debug"> <strong>key :</strong> <?php
                var_dump($key);
                ?> </p><p class="debug"><strong>| value :</strong> <?php
                var_dump($value);
                ?> </p> <?php
            }
        }
    }

}

?>