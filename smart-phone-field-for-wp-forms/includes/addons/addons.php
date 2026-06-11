<?php

defined('ABSPATH') || exit;

class PCafe_SPF_Addons {

    private static $instance = null;

    private $active_addons = [];

    public function __construct() {
        $this->active_addons = PCafe_SPF_Utils::instance()->active_addon_list();

        $this->active_addons_file();
    }

    public function active_addons_file() {
        foreach ($this->active_addons as $addon) {
            if ($addon['path'] !== '') {
                $addon_dir = $addon['path'];
            } else {
                $addon_dir = PCAFE_SPF_PATH . 'includes/addons/' . $addon['slug'] . '/';
            }

            if (file_exists($addon_dir . $addon['slug'] . '.php')) {
                include_once $addon_dir . $addon['slug'] . '.php';
            }
        }
    }

    public static function instance() {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }
}


PCafe_SPF_Addons::instance();
