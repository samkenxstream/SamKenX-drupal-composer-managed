<?php
namespace DrupalComposerManaged;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Test requiring and updating upstream dependencies.
 */
class InstallSiteTest extends TestCase
{
    use Fixtures;

    public function setUp(): void {
        $this->initFixtures();
    }

    public function tearDown(): void {
        $this->cleanupFixtures();
    }

    public function testInstallSite() {
        $this->createSut();
        $process = $this->composer('install');
        $this->assertProcessSuccessful($process);

        $db_url = getenv('DRUSH_SI_DB_URL') ?: 'mysql://root:@127.0.0.1/testdb';
        $process = $this->drush('site:install', ["--db-url=$db_url", '--yes'
]);
        $this->assertProcessSuccessful($process);
        $process = $this->drush('status');
        $this->assertProcessSuccessful($process);
    }
}
