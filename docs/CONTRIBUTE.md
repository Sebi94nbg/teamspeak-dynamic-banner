# CONTRIBUTE

This documentation describes, how you can change and test things and contribute to this project.


## Requirements

* [Docker](https://www.docker.com/get-started/) (or you setup a web server with PHP by your own)
* Your favourite text editor for writing code


## Getting started

Build the application:

```shell
docker-compose build
```

Start the application:

```shell
docker-compose up -d
```

Install the dependencies and adjust the DotEnv file for the Docker setup:

```shell
cd laravel/ && npm install && npm run build && cd -
```

```shell
docker-compose exec -it backend bash
```

```shell
/tmp/prepare-docker-setup.sh
```

```shell
exit
```

The application should be now accessible via http://localhost/.

You should be also able to connect to the local TeamSpeak server test instance: [ts3server://localhost](ts3server://localhost)

Get your Server Admin token using `docker-compose logs teamspeak | grep "|token="`.


## Testing

This project uses PHPUnit for testing the application. You will find the PHPUnit tests in `laravel/tests/`.

You can run all tests by using the Laravel Artisan command:

```shell
php artisan test
```


## Contribute

1. Fork this repository
2. Create a new feature branch for your changes
3. Change the code and test it
4. Commit your changes to the feature branch
5. Create a pull request

If you need to run any `artisan` command, run it for example on the `backend` container:

```shell
docker-compose exec backend php artisan <command>
```
