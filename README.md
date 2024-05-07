# Sonar KML stuff

Processing KMLs made by SonarTRX.

## Installation

Clone the repo and let composer prepare the autoloader.

```sh
git clone https://github.com/tontonsb/sonar.git
cd sonar
composer dump-autoload
```

## Usage

In dir with the SonarTRX export directories:

```sh
php /path/to/prepkml.php
```

or with a variable exports' directory

```sh
php prepkml.php ../exports
```
