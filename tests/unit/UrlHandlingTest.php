<?php

namespace Sepehr\PHPUnitSelenium\Tests\Unit;

use phpmock\mockery\PHPMockery;
use Sepehr\PHPUnitSelenium\Exception\InvalidArgument;

class UrlHandlingTest extends UnitSeleniumTestCase
{

    /** @test */
    public function normalizesUrlsByLeavingFileUrlsIntact()
    {
        $this->setUrl($expected = 'file:///path/to/some/file.html');

        $this->assertSame($expected, $this->url());
    }

    /** @test */
    public function normalizesUrlsByDroppingRedundantSlashes()
    {
        $this->setBaseUrl('https://github.com//');

        $this->setUrl('//sepehr/phpunit-selenium-webdriver//');

        $this->assertSame(
            'https://github.com/sepehr/phpunit-selenium-webdriver',
            $this->url()
        );
    }

    /** @test */
    public function throwsAnExceptionWhenProvidedWithMalformedBaseUrl()
    {
        $this->expectException(InvalidArgument::class);

        $this->setBaseUrl('/some/invalid/url');
    }

    /** @test */
    public function throwsAnExceptionWhenNoBaseUrlIsProvided()
    {
        $this->expectException(InvalidArgument::class);

        $this->baseUrl = null;
        $this->setUrl('/some/valid/path/with/no/base/url');
    }

    /** @test */
    public function updatesUrlSourcingFromWebDriverUrl()
    {
        $this->injectMockedWebDriver(
            $this->webDriverMock
                 ->shouldReceive('getCurrentURL')
                 ->once()
                 ->andReturn($expected = 'https://github.com/sepehr')
                 ->getMock()
        );

        // Mock PHP's buit-in sleep() with SUT's namespace
        PHPMockery::mock('Sepehr\PHPUnitSelenium', 'sleep')
                  ->once()
                  ->andReturn(0);

        $this->updateUrl();

        $this->assertSame($expected, $this->url());
    }

    /** @test */
    public function assemblesProperFullUrl()
    {
        $this->setBaseUrl('https://github.com/');

        $this->setUrl('/sepehr');

        $this->assertSame('https://github.com/sepehr', $this->url());
    }

    /** @test */
    public function proxiesWebDriverCurrentUrl()
    {
        $this->injectMockedWebDriver(
            $this->webDriverMock
                 ->shouldReceive('getCurrentURL')
                 ->once()
                 ->andReturn($expected = 'https://github.com/')
                 ->getMock()
        );

        $this->assertSame($expected, $this->webDriverUrl());
    }
}
