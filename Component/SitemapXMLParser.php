<?php

namespace SnowDogSitemap\SitemapImporter\Component;

use Exception;
use SimpleXMLElement;

class SitemapXMLParser
{
    protected $sitemap;

    public function __construct($sitemap_file)
    {
        if (!file_exists($sitemap_file)) {
            throw new Exception('File doesn\'t exists');
        }

        $this->sitemap = simplexml_load_file($sitemap_file);
    }

    /**
     * @return array
     */
    public function parseData()
    {
        $parsed = [];
        foreach ($this->sitemap as $el) {
            $path = parse_url($el->loc, PHP_URL_PATH);
            $params = parse_url($el->loc, PHP_URL_QUERY);
            $fragment = parse_url($el->loc, PHP_URL_FRAGMENT);

            if ($path !== "/") {
                $path = substr($path, 1);
            }
            $page_url = $path;
            if ($params) {
                $page_url .= "?" . $params;
            }
            if ($fragment) {
                $page_url .= "#" . $fragment;
            }
            $parsed[parse_url($el->loc, PHP_URL_HOST)][] = $page_url;
        }
        return $parsed;
    }
}
