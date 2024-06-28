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

use tool_brickfield\accessibility as starter;
use tool_brickfield\registration;
use tool_bfplus\accessibility as enterprise;
use tool_bfplus\brickfieldconnect;
use tool_bfplus\sitedata;
use tool_bfplus\authorizer;

/**
 * Definition of the accessibility_overview block.
 *
 * @package     block_accessibility_overview
 * @author      Michael Pound (michael@brickfieldlabs.ie)
 * @copyright   2024 Brickfield Education Labs, www.brickfield.ie
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_accessibility_overview extends block_base {
    /** @var string */
    const STARTER_DOCS_URL = 'https://brickfield.ie/actoov-starterdocs';
    /** @var string */
    const ENTERPRISE_DOCS_URL = 'https://brickfield.ie/actoov-enterprisedocs';
    /** @var string */
    const ENTERPRISE_INFO_URL = 'https://brickfield.ie/actoov-enterpriseinfo';
    /** @var string */
    const BOOK_DEMO_URL = 'https://brickfield.ie/actoov-freedemo';
    /** @var string */
    const CONTACT_FORM_URL = 'https://brickfield.ie/actoov-contact';
    /** @var string */
    const COURSE_OVERVIEW_URL = 'https://brickfield.ie/actoov-training';
    /** @var string */
    const LETTER_SIGNUP_URL = 'https://brickfield.ie/actoov-newsletter';
    /** @var string */
    const LINKEDIN_URL = 'https://brickfield.ie/actoov-linkedin';
    /** @var string */
    const TWITTER_URL = 'https://brickfield.ie/actoov-twitter';
    /** @var string */
    const FACEBOOK_URL = 'https://brickfield.ie/actoov-facebook';
    /** @var string */
    const INSTAGRAM_URL = 'https://brickfield.ie/actoov-instagram';

    /**
     * Sets the block title.
     */
    public function init(): void {
        $this->title = get_string('pluginname', 'block_accessibility_overview');
    }

    /**
     * Creates the block's main content
     *
     * @return stdClass|null
     */
    public function get_content(): ?stdClass {
        global $COURSE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);

        // If the user does not have permission to view the block, do nothing.
        if (!isloggedin() || isguestuser() || !has_capability('block/accessibility_overview:view', $context)) {
            return null;
        }
        $this->content = new stdClass;
        $this->content->text = $OUTPUT->render_from_template('block_accessibility_overview/block_content', $this->get_data());

        return $this->content;
    }

    /**
     * Helper function to create an entry array element.
     *
     * @param string $label
     * @param string $value
     * @return array
     */
    private function get_entry(string $label, string $value = null): array {
        return [
            'label' => $label,
            'value' => $value,
            'hasvalue' => $value !== null,
        ];
    }

    /**
     * Get the data.
     *
     * @return array
     */
    public function get_data(): array {
        $sections = [];

        // Add the starter section.
        $startertoolkit = get_string('startertoolkit', 'block_accessibility_overview');
        $reviewblock = get_string('reviewblock', 'block_accessibility_overview');
        $coursesreviewed = get_string('coursesreviewed', 'block_accessibility_overview');
        $starterdocs = get_string('starterdocs', 'block_accessibility_overview');

        $sections[] = [
            'title' => get_string('brickfieldstarter', 'block_accessibility_overview'),
            'entries' => [
                $this->get_entry($startertoolkit, $this->get_starter_status()),
                $this->get_entry($reviewblock, $this->get_accessreview_status()),
                $this->get_entry($coursesreviewed, $this->get_starter_courses_reviewed()),
                $this->get_entry(html_writer::link(self::STARTER_DOCS_URL, $starterdocs)),
            ],
        ];

        // Add the enterprise section.
        $enterprisetoolkit = get_string('enterprisetoolkit', 'block_accessibility_overview');
        $reviewplusblock = get_string('reviewplusblock', 'block_accessibility_overview');
        $enterprisedocs = get_string('enterprisedocs', 'block_accessibility_overview');
        $enterpriseinfo = get_string('enterpriseinfo', 'block_accessibility_overview');

        $sections[] = [
            'title' => get_string('brickfieldenterprise', 'block_accessibility_overview'),
            'entries' => [
                $this->get_entry($enterprisetoolkit, $this->get_enterprise_status()),
                $this->get_entry($reviewplusblock, $this->get_manager_status()),
                $this->get_entry($coursesreviewed, $this->get_enterprise_courses_reviewed()),
                $this->get_entry(html_writer::link(self::ENTERPRISE_DOCS_URL, $enterprisedocs)),
                $this->get_entry(html_writer::link(self::ENTERPRISE_INFO_URL, $enterpriseinfo)),
            ],
        ];

        // Add the resources section.
        $training = get_string('training', 'block_accessibility_overview');
        $demo = get_string('demo', 'block_accessibility_overview');
        $newsletter = get_string('newsletter', 'block_accessibility_overview');
        $coursecurriculum = get_string('coursecurriculum', 'block_accessibility_overview');
        $bookmeeting = get_string('bookmeeting', 'block_accessibility_overview');
        $lettersignup = get_string('lettersignup', 'block_accessibility_overview');

        $sections[] = [
            'title' => get_string('brickfieldservices', 'block_accessibility_overview'),
            'entries' => [
                $this->get_entry($training, html_writer::link(self::COURSE_OVERVIEW_URL, $coursecurriculum)),
                $this->get_entry($demo, html_writer::link(self::BOOK_DEMO_URL, $bookmeeting)),
                $this->get_entry($newsletter, html_writer::link(self::LETTER_SIGNUP_URL, $lettersignup)),
            ],
        ];

        // Add the social links.
        $socials = [
            [
                'url' => self::LINKEDIN_URL,
                'name' => get_string('linkedin', 'block_accessibility_overview'),
                'icon' => 'fa fa-linkedin-square',
            ],
            [
                'url' => self::FACEBOOK_URL,
                'name' => get_string('facebook', 'block_accessibility_overview'),
                'icon' => 'fa fa-facebook-square',
            ],
            [
                'url' => self::TWITTER_URL,
                'name' => get_string('twitter', 'block_accessibility_overview'),
                'icon' => 'fa fa-twitter',
            ],
            [
                'url' => self::INSTAGRAM_URL,
                'name' => get_string('instagram', 'block_accessibility_overview'),
                'icon' => 'fa fa-instagram',
            ],
        ];

        return [
            "sections" => $sections,
            "socials" => $socials,
        ];
    }

    /**
     * Get starter toolkit status.
     *
     * @return string
     */
    private function get_starter_status(): string {
        if (\core_plugin_manager::instance()->get_plugin_info('tool_brickfield') === null) {
            return get_string('notinstalled', 'block_accessibility_overview');
        }
        if (!starter::is_accessibility_enabled()) {
            $disabledlink = new \moodle_url('admin/settings.php?section=optionalsubsystems');
            return html_writer::link($disabledlink, get_string('disabled', 'block_accessibility_overview'));
        }
        if ((new registration())->toolkit_is_active()) {
            return get_string('registered', 'block_accessibility_overview');
        }
        $registerurl = new \moodle_url('/admin/tool/brickfield/registration.php');
        return html_writer::link($registerurl, get_string('unregistered', 'block_accessibility_overview'));
    }

    /**
     * Get starter block accessreview status.
     *
     * @return string
     */
    private function get_accessreview_status(): string {
        if (\core_plugin_manager::instance()->get_plugin_info('block_accessreview') === null) {
            return get_string('notinstalled', 'block_accessibility_overview');
        }
        if ((\core_plugin_manager::instance()->get_plugin_info('block_accessreview'))->is_enabled()) {
            return get_string('enabled', 'block_accessibility_overview');
        }
        $disabledurl = new \moodle_url('admin/blocks.php');
        return html_writer::link($disabledurl, get_string('disabled', 'block_accessibility_overview'));
    }

    /**
     * Get courses reviewed by starter toolkit.
     *
     * @return int
     */
    private function get_starter_courses_reviewed(): int {
        global $DB;
        if ((new registration())->toolkit_is_active()) {
            return $DB->count_records_select('tool_brickfield_summary', '', [], 'COUNT(DISTINCT courseid)');
        }
        return 0;
    }

    /**
     * Get enterprise toolkit bfplus status.
     *
     * @return string
     */
    private function get_enterprise_status(): string {
        if (\core_plugin_manager::instance()->get_plugin_info('tool_bfplus') === null) {
            return get_string('notinstalled', 'block_accessibility_overview');
        }
        if (!enterprise::is_accessibility_enabled()) {
            $disabledurl = new \moodle_url('admin/settings.php?section=optionalsubsystems');
            return html_writer::link($disabledurl, get_string('disabled', 'block_accessibility_overview'));
        }
        if (brickfieldconnect::site_is_registered()) {
            return get_string('registered', 'block_accessibility_overview');
        }
        $registerurl = new \moodle_url('/admin/tool/bfplus/registration.php');
        return html_writer::link($registerurl, get_string('unregistered', 'block_accessibility_overview'));
    }

    /**
     * Get enterprise block bfmanager status.
     *
     * @return string
     */
    private function get_manager_status(): string {
        if (\core_plugin_manager::instance()->get_plugin_info('block_bfmanager') === null) {
            return get_string('notinstalled', 'block_accessibility_overview');
        }
        if ((\core_plugin_manager::instance()->get_plugin_info('block_bfmanager'))->is_enabled()) {
            return get_string('enabled', 'block_accessibility_overview');
        }
        $disabledurl = new \moodle_url('admin/blocks.php');
        return html_writer::link($disabledurl, get_string('disabled', 'block_accessibility_overview'));
    }

    /**
     * Get courses reviewed by enterprise toolkit.
     *
     * @return int
     */
    private function get_enterprise_courses_reviewed(): int {
        if (\core_plugin_manager::instance()->get_plugin_info('tool_bfplus') !== null) {
            if (authorizer::is_authorized()) {
                return sitedata::get_total_courses_checked();
            }
        }
        return 0;
    }

    /**
     * Defines where the block can be added.
     *
     * @return array
     */
    public function applicable_formats(): array {
        return [
            'course-view' => true,
            'site' => true,
            'mod' => false,
            'my' => false,
        ];
    }

    /**
     * Allow multiple instances.
     *
     * @return bool
     */
    public function instance_allow_multiple(): bool {
        return false;
    }
}
