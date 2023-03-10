# INSTALLATION

This documentation describes, how you can install this project.


## Getting started

### Requirements

See the [Requirements section](/README.md#requirements) in the main README.


### Clone repository

First of all, you need to clone this project on your web server:

```shell
# install it in any directory, where you want
cd /var/www/
```

```shell
git clone https://github.com/Sebi94nbg/teamspeak-dynamic-banner.git
```


### Checkout a specific version

Next, checkout a specific version of this project.

Switch into the project directory:

```shell
cd teamspeak-dynamic-banner
```

Checkout the latest version:

```shell
git checkout $(git tag | tail -1)
```


### Install dependencies

Install the dependencies:

```shell
cd laravel/
```

```shell
npm install
```

```shell
npm run build
```

```shell
composer install --optimize-autoloader --no-dev
```


### Configure and prepare the application

Generate an unique application key:

```shell
php artisan key:generate
```

Edit the `.env` file to your needs:

* `APP_NAME`: The name of this application (only shown in the browsers tab)
* `APP_ENV`: Based on the environment, some implementations may work different (e.g. more or less logging, more or less schedules, ...)
    * `local`: For local development using the here provided Docker setup use only
    * `staging`: For testing/staging use only
    * `production`: For production use only
* `APP_KEY`: This should be set automatically by the `composer install` command.
    * Backup this key. It's used for encrypting and decrypting secrets in the database.
    * NEVER ever share this with anyone else!
* `APP_DEBUG`: Should be `false` in testing/staging and production as it reveals sensitive data such as passwords.
* `APP_URL`: The base URL of your application (mainly for setting the correct URL in e.g. password reset emails).
* `DB_*`: Database connection settings
* `REDIS_*`: Redis connection settings
* `MAIL_*`: SMPT settings for sending emails (e.g. password reset email)

Cache and optimize the application:

```shell
php artisan optimize
```

```shell
php artisan view:cache
```

Install the database schemas:

```shell
php artisan migrate
```

Grant the web server user (e.g. `www-data`) the necessary permissions:

```shell
chown www-data:www-data -R teamspeak-dynamic-banner/
```


### Setup Scheduling

For the scheduling, you need to set up a regular cronjob, which runs every minute. The application itself takes care about how often which task should run.

1. Create a new cron file (e.g. `/etc/cron.d/teamspeak-dynamic-banner`)
2. Insert the following configuration:

    ```shell
    * * * * * www-data cd /var/www/teamspeak-dynamic-banner/laravel/ && /usr/bin/php artisan schedule:run >> storage/logs/scheduler.log 2>&1
    ```

3. Adjust the user and directory to your requirements
4. Save the file

You can verify, if the scheduling works as expected when it automatically creates the `scheduler.log` with some output.


### Setup Queue

For the queue, you probably want to set up Supervisor, which automatically monitors your queue processes and restarts them, if necessary.

1. Install the respective Supervisor package: `sudo apt-get install supervisor`
2. Create a new Supervisor config file (e.g. `/etc/supervisor/conf.d/teamspeak-dynamic-banner.conf`) with the following content:

    ```shell
    [program:teamspeak-dynamic-banner-worker]
    process_name=%(program_name)s_%(process_num)02d
    directory=/var/www/teamspeak-dynamic-banner/laravel
    command=/usr/bin/php artisan queue:work --queue default --timeout 10 --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    user=www-data
    numprocs=8
    redirect_stderr=true
    stdout_logfile=/var/www/teamspeak-dynamic-banner/laravel/storage/logs/worker.log
    stopwaitsecs=3600
    ```

3. Save the file
4. Reread the Supervisor config: `sudo supervisorctl reread`
5. Update the Supervisor config: `sudo supervisorctl update`
6. Start the workers: `sudo supervisorctl start teamspeak-dynamic-banner-worker:*`
7. Ensure, that the workers are running: `supervisorctl status teamspeak-dynamic-banner-worker:*`

You can verify, if it's working as expected, when you later upload your first templates. Those get processed by the queue:

```shell
$ cat /var/www/teamspeak-dynamic-banner/laravel/storage/logs/worker.log
  [...]
  2023-03-13 20:50:34 App\Jobs\DrawGridSystemOnTemplate .............. RUNNING
  2023-03-13 20:50:34 App\Jobs\DrawGridSystemOnTemplate .............. RUNNING
  2023-03-13 20:50:34 App\Jobs\DrawGridSystemOnTemplate .............. RUNNING
  2023-03-13 20:50:34 App\Jobs\DrawGridSystemOnTemplate .............. RUNNING
  2023-03-13 20:50:34 App\Jobs\DrawGridSystemOnTemplate ........ 251.57ms DONE
  [...]
```

### Setup web server virtual host

Setup a virtual host for this web application and point the root directory to the project `laravel/public` directory.

In this documentation, it would be for example `/var/www/teamspeak-dynamic-banner/laravel/public`.

After enabling this virtual host, the web application should be available at the specified domain and show you the install wizard.


### First Steps

After you have completed the install wizard, you will see the dashboard of the application.

Your next steps should be the following:

1. Add and configure an instance
2. Start the newly created instance
3. Upload one or more templates
4. Add a new banner configuration
5. Add one or more recently uploaded templates to the banner configuration
6. Configure your templates for your banner with some texts
7. Configure the banner API URL in your TeamSpeak server as Hostbanner Gfx URL
8. Enjoy your dynamic banner :-)
