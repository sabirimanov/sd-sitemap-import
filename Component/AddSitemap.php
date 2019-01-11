<?php

namespace SnowDogSitemap\SitemapImporter\Component;

class AddSitemap
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render()
    {
        require __DIR__ . '/../view/add_sitemap.phtml';
    }
}
