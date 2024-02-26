<?php

namespace MauticPlugin\MauticCheckBundle\Tests\Unit\Service;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use MauticPlugin\MauticCheckBundle\Helper\FilesHelper;
use MauticPlugin\MauticCheckBundle\Service\DevilMethods;

class DevilMethodsTest extends MauticMysqlTestCase
{
    private $files;
    private $result;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->files = FilesHelper::getFiles(__DIR__.'/../../Assets/Project1/');
        $this->getResultCommand();
    }

    public function testDevilMethodEval()
    {
        $firstItem = reset($this->result);
        $this->assertSame('eval', $firstItem[0]['details']['name']);
        $this->assertSame(9, $firstItem[0]['line']);
        $this->assertStringContainsString('$result1 = eval($code);', $firstItem[0]['lineCode']);
    }

    public function testDevilMethodSystem()
    {
        $firstItem = reset($this->result);
        $this->assertSame('system', $firstItem[3]['details']['name']);
        $this->assertSame(22, $firstItem[3]['line']);
        $this->assertStringContainsString('$result    = system($code);', $firstItem[3]['lineCode']);
        $count = 0;
        foreach ($firstItem as $item) {
            if (str_contains('system', $item['details']['name'])) {
                ++$count;
            }
        }
        $this->assertSame(2, $count);
    }

    public function testDevilMethodShellExec()
    {
        $firstItem = reset($this->result);
        $this->assertSame('shell_exec', $firstItem[2]['details']['name']);
        $this->assertSame(23, $firstItem[2]['line']);
        $this->assertStringContainsString('$result1   = shell_exec($code);', $firstItem[2]['lineCode']);
    }

    public function testDevilMethodAssert()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('assert', $item[6]['details']['name']);
        $this->assertSame(25, $item[6]['line']);
        $this->assertStringContainsString('$result3   = assert($code);', $item[6]['lineCode']);
    }

    public function testDevilMethodParseStr()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('parse_str', $item[7]['details']['name']);
        $this->assertSame(26, $item[7]['line']);
        $this->assertStringContainsString('$result4   = parse_str($code, $result);', $item[7]['lineCode']);
    }

    public function testDevilMethodProcOpen()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('proc_open', $item[3]['details']['name']);
        $this->assertSame(23, $item[3]['line']);
        $this->assertStringContainsString('$result1   = proc_open($code);', $item[3]['lineCode']);
    }

    public function testDevilMethodUnlink()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('unlink', $item[9]['details']['name']);
        $this->assertSame(35, $item[9]['line']);
        $this->assertStringContainsString('$result1   = unlink($code);', $item[9]['lineCode']);
    }

    public function testDevilMethodPassthru()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('passthru', $item[2]['details']['name']);
        $this->assertSame(22, $item[2]['line']);
        $this->assertStringContainsString('$result    = passthru($code);', $item[2]['lineCode']);
    }

    public function testDevilMethodExtract()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('extract', $item[8]['details']['name']);
        $this->assertSame(29, $item[8]['line']);
        $this->assertStringContainsString('$result7   = extract($code, $result, \'aaaa\');', $item[8]['lineCode']);
    }

    public function testDevilMethodUnserialize()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('unserialize', $item[5]['details']['name']);
        $this->assertSame(34, $item[5]['line']);
        $this->assertStringContainsString('$result    = unserialize($code);', $item[5]['lineCode']);
    }

    public function testDevilMethodRmdir()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('rmdir', $item[10]['details']['name']);
        $this->assertSame(36, $item[10]['line']);
        $this->assertStringContainsString('$result2   = rmdir($code);', $item[10]['lineCode']);
    }

    public function testDevilMethodReadfile()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('readfile', $item[14]['details']['name']);
        $this->assertSame(37, $item[14]['line']);
        $this->assertStringContainsString('$result3   = readfile($code);', $item[14]['lineCode']);
    }

    public function testDevilMethodFileGetContents()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('file_get_contents', $item[13]['details']['name']);
        $this->assertSame(38, $item[13]['line']);
        $this->assertStringContainsString('$result4   = file_get_contents($code);', $item[13]['lineCode']);
    }

    public function testDevilMethodFilePutContents()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('file_put_contents', $item[12]['details']['name']);
        $this->assertSame(39, $item[12]['line']);
        $this->assertStringContainsString('$result5   = file_put_contents($code, $result);', $item[12]['lineCode']);
    }

    public function testDevilMethodFile()
    {
        $item = $this->extractFile('ClassExample2Controller.php');
        $this->assertSame('file', $item[15]['details']['name']);
        $this->assertSame(40, $item[15]['line']);
        $this->assertStringContainsString('$result6   = file($code);', $item[15]['lineCode']);
    }

    private function getResultCommand(): void
    {
        $devilMethods = new DevilMethods(new FilesHelper());
        $result       = [];
        foreach ($this->files['files'] as $filePath) {
            $file              = file_get_contents($filePath);
            $result[$filePath] = $devilMethods->check($file);
        }
        $this->result = $result;
    }

    private function extractFile($file)
    {
        foreach ($this->result as $key=>$resultFile) {
            if (str_contains($key, $file)) {
                return $resultFile;
            }
        }
    }
}