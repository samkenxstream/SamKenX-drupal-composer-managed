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

    /**
     * Test to see if Drupal can be installed using the php files from
     * the repository after `composer update` has been run
     */
    public function testInstallDrupal9Site() {
        $this->createSut();

        $this->assertSutFileNotContains('upstream-require', 'composer.json');

        $process = $this->composer('config', ['platform.php']);
        $this->assertProcessSuccessful($process);
        $this->assertStringContainsString('8.1.', $process->getOutput());

        $process = $this->composer('info', ['drupal/*']);
        $this->assertProcessSuccessful($process);
        $this->assertMatchesRegularExpression('#drupal/core-recommended *9#', $process->getOutput());

        $process = $this->installDrupal();
        $this->assertProcessSuccessful($process);

        $process = $this->drush('status', ['--format=yaml']);
        $this->assertProcessSuccessful($process);
        $this->assertMatchesRegularExpression('/^drupal-version: 9/', $process->getOutput());
    }

    /**
     * Test to see if Drupal can be installed after running our build-time
     * script to convert to Drupal 10
     */
    public function testInstallDrupal10Site() {
        $this->createSut();
        $this->runPhpDevopsScript('apply_drupal10_composer_changes.php');
        $process = $this->composer('update');
        $this->assertProcessSuccessful($process);

        $process = $this->composer('config', ['platform.php']);
        $this->assertProcessSuccessful($process);
        $this->assertStringContainsString('8.2.', $process->getOutput());

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
