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

    public function testInstallDrupal9Site() {
        $this->createSut();
        $process = $this->composer('install');
        $this->assertProcessSuccessful($process);

        $process = $this->composer('info', ['drupal/*']);
        $this->assertProcessSuccessful($process);
        $this->assertMatchesRegularExpression('#drupal/core-recommended *9#', $process->getOutput());

        $process = $this->installDrupal();
        $this->assertProcessSuccessful($process);

        $process = $this->drush('status', ['--format=yaml']);
        $this->assertProcessSuccessful($process);
        $this->assertMatchesRegularExpression('/^drupal-version: 9/', $process->getOutput());
    }

    public function testInstallDrupal10Site() {
        $this->createSut();
        $this->runPhpDevopsScript('apply_drupal10_composer_changes.php');
        $process = $this->composer('update');
        $this->assertProcessSuccessful($process);

        $process = $this->composer('info', ['drupal/*']);
        $this->assertProcessSuccessful($process);
        $this->assertMatchesRegularExpression('#drupal/core-recommended *10#', $process->getOutput());

        $process = $this->installDrupal();
        $this->assertProcessSuccessful($process);

        $process = $this->drush('status', ['--format=yaml']);
        $this->assertProcessSuccessful($process);
        $this->assertMatchesRegularExpression('/drupal-version: 10/', $process->getOutput());
    }

}
