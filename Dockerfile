FROM php:7.4-cli

COPY . /usr/src/spotify-job-scraper
WORKDIR /usr/src/spotify-job-scraper

CMD ["php", "bin/console", "app:scrape-spotify-jobs"]