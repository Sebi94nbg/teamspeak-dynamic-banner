# UPDATE

This documentation describes, how you can update an existing installation of this project.


## Read release notes

First of all, you should read the release notes and understand them. You can find those here: https://github.com/Sebi94nbg/teamspeak-dynamic-banner/releases

There might be some breaking changes, which will affect your installation. Please check the release notes and do the necessary changes, if required.


## Actual Update

Switch into the projects installation directory:

```shell
cd /var/www/teamspeak-dynamic-banner/laravel/
```

Fetch the latest Git tags:

```shell
git fetch --tags
```

Check on which version you are currently at (marked with a star in front of the line):

```shell
git branch
```

Checkout the latest version:

```shell
git checkout $(git tag | tail -1)
```

Confirm, that you are on the respective new version (marked with a star in front of the line):

```shell
git branch
```

If necessary, apply the necessary changes for the breaking changes from the release notes.

Ensure, you have installed the required dependencies:

```shell
npm install
```

```shell
npm run build
```

```shell
composer install --optimize-autoloader --no-dev
```

Cache and optimize the application:

```shell
php artisan optimize
```

```shell
php artisan view:cache
```

Ensure, you have installed the required database schemas:

```shell
php artisan migrate
```

Grant the web server user (e.g. `www-data`) the necessary permissions:

```shell
chown www-data:www-data -R teamspeak-dynamic-banner/
```

Next, restart all your queue workers, so that they pick up the new code:

```shell
sudo supervisorctl restart teamspeak-dynamic-banner-worker:*
```

As last step, you should also restart all your currently running instances, so that they can pick up the latest code.

Afterwards, your application has been successfully updated.
