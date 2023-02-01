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
        $this->composer('install');
        // We need to set up mysql for this to work, but once we do
        // that, this should pretty much do the trick.
        $this->drush('site:install');
        // We could probably use a nice wrapper assert function for this
        $this->assertTrue($process->isSuccessful(), $process->getOutput() . PHP_EOL . $process->getErrorOutput());
        $this->drush('status');
        $this->assertTrue($process->isSuccessful(), $process->getOutput() . PHP_EOL . $process->getErrorOutput());
    }
}
