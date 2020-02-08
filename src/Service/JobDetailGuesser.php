<?php

namespace App\Service;

use App\Entity\Job;

class JobDetailGuesser
{
    private const JUNIOR_KEYWORDS = [
        'student',
        'intern',
        'internship',
        'mentorship',
        'apprenticeship',
        'junior',
        'traineeship',
        'trainee',
        'boot camp',
    ];

    /**
     * @param Job $job
     * @return Job
     */
    public function guessJobDetails(Job $job): Job
    {
        $this->guessIfJunior($job);
        // If there is no sign about the job is for juniors, I assume it's for experts
        if (!$job->getLevel()) {
            $job->setLevel(Job::LEVEL_EXPERT);
        }

        return $job;
    }

    /**
     * @param Job $job
     * @return Job
     */
    private function guessIfJunior(Job $job): Job
    {
        $matchesInDescription = 0;

        foreach (self::JUNIOR_KEYWORDS as $keyword) {
            $pattern = sprintf("/\b%ss?\b/i", $keyword);

            $matchesInTitle = preg_match_all($pattern, $job->getTitle());
            $matchesInCategory = preg_match_all($pattern, $job->getCategory());
            $matchesInDescription += preg_match_all($pattern, $job->getDescription());

            // At least two matches need in description, because maybe one of the roles is mentoring juniors and etc.
            if (
                $matchesInTitle > 0
                || $matchesInCategory > 0
                || $matchesInDescription >= 2
            ) {
                $job->setLevel(Job::LEVEL_JUNIOR);
                break;
            }
        }

        return $job;
    }
}