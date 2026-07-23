<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_accessibility_overview;

/**
 * Tests for the versionshim class.
 *
 * @package    block_accessibility_overview
 * @copyright  2026 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_accessibility_overview\versionshim
 */
final class versionshim_test extends \advanced_testcase {
    /**
     * Whether tool_bfplus is installed varies by test site, so this checks the wrapper agrees
     * with core_plugin_manager rather than assuming a fixed install state.
     *
     * @covers \block_accessibility_overview\versionshim::bfplus_is_installed
     */
    public function test_bfplus_is_installed_matches_plugin_manager(): void {
        $this->resetAfterTest();

        $expected = \core_plugin_manager::instance()->get_plugin_info('tool_bfplus') !== null;
        $this->assertSame($expected, versionshim::bfplus_is_installed());
    }

    /**
     * Whether local_bfaltformat is installed varies by test site, so this checks the wrapper
     * agrees with core_plugin_manager rather than assuming a fixed install state.
     *
     * @covers \block_accessibility_overview\versionshim::bfaltformat_is_installed
     */
    public function test_bfaltformat_is_installed_matches_plugin_manager(): void {
        $this->resetAfterTest();

        $expected = \core_plugin_manager::instance()->get_plugin_info('local_bfaltformat') !== null;
        $this->assertSame($expected, versionshim::bfaltformat_is_installed());
    }

    /**
     * Regression guard on the method whose docblock was found stale (claimed bool, returns int)
     * during the bf-phpdoc pass.
     *
     * @covers \block_accessibility_overview\versionshim::bfplus_get_total_courses_checked
     */
    public function test_bfplus_get_total_courses_checked_returns_nonnegative_int(): void {
        $this->resetAfterTest();

        $result = versionshim::bfplus_get_total_courses_checked();

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    /**
     * Each tool_bfplus wrapper runs cleanly and returns the documented bool type.
     *
     * @dataProvider bfplus_bool_methods_provider
     * @param string $method the static versionshim method name to invoke
     * @covers \block_accessibility_overview\versionshim::bfplus_site_is_registered
     * @covers \block_accessibility_overview\versionshim::bfplus_is_accessibility_enabled
     * @covers \block_accessibility_overview\versionshim::bfplus_is_authorized
     */
    public function test_bfplus_wrapper_returns_bool(string $method): void {
        $this->resetAfterTest();

        $this->assertIsBool(versionshim::$method());
    }

    /**
     * Data provider for tool_bfplus wrapper methods that return bool.
     *
     * @return array
     */
    public static function bfplus_bool_methods_provider(): array {
        return [
            'site_is_registered' => ['bfplus_site_is_registered'],
            'is_accessibility_enabled' => ['bfplus_is_accessibility_enabled'],
            'is_authorized' => ['bfplus_is_authorized'],
        ];
    }

    /**
     * Each local_bfaltformat wrapper runs cleanly and returns the documented bool type.
     *
     * @dataProvider bfaltformat_bool_methods_provider
     * @param string $method the static versionshim method name to invoke
     * @covers \block_accessibility_overview\versionshim::bfaltformat_setting_enabled
     * @covers \block_accessibility_overview\versionshim::bfaltformat_sensusaccess_validated
     */
    public function test_bfaltformat_wrapper_returns_bool(string $method): void {
        $this->resetAfterTest();

        $this->assertIsBool(versionshim::$method());
    }

    /**
     * Data provider for local_bfaltformat wrapper methods that return bool.
     *
     * @return array
     */
    public static function bfaltformat_bool_methods_provider(): array {
        return [
            'setting_enabled' => ['bfaltformat_setting_enabled'],
            'sensusaccess_validated' => ['bfaltformat_sensusaccess_validated'],
        ];
    }
}
