# Installation for Development/Local

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
git clone git@github.com:ninshiki-project/RecognizeIt-backend-community.git
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
php artisan migrate --seed
```
8. Generate Reverb Key
```Bash
php artisan reverb:key
```
9. Now your backend is ready for integration with your frontend.


### Default Account
| **Email**        | **Password** | **Role** |
|------------------|--------------|----------|
| test@example.com | password     | Owner    |


