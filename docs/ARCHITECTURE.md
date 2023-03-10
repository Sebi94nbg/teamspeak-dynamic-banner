# ARCHITECTURE

This documentation provides some information about the architecture of this project.


## Components

| Component  | Description  |
|---|---|
| Laravel API Routes | Defines the routes and features for HTTP requests on the API. (e.g. get current banner) |
| Laravel WEB Routes | Defines the routes and features for HTTP requests to configure the application.  |
| Laravel Schedules | Cronjobs. Tasks, which get regulary executed. (e.g. cleanup dead PIDs from the database) |
| Laravel Queue | A queue for executing tasks, whenever a worker is available. (e.g. start TeamSpeak bot) |
| POSIX PCNTL Signal Handler | More a feature. When a TeamSpeak bot process gets killed, this ensures, that it gets properly stopped. |

The following figure shows all in- and outgoing connections from view of the Laravel application:

![Architecture](/images/TeamSpeak_Dynamic_Banner_v1.0.0.png)
