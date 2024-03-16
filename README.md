[![codecov](https://codecov.io/gh/IronSinew/recipebox/graph/badge.svg?token=IJC5HGMP0W)](https://codecov.io/gh/IronSinew/recipebox)
# Recipebox

## Overview
Recipebox is a [Laravel Jetstream](https://jetstream.laravel.com)-based site using Vue and InertiaJS. It's also built with:
- Docker
- Caddy
- PostgreSQL
- Laravel Scout

## Installation Prerequisites
- Docker Desktop
- [Bun](https://bun.sh)
- **If using Windows:**
    - You will need to set up [WSL2](https://docs.microsoft.com/en-us/windows/wsl/install) (Ubuntu is known to work) and configure Docker Desktop to use it as a [backend](https://docs.docker.com/desktop/windows/wsl/).
    - All of the following command line commands should be run inside your WSL2 / shell.

## Get the Code and Configure Git
Get the code.  
`git clone git@github.com:IronSinew/recipebox.git`  
`cd recipebox`

Configure Git options for the local repo.  
`git config core.fileMode false`  
`git config core.autocrlf input`  
`git config core.eol lf`

## Installation
`cp provision/caddy/Caddyfile.example provision/caddy/Caddyfile`

It is highly likely that `.env` will not be present, but rather, `.env.example` instead. To fix this, run:  
`cp .env.example .env`  

`docker-compose up -d`  
`docker-compose exec php composer install` 

`docker-compose exec php php artisan key:generate`  
`bun install`

Confirm the `recipebox` schema was created during PostgreSQL container creation, otherwise, create the schema.  
`docker-compose exec php php artisan migrate` 

If migration fails 
`docker-compose exec pgsql createdb -U root recipebox`  

Also, create the testing schema
`docker-compose exec pgsql createdb -U root recipebox_test`

Configure folder permissions.  
`sudo chmod -R 777 storage/` 

See if it works!

`bun run dev`

You should now be able to view the site at <http://recipebox.localhost>.

---

## Testing emails
Access mailhog locally at <http://localhost:1025>

## Stop Application
`docker-compose down`

## Start Application (after install)
`docker-compose up -d`  
`bun run dev`
- It may not be an awful idea to check migration / npm / composer dependencies in case other installs are needed as well.

---

## Extras
You may want to add these aliases to your `.bashrc` or `.bash_aliases` file for working with Docker easier. Use these commands at the root of the project.

    alias dockup="docker-compose up -d"
    alias dockdown="docker-compose down"
    dockin() {
        docker exec -it "$1" /bin/bash
    }

### Start application:
`dockup`

### Stop application:
`dockdown`

### Access Containers Directly
`dockin <container_name>`  
Example:  
`dockin recipebox-php-1`  

### Xdebug

Xdebug is available in the PHP container but is turned off by default. To turn it on, set `XDEBUG_MODE` (`XDEBUG_MODE=develop` is Xdebug's default, other options are [documented](https://xdebug.org/docs/all_settings#mode)) in `.env` and restart PHP via docker-compose:

```
docker-compose up -d php
```

# Roadmap
### Admin section
- [ ] Label Manager
- [ ] Category Manager
- [ ] Recipe Manager
- [ ] Image uploads
### Other
- [ ] Customization of intro blurb
- [ ] Recent recipes component
### Possible future features
- [ ] "I made it" counter
- [ ] Reviews/ratings
- [ ] Roles for access to the recipe manager as a contributor
