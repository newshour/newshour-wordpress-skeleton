<?php
/**
 * An example test case which tests if the WP_ENV environment variable exists.
 *
 * ./vendor/bin/phpunit --bootstrap vendor/autoload.php web/app/themes/core/src/Tests/ExampleTest
 *
 * @version 1.0.0
 */
namespace App\Cron\Tests;

use Env\Env;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase {

    public function testEnvExists(): void {

        $this->assertNotEquals(
            'None',
            Env::get('WP_ENV', 'None')
        );

    }

}