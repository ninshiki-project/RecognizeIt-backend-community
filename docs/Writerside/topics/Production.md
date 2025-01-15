# Installation for Production

## Pre-requisite

### PHP Extension
* PHP >= 8.2
* Ctype PHP Extension
* cURL PHP Extension
* DOM PHP Extension
* Fileinfo PHP Extension
* Filter PHP Extension
* Hash PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PCRE PHP Extension
* PDO PHP Extension
* Session PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Intl PHP Extension

### Email Notification
Create an account in [RESEND](https://resend.com/) for email services.

### CDN
Create an account in [Cloudinary](https://cloudinary.com/) for Image CDN.

## Installation
1. Clone the repository
```Bash
git clone git@github.com:MarJose123/Ninshiki-backend.git
```
2. Copy the `.env.example` to `.env`
3. Update the Laravel Reverb Key by providing a unique key and App Key.
```Bash
php artisan reverb:key
```
4. Update your Cloudinary `CLOUDINARY_URL` and `RESEND_KEY`.
5. Install dependencies
```Bash
composer install
```
6. Generate App Key
```Bash
php artisan generate:key
```
7. Run Database Migration and Seeder
```Bash
php artisan migrate
```
8. Create a user with a permission of an Owner/Administrator
```Bash
php artisan make:ninshiki-user
```
9. Generate Reverb Key
```Bash
php artisan reverb:key
```
10. Now your backend is ready for integration with your frontend.


## Supervisor

To install Supervisor on Ubuntu, you may use the following command: `sudo apt-get install supervisor`.

Supervisor configuration files are typically stored in the `/etc/supervisor/conf.d` directory. Within this directory,
you may create any number of configuration files that instruct supervisor how your processes should be monitored.


###### Starting Supervisor
Once the configuration file has been created, you may update the Supervisor configuration and start the processes using the following commands:
```bash
sudo supervisorctl reread
 
sudo supervisorctl update
 
sudo supervisorctl start "ninshiki-worker-*"
```

###### Queues

Let's create a `ninshiki-worker-queue.conf` file that starts and monitors `queue:work` processes:

```bash
[program:ninshiki-worker-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /www/backend-ninshiki.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/home/ninshiki/worker-queue.log
stopwaitsecs=3600
```

###### Pulse Check

Let's create a `ninshiki-worker-pulse.conf` file that starts and monitors `pulse:check` processes:

```bash
[program:ninshiki-worker-pulse]
process_name=%(program_name)s_%(process_num)02d
command=php /www/backend-ninshiki.com/artisan pulse:check
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/home/ninshiki/worker-pulse.log
```


## Cron Job

In this step, we'll set up a cron job command on the server.
If you're using Ubuntu Server, crontab is likely already installed.
Run the command below to add a new entry for the cron job.

```bash
crontab -e
```

###### Task Scheduler (run every 5 minutes)
Copy this cron script to add a new entry for the cron job.
Don't forget to update its path of your app.
```
*/5 * * * * php /www/backend-ninshiki.com/artisan schedule:run >/dev/null 2>&1
```







