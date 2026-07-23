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

namespace block_accessibility_overview\privacy;

/**
 * Tests for the null privacy provider.
 *
 * @package    block_accessibility_overview
 * @copyright  2026 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_accessibility_overview\privacy\provider
 */
final class provider_test extends \advanced_testcase {
    /**
     * The declared reason key must resolve to a real, non-empty language string.
     *
     * @covers \block_accessibility_overview\privacy\provider::get_reason
     */
    public function test_get_reason_resolves_to_language_string(): void {
        $this->resetAfterTest();

        $reason = provider::get_reason();

        $this->assertSame('privacy:nullproviderreason', $reason);
        $this->assertTrue(get_string_manager()->string_exists($reason, 'block_accessibility_overview'));

        $string = get_string($reason, 'block_accessibility_overview');
        $this->assertIsString($string);
        $this->assertNotSame('', $string);
    }
}
