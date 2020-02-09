<?php

namespace App\Service;

use App\Entity\Job;
use Goutte\Client;

class JobScraper
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param Job $job
     * @return Job
     */
    public function scrapeJob(Job $job): Job
    {
        $crawler = $this->client->request('GET', $job->getUrl());

        $jobCategory = $crawler->filter('html div.single-post--meta')->html();
        $jobDescription = $crawler->filter('html div.column-inner')->html();

        $job->setCategory(trim(strip_tags($jobCategory)));
        $job->setDescription(trim(strip_tags($jobDescription)));

        return $job;
    }
}