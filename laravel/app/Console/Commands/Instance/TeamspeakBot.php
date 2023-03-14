<?php

namespace App\Console\Commands\Instance;

use App\Http\Controllers\Helpers\BannerVariableController;
use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use PlanetTeamSpeak\TeamSpeak3Framework\Adapter\ServerQuery;
use PlanetTeamSpeak\TeamSpeak3Framework\Adapter\ServerQuery\Event;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\Signal;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Host;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Predis\Connection\ConnectionException;

class TeamspeakBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instance:start-teamspeak-bot 
        {instance_id : The database ID of the instance.}
        {--background : Actually only switches the command output to file logging.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts a TeamSpeak bot for a specific instance.';

    /**
     * The instance model.
     */
    protected Instance $instance;

    /**
     * The instance process model.
     */
    protected InstanceProcess $instance_process;

    /**
     * The TeamSpeak virtualserver connection.
     */
    protected Server $virtualserver;

    /**
     * A helper variable for properly stopping this endless running script.
     */
    protected bool $keep_running = true;

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->virtualserver);
    }

    /**
     * Print the message or log it.
     */
    protected function message(string $log_level, string $message)
    {
        switch (strtoupper($log_level)) {
            case 'DEBUG':
                (boolval($this->option('background'))) ? Log::debug($message) : $this->warn($message);
                break;
            case 'INFO':
                (boolval($this->option('background'))) ? Log::info($message) : $this->info($message);
                break;
            case 'WARNING':
                (boolval($this->option('background'))) ? Log::warning($message) : $this->warn($message);
                break;
            case 'ERROR':
                (boolval($this->option('background'))) ? Log::error($message) : $this->error($message);
                break;
            default:
                (boolval($this->option('background'))) ? Log::debug($message) : $this->warn($message);
                break;
        }
    }

    /**
     * Callback method for 'serverqueryWaitTimeout' signals.
     *
     * @param int $seconds
     * @return void
     */
    public function onWaitTimeout(int $idle_seconds, ServerQuery $serverquery)
    {
        // Just for debugging and development purposes
        // Print (or log) every 30 seconds the current idle time of the bot connection.
        if ($idle_seconds % 30 == 0) {
            $this->message('DEBUG', "No reply from the server for $idle_seconds seconds.");
        }

        // If the timestamp on the last query is more than 300 seconds (5 minutes) in the past, send 'keepalive'
        // 'keepalive' command is just server query command 'clientupdate' which does nothing without properties. So nothing changes.
        if ($serverquery->getQueryLastTimestamp() < time() - 260) {
            $this->message('DEBUG', 'Sending keep-alive.');
            $serverquery->request('clientupdate');
        }

        // Update data every 15 seconds
        if ($idle_seconds % 15 == 0) {
            call_user_func($this->updateDatetime(...));
        }

        // Update data every minute
        if ($idle_seconds % 60 == 0) {
            call_user_func($this->updateServergroupList(...));
            call_user_func($this->updateVirtualserverInfo(...));
        }
    }

    /**
     * Callback method for 'notifyEvent' signals.
     *
     * @param int $seconds
     * @return void
     */
    public function onEvent(Event $event, Host $host)
    {
        $this->message('DEBUG', 'Received the following event: '.json_encode($event->getType()));

        // Those `client*view` events also include events for kicked and banned clients.
        if (in_array($event->getType(), ['cliententerview', 'clientleftview'])) {
            call_user_func($this->updateClientList(...));
            call_user_func($this->updateServergroupList(...));
            call_user_func($this->updateVirtualserverInfo(...));
        }
    }

    /**
     * Inserts and updates the given $data in the $redis_key for a duration of $ttl seconds.
     *
     * @param array $data
     * @param string $redis_key
     * @param int $ttl
     * @return void
     */
    protected function update_data_in_redis(array $data, string $redis_key, int $ttl = 60)
    {
        try {
            Redis::hmset($redis_key, $data);
            Redis::expire($redis_key, $ttl);
        } catch (ConnectionException) {
            // Do nothing when the Redis should not answer within the expected timeout time.
            // The next iteration will retry it.
        }
    }

    /**
     * Caches the current datetime in various formats.
     */
    public function updateDatetime()
    {
        $this->message('DEBUG', 'Caching the current datetime in various formats...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_time_data(),
            'instance_'.$this->instance->id.'_datetime',
            60 * 5 // 5 minutes
        );
    }

    /**
     * Fetches current client list from the TeamSpeak.
     */
    public function updateClientList()
    {
        $this->message('DEBUG', 'Caching the current client list...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_client_list(),
            'instance_'.$this->instance->id.'_clientlist',
            60 * 60 * 12 // 12 hours
        );
    }

    /**
     * Fetches current servergroup list incl. member online counter from the TeamSpeak.
     */
    public function updateServergroupList()
    {
        $this->message('DEBUG', 'Caching the current servergroup list...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_servergroup_list(),
            'instance_'.$this->instance->id.'_servergrouplist',
            60 * 1.25 // 1 minute, 15 seconds
        );
    }

    /**
     * Fetches current virtualserver info from the TeamSpeak.
     */
    public function updateVirtualserverInfo()
    {
        $this->message('DEBUG', 'Caching the current virtualserver info...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_virtualserver_info(),
            'instance_'.$this->instance->id.'_virtualserver_info',
            60 * 15 // 15 minutes
        );
    }

    /**
     * PCNTL Signal Handler
     */
    protected function pcntl_signal_handler(int $signal_number)
    {
        $this->message('INFO', "Received the PCNTL signal number `$signal_number`. Stopping the bot.");

        $this->keep_running = false;
        $this->virtualserver->request('quit');

        if (! $this->instance_process->delete()) {
            $this->message('WARNING', 'Failed to delete the process ID from the database.');
        }

        $this->message('INFO', 'Successfully stopped the bot.');

        // Finally exit the script.
        // Otherwise the Artisan command hangs forever in the PCNTL signal handler.
        exit;
    }

    /**
     * Starts the actual bot for the instance.
     */
    protected function start_bot()
    {
        $this->message('INFO', 'Starting TeamSpeak bot instance: '.$this->instance->virtualserver_name);

        $virtualserver_helper = new TeamSpeakVirtualserver($this->instance);

        try {
            $this->virtualserver = $virtualserver_helper->get_virtualserver_connection(false);
        } catch (TransportException $transport_exception) {
            $this->message('ERROR', "Coult not connect to the host `$this->instance->host`: ".$transport_exception->getMessage());

            return;
        } catch (ServerQueryException $serverquery_exception) {
            $this->message('ERROR', 'ServerQuery command failed: '.$serverquery_exception->getMessage().' (Error #'.$serverquery_exception->getCode().')');

            return;
        }

        // Update all data once immediately, when the bot initially starts
        $this->updateDatetime();
        $this->updateClientList();
        $this->updateServergroupList();
        $this->updateVirtualserverInfo();

        // register for server events
        $this->virtualserver->notifyRegister('server');

        // register a callback for notifyEvent events
        Signal::getInstance()->subscribe('notifyEvent', $this->onEvent(...));

        // register a callback for serverqueryWaitTimeout events
        Signal::getInstance()->subscribe('serverqueryWaitTimeout', $this->onWaitTimeout(...));

        // wait for events
        while ($this->keep_running) {
            $this->virtualserver->getAdapter()->wait();
        }
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $process_id = getmypid();

        $this->message('INFO', "My Process ID (PID) is $process_id.");

        // setup signal handlers
        $this->message('INFO', 'Setting up signal handlers.');
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, $this->pcntl_signal_handler(...)); // handle Ctrl+C signals
        pcntl_signal(SIGTERM, $this->pcntl_signal_handler(...)); // handle shutdown signals

        $instance_id = intval($this->argument('instance_id'));

        try {
            $this->instance = Instance::findOrFail($instance_id);
        } catch (ModelNotFoundException) {
            $this->message('ERROR', "Could not find any instance with the ID `$instance_id`.");

            return;
        }

        $this->instance_process = InstanceProcess::create([
            'instance_id' => $this->instance->id,
            'command' => 'php artisan instance:start-teamspeak-bot '.$this->instance->id,
            'process_id' => $process_id,
        ]);

        if (! $this->instance_process->save()) {
            $this->message('WARNING', 'Failed to save the process ID to the database, so that it can be stopped later by the UI.');
        }

        try {
            $this->start_bot();
        } catch (TransportException $transport_exception) {
            $this->message('ERROR', $transport_exception->getMessage());
        }
    }
}
