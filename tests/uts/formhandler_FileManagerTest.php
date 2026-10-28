<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The connector of the file manager in src/FHTML has no login: it must be off
 * unless the web server enables it, and it must not leave the document root.
 */
final class formhandler_FileManagerTest extends FormhandlerTestCase
{
    /**
     * load the connector config like connector.php does and return it
     */
    private function loadConfig(?string $serverPath, ?string $enabled): array
    {
        if ($serverPath !== null)
        {
            $_GET['ServerPath'] = $serverPath;
        }
        putenv($enabled === null ? 'FH_FILEMANAGER_ENABLED' : "FH_FILEMANAGER_ENABLED=$enabled");

        require FH_INCLUDE_DIR . 'FHTML/filemanager/connectors/php/config.php';

        return $GLOBALS['Config'];
    }

    public function test_offByDefault(): void
    {
        $config = $this->loadConfig('/uploads', null);

        static::assertFalse($config['Enabled']);
    }

    public function test_enabledByEnvironment(): void
    {
        $config = $this->loadConfig('/uploads', '1');

        static::assertTrue($config['Enabled']);
        static::assertSame('/uploads/', $config['UserFilesPath']);
    }

    public function test_noServerPath(): void
    {
        $config = $this->loadConfig(null, '1');

        static::assertTrue($config['Enabled']);
        static::assertSame('/', $config['UserFilesPath']);
    }

    public static function dataInvalidServerPath(): array
    {
        return [
            'parent folder'     => ['/../../etc'],
            'only dots'         => ['..'],
            'backslash'         => ['/uploads\\..\\x'],
            'drive'             => ['C:/Windows'],
            'control character' => ["/uploads\0"],
        ];
    }

    #[DataProvider('dataInvalidServerPath')]
    public function test_invalidServerPathDisables(string $serverPath): void
    {
        $config = $this->loadConfig($serverPath, '1');

        static::assertFalse($config['Enabled']);
        static::assertSame('/', $config['UserFilesPath']);
    }

    public function test_deleteScriptIsGone(): void
    {
        // it deleted any file below the document root, without a path check
        static::assertFileDoesNotExist(FH_INCLUDE_DIR . 'FHTML/filemanager/connectors/php/delete.php');
    }
}
