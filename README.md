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

### Start application:
`docker-compose up -d`

### Stop application:
`docker-compose down`

### Xdebug

Xdebug is available in the PHP container but is turned off by default. To turn it on, set `XDEBUG_MODE` (`XDEBUG_MODE=develop` as Xdebug's default, other options are [documented](https://xdebug.org/docs/all_settings#mode)) in `.env` and restart PHP via docker-compose:

```
docker-compose up -d php
```

# Roadmap
### Admin section
- [x] Label Manager
- [x] Category Manager
- [x] Recipe Manager
- [x] Image uploads
- [ ] Paginate Recipe Manager
- [x] User Management
### Other
- [ ] Customizable intro blurb and logo
- [x] Recent recipes on homepage
- [x] Infinite scroll for recipe lists
### Possible future features
- [ ] "I made it" counter
- [ ] Reviews/ratings
- [x] Roles for access to the recipe manager as a contributor
- [ ] Associate images to labels/categories (?)
- [x] [Recipe schema markup](https://developers.google.com/search/docs/appearance/structured-data/recipe)
