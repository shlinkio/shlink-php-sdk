<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Model;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;

class TagRenamingTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideNames
     */
    public function properPayloadIsGenerated(string $oldName, string $newName, array $expectedPayload): void
    {
        $renaming = TagRenaming::fromOldNameAndNewName($oldName, $newName);

        self::assertEquals($expectedPayload, $renaming->jsonSerialize());
    }

    public function provideNames(): iterable
    {
        yield ['', '', ['oldName' => '', 'newName' => '']];
        yield ['foo', 'bar', ['oldName' => 'foo', 'newName' => 'bar']];
        yield ['old_name', 'new_name', ['oldName' => 'old_name', 'newName' => 'new_name']];
    }
}
