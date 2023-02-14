<?php
namespace DrupalComposerManaged;

use PHPUnit\Framework\TestCase;

/**
 * Test our 'help' command.
 */
class ComposerScriptsTest extends TestCase
{
    /**
     * Data provider for testValidBestPhpPatchVersion
     */
    function validPhpVersionValues() {
      return [
          [ '8.2' ],
          [ '8.1' ],
          [ '8.0' ],
          [ '7.4' ],
          [ '7.3' ],
          [ '7.2' ],
          [ '7.1' ],
      ];
    }

    /**
     * @dataProvider validPhpVersionValues
     */
    public function testValidBestPhpPatchVersion($versionToTest) {
        $patchVersion = $this->callProtected('bestPhpPatchVersion', [$versionToTest]);
        $this->assertMatchesRegularExpression('/^[0-9]+\.[0-9]+\.[0-9]+$/', $patchVersion);
    }

    /**
     * Data provider for testInvalidBestPhpPatchVersion
     */
    public function invalidPhpVersionValues() {
      return [
          [ '8.3' ],
          [ '7.0' ],
          [ '5.6' ],
          [ '' ],
          [ 'Fred' ],
      ];
    }

    /**
     * @dataProvider invalidPhpVersionValues
     */
    public function testInvalidBestPhpPatchVersion($versionToTest) {
        $patchVersion = $this->callProtected('bestPhpPatchVersion', [$versionToTest]);
        $this->assertEquals('', $patchVersion);
    }

    protected function callProtected($method, $args) {
        $class = new \ReflectionClass('\DrupalComposerManaged\ComposerScripts');
        $fnPtr = $class->getMethod($method);
        $fnPtr->setAccessible(true);
        return $fnPtr->invokeArgs(null, $args);
    }
}
