# Booklib - Ebook and Comic library system

## Requirements
Pre-installed Linux-based distro with a webserver, PHP8 and MySQL Server installed.

You will need to create a new, empty database for this system.

Additional software required:
- PHP Extension: zip
- PHP Extension: gd
- PHP Extension: mysql 
- PHP Extension: xml
- Application: git
- Application: unrar (5.21+)


## Installation

1. Checkout our repo in the location where you want to host the files:

   git clone "https://github.com/MKaterbarg/Booklib.git" .
   
2. Install composer packages with `php composer.phar install`
3. Add the appropriate webserver configuration to your webserver. Please point your document root not to the base directory, but to the "public" subdirectory of our system 
4. Rename the .env.example file to .env and edit the required values. Make sure you update the following values:

    - DB_HOST - This probably needs to be "localhost", unless you use an external MySQL server
    - DB_DATABASE - The name of your database
    - DB_USERNAME - The username that has access to the database
    - DB_PASSWORD - The password of the above user

5. Execute the initialization of the database using the following commands:
   
   `php artisan key:generate`
   
   `php artisan migrate`
   
   `php artisan db:seed`


This will actually create the database tables, set basic settings and add a default user. 

5. Add the cron scheduler to you /etc/crontab file. This cron should run every minute, as the application itself will manage when which command should run. Please add as:
    
    `* * * * * root php <path> artisan schedule:run`

6. Due to a security policy in ImageMagick, you may need to update the security policy in /etc/ImageMagick-7/policy.xml. Just before \</policymap\>, add:
   
    `<policy domain="coder" rights="read | write" pattern="PDF" />`
   

7. Once this is done, you may login to the system using the URL you specified in your webserver configuration. Login using the initial login details:
    1. Username: admin
    2. Password: password
    3. Make sure to update this as soon as possible using the top-right settings icon/menu
    
8. Add your first library using the top right menu. Make sure the www-data user has read permissions on the directories you're adding"


## Known issues
1. The number of files counters are currently not working

## Todo
- Add monitoring of failed jobs, and notifications of it.

