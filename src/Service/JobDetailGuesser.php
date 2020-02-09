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

    private const EXPERT_KEYWORDS = [
        'expert',
        'experienced',
        'experience',
        'skilled',
        'professional',
        'senior',
        'tech lead',
        'chief',
        'superior',
    ];

    private const YEARS_OF_EXPERIENCE_KEYWORDS = [
        1 => "one",
        2 => "two",
        3 => "three",
        4 => "four",
        5 => "five",
        6 => "six",
        7 => "seven",
        8 => "eight",
        9 => "nine",
        10 => "ten",
    ];

    /**
     * @param Job $job
     * @return Job
     */
    public function guessJobDetails(Job $job): Job
    {
        /** @var string|null $level */
        $level = $this->guessLevel($job);
        /** @var int|null $yearsOfExperience */
        $yearsOfExperience = $this->guessYearsOfExperience($job, $level);

        if (
            !$level
            && $yearsOfExperience !== null
            && $yearsOfExperience <= Job::YEARS_OF_EXPERIENCE_JUNIOR
        ) {
            $level = Job::LEVEL_JUNIOR;
        }

        // If there is no result for the level, I assume the job is for experts
        $job->setLevel($level ?: Job::LEVEL_EXPERT);
        $job->setYearsOfExperience($yearsOfExperience);

        return $job;
    }

    /**
     * @param Job $job
     * @return string|null
     */
    private function guessLevel(Job $job): ?string
    {
        $isJunior = $this->isLevelMatching($job, self::JUNIOR_KEYWORDS);
        if ($isJunior) {
            return Job::LEVEL_JUNIOR;
        }

        return $this->isLevelMatching($job, self::EXPERT_KEYWORDS) ? job::LEVEL_EXPERT : null;
    }

    /**
     * @param Job $job
     * @param string|null $level
     * @return int|null
     */
    private function guessYearsOfExperience(Job $job, string $level = null): ?int
    {
        foreach (self::YEARS_OF_EXPERIENCE_KEYWORDS as $intValue => $stringValue) {
            $pattern = sprintf("/\b(\d+|%s) years?\b/i", $stringValue);
            if (preg_match($pattern, $job->getDescription(), $matches)) {
                $job->setFoundYearsOfExperienceInText(true);
                return $this->filterOutIntValue(array_shift($matches)) ?: $intValue;
            }
        }

        if ($level === Job::LEVEL_JUNIOR) {
            return Job::YEARS_OF_EXPERIENCE_JUNIOR;
        }

        if ($level === Job::LEVEL_EXPERT) {
            return Job::YEARS_OF_EXPERIENCE_EXPERT;
        }

        return null;
    }

    /**
     * @param $str
     * @return int|null
     */
    private function filterOutIntValue($str): ?int {
        preg_match('!\d+!', $str, $matches);

        return $matches ? (int) array_shift($matches) : null;
    }

    /**
     * @param Job $job
     * @param array $keywords
     * @return bool
     */
    private function isLevelMatching(Job $job, array $keywords): bool
    {
        $matchesInDescription = 0;

        foreach ($keywords as $keyword) {
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
                return true;
            }
        }

        return false;
    }
}