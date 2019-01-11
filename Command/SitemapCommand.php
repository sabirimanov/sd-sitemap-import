<?php

namespace SnowDogSitemap\SitemapImporter\Command;

use SnowDogSitemap\SitemapImporter\Component\SitemapXMLParser;
use SnowDogSitemap\SitemapImporter\Model\SitemapManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

class SitemapCommand
{
    private $sitemapManager;
    private $helper;

    public function __construct(SitemapManager $sitemapManager, QuestionHelper $helper)
    {
        $this->sitemapManager = $sitemapManager;
        $this->helper = $helper;
    }

    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        $prompt = new Question('Enter path to XML file: ');
        $input_file = $this->helper->ask($input, $output, $prompt);
        try {
            $user = $input->getArgument('user');
            if (!$user) {
                $output->writeln('<error>No such user in DB</error>');
                return false;
            }
            $sitemapParser = new SitemapXMLParser($input_file);
            $parsedXML = $sitemapParser->parseData();
            $parseResult = $this->sitemapManager->importSitemap($parsedXML, $input->getArgument('user'));
            $output->writeln('Import finished. Imported '.$parseResult['page_total'].' pages from '.$parseResult['website_total'].' websites');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
