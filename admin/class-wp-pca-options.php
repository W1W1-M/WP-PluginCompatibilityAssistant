<?php

require_once ABSPATH . 'wp-includes/option.php';
require_once ABSPATH . 'wp-admin/includes/template.php';

class WP_PCA_Options {

    public function run( $wp_pca_logic ) {
        add_action('admin_menu', function() use ( $wp_pca_logic ) {
            $this->page($wp_pca_logic);
        });
        add_action('admin_init', array(&$this, 'wp_pca_debug_info_settings_init'));
    }

    public function page( $wp_pca_logic ) {
        $submenu_text = __('Plugin Compatibility', 'wp-plugin-compatibility-assistant');
        add_submenu_page(
            'plugins.php',
            'WP Plugin Compatibility Assistant',
            $submenu_text,
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
                    $this->load_plugin_table($wp_pca_logic);
                    $this->wp_pca_settings();
                    if ($this->get_pca_debug_info_option() == true) {
                        $this->dump_plugin_metadata_debug_info($wp_pca_logic);
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

    public function plugin_table_header() {
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

    public function load_plugin_table( $wp_pca_logic ) {
        ?>
            <table class="wp-pca-table">
        <?php
        $this->plugin_table_header();
        foreach ($wp_pca_logic->get_installed_plugins_metadata() as $plugin_path=>$plugin) {
            ?> 
                <tr>
                    <td class="wp-pca-table-light"><a href="<?php echo $plugin['PluginURI']?>" target="_blank"><?php echo $plugin['Name']?></a></td>
                    <td class="wp-pca-table-light"><a href="<?php echo $wp_pca_logic->get_plugin_editor_url($plugin_path)?>" target="_blank"><?php echo $plugin['path']?></a></td>
                    <td class="wp-pca-table-light"><a href="<?php echo $plugin['AuthorURI']?>" target="_blank"><?php echo $plugin['Author']?></a></td>
                    <td <?php echo $wp_pca_logic->plugin_up_to_date($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-warning"' ?>><i><?php echo $plugin['Version']?></i></td>
                    <td class="wp-pca-table-light"><i><?php echo $plugin['version']?></i><br/><?php echo " (" . $plugin['last_updated'] . ")"?></td>
                    <td <?php echo $wp_pca_logic->min_php_plugin_require($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-invalid"' ?>><i><?php echo $plugin['RequiresPHP']?></i></td>
                    <td <?php echo $wp_pca_logic->min_wp_plugin_require($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-invalid"' ?>><i><?php echo $plugin['RequiresWP']?></i></td>
                    <td <?php echo $wp_pca_logic->max_wp_plugin_tested($plugin) ? 'class="wp-pca-table-valid"' : 'class="wp-pca-table-warning"' ?>><i><?php echo $plugin['tested']?></i></td>
                    <td class="wp-pca-table-light"><input type="checkbox" <?php checked($plugin['status']) ?> disabled="disabled"/></td>
                </tr>
            <?php
        }
        ?>
            </table>
        <?php
    }

    public function dump_plugin_metadata_debug_info( $wp_pca_logic ) {
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

    // PCA options methods

    public function wp_pca_debug_info_settings_init() {
        $wp_pca_debug_info_args = array(
            'sanitize_callback' => array(&$this, 'sanitize_input_value'),
            'default' => '0'
		);
	    register_setting(
            'wp_pca_options', 
            'wp_pca_debug_info_option', 
            $wp_pca_debug_info_args
        );
        $settings_section_text = __('WP-PCA Settings', 'wp-plugin-compatibility-assistant');
        add_settings_section(
            'wp_pca_settings_section',
            $settings_section_text, array(&$this, 'wp_pca_settings_section_callback'),
            'wp-plugin-compatibility-assistant'
        );
        $settings_field_text = __('Show debug info', 'wp-plugin-compatibility-assistant');
        add_settings_field(
            'wp_pca_settings_field',
            $settings_field_text, array(&$this, 'wp_pca_settings_field_callback'),
            'wp-plugin-compatibility-assistant',
            'wp_pca_settings_section'
        );
    }

    public function sanitize_input_value( $input ) {
        if ($input == '1') {
            $input = '1';
        } else {
            $input = '0';
        }
        return $input;
    }

    public function wp_pca_settings_section_callback() {
        return;
    }

    public function wp_pca_settings_field_callback() {
        $wp_pca_debug_info = $this->get_pca_debug_info_option();
        ?>
            <input type="checkbox" id="wp_pca_debug_info_on" name="wp_pca_debug_info_option" value="1" <?php checked($wp_pca_debug_info) ?>/>
        <?php
    }

    public function get_pca_debug_info_option() {
        $wp_pca_debug_info = get_option('wp_pca_debug_info_option', '0');
        return $wp_pca_debug_info;
    }

    public function wp_pca_settings() {
        ?>
            <form action="options.php" method="post">
                <?php 
                    settings_fields('wp_pca_options');
                    do_settings_sections('wp-plugin-compatibility-assistant');
                    submit_button(__('Save settings', 'wp-plugin-compatibility-assistant'));
                ?>
            </form>
        <?php
    }
}

?>