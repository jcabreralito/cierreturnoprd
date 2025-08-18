<?php
namespace App\Providers;

use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Support\ServiceProvider;

class CustomSqlServerConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('db.connector.sqlsrv', function () {
            return new CustomSqlServerConnector();
        });
    }
}
