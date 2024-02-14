# Deathgarden Rebirth Rewrite
Rewrite of the [Deathgarden rebirth](https://github.com/wolfswolke/DeathGarden_API_Rebirth) project for better maintainability and (hopefully) less bugs.

This project is written in *PHP* with Laravel.

## How to start developing
You don't need much to start developing since you can use laravel sail do quickly get a local dev environment running.

### Prerequisites

- Docker
- Docker-compose
- Linux wsl (Windows)
- Laravel Knowledge

### Step 1 - Clone the Repository

### Step 2 - Install Composer dependencies
Move into the ./dist folder and install the composer dependencies.<br>
If its your first time setting up the project and you dont have composer installed, you can use this command to install them:
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### Step 3 - Setup .env

Copy and rename the `.env.example` file to .env and fill out your Environment variables

### Step 4 - Starting Sail
Now you can start the sail container with
```shell
./vendor/bin/sail up -d
```
or if you have the sail alias configured 
```shell
sail up -d
```

### Step 5 - Setup Database
After starting sail you can run the database migrations with 
```shell
sail artisan migrate
```

### Step 6 - Installing JS Dependencias and Compile
First you can install the JS dependencies with
```shell
sail npm install
```

and build the JS with
```shell
# for building files a single time
sail npm run build
# for active developing with vite
sail npm run dev
```

