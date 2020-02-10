# spotify-job-scraper


Spotify job scraper is a cli application which scrapes swedish job opportunities from Spotify's careers site.

## Requirements
##### (recommended)

* docker https://docs.docker.com/install/
* docker-compose https://docs.docker.com/compose/install/

##### or

* PHP 7.4 
* composer https://getcomposer.org/download/

## Usage

```sh
$ make build // composer install & docker-compose build
$ make up // docker-compose up and runs the scraper command
```

## Architecture


| File | Description |
| ------ | ------ |
| src/Command/SpotifyScraperCommand.php | `app:scrape-spotify-jobs` Symfony command. It will be automatically executed on docker-compose up |
| src/Service/JobCollector.php | First part of the process. It collects the jobs from an endpoint and push them into Job objects |
| src/Service/JobScraper.php | It scrapes everything from the detail of the given job which can be useful later |
| src/Service/JobDetailGuesser.php | It tries to guess some information from the scraped data |

At the end of the process the command will create a CSV file as a result. It will be found in the project root.

## Todos

* Location selector (Sweden is hardcoded)
* Improve error handling
* Improve logging
