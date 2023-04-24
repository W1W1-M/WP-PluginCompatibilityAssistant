<?php

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-includes/general-template.php';
require_once ABSPATH . 'wp-includes/link-template.php';

class WP_PCA_Logic {

    // variables

    private array $installed_plugins;
    private ?int $installed_plugins_count;
    private ?string $plugins_url;
    private ?string $wordpress_version;
    private ?string $php_version;
    private array $installed_plugins_metadata;
    private ?string $site_url;

    // class methods

    public function __construct() {
        $this->installed_plugins = [];
        $this->installed_plugins_count = NULL;
        $this->plugins_url = NULL;
        $this->wordpress_version = NULL;
        $this->php_version = NULL;
        $this->installed_plugins_metadata = [];
        $this->site_url = NULL;
    }

    public function run() {
        $this->get_installed_plugins();
        $this->count_installed_plugins();
        $this->get_plugins_url();
        $this->get_wordpress_version();
        $this->get_php_version();
        $this->get_wp_site_url();
    }

    // installed_plugins methods

    public function get_installed_plugins() {
        $this->installed_plugins = get_plugins();
        return $this->installed_plugins;
    }

    public function count_installed_plugins() {
        $this->installed_plugins_count = count($this->installed_plugins);
    }

    public function get_installed_plugins_metadata() {
        $this->installed_plugins_metadata = [];
        $plugin_page_fields = array(
            'active_installs' => false,
            'added' => true, 
            'author' => true,
            'author_profile' => true,
            'banners' => false,
            'compatibility' => false,
            'contributors' => false,
            'description' => false,
            'donate_link' => false,
            'download_link' => false,
            'downloaded' => false,
            'homepage' => true,
            'icons' => true,
            'last_updated' => true,
            'name' => true,
            'num_ratings' => true,
            'rating' => true,
            'ratings' => true,
            'requires' => true,
            'requires_php' => true,
            'screenshots' => false,
            'sections' => false,
            'short_description' => false,
            'slug' => true,
            'support_threads' => false,
            'support_threads_resolved' => false,
            'tags' => true,
            'tested' => true,
            'version' => true,
            'versions' => true,
        );
        foreach ($this->installed_plugins as $plugin=>$metadata) {
            $metadata['path'] = $plugin;
            $metadata['status'] = $this->get_plugin_status($plugin);
            $plugin_slug = dirname(plugin_basename($plugin));
            $plugin_page = plugins_api('plugin_information', array('slug' => $plugin_slug, 'fields' => $plugin_page_fields));
            if (is_wp_error($plugin_page)) {
                foreach ($plugin_page_fields as $key=>$value) {
                    $metadata[$key] = "Unknown metadata";
                }
            } else {
                foreach ($plugin_page as $page_metadata=>$value) {
                    $metadata[$page_metadata] = $value;
                }
            }
            $this->installed_plugins_metadata[$plugin] = $metadata;
        }
        return $this->installed_plugins_metadata;
    }

    public function get_plugin_status( $plugin ) {
        $status = is_plugin_active($plugin);
        return $status;
    }

    // plugins_url methods

    public function get_plugins_url() {
        $this->plugins_url = plugins_url();
        return $this->plugins_url;
    }

    public function print_plugins_url() {
        echo $this->plugins_url;
    }

    // wordpress_version methods

    public function get_wordpress_version() {
        $this->wordpress_version = get_bloginfo('version');
    }

    public function print_wordpress_version() {
        echo $this->wordpress_version;
    }

    // php_version methods

    public function get_php_version() {
        $this->php_version = phpversion();
        return $this->php_version;
    }

    public function print_php_version() {
        echo $this->php_version;
    }

    public function get_wp_site_url() {
        $this->site_url = site_url();
        return $this->site_url;
    }

    public function get_plugin_editor_url( $plugin_path ) {
        $plugin_editor_url = $this->get_wp_site_url() . "/wp-admin/plugin-editor.php?plugin=" . $plugin_path . "&Submit=Select";
        return $plugin_editor_url;
    }

    // plugin compatibility tests

    public function min_wp_plugin_require($plugin) {
        if ($this->wordpress_version <= $plugin['RequiresWP']) {
            return false;
        } else {
            return true;
        }
    }
}

?>