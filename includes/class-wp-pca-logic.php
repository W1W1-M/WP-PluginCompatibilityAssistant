<?php

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-includes/general-template.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

class WP_PCA_Logic {

    // variables

    private array $installed_plugins;
    private ?int $installed_plugins_count;
    private ?string $plugins_url;
    private ?string $wordpress_version;
    private ?string $php_version;
    private array $installed_plugins_metadata;

    // class methods

    public function __construct() {
        $this->installed_plugins = [];
        $this->installed_plugins_count = NULL;
        $this->plugins_url = NULL;
        $this->wordpress_version = NULL;
        $this->php_version = NULL;
        $this->installed_plugins_metadata = [];
    }

    public function run() {
        $this->get_installed_plugins();
        $this->count_installed_plugins();
        $this->get_plugins_url();
        $this->get_wordpress_version();
        $this->get_php_version();
        $this->get_installed_plugins_metadata();
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
        foreach ($this->installed_plugins as $plugin=>$metadata) {
            $plugin_slug = dirname(plugin_basename($plugin));
            $plugin_page = plugins_api('plugin_information', array('slug' => $plugin_slug));
            array_push($metadata, $plugin_page);
            array_push($this->installed_plugins_metadata, $metadata);
        }
        return $this->installed_plugins_metadata;
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
}

?>