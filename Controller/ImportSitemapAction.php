<?php

namespace SnowDogSitemap\SitemapImporter\Controller;

use SnowDogSitemap\SitemapImporter\Component\SitemapXMLParser;
use SnowDogSitemap\SitemapImporter\Model\SitemapManager;
use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use RuntimeException;

class importSitemapAction
{

    /**
     * @var SitemapManager
     */
    private $sitemapManager;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager, SitemapManager $sitemapManager)
    {
        $this->userManager = $userManager;
        $this->sitemapManager = $sitemapManager;
    }

    public function execute()
    {
        $user = $this->userManager->getByLogin($_SESSION['login']);

        $xml_file = $_FILES['xml_file'];

        if (!isset($xml_file['error']) || is_array($xml_file['error'])) {
            throw new RuntimeException('Error. Please, try again.');
        }

        if (!in_array($xml_file['type'], ['text/xml'])) {
            $_SESSION['flash'] = "Invalid file. Please, upload sitemap file in XML format";
            return header("Location: /");
        }

        $directory = __DIR__.'/'.'sitemaps/';
        $new_name = $directory . time() . "_" . basename($xml_file['name']);
        if (!file_exists($directory)) {
            mkdir($directory);
        }

        if (!move_uploaded_file($xml_file['tmp_name'], $new_name)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        $sitemapParser = new SitemapXMLParser($new_name);
        $parsedXML = $sitemapParser->parseData();
        $parseResult = $this->sitemapManager->importSitemap($parsedXML, $user->getLogin());

        if ($parseResult['page_total'] || $parseResult['website_total']) {
            $_SESSION['flash'] = 'Import finished. Imported '.$parseResult['page_total'].' pages from '.$parseResult['website_total'].' websites';
        } else {
            $_SESSION['flash'] = 'No new content to import';
        }

        return header('Location: /');
    }
}
