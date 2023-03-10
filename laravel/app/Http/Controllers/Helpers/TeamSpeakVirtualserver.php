<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;

class TeamSpeakVirtualserver extends Controller
{
    /**
     * Class properties
     */
    private string $serverquery_username;

    private string $serverquery_password;

    private string $host;

    private string $serverquery_port;

    private bool $is_ssh;

    private string $voice_port;

    private string $client_nickname;

    private ?int $default_channel_id = null;

    public Server $virtualserver;

    /**
     * The class constructor
     */
    public function __construct(Instance $instance)
    {
        $this->serverquery_username = $instance->serverquery_username;
        $this->serverquery_password = $instance->serverquery_password;
        $this->host = $instance->host;
        $this->serverquery_port = $instance->serverquery_port;
        $this->is_ssh = $instance->is_ssh;
        $this->voice_port = $instance->voice_port;
        $this->client_nickname = $instance->client_nickname;
        $this->default_channel_id = $instance->default_channel_id;
    }

    /**
     * The class destructor
     */
    public function __destruct()
    {
        if (isset($this->virtualserver) and is_resource($this->virtualserver)) {
            $this->virtualserver->disconnect();
        }
    }

    /**
     * Connects to an instance and returns the virtualserver object.
     */
    public function get_virtualserver_connection(bool $blocking = true): Server
    {
        $TS3PHPFramework = new TeamSpeak3();

        $client_nickname = urlencode($this->client_nickname);

        if (filter_var($this->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // IPv6 addresses must be enclosed in brackets.
            $connection_uri = "serverquery://$this->serverquery_username:$this->serverquery_password@[$this->host]:$this->serverquery_port/?server_port=$this->voice_port&nickname=$client_nickname";
        } else {
            // IPv4 address or domain
            $connection_uri = "serverquery://$this->serverquery_username:$this->serverquery_password@$this->host:$this->serverquery_port/?server_port=$this->voice_port&nickname=$client_nickname";
        }

        if ($this->is_ssh) {
            $connection_uri = $connection_uri.'&ssh=1';
        }

        if (! $blocking) {
            $connection_uri = $connection_uri.'&blocking=0';
        }

        $connection_uri = $connection_uri.'#no_query_clients';

        try {
            $this->virtualserver = $TS3PHPFramework->factory($connection_uri);
        } catch (TransportException $transport_exception) {
            throw new TransportException($transport_exception->getMessage(), $transport_exception->getCode());
        } catch (ServerQueryException $serverquery_exception) {
            if ($serverquery_exception->getCode() == 513) {
                // Error: nickname is already in use
                // Do nothing. The library automatically adds an incremental number to the client nickname.
            } else {
                throw new ServerQueryException($serverquery_exception->getMessage(), $serverquery_exception->getCode());
            }
        }

        if (! is_null($this->default_channel_id)) {
            try {
                $this->virtualserver->clientMove($this->virtualserver->whoamiGet('client_id'), $this->default_channel_id);
            } catch (ServerQueryException $serverquery_exception) {
                throw new ServerQueryException($serverquery_exception->getMessage(), $serverquery_exception->getCode());
            }
        }

        return $this->virtualserver;
    }
}
