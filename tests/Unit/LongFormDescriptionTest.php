<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LongFormDescriptionTest extends TestCase
{
    public function test_history_builder_produces_structured_long_form_content(): void
    {
        $build = require dirname(__DIR__, 2).'/database/seeders/data/build_long_form_description.php';
        $text = $build(
            'Test Monument',
            'A representative Georgian monument with evidence from architecture, inscriptions, archaeology, and historical scholarship.',
            'artifact',
            'Medieval period',
            'Georgia',
            'Architecture',
        );

        preg_match_all('/[\\p{L}\\p{N}’-]+/u', $text, $words);
        $this->assertGreaterThanOrEqual(700, count($words[0]));
        $this->assertStringContainsString('OVERVIEW', $text);
        $this->assertStringContainsString('HISTORICAL SETTING', $text);
        $this->assertStringContainsString('EVIDENCE AND SOURCES', $text);
        $this->assertStringContainsString('QUESTIONS FOR FURTHER STUDY', $text);
    }
}
