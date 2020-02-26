# Deploy
## Prepare api directory
### 1) in directory /var/www execute sudo git clone https://github.com/CzechGlobe-DoAB/BioArInEO-API.git
### 2) in directory /var/www/BioArInEO-API create writeable dirs cache/proxies and logs
### 3) set database connection in /var/www/BioArInEO-API/app/settings.local.php
### 4) execute sudo composer install (/var/www/BioArInEO-API/app)
## Set Virtual-Host
### 5) cd /etc/apache2/sites-available
### 6) create configuration file <api-domain>.conf (example api.bioarineo.tech.conf)
  `<VirtualHost *:80>
        ServerName <server name>
        ServerAlias <server alias>
        ServerAdmin cerveny.j@czechglobe.cz
        DocumentRoot /var/www/BioArInEO-API/www
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
  </VirtualHost>`

## In case of problems 
   Errors are loging in /var/www/BioArInEO-API/logs or /var/log/apache2 file error.log (You have to be in superuser mode)
   
# Installation
## OAuth
public/private key pair needs to be generated as well as encryption key
##### Generating public/private key pair
Use the following commands to create private/public key pair
```bash
openssl genrsa -out oauth2.priv 2048
openssl rsa -in oauth2.priv -pubout -out oauth2.pub
```
then set path to these files inside `settings.local.php`

##### Generating encryption key
`composer update` needs to be run before this step at least once
```bash
vendor/bin/generate-defuse-key
```
then copy and paste the generated string into `settings.local.php`
