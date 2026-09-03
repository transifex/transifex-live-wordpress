<?php

include_once __DIR__ .'/BaseTestCase.php';

class NormalizeRewriteOptionsTest extends BaseTestCase
{

    protected function setUp(): void
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/transifex-live-integration-defaults.php';
        include_once './includes/admin/transifex-live-integration-admin-util.php';
    }

    public function testEveryKnownOptionGetsAnExplicitValue()
    {
        // A browser leaves unticked checkboxes out of the submission, so this
        // is the whole payload when only two of the options are enabled.
        $submitted = [
        'add_rewrites_page' => '1',
        'add_rewrites_root' => '1',
        ];

        $result = Transifex_Live_Integration_Admin_Util::normalize_rewrite_options($submitted);

        $this->assertEquals(
            array_keys(Transifex_Live_Integration_Defaults::options_values()),
            array_keys($result)
        );
        $this->assertEquals(1, $result['add_rewrites_page']);
        $this->assertEquals(1, $result['add_rewrites_root']);
        $this->assertEquals(0, $result['add_rewrites_post']);
        // This one defaults to on, so storing an explicit 0 is what allows the
        // front end to tell "switched off" apart from "never saved" instead of
        // falling back to the default.
        $this->assertEquals(0, $result['add_rewrites_reverse_template_links']);
    }

    public function testUnknownKeysAreDropped()
    {
        $result = Transifex_Live_Integration_Admin_Util::normalize_rewrite_options([
        'add_rewrites_page' => '1',
        'not_an_option' => '1',
        ]);

        $this->assertArrayNotHasKey('not_an_option', $result);
        $this->assertEquals(1, $result['add_rewrites_page']);
    }

    public function testEmptySubmissionDisablesEveryOption()
    {
        $result = Transifex_Live_Integration_Admin_Util::normalize_rewrite_options([]);

        $expected = array_fill_keys(
            array_keys(Transifex_Live_Integration_Defaults::options_values()),
            0
        );
        $this->assertEquals($expected, $result);
    }

}
