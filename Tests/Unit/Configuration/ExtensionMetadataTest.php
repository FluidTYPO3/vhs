<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;

class ExtensionMetadataTest extends TestCase
{
    public function testBuildWorkflowBranchFiltersIncludeMainDevelopmentAndReleaseBranches(): void
    {
        foreach (['push', 'pull_request'] as $event) {
            $branches = self::extractWorkflowBranches($event);

            self::assertContains('main', $branches);
            self::assertContains('development', $branches);
            self::assertContains('[0-9]+.[0-9]+', $branches);
            self::assertContains('[0-9]+.[0-9]+.[0-9]+', $branches);
        }
    }

    public function testBuildWorkflowRunsFunctionalTests(): void
    {
        $workflow = (string) file_get_contents(self::rootPath('.github/workflows/build.yml'));

        self::assertStringContainsString('phpunit-functional.xml.dist', $workflow);
        self::assertStringContainsString('typo3DatabaseDriver=pdo_sqlite', $workflow);
    }

    public function testTypo3SupportMetadataIsConsistent(): void
    {
        $composer = self::readJsonFile('composer.json');
        $composerMinors = self::extractComposerTypo3Minors($composer['require']['typo3/cms-core']);

        self::assertSame(['13.4', '14.3'], $composerMinors);
        foreach (['typo3/cms-extbase', 'typo3/cms-fluid', 'typo3/cms-frontend', 'typo3/cms-backend'] as $package) {
            self::assertSame($composerMinors, self::extractComposerTypo3Minors($composer['require'][$package]));
        }

        self::assertSame($composerMinors, self::extractPreflightTypo3Minors());
        self::assertSame($composerMinors, self::extractWorkflowTypo3Minors());

        $emConf = self::readExtensionManagerConfiguration();
        $emConfTypo3Constraint = $emConf['constraints']['depends']['typo3'];
        self::assertSame($composerMinors, self::extractExtensionManagerTypo3Minors($emConfTypo3Constraint));
        self::assertSame('13.4.0-14.3.99', $emConfTypo3Constraint);
    }

    private static function readJsonFile(string $path): array
    {
        return json_decode((string) file_get_contents(self::rootPath($path)), true, flags: JSON_THROW_ON_ERROR);
    }

    private static function readExtensionManagerConfiguration(): array
    {
        $EM_CONF = [];
        include self::rootPath('ext_emconf.php');
        return $EM_CONF['vhs'];
    }

    private static function extractComposerTypo3Minors(string $constraint): array
    {
        preg_match_all('/\^(\d+\.\d+)/', $constraint, $matches);
        return self::sortUnique($matches[1]);
    }

    private static function extractExtensionManagerTypo3Minors(string $constraint): array
    {
        self::assertMatchesRegularExpression('/^(\d+\.\d+)\.\d+-(\d+\.\d+)\.\d+$/', $constraint);
        preg_match('/^(\d+\.\d+)\.\d+-(\d+\.\d+)\.\d+$/', $constraint, $matches);
        return self::sortUnique([$matches[1], $matches[2]]);
    }

    private static function extractPreflightTypo3Minors(): array
    {
        $preflight = (string) file_get_contents(self::rootPath('Tests/preflight.php'));
        self::assertMatchesRegularExpression('/\$versions\s*=\s*\[(.*?)\];/s', $preflight);
        preg_match('/\$versions\s*=\s*\[(.*?)\];/s', $preflight, $versionList);
        preg_match_all('/[\'\"](\d+\.\d+)[\'\"]/', $versionList[1], $matches);
        return self::sortUnique($matches[1]);
    }

    private static function extractWorkflowTypo3Minors(): array
    {
        $workflow = (string) file_get_contents(self::rootPath('.github/workflows/build.yml'));
        preg_match_all('/typo3:\s*[\'\"]?\^?(\d+\.\d+)[\'\"]?/', $workflow, $matches);
        return self::sortUnique($matches[1]);
    }

    private static function extractWorkflowBranches(string $event): array
    {
        $workflow = (string) file_get_contents(self::rootPath('.github/workflows/build.yml'));
        $branchListPattern = '/' . preg_quote($event, '/') . ':\s*\n\s*branches:\s*\[(.*)\]\s*$/m';
        self::assertMatchesRegularExpression($branchListPattern, $workflow);
        preg_match($branchListPattern, $workflow, $matches);
        $branches = array_map(
            static fn (string $branch): string => trim($branch, " \t\n\r\0\x0B'\""),
            explode(',', $matches[1])
        );
        return self::sortUnique(array_filter($branches));
    }

    private static function sortUnique(array $values): array
    {
        $values = array_values(array_unique($values));
        sort($values, SORT_NATURAL);
        return $values;
    }

    private static function rootPath(string $path): string
    {
        return dirname(__DIR__, 3) . '/' . $path;
    }
}
