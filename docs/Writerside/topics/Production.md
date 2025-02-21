# Installation for Production

## Pre-requisite

### PHP Extension
* PHP >= 8.3
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
* GD PHP Extension


## Server Configuration

### Installation of pre-requisite
```bash
sudo apt install composer nginx
sudo apt install php8.3-common php8.3-cli php8.3-fpm php8.3-{curl,bz2,mbstring,intl,xml,gd}
```

### Firewall
Enable nginx in firewall by running this command
```bash
ufw allow 'Nginx HTTP'
ufw allow 'Nginx HTTPS'
ufw enable
ufw status
```

### Ngix Sites
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /srv/example.com/public;
 
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
 
    index index.php;
 
    charset utf-8;
 
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
 
    error_page 404 /index.php;
 
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
 
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Server Certificates
As root , to install the tool to install certificate, run:
```bash
apt-get install certbot python3-certbot-nginx
```
If you need to install a certificate for hostname host.mydomain.com:
```bash
certbot --nginx -d host.mydomain.com
```



## Laravel Reverb (Real-time Notification/Broadcast)
```NGINX
server {
    ...
 
    location / {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
 
        proxy_pass http://0.0.0.0:8080;
    }
 
    ...
}
```

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







