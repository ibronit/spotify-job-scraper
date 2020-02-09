<?php

namespace App\Service;

use App\Entity\Job;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JobCollector
{
	/**
	 * @var HttpClientInterface
	 */
	private $httpClient;

	/**
	 * JobCollector constructor.
	 *
	 * @param HttpClientInterface $httpClient
	 */
	public function __construct(HttpClientInterface $httpClient)
	{
		$this->httpClient = $httpClient;
	}

    /**
     * @return array
     * @throws DecodingExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
	public function getJobs(): array
	{
	    $jobs = [];

	    foreach ($this->getJobRequestGenerator() as $response) {
            $remappedJobs = array_map(function ($jobArray) {
                $job = new Job();
                $job->setUrl($jobArray['url']);
                $job->setTitle($jobArray['title']);

                return $job;
            }, $response);

            $jobs = array_merge($jobs, $remappedJobs);
        }

	    return $jobs;
	}

    /**
     * @return \Generator
     * @throws DecodingExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
	private function getJobRequestGenerator()
    {
        $found = 99;
        $perPage = 99;
        $pageNr = 1;

        while ($found === $perPage) {
            $response = $this->httpClient->request(
                'POST',
                'https://www.spotifyjobs.com/wp-admin/admin-ajax.php',
                [
                    'body' => [
                        'action' => 'get_jobs',
                        'pageNr' => $pageNr,
                        'perPage' => $perPage,
                        'locations' => ['sweden'],
                    ]
                ]
            );
            $responseArray = $response->toArray()['data']['items'];
            $found = count($responseArray);
            $pageNr++;

            yield $responseArray;
        }
    }
}