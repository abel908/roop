<?php

namespace Tests\Unit;

use App\Support\Html;
use App\Support\Locales;
use Tests\TestCase;

class SupportTest extends TestCase
{
    public function test_html_sanitizer_keeps_editorial_tags_only(): void
    {
        $clean = Html::clean('<p onclick="x()">Hello <strong>world</strong><script>alert(1)</script> <a href="javascript:alert(1)">bad</a> <a href="https://ok.test">ok</a></p><iframe src="x"></iframe>');

        $this->assertStringContainsString('<strong>world</strong>', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('iframe', $clean);
        $this->assertStringContainsString('href="https://ok.test"', $clean);
    }

    public function test_browser_language_detection(): void
    {
        $this->assertSame('zh', Locales::fromBrowser('zh-CN,zh;q=0.9,en;q=0.8'));
        $this->assertSame('fr', Locales::fromBrowser('fr-FR,fr;q=0.9'));
        $this->assertNull(Locales::fromBrowser('de-DE'));
    }
}
