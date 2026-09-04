<?php

include_once __DIR__ .'/BaseTestCase.php';

class RewriteOptionsValuesTest extends BaseTestCase
{

    protected function setUp(): void
    {
        include_once './includes/transifex-live-integration-defaults.php';
    }

    private function optionKeys()
    {
        return array_keys(Transifex_Live_Integration_Defaults::options_values());
    }

    public function testFillGivesEveryKnownOptionAnExplicitValue()
    {
        // A browser leaves unticked checkboxes out of the submission, so this is
        // the whole payload when only two of the options are enabled.
        $result = Transifex_Live_Integration_Defaults::fill_options_values([
        'add_rewrites_page' => '1',
        'add_rewrites_root' => '1',
        ]);

        $this->assertEquals($this->optionKeys(), array_keys($result));
        $this->assertEquals(1, $result['add_rewrites_page']);
        $this->assertEquals(1, $result['add_rewrites_root']);
        $this->assertEquals(0, $result['add_rewrites_post']);
        // Storing an explicit 0 for an option that defaults to on is what lets a
        // later read tell "switched off" apart from "never saved".
        $this->assertEquals(0, $result['add_rewrites_reverse_template_links']);
    }

    public function testFillDropsUnknownKeys()
    {
        $result = Transifex_Live_Integration_Defaults::fill_options_values([
        'add_rewrites_page' => '1',
        'not_an_option' => '1',
        ]);

        $this->assertArrayNotHasKey('not_an_option', $result);
        $this->assertEquals(1, $result['add_rewrites_page']);
    }

    public function testFillTreatsAnEmptySubmissionAsEverythingOff()
    {
        // Every box unticked, which must not be read as "use the defaults".
        $this->assertEquals(
            array_fill_keys($this->optionKeys(), 0),
            Transifex_Live_Integration_Defaults::fill_options_values([])
        );
    }

    public function testResolveFallsBackToDefaultsWhenNothingWasEverSaved()
    {
        foreach ([false, null, []] as $nothing_stored) {
            $this->assertEquals(
                Transifex_Live_Integration_Defaults::options_values(),
                Transifex_Live_Integration_Defaults::resolve_options_values($nothing_stored),
                'a site that never saved its options should get the defaults'
            );
        }
    }

    public function testResolveKeepsAnUntickedOptionOff()
    {
        // The regression this guards: an install whose owner unticked "Reverse
        // Template Links" has no such key in storage. Reading that as "use the
        // default" would switch every link filter back on behind their back.
        $result = Transifex_Live_Integration_Defaults::resolve_options_values([
        'add_rewrites_post' => '1',
        ]);

        $this->assertEquals(0, $result['add_rewrites_reverse_template_links']);
        $this->assertEquals(1, $result['add_rewrites_post']);
        $this->assertEquals($this->optionKeys(), array_keys($result));
    }

    public function testResolveHonoursAnExplicitlyStoredZero()
    {
        // Saves now store explicit values, so a 0 has to read as off. The
        // isset() check this replaced could not express that.
        $stored = array_fill_keys($this->optionKeys(), 0);
        $stored['add_rewrites_page'] = 1;

        $result = Transifex_Live_Integration_Defaults::resolve_options_values($stored);

        $this->assertEquals(0, $result['add_rewrites_reverse_template_links']);
        $this->assertEquals(1, $result['add_rewrites_page']);
    }

}
