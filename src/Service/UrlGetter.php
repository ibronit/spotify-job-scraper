<?php

namespace App\Service;

use App\Entity\Job;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlGetter
{
	/**
	 * @var HttpClientInterface
	 */
	private $httpClient;

	/**
	 * UrlGetter constructor.
	 *
	 * @param HttpClientInterface $httpClient
	 */
	public function __construct(HttpClientInterface $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	/**
	 * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	public function getUrls(): array
	{
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

		return array_map(function ($jobArray) {
			$job = new Job();
			$job->setUrl($jobArray['url']);
			$job->setTitle($jobArray['title']);

			return $job;
		}, $response->toArray()['data']['items']);

	}
}