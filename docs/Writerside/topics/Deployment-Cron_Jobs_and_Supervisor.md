# Deployment - Cron Jobs & Supervisor


## Cron Job
Create a cron job in you server machine and add the below cron job script.
```Bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Supervisor Configuration
In production, you need a way to keep your `queue:work` processes running. A `queue:work` process may stop running for a variety of reasons, such as an exceeded worker timeout or the execution of the `queue:restart` command.

### Installing Supervisor
Supervisor is a process monitor for the Linux operating system, and will automatically restart your `queue:work` processes if they fail. To install Supervisor on Ubuntu, you may use the following command:
```Bash
sudo apt-get install supervisor
```

### Configuring Supervisor
Supervisor configuration files are typically stored in the `/etc/supervisor/conf.d` directory. Within this directory, you may create any number of configuration files that instruct supervisor how your processes should be monitored. For example, let's create a `laravel-worker.conf` file that starts and monitors `queue:work` processes:
```Bash
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/www/htmlworker.log
stopwaitsecs=3600
```

### Starting Supervisor
Once the configuration file has been created, you may update the Supervisor configuration and start the processes using the following commands:
```Bash
sudo supervisorctl reread
 
sudo supervisorctl update
 
sudo supervisorctl start "laravel-worker:*"
```
For more information on Supervisor, consult the [Supervisor documentation](http://supervisord.org/index.html).
