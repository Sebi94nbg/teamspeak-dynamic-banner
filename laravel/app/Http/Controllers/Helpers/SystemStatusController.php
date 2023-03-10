<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

/**
 * Possible system status severities.
 */
enum SystemStatusSeverity: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}

class SystemStatusController extends Controller
{
    /**
     * Checks PHP version.
     */
    protected function check_php_version(): array
    {
        $requirements = [];

        $requirements['VERSION_ID']['name'] = 'PHP Version';
        $requirements['VERSION_ID']['current_value'] = PHP_VERSION_ID.' ('.PHP_VERSION.')';
        $requirements['VERSION_ID']['required_value'] = '>= 80100 (8.1.0)';
        $requirements['VERSION_ID']['severity'] = (PHP_VERSION_ID >= 80100) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks PHP extensions.
     */
    protected function check_php_extensions(): array
    {
        $requirements = [];

        $requirements['name'] = 'PHP Extensions';
        $requirements['required_value'] = 'should be enabled';

        /**
         * Laravel specific requirements
         */
        $requirements['CTYPE']['name'] = 'ctype';
        $requirements['CTYPE']['severity'] = (extension_loaded('ctype')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['CURL']['name'] = 'curl';
        $requirements['CURL']['severity'] = (extension_loaded('curl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['DOM']['name'] = 'dom';
        $requirements['DOM']['severity'] = (extension_loaded('dom')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['FILEINFO']['name'] = 'fileinfo';
        $requirements['FILEINFO']['severity'] = (extension_loaded('fileinfo')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['FILTER']['name'] = 'filter';
        $requirements['FILTER']['severity'] = (extension_loaded('filter')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['HASH']['name'] = 'hash';
        $requirements['HASH']['severity'] = (extension_loaded('hash')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['MBSTRING']['name'] = 'mbstring';
        $requirements['MBSTRING']['severity'] = (extension_loaded('mbstring')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['OPENSSL']['name'] = 'openssl';
        $requirements['OPENSSL']['severity'] = (extension_loaded('openssl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PCRE']['name'] = 'pcre';
        $requirements['PCRE']['severity'] = (extension_loaded('openssl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PDO']['name'] = 'pdo';
        $requirements['PDO']['severity'] = (extension_loaded('pdo')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['SESSION']['name'] = 'session';
        $requirements['SESSION']['severity'] = (extension_loaded('session')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['TOKENIZER']['name'] = 'tokenizer';
        $requirements['TOKENIZER']['severity'] = (extension_loaded('tokenizer')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['XML']['name'] = 'xml';
        $requirements['XML']['severity'] = (extension_loaded('xml')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PDO_MYSQL']['name'] = 'pdo_mysql';
        $requirements['PDO_MYSQL']['severity'] = (extension_loaded('pdo_mysql')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        /**
         * Project specific requirements
         */
        $requirements['SSH2']['name'] = 'ssh2';
        $requirements['SSH2']['severity'] = (extension_loaded('ssh2')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $requirements['GD_PNG_SUPPORT']['name'] = 'gd (PNG Support)';
        $requirements['GD_PNG_SUPPORT']['severity'] = ((extension_loaded('gd')) ?? gd_info()['PNG Support']) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $requirements['GD_JPEG_SUPPORT']['name'] = 'gd (JPEG Support)';
        $requirements['GD_JPEG_SUPPORT']['severity'] = ((extension_loaded('gd')) ?? gd_info()['JPEG Support']) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks PHP INI setings.
     */
    protected function check_php_ini_settings(): array
    {
        $max_execution_time = ini_get('max_execution_time');
        $requirements['MAX_EXECUTION_TIME']['name'] = 'PHP max_execution_time';
        $requirements['MAX_EXECUTION_TIME']['current_value'] = $max_execution_time;
        $requirements['MAX_EXECUTION_TIME']['required_value'] = '0, -1 OR >=30';
        $requirements['MAX_EXECUTION_TIME']['severity'] = (
            ($max_execution_time == 0) or
            ($max_execution_time == '-1') or
            ($max_execution_time >= 0)
        ) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $memory_limit = ini_get('memory_limit');
        $requirements['MEMORY_LIMIT']['name'] = 'PHP memory_limit';
        $requirements['MEMORY_LIMIT']['current_value'] = $memory_limit;
        $requirements['MEMORY_LIMIT']['required_value'] = '>= 128M';
        $requirements['MEMORY_LIMIT']['severity'] = (substr($memory_limit, 0, -1) >= 128) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $upload_max_filesize = ini_get('upload_max_filesize');
        $requirements['UPLOAD_MAX_FILESIZE']['name'] = 'PHP upload_max_filesize';
        $requirements['UPLOAD_MAX_FILESIZE']['current_value'] = $upload_max_filesize;
        $requirements['UPLOAD_MAX_FILESIZE']['required_value'] = '>= 5M';
        $requirements['UPLOAD_MAX_FILESIZE']['severity'] = (substr($upload_max_filesize, 0, -1) * 1024 * 1024 >= 5 * 1024 * 1024) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $post_max_size = ini_get('post_max_size');
        $requirements['POST_MAX_SIZE']['name'] = 'PHP post_max_size';
        $requirements['POST_MAX_SIZE']['current_value'] = $post_max_size;
        $requirements['POST_MAX_SIZE']['required_value'] = '>= upload_max_filesize';
        $requirements['POST_MAX_SIZE']['severity'] = (substr($post_max_size, 0, -1) * 1024 * 1024 >= substr($upload_max_filesize, 0, -1) * 1024 * 1024) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $date_timezone = ini_get('date.timezone');
        $requirements['DATE_TIMEZONE']['name'] = 'PHP date.timezone';
        $requirements['DATE_TIMEZONE']['current_value'] = $date_timezone;
        $requirements['DATE_TIMEZONE']['required_value'] = 'should be set';
        $requirements['DATE_TIMEZONE']['severity'] = (! empty(trim($date_timezone))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks database connection.
     */
    protected function check_database_connection(): array
    {
        $requirements = [];

        try {
            $db_name = DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            $db_name = $exception;
        }

        $requirements['TEST']['name'] = 'Database Connection';
        $requirements['TEST']['current_value'] = (is_string($db_name)) ? 'Connected' : "Error: $db_name";
        $requirements['TEST']['required_value'] = '`.env` should contain valid `DB_` settings';
        $requirements['TEST']['severity'] = (is_string($db_name)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks database settings.
     */
    protected function check_database_settings(): array
    {
        $requirements = [];

        try {
            $db_name = DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            $db_name = $exception->getMessage();
        }

        $requirements['DB_NAME']['name'] = 'Database Name';
        $requirements['DB_NAME']['current_value'] = $db_name;
        $requirements['DB_NAME']['severity'] = (is_string($db_name)) ? SystemStatusSeverity::Info : SystemStatusSeverity::Danger;

        $username = config('database.connections.'.Config::get('database.default').'.username');
        $requirements['DB_USER']['name'] = 'Database User';
        $requirements['DB_USER']['current_value'] = $username;
        $requirements['DB_USER']['severity'] = SystemStatusSeverity::Info;

        $charset = config('database.connections.'.Config::get('database.default').'.charset');
        $requirements['CHARACTER_SET']['name'] = 'Character Set';
        $requirements['CHARACTER_SET']['current_value'] = $charset;
        $requirements['CHARACTER_SET']['required_value'] = 'should be utf8-like';
        $requirements['CHARACTER_SET']['severity'] = (preg_match('/^utf8/', $charset)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $collation = config('database.connections.'.Config::get('database.default').'.collation');
        $requirements['COLLATION']['name'] = 'Collation';
        $requirements['COLLATION']['current_value'] = $collation;
        $requirements['COLLATION']['required_value'] = 'should be utf8-like';
        $requirements['COLLATION']['severity'] = (preg_match('/^utf8/', $collation)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks directories.
     */
    protected function check_directories(): array
    {
        $requirements = [];

        $requirements['name'] = 'Directories';
        $requirements['required_value'] = 'should be writeable';

        $requirements['STORAGE_FRAMEWORK_DIR']['name'] = storage_path('framework');
        $requirements['STORAGE_FRAMEWORK_DIR']['required_value'] = 'must be writeable';
        $requirements['STORAGE_FRAMEWORK_DIR']['severity'] = (is_writable(storage_path('framework'))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['STORAGE_LOGS_DIR']['name'] = storage_path('logs');
        $requirements['STORAGE_LOGS_DIR']['required_value'] = 'must be writeable';
        $requirements['STORAGE_LOGS_DIR']['severity'] = (is_writable(storage_path('logs'))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PUBLIC_DIR']['name'] = public_path();
        $requirements['PUBLIC_DIR']['required_value'] = 'must be writeable';
        $requirements['PUBLIC_DIR']['severity'] = (is_writable(public_path())) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks Redis connection.
     */
    protected function check_redis_connection(): array
    {
        $requirements = [];

        $reachable = false;
        try {
            Redis::ping();
            $reachable = true;
        } catch (ConnectionException $connection_exception) {
            $redis_connection_exception = $connection_exception->getMessage();
        }

        $requirements['TEST']['name'] = 'Redis Connection';
        $requirements['TEST']['current_value'] = ($reachable) ? 'Connected' : $redis_connection_exception;
        $requirements['TEST']['required_value'] = '`.env` should contain valid `REDIS_` settings';
        $requirements['TEST']['severity'] = ($reachable) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks versions.
     */
    protected function check_versions(): array
    {
        $requirements = [];

        $requirements['PHP_VERSION']['name'] = 'PHP Version';
        $requirements['PHP_VERSION']['current_value'] = PHP_VERSION;
        $requirements['PHP_VERSION']['severity'] = SystemStatusSeverity::Info;

        $requirements['LARAVEL_VERSION']['name'] = 'Laravel Version';
        $requirements['LARAVEL_VERSION']['current_value'] = Application::VERSION;
        $requirements['LARAVEL_VERSION']['severity'] = SystemStatusSeverity::Info;

        return $requirements;
    }

    /**
     * Checks various information.
     */
    protected function check_various_information(): array
    {
        $requirements = [];

        $requirements['IS_GIT_DEPLOYMENT']['name'] = 'Is Git Deployment';
        $requirements['IS_GIT_DEPLOYMENT']['current_value'] = (file_exists('../.git/')) ? 'Yes' : 'No';
        $requirements['IS_GIT_DEPLOYMENT']['severity'] = SystemStatusSeverity::Info;

        $requirements['APP_ENVIRONMENT']['name'] = 'Application Environment';
        $requirements['APP_ENVIRONMENT']['current_value'] = Config::get('app.env');
        $requirements['APP_ENVIRONMENT']['required_value'] = 'should be set to `production` in production';
        $requirements['APP_ENVIRONMENT']['severity'] = SystemStatusSeverity::Info;

        $requirements['APP_DEBUG']['name'] = 'Application Debug';
        $requirements['APP_DEBUG']['current_value'] = (Config::get('app.debug')) ? 'Enabled' : 'Disabled';
        $requirements['APP_DEBUG']['required_value'] = 'should be disabled in production';
        $requirements['APP_DEBUG']['severity'] = SystemStatusSeverity::Info;

        $requirements['SERVER_SOFTWARE']['name'] = 'Server Software';
        $requirements['SERVER_SOFTWARE']['current_value'] = $_SERVER['SERVER_SOFTWARE'];
        $requirements['SERVER_SOFTWARE']['severity'] = SystemStatusSeverity::Info;

        $requirements['PHP_BINARY']['name'] = 'PHP Binary';
        $requirements['PHP_BINARY']['current_value'] = PHP_BINARY;
        $requirements['PHP_BINARY']['severity'] = SystemStatusSeverity::Info;

        return $requirements;
    }

    /**
     * Returns a summary of the system status in JSON format.
     */
    public function system_status_json($optional_information = true)
    {
        $system_status = [];

        $system_status['PHP']['VERSION'] = $this->check_php_version();
        $system_status['PHP']['EXTENSIONS'] = $this->check_php_extensions();
        $system_status['PHP']['INI_SETTINGS'] = $this->check_php_ini_settings();
        $system_status['DATABASE']['CONNECTION'] = $this->check_database_connection();
        $system_status['DATABASE']['SETTINGS'] = $this->check_database_settings();
        $system_status['PERMISSIONS']['DIRECTORIES'] = $this->check_directories();
        $system_status['REDIS']['CONNECTION'] = $this->check_redis_connection();

        if ($optional_information) {
            $system_status['VERSIONS']['SOFTWARE'] = $this->check_versions();
            $system_status['VARIOUS']['INFORMATION'] = $this->check_various_information();
        }

        $system_status_json = json_encode($system_status);

        return $system_status_json;
    }
}
