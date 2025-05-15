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


## Server Configuration

### Installation of pre-requisite
```bash
sudo apt install php8.3-common php8.3-cli php8.3-fpm php8.3-{curl,bz2,mbstring,intl,xml,gd,sqlite3,zip,mysql}
sudo apt install nginx
```

## Folder Permissions
```bash
sudo chown -R $USER:www-data /var/www/ninshiki-backend/storage
sudo chown -R $USER:www-data /var/www/ninshiki-backend/bootstrap/cache
sudo chmod -R 775 /var/www/ninshiki-backend/storage
sudo chmod -R 775 /var/www/ninshiki-backend/bootstrap/cache
```

### install composer
```Bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
Put the composer.phar into a directory on your PATH, so you can simply call composer from any directory (Global install)
```bash
sudo mv composer.phar /usr/local/bin/composer
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
Create a new site `sudo nano /etc/nginx/sites-available/ninshiki-backend.example.com` 

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name ninshiki.example.com;
    root /var/www/ninshiki-backend/public;
 
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
Once you're done, then create a symlink `sudo ln -s /etc/nginx/sites-available/ninshiki-backend.example.com /etc/nginx/sites-enabled/`

### Server Certificates
As root , to install the tool to install certificate, run:
```bash
apt-get install certbot python3-certbot-nginx
```
If you need to install a certificate for hostname host.mydomain.com:
```bash
certbot --nginx -d host.mydomain.com
sudo ufw allow 'Nginx Full'
sudo ufw delete allow 'Nginx HTTP'
```
Once the Certbot run successfully, restart your nginx services `sudo systemctl reload nginx`




## Laravel Reverb (Real-time Notification/Broadcast)

### NGINX
```nginx
server {
....

# Laravel Reverb
# The Websocket Client/Laravel Echo would connect and listen to this

location ~ /app/(?<reverbkey>.*) { # variable reverbkey
  proxy_pass http://127.0.0.1:8080/app/$reverbkey;
  proxy_http_version 1.1;
  proxy_set_header Host $http_host;
  proxy_set_header Scheme $scheme;
  proxy_set_header SERVER_PORT $server_port;
  proxy_set_header REMOTE_ADDR $remote_addr;
  proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
  proxy_set_header Upgrade $http_upgrade;
  proxy_set_header Connection "Upgrade";
  proxy_read_timeout 120;
  proxy_send_timeout 120;
}

# The Laravel Backend would broadcast to this
location ~ ^/apps/(?<reverbid>[^/]+)/events$ { # variable reverbid
  proxy_pass http://127.0.0.1:8080/apps/$reverbid/events;
  proxy_set_header Host $host;
  proxy_set_header X-Real-IP $remote_addr;
  proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
  proxy_set_header X-Forwarded-Proto $scheme;
  proxy_read_timeout 120;
  proxy_send_timeout 120;
}

    
}
```
Alternatively, you can use [this](https://laracasts.com/discuss/channels/reverb/pusher-error-authentication-signature-invalid?page=1&replyId=958032)
```nginx
    # Laravel Reverb
    ## The Websocket Client/Laravel Echo would connect to /app
    ## The Laravel Backend would broadcast to /apps
    location ~ ^/apps? {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        proxy_pass http://127.0.0.1:8080;
    }

```


### Apache
```diff
+ ProxyPreserveHost On
+ <Location /app>
+        ProxyPass ws://0.0.0.0:8080/app
+        ProxyPassReverse ws://0.0.0.0:8080/app
+ </Location>
+ <Location /apps>
+        ProxyPass http://0.0.0.0:8080/apps
+        ProxyPassReverse http://0.0.0.0:8080/apps
+ </Location>
```


Your `.env` file for the reverb should look like this. Make sure your `REVERB_SERVER_PORT` and the port in your nginx proxy is the same.

Reference: [#117](https://github.com/laravel/reverb/issues/117#issuecomment-2022571567)
```bash
REVERB_SERVER_HOST=127.0.0.1 # dont change this if the frontend and backend are in the same domain

REVERB_APP_ID=xxxx
REVERB_APP_KEY=xxxxx
REVERB_APP_SECRET=xxxxx
REVERB_HOST=ninshiki.example.com #App URL without 'http(s)'
REVERB_PORT=443 #or 80 if you are not in SSL
REVERB_SCHEME=https #or 'http' if you are not in SSL
```


## Supervisor

To install Supervisor on Ubuntu, you may use the following command: `sudo apt-get install supervisor`.

Supervisor configuration files are typically stored in the `/etc/supervisor/conf.d` directory. Within this directory,
you may create any number of configuration files that instruct supervisor how your processes should be monitored.


###### Starting Supervisor
Once the configuration file has been created, you may update the Supervisor configuration and start the processes using the following commands:
```bash
sudo supervisorctl reread
sudo supervisorctl update
 
```

###### Websocket
Let's create a `ninshiki-worker-websocket.conf` file that starts and monitors `reverb:start` processes:
```bash
[program:ninshiki-worker-websocket]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/backend-ninshiki.com/artisan reverb:start --no-interaction
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stopwaitsecs=3600
```


###### Queues

Let's create a `ninshiki-worker-queue.conf` file that starts and monitors `queue:work` processes:

```bash
[program:ninshiki-worker-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/backend-ninshiki.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=3
redirect_stderr=true
stopwaitsecs=3600
```

###### Pulse Check

Let's create a `ninshiki-worker-pulse.conf` file that starts and monitors `pulse:check` processes:

```bash
[program:ninshiki-worker-pulse]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/backend-ninshiki.com/artisan pulse:check
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
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







