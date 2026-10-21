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

/**
 * Admin settings.
 *
 * @package    qbank_collapsecategories
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig && $settings) {
    $choices = [
        0 => new lang_string('openlevels_all', 'qbank_collapsecategories'),
        1 => new lang_string('openlevels_one', 'qbank_collapsecategories'),
    ];
    foreach ([2, 3, 4] as $n) {
        $choices[$n] = new lang_string('openlevels_n', 'qbank_collapsecategories', $n);
    }
    $settings->add(new admin_setting_configselect(
        'qbank_collapsecategories/openlevels',
        new lang_string('openlevels', 'qbank_collapsecategories'),
        new lang_string('openlevels_desc', 'qbank_collapsecategories'),
        1,
        $choices
    ));
    $settings->add(new admin_setting_configcheckbox(
        'qbank_collapsecategories/remember',
        new lang_string('remember', 'qbank_collapsecategories'),
        new lang_string('remember_desc', 'qbank_collapsecategories'),
        1
    ));
}
