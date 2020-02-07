<?php

namespace App\Command;

use App\Service\UrlGetter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpotifyScraperCommand extends Command
{
	protected static $defaultName = 'app:scrape-spotify-jobs';

	/**
	 * @var UrlGetter
	 */
	private $urlGetter;

	/**
	 * SpotifyScraperCommand constructor.
	 *
	 * @param UrlGetter $urlGetter
	 */
	public function __construct(UrlGetter $urlGetter)
	{
		$this->urlGetter = $urlGetter;

		parent::__construct();
	}

	protected function configure()
	{
		$this
			->setDescription('Collects job information from spotify')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$section1 = $output->section();
		$progress1 = new ProgressBar($section1);
		$progress1->start(100);

		$jobs = $this->urlGetter->getUrls();

		$progress1->finish();
		$section2 = $output->section();
		$progress2 = new ProgressBar($section2);
		$progress2->start(100);

		foreach($jobs as $job) {
			$progress2->advance(100 / count($jobs));
		}
		$progress2->finish();

		return 0;
	}
}