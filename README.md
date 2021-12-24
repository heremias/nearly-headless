# Standard Sites

This is a distribution for Pantheon containing our "standard site" distribution which
is released via a [built upstream here](https://code.osu.edu/umarketing/standard-built).

## Requirements

To run locally:

* [MySQL](https://mariadb.com/kb/en/library/installing-mariadb-on-macos-using-homebrew/)
* PHP 7+
* Composer
* NVM and node 8


## Quick Start

```
# Clone the project
git clone git@code.osu.edu:umarketing/standard.git
cd standard

# Install composer dependencies
# This will also install theme dependencies.
# It will take a loooong time.
composer install

# Build theme.
robo theme:build
```

## Robo
We use [robo](https://robo.li/) for most CLI tooling.
You can run robo commands by invoking `vendor/bin/robo` 

```
$ robo
Robo 1.4.13

Usage:
  command [options] [arguments]

Available commands:
  drush                        Run a drush command from the project root on the web folder
  help                         Displays help for a command
  list                         Lists commands
 local
  local:cache                  Creates a local cache of a pantheon environment's database and rsync'd site files in /tmp/standard.
  local:install
  local:release                Create a build artifact and deploy it to standard-built.
  local:replace                Replaces your local dev site's files and database with versions from pantheon or a local cache.
  local:start                  Starts a local server using drush.
  local:user-create            Creates a developer, manager, and editor to aid in local development.
 pantheon
  pantheon:backup              Backup specified sites, one at a time. This can be slow.
  pantheon:create              Creates a new site on the distribution on pantheon.
  pantheon:release             Releases pending changes on the following sites.
  pantheon:secrets-initialize  Sets the secrets needed for saml authentication
  pantheon:secrets-set         Set a secret for Pantheon site. Use flag -s to provide comma seperated list of sites or omit for all.
  pantheon:sites               Returns a list of urls for all sites on the distribution.
  pantheon:stage               Stages a deployment to all sites test environments
  pantheon:updates             Checks for upstream updates.
 project
  project:audit
  project:enable
  project:uninstall
 setup
  setup:theme                  Pulls down the latest theme git repo for development.
 site
  site:usage
 theme
  theme:build
```

## Deployments

This is a "source" repository. We have a separate repo that contains a built pantheon upstream
with node_modules, vendor, etc.

To push a release into this upstream, run the local:release command. This will take 5-10 minutes.

```
robo local:release
``` 

Once a release has been pushed to the Pantheon upstream, you may then deploy it 
with the pantheon:stage and pantheon:release commands.

```
# Deploy changes to dev, test and replicate live's content in test.
robo pantheon:stage examplesite-osu "added widget"

# Releases changes already staged to the site's production environment.
robo pantheon:release examplesite-osu "added widget"
```

These commands also sort batch operations.

```
# Deploy changes to dev, test and replicate live's content in test 
# for each of these sites, deploying 2 in parallel.
robo pantheon:stage site1-osu,site2-osu,site3-osu,site4-osu "added widget" 2

# Deploy changes to dev, test and replicate live's content in test 
# for every site in standard, deploying 5 in parallel.
robo pantheon:stage @all "added widget" 5

# Releases changes already staged to the site's production environment
# for each of these sites, deploying 2 in parallel.
robo pantheon:release site1-osu,site2-osu,site3-osu,site4-osu "added widget" 2

# Releases changes already staged to the site's production environment
# for every site in standard, deploying 5 in parallel.
robo pantheon:release @all "added widget" 5
```


# Drush Locally

As noted in some of the instructions above, you can run drush on your local site
from anywhere within the 'web folder' after you have install drupal (or to install
drupal).

```
cd web
../vendor/bin/drush help
../vendor/bin/drush user-login
```

# Pantheon & Terminus

To do things on pantheon, you need to [install terminus](https://pantheon.io/docs/terminus/install/)
and [authenticate it](https://pantheon.io/docs/terminus/install/#authenticate).

## Drush on Pantheon

You can run drush commands on pantheon either with Drush and aliases or with Terminus.
It should be noted that some commands like php-eval are not supported. In addition, I've
noticed that some parameters like config-get's '--include-overridden' may not work.

https://pantheon.io/docs/drush/

### Drush w\Terminus

You can also run drush commands via terminus like so.

```
terminus drush SITENAME.ENV -- sql-query "SELECT * FROM users WHERE uid=1"
terminus drush SITENAME.ENV -- core-cli
```
