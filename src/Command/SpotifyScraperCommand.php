<?php

namespace App\Command;

use App\Entity\Job;
use App\Service\JobDetailGuesser;
use App\Service\JobScraper;
use App\Service\JobCollector;
use League\Csv\Writer;
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
	private JobCollector $jobCollector;
    /**
     * @var JobScraper
     */
    private JobScraper $jobScraper;
    /**
     * @var JobDetailGuesser
     */
    private JobDetailGuesser $jobDetailGuesser;

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $jobs = $this->getJobs($output);

        $this->scrapeJobs($jobs, $output);

        $this->generateCSV($jobs, $output);

		return 0;
	}

    /**
     * @param OutputInterface $output
     * @return array
     */
	private function getJobs(OutputInterface $output): array
    {
        $section = $output->section();
        $progress = new ProgressBar($section);
        $progress->setFormat('<comment>Collecting jobs...</comment> [%bar%] %percent%%');
        $progress->start(100);

        $jobs = $this->jobCollector->getJobs();

        $progress->finish();
        $output->writeln('');
        $output->writeln('<info>Done!</info>');

        return $jobs;
    }

    /**
     * @param array $jobs
     * @param OutputInterface $output
     */
    private function scrapeJobs(array $jobs, OutputInterface $output)
    {
        $section = $output->section();
        $progress = new ProgressBar($section);
        $progress->setFormat('<comment>Scraping and processing jobs...</comment> [%bar%] %percent%%');
        $progress->start(count($jobs));

        /** @var Job $job */
        foreach($jobs as $job) {
            $this->jobScraper->scrapeJob($job);
            $this->jobDetailGuesser->guessJobDetails($job);

            $progress->advance(1);
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln('<info>Done!</info>');
    }

    /**
     * @param array $jobs
     * @param OutputInterface $output
     * @throws \League\Csv\CannotInsertRecord
     */
    private function generateCSV(array $jobs, OutputInterface $output)
    {
        $section = $output->section();
        $progress = new ProgressBar($section);
        $progress->setFormat('<comment>Generating CSV...</comment> [%bar%] %percent%%');
        $progress->start(100);

        $fileName = sprintf('spotify-scraped-jobs-%s.csv', date('Ymdhis'));
        $writer = Writer::createFromPath($fileName, 'w+');
        $writer->insertOne(['Title', 'URL', 'Description', 'Level', 'Years of experience']);

        /** @var Job $job */
        foreach ($jobs as $job) {
            $writer->insertOne([
                $job->getTitle(),
                $job->getUrl(),
                $job->getDescription(),
                $job->getLevel(),
                $job->getFormattedYearsOfExperience(),
            ]);
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln(sprintf('<info>Done! Now you can open your csv: %s</info>', $fileName));
    }
}