<?php

require_once ABSPATH . 'wp-admin/includes/plugin.php';

class WP_PCA_Logic {

    public $installed_plugins;
    public $installed_plugins_count;

    public function __construct() {
        $this->$installed_plugins = [];
        $this->$installed_plugins_count = NULL;
    }

    public function run() {
        $this->get_installed_plugins();
        $this->count_installed_plugins();
    }

    public function get_installed_plugins() {
        $this->$installed_plugins = get_plugins();
    }

    public function count_installed_plugins() {
        $this->$installed_plugins_count = count($this->$installed_plugins);
    }
}

?>