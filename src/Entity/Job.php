<?php

namespace App\Entity;

class Job
{
    const LEVEL_JUNIOR = 'junior';
    const LEVEL_EXPERT = 'expert';

    const YEARS_OF_EXPERIENCE_JUNIOR = 2;
    const YEARS_OF_EXPERIENCE_EXPERT = 3;

	/** @var string */
	private string $title = '';

	/** @var string */
	private string $url = '';

	/** @var string  */
	private string $category = '';

	/** @var string */
	private string $description = '';

	/** @var string */
	private string $level = '';

	/** @var int|null */
	private ?int $yearsOfExperience = null;

	/** @var bool */
	private bool $foundYearsOfExperienceInText = false;

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param string $level
     */
    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    /**
     * @return int|null
     */
    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    /**
     * @param int|null $yearsOfExperience
     */
    public function setYearsOfExperience(?int $yearsOfExperience): void
    {
        $this->yearsOfExperience = $yearsOfExperience;
    }

    /**
     * @return bool
     */
    public function isFoundYearsOfExperienceInText(): bool
    {
        return $this->foundYearsOfExperienceInText;
    }

    /**
     * @param bool $foundYearsOfExperienceInText
     */
    public function setFoundYearsOfExperienceInText(bool $foundYearsOfExperienceInText): void
    {
        $this->foundYearsOfExperienceInText = $foundYearsOfExperienceInText;
    }

    /**
     * @return string
     */
    public function getFormattedYearsOfExperience(): string
    {
        if ($this->isFoundYearsOfExperienceInText()) {
            if ($this->getYearsOfExperience() === 1) {
                return sprintf('%d year', $this->getYearsOfExperience());
            } else {
                return sprintf('%d year(s)', $this->getYearsOfExperience());
            }
        }

        if ($this->getYearsOfExperience() === self::YEARS_OF_EXPERIENCE_JUNIOR) {
            return sprintf('<= %d year(s)', self::YEARS_OF_EXPERIENCE_JUNIOR);
        }

        if ($this->getYearsOfExperience() === self::YEARS_OF_EXPERIENCE_EXPERT) {
            return sprintf('>= %d years', self::YEARS_OF_EXPERIENCE_EXPERT);
        }

        return 'Cannot find out';
    }
}