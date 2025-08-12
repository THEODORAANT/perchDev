<?php
    switch($_SERVER['SERVER_NAME']) {

        case '':
            include(__DIR__.'/config.ra-d41d-cd98.php');
            break;

        default:
        include(__DIR__.'/config.ra-d41d-cd98.php');
          //  include('config.production.php');
            break;
    }

    define('PERCH_LICENSE_KEY', 'R42308-LWM253-UNP582-WFJ012-GLQ018');
   // define('PERCH_LICENSE_KEY', 'R4-LOCAL-XAV221-KHZ122-FVN017');
    define('PERCH_EMAIL_FROM', 'terrencelovegrove@outlook.com');
    define('PERCH_EMAIL_FROM_NAME', 'Terrence Lovegrove');

    define('PERCH_LOGINPATH', '/perch-dev/perch');

    define('PERCH_PATH', str_replace(DIRECTORY_SEPARATOR.'config', '', __DIR__));
    define('PERCH_CORE', PERCH_PATH.DIRECTORY_SEPARATOR.'core');

    define('PERCH_RESFILEPATH', PERCH_PATH . DIRECTORY_SEPARATOR . 'resources');
    define('PERCH_RESPATH', PERCH_LOGINPATH . '/resources');
    
    define('PERCH_HTML5', true);
    define('PERCH_TZ', 'UTC');
   //  define('PERCH_LOCALE', 'fr_FR');
    define('PERCH_DEBUG', true);
   define('PERCH_STRIPSLASHES', false);
   define('PERCH_TWILLIO_AUTHTOKEN', "");
   //define('PERCH_TWILLIO_FROM',"+14155238886");
define('PERCH_EMAIL_METHOD', 'smtp');
define('PERCH_EMAIL_HOST', 'sandbox.smtp.mailtrap.io');
define('PERCH_EMAIL_AUTH', true);
//define('PERCH_EMAIL_SECURE', 'ssl');
define('PERCH_EMAIL_PORT', 2525);
define('PERCH_EMAIL_USERNAME', '68250d4952219e');
define('PERCH_EMAIL_PASSWORD', 'd00dfc1d33635d');
