# TeamSpeak Dynamic Banner

[![Code-Style](https://github.com/Sebi94nbg/teamspeak-dynamic-banner/actions/workflows/phpcsfixer.yml/badge.svg?branch=main)](https://github.com/Sebi94nbg/teamspeak-dynamic-banner/actions/workflows/phpcsfixer.yml?branch=main)

I first played around with building dynamic banners for TeamSpeak in 2015. After gaining some knowledge, however, it was not pushed further. In 2018 I re-tackled the topic again, as it was a project for a small hosting company, but due to unresolved technical questions it was never released. Then in 2023 I decided to revive the project and release it on Github as an OpenSource project. And here we are now.

Usually you configure a hostbanner Gfx URL on your TeamSpeak server to show your users a beautiful banner with e.g. announcements or the current time on it.

This project extends your configured banner by allowing you to configure as many images (here called templates) as you want and each of those templates can have one or more dynamic texts on it.

Just a few examples:

- Dynamically greet your TeamSpeak clients in your banner with e.g. `Hello Max`
- Dynamically add the current date and/or time to your banner
- Dynamically show on your banner the amount of supporters, which are currently online

Be a [stargazer](https://github.com/Sebi94nbg/teamspeak-dynamic-banner/stargazers), star it!


## Features

* Self-hosted / On-Premise
    * You have the full control about the application
    * Nobody can tell you, that you are not allowed to use it
    * It's OpenSource, so free-of-charge
* Supported instances: TeamSpeak 3 server, TeamSpeak 5 server
* Add one or more instances as data source
    * ServerQuery credentials are stored encrypted in the database
    * Supports the ServerQuery protocol RAW and SSH
    * Define the client nickname
    * Define the default channel, where the client should be in when connected
* Upload one or more images as templates
* Define one or more banners (each gets a dedicated link)
    * Supports various rotation configurations
        * Non-Random: Every client, which requests the banner, will always see the same template as all other clients.
        * Random: Every client, which requests the banner, gets a random, maybe different template shown.
* Add and configure one or more templates for each banner
    * Over 140 standard variables (e.g. current time, count of online clients, client nickname, etc.)
    * Set the font size for your texts
    * Set the font color (RGB) for your texts
    * Optionally disable a specific template in a banner configuration if you don't want to delete it's configuration and is only necessary temporary for announcements or events for example
* The ServerQuery client stays 24x7 connected on your TeamSpeak server
    * This avoids annoying connects / disconnects to get all necessary data every time
    * The required data gets fetched event based (e.g. client joins the server) and partitially regulary (e.g. every 5 minutes)
    * All fetched data will be stored in the Redis to speed up the entire application


## Requirements

* Web server (e.g. apache, nginx)
* PHP 8.1 or 8.2 with the following extensions:
    * see [Laravel PHP requirements](https://laravel.com/docs/10.x/deployment#server-requirements)
    * ssh2 (if you want to connect your instance via SSH)
    * gd
* Database (see [supported databases by Laravel](https://laravel.com/docs/10.x/database#introduction))
* [Redis](https://redis.io/)
* Git
* [Composer](https://getcomposer.org/)
* [npm](https://www.npmjs.com/)


## TeamSpeak Permissions

This project requires the following permissions on your TeamSpeak server:

| Permission Name | Description |
|-----------------|-------------|
| `b_serverquery_login` | Allow the ServerQuery to login |
| `b_virtualserver_channel_list` | Get the channel list to be able to configure a default channel for the bot. |
| `b_channel_join_*` | The respective permission to allow the bot to join the configured default channel. |
| `i_channel_join_power` | The join power, which the bot should have. |
| `i_client_move_power` | The move power, which the bot should have to move itself into the default channel. |
| `b_virtualserver_notify_register` | Allow the bot to register and listen for TeamSpeak server events. |
| `b_virtualserver_info_view` | Allow the bot to get some information. |
| `b_virtualserver_connectioninfo_view` | Allow the bot to get some information. |
| `b_virtualserver_client_list` | Allow the bot to get some information. |
| `b_virtualserver_client_dbinfo` | Allow the bot to get some information. |
| `b_virtualserver_servergroup_list` | Allow the bot to get some information. |
| `b_virtualserver_servergroup_client_list` | Allow the bot to get some information. |


## Supported Languages

The application UI is currently available in the following languages:

* English


## Architecture

[Open Architecture README](/docs/ARCHITECTURE.md)


## Installation

[Open Installation README](/docs/INSTALLATION.md)


## Update

[Open Update README](/docs/UPDATE.md)


## Backup

If you want to regulary backup this application, you should backup the following things:

* `.env` file (or only the `APP_KEY` from this file)
* Database
* `laravel/public/uploads/` directory


## Feature Request? Bug Report?

Open a Github issue [here](https://github.com/Sebi94nbg/teamspeak-dynamic-banner/issues/new)!


## Contribute

[Open Contribute README](/docs/CONTRIBUTE.md)
