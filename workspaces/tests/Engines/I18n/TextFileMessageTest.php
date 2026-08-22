<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Unit testing — TextFileMessage class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

namespace Waystone\Workspaces\Tests\Engines\I18n;

use Waystone\Workspaces\Engines\I18n\Language;
use Waystone\Workspaces\Engines\I18n\TextFileMessage;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use Exception;

/**
 * Tests TextFileMessage class
 */
#[CoversClass(TextFileMessage::class)]
class TextFileMessageTest extends TestCase
{
    /**
     * The temporary folder containing message fixtures.
     */
    private string $folder;

    public static function setUpBeforeClass(): void
    {
        require_once(__DIR__ . '/../../../src/includes/GlobalFunctions.php');
    }

    protected function setUp(): void
    {
        $this->folder = $this->makeTempFolder();
    }

    private function makeTempFolder(): string {
        $folder = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'TextFileMessageTest-'
            . bin2hex(random_bytes(8));

        $this->assertTrue(mkdir($folder));

        return $folder;
    }

    public function testLoadsLocalizedFiles(): void
    {
        $this->writeFile('welcome-en.txt', 'Welcome');
        $this->writeFile('welcome-fr.txt', 'Bienvenue');

        //Only a simple language suffix and the .txt extension are supported.
        $this->writeFile('welcome-pt-BR.txt', 'Bem-vindo');
        $this->writeFile('welcome-de.md', 'Willkommen');
        $this->writeFile('another-nl.txt', 'Welkom');

        $message = new TextFileMessage($this->folder, 'welcome');

        $this->assertSame($this->folder, $message->folder);
        $this->assertSame('welcome', $message->filename);
        $this->assertSame([
            'en' => 'Welcome',
            'fr' => 'Bienvenue',
        ], $message->localizations);
        $this->assertSame('Welcome', (string)$message);
    }

    public function testLoadsSingleFileAsFallbackLocalization(): void
    {
        $this->writeFile('welcome.txt', 'Welcome');

        $message = new TextFileMessage($this->folder, 'welcome');

        $this->assertSame([
            Language::FALLBACK => 'Welcome',
        ], $message->localizations);
        $this->assertSame('Welcome', (string)$message);
    }

    public function testSingleFileCompletesLocalizedFilesWithoutFallback(): void
    {
        $this->writeFile('welcome-fr.txt', 'Bienvenue');
        $this->writeFile('welcome.txt', 'Welcome');

        [$message, $notices] = $this->createMessageAndCaptureNotices('welcome');

        $this->assertSame([
            'fr' => 'Bienvenue',
            Language::FALLBACK => 'Welcome',
        ], $message->localizations);
        $this->assertSame([
            'You have welcome.txt and welcome-<lang>.txt files; '
            . 'you should have one or the other, but not both',
        ], $notices);
    }

    public function testSingleFileIsIgnoredWhenFallbackLocalizationExists(): void
    {
        $this->writeFile('welcome-en.txt', 'Localized fallback');
        $this->writeFile('welcome-fr.txt', 'Bienvenue');
        $this->writeFile('welcome.txt', 'Single-file fallback');

        [$message, $notices] = $this->createMessageAndCaptureNotices('welcome');

        $this->assertSame([
            'en' => 'Localized fallback',
            'fr' => 'Bienvenue',
        ], $message->localizations);
        $this->assertSame([
            'Ignored file: welcome.txt, as welcome-en.txt already exists '
            . 'and is used for fallback purpose',
        ], $notices);
    }

    public function testThrowsWhenMessageDoesNotExist(): void
    {
        $this->writeFile('another-en.txt', 'Another message');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('TextFileMessage not found: welcome');

        new TextFileMessage($this->folder, 'welcome');
    }

    protected function tearDown(): void
    {
        foreach (scandir($this->folder) as $filename) {
            if ($filename !== '.' && $filename !== '..') {
                unlink($this->folder . DIRECTORY_SEPARATOR . $filename);
            }
        }

        rmdir($this->folder);
    }

    private function writeFile(string $filename, string $contents): void
    {
        $bytesWritten = file_put_contents(
            $this->folder . DIRECTORY_SEPARATOR . $filename,
            $contents
        );

        $this->assertSame(strlen($contents), $bytesWritten);
    }

    /**
     * @return array{TextFileMessage, string[]}
     */
    private function createMessageAndCaptureNotices(string $filename): array
    {
        $notices = [];
        set_error_handler(
            static function (int $severity, string $message) use (&$notices): bool {
                if ($severity !== E_USER_NOTICE) {
                    return false;
                }

                $notices[] = $message;
                return true;
            }
        );

        try {
            $message = new TextFileMessage($this->folder, $filename);
        } finally {
            restore_error_handler();
        }

        return [$message, $notices];
    }
}
