<?php

include_once __DIR__ .'/BaseTestCase.php';

class CalcEnablePickerTest extends BaseTestCase
{

    private $data;

    protected function setUp(): void
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/admin/transifex-live-integration-admin-util.php';

        // Mirrors a manifest where the two environments disagree, which is the
        // case that used to report the production picker on a staging site.
        $mixed = json_encode([
        'staging' => [ 'picker' => 'bottom-left' ],
        'production' => [ 'picker' => 'no-picker' ],
        ]);
        $both_off = json_encode([
        'staging' => [ 'picker' => 'no-picker' ],
        'production' => [ 'picker' => 'no-picker' ],
        ]);
        $both_on = json_encode([
        'staging' => [ 'picker' => 'bottom-left' ],
        'production' => [ 'picker' => 'bottom-right' ],
        ]);

        $this->data = [[
        'settings' => $mixed,
        'enable_staging' => true,
        'result' => true
        ],
        [
        'settings' => $mixed,
        'enable_staging' => false,
        'result' => false
        ],
        [
        'settings' => $both_on,
        'enable_staging' => false,
        'result' => true
        ],
        [
        'settings' => $both_off,
        'enable_staging' => true,
        'result' => false
        ],
        ];
        // A payload that carries no usable picker setting leaves the picker on
        $empty_settings = [ '', 'not json', '{}', json_encode([ 'production' => [] ]) ];
        foreach ($empty_settings as $s) {
            array_push($this->data, ['settings' => $s, 'enable_staging' => false, 'result' => true ]);
            array_push($this->data, ['settings' => $s, 'enable_staging' => true, 'result' => true ]);
        }
    }

    public function testMe()
    {
        foreach ($this->data as $d) {
            $result = Transifex_Live_Integration_Admin_Util::calc_enable_picker($d['settings'], $d['enable_staging']);

            $this->assertEquals($d['result'], $result);
        }
    }

}
