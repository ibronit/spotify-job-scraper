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
     */
	public function getUrls(): array
	{
	    try {
            $response = $this->httpClient->request(
                'POST',
                'https://www.spotifyjobs.com/wp-admin/admin-ajax.php',
                [
                    'body' => [
                        'action' => 'get_jobs',
                        'pageNr' => 1, // TODO: there can be more pages
                        'perPage' => 100,
                        'locations' => ['sweden'],
                    ]
                ]
            );

            // TODO: validate values
            $responseArray = $response->toArray();
        } catch (TransportExceptionInterface | HttpExceptionInterface | DecodingExceptionInterface $e) {
            // TODO: cli error log
        }

		return array_map(function ($jobArray) {
			$job = new Job();
			$job->setUrl($jobArray['url']);
			$job->setTitle($jobArray['title']);

			return $job;
		}, $responseArray['data']['items']);

	}
}