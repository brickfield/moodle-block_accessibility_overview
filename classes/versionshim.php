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
 * Version shim class to run functions from either rel1 or rel2 depending on installed version.
 *
 * @package    block_accessibility_overview
 * @copyright  2026 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class versionshim {
    /**
     * Determine if we're running rel2.
     *
     * @return bool True if we're running rel2, false if we're running rel1.
     */
    protected static function bfplus_is_rel2(): bool {
        $plugin = \core_plugin_manager::instance()->get_plugin_info('tool_bfplus');
        return ($plugin->versiondisk >= 2025081100) ? true : false;
    }

    /**
     * Determine whether any version of bfplus is installed.
     *
     * @return bool True if bfplus is installed (any version), false if not.
     */
    public static function bfplus_is_installed(): bool {
        return (\core_plugin_manager::instance()->get_plugin_info('tool_bfplus') === null) ? false : true;
    }

    /**
     * Version-aware wrapper for brickfieldconnect::site_is_registered().
     *
     * @return bool True if this site is registered with the Enterprise toolkit.
     */
    public static function bfplus_site_is_registered(): bool {
        if (static::bfplus_is_installed() != true) {
            return false;
        }

        if (static::bfplus_is_rel2()) {
            // Rel2.
            return \tool_bfplus\local\authorization\brickfieldconnect::site_is_registered();
        } else {
            // Rel1.
            return \tool_bfplus\brickfieldconnect::site_is_registered();
        }
    }

    /**
     * Version-aware wrapper for accessibility::is_accessibility_enabled().
     *
     * @return bool True if the Enterprise toolkit's accessibility checking is enabled.
     */
    public static function bfplus_is_accessibility_enabled(): bool {
        if (static::bfplus_is_installed() != true) {
            return false;
        }

        if (static::bfplus_is_rel2()) {
            return \tool_bfplus\local\contentprovider\accessibility::is_accessibility_enabled();
        } else {
            return \tool_bfplus\accessibility::is_accessibility_enabled();
        }
    }

    /**
     * Version-aware wrapper for {sitedata|coursedata}::get_total_courses_checked().
     *
     * @return int Total number of courses checked by the Enterprise toolkit.
     */
    public static function bfplus_get_total_courses_checked(): int {
        if (static::bfplus_is_installed() != true) {
            return 0;
        }

        if (static::bfplus_is_rel2()) {
            return \tool_bfplus\local\contentprovider\coursedata::get_total_courses_checked();
        } else {
            return \tool_bfplus\sitedata::get_total_courses_checked();
        }
    }

    /**
     * Version-aware wrapper for authorizer::is_authorized().
     *
     * @return bool True if the Enterprise toolkit is authorized for this site.
     */
    public static function bfplus_is_authorized(): bool {
        if (static::bfplus_is_installed() != true) {
            return false;
        }

        if (static::bfplus_is_rel2()) {
            return \tool_bfplus\local\authorization\authorizer::is_authorized();
        } else {
            return \tool_bfplus\authorizer::is_authorized();
        }
    }

    /**
     * Determine whether any version of bfplus is installed.
     *
     * @return bool True if bfplus is installed (any version), false if not.
     */
    public static function bfaltformat_is_installed(): bool {
        return (\core_plugin_manager::instance()->get_plugin_info('local_bfaltformat') === null) ? false : true;
    }

    /**
     * Version-aware wrapper for \local_bfaltformat\authorizer::setting_enabled().
     *
     * @return bool True if the bfaltformat setting is enabled.
     */
    public static function bfaltformat_setting_enabled(): bool {
        if (static::bfaltformat_is_installed() != true) {
            return false;
        }
        return \local_bfaltformat\authorizer::setting_enabled();
    }

    /**
     * Version-aware wrapper for \local_bfaltformat\sensusaccess::validated().
     *
     * @return bool True if the SensusAccess registration has been validated.
     */
    public static function bfaltformat_sensusaccess_validated(): bool {
        if (static::bfaltformat_is_installed() != true) {
            return false;
        }
        return \local_bfaltformat\sensusaccess::validated();
    }
}
