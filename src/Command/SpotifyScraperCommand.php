<?php

namespace App\Command;

use App\Entity\Job;
use App\Service\JobDetailGuesser;
use App\Service\JobScraper;
use App\Service\JobCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpotifyScraperCommand extends Command
{
	protected static $defaultName = 'app:scrape-spotify-jobs';

	/**
	 * @var JobCollector
	 */
	private $jobCollector;
    /**
     * @var JobScraper
     */
    private $jobScraper;
    /**
     * @var JobDetailGuesser
     */
    private $jobDetailGuesser;

    /**
     * SpotifyScraperCommand constructor.
     *
     * @param JobCollector $jobCollector
     * @param JobScraper $jobScraper
     * @param JobDetailGuesser $jobDetailGuesser
     */
	public function __construct(JobCollector $jobCollector, JobScraper $jobScraper, JobDetailGuesser $jobDetailGuesser)
	{
		$this->jobCollector = $jobCollector;
        $this->jobScraper = $jobScraper;
        $this->jobDetailGuesser = $jobDetailGuesser;

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
        $jobs = $this->getJobs($output);

        $this->scrapeJobs($jobs, $output);


		return 0;
	}

    /**
     * @param OutputInterface $output
     * @return array
     */
	private function getJobs(OutputInterface $output): array
    {
        $section1 = $output->section();
        $progress1 = new ProgressBar($section1);
        $progress1->start(100);

        $jobs = $this->jobCollector->getUrls();

        $progress1->finish();

        return $jobs;
    }

    /**
     * @param array $jobs
     * @param OutputInterface $output
     */
    private function scrapeJobs(array $jobs, OutputInterface $output)
    {
        $section2 = $output->section();
        $progress2 = new ProgressBar($section2);
        $progress2->start(100);

        /** @var Job $job */
        foreach($jobs as $job) {
            $this->jobScraper->scrapeJob($job);
            $this->jobDetailGuesser->guessJobDetails($job);

            $progress2->advance(100 / count($jobs));
        }

        $progress2->finish();
    }
}