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

        $process = $this->installDrupal();
        $this->assertProcessSuccessful($process);

        $process = $this->drush('status');
        $this->assertProcessSuccessful($process);
    }
}
