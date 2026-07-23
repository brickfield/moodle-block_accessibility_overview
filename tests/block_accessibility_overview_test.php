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
 * Tests for the accessibility_overview block class.
 *
 * @package    block_accessibility_overview
 * @copyright  2026 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_accessibility_overview
 */
final class block_accessibility_overview_test extends \advanced_testcase {
    /**
     * Invokes a private or protected method on the given object.
     *
     * @param object $object
     * @param string $method
     * @param array $args
     * @return mixed
     */
    private function invoke_private_method(object $object, string $method, array $args = []) {
        $reflection = new \ReflectionMethod($object, $method);
        return $reflection->invokeArgs($object, $args);
    }

    /**
     * Creates a course with an accessibility_overview block instance loaded onto $PAGE.
     *
     * @return array [\block_accessibility_overview $block, \stdClass $course]
     */
    private function create_block_on_course(): array {
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $record = $this->getDataGenerator()->create_block(
            'accessibility_overview',
            ['parentcontextid' => $coursecontext->id]
        );

        $GLOBALS['PAGE']->set_course($course);

        return [block_instance('accessibility_overview', $record), $course];
    }

    /**
     * A user without the view capability (e.g. a student) gets no block content.
     *
     * @covers \block_accessibility_overview::get_content
     */
    public function test_get_content_denies_user_without_capability(): void {
        $this->resetAfterTest();

        [$block, $course] = $this->create_block_on_course();
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);

        $this->assertNull($block->get_content());
    }

    /**
     * The guest user never gets block content, regardless of capability.
     *
     * @covers \block_accessibility_overview::get_content
     */
    public function test_get_content_denies_guest_user(): void {
        $this->resetAfterTest();

        [$block] = $this->create_block_on_course();
        $this->setGuestUser();

        $this->assertNull($block->get_content());
    }

    /**
     * A user with the view capability (e.g. an editing teacher) gets rendered content.
     *
     * @covers \block_accessibility_overview::get_content
     */
    public function test_get_content_renders_for_capable_user(): void {
        $this->resetAfterTest();

        [$block, $course] = $this->create_block_on_course();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);

        $content = $block->get_content();

        $this->assertInstanceOf(\stdClass::class, $content);
        $this->assertIsString($content->text);
        $this->assertNotSame('', $content->text);
    }

    /**
     * Calling get_content() twice returns the same cached object, not a rebuilt one.
     *
     * @covers \block_accessibility_overview::get_content
     */
    public function test_get_content_caches_result(): void {
        $this->resetAfterTest();

        [$block, $course] = $this->create_block_on_course();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);

        $first = $block->get_content();
        $second = $block->get_content();

        $this->assertSame($first, $second);
    }

    /**
     * Both courses-reviewed methods return a non-negative int, regardless of sibling-plugin state.
     *
     * @dataProvider courses_reviewed_methods_provider
     * @param string $method the private courses-reviewed method name to invoke
     * @covers \block_accessibility_overview::get_starter_courses_reviewed
     * @covers \block_accessibility_overview::get_enterprise_courses_reviewed
     */
    public function test_courses_reviewed_method_returns_nonnegative_int(string $method): void {
        $this->resetAfterTest();

        $block = new \block_accessibility_overview();
        $result = $this->invoke_private_method($block, $method);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    /**
     * Data provider for the two courses-reviewed count methods.
     *
     * @return array
     */
    public static function courses_reviewed_methods_provider(): array {
        return [
            'starter (tool_brickfield)' => ['get_starter_courses_reviewed'],
            'enterprise (tool_bfplus)' => ['get_enterprise_courses_reviewed'],
        ];
    }

    /**
     * These status methods each depend on a sibling plugin that may or may not be present in
     * the site running the tests. Rather than assuming a fixed install state, this checks
     * whether the relevant plugin is actually installed and asserts the branch that implies:
     * the "not installed" string when it's absent, and a different non-empty string when present.
     *
     * @dataProvider status_methods_provider
     * @param string $method the private status method name to invoke
     * @param string $component the Frankenstyle name of the sibling plugin this method checks for
     * @covers \block_accessibility_overview::get_starter_status
     * @covers \block_accessibility_overview::get_accessreview_status
     * @covers \block_accessibility_overview::get_enterprise_status
     * @covers \block_accessibility_overview::get_manager_status
     * @covers \block_accessibility_overview::get_altformat_status
     */
    public function test_status_method_returns_nonempty_string(string $method, string $component): void {
        $this->resetAfterTest();

        $block = new \block_accessibility_overview();
        $result = $this->invoke_private_method($block, $method);

        $this->assertIsString($result);
        $this->assertNotSame('', $result);

        $notinstalled = get_string('notinstalled', 'block_accessibility_overview');
        $isinstalled = \core_plugin_manager::instance()->get_plugin_info($component) !== null;
        if ($isinstalled) {
            $this->assertNotSame($notinstalled, $result);
        } else {
            $this->assertSame($notinstalled, $result);
        }
    }

    /**
     * Data provider for status methods and the sibling plugin component each one checks for.
     *
     * @return array
     */
    public static function status_methods_provider(): array {
        return [
            'starter (tool_brickfield)' => ['get_starter_status', 'tool_brickfield'],
            'accessreview (block_accessreview)' => ['get_accessreview_status', 'block_accessreview'],
            'enterprise (tool_bfplus)' => ['get_enterprise_status', 'tool_bfplus'],
            'manager (block_bfmanager)' => ['get_manager_status', 'block_bfmanager'],
            'altformat (local_bfaltformat)' => ['get_altformat_status', 'local_bfaltformat'],
        ];
    }

    /**
     * The block is only applicable to course and site pages, never inside an activity or /my.
     *
     * @covers \block_accessibility_overview::applicable_formats
     */
    public function test_applicable_formats_returns_expected_map(): void {
        $this->resetAfterTest();

        $block = new \block_accessibility_overview();

        $this->assertSame([
            'course-view' => true,
            'site' => true,
            'mod' => false,
            'my' => false,
        ], $block->applicable_formats());
    }
}
