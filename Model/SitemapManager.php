<?php

namespace SnowDogSitemap\SitemapImporter\Model;

use Snowdog\DevTest\Model\Page;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;

class SitemapManager
{
    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager, PageManager $pageManager, WebsiteManager $websiteManager)
    {
        $this->pageManager = $pageManager;
        $this->websiteManager = $websiteManager;
        $this->userManager = $userManager;
    }

    /**
     * @param array $siteMapArray
     * @param $login
     * @return array
     * @throws \Exception
     */
    public function importSitemap(array $siteMapArray, $login)
    {
        if (empty($siteMapArray)) {
            throw new \Exception('Invalid file');
        }

        $totalPages = 0;
        $totalWebsites = 0;
        $user = $this->userManager->getByLogin($login);

        if (!$user instanceof User) {
            throw new \Exception('User does not exists');
        }

        foreach ($siteMapArray as $url => $pages) {
            $website = $this->websiteManager->getWebsiteByHostname($url);

            if (!$website instanceof Website) {
                $websiteId = $this->websiteManager->create($user, $url, $url);
                $website = $this->websiteManager->getById($websiteId);
                $totalWebsites++;
            }

            foreach ($pages as $pageImport) {
                $page = $this->pageManager->getPageByURL($pageImport);
                if (!$page instanceof Page) {
                    $this->pageManager->create($website, $pageImport);
                    $totalPages++;
                }
            }
        }

        return [
            'page_total' => $totalPages,
            'website_total' => $totalWebsites
        ];
    }
}
