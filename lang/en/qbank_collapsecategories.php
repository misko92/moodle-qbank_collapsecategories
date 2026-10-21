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
 * English strings.
 *
 * @package    qbank_collapsecategories
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['openlevels'] = 'Levels shown expanded';
$string['openlevels_all'] = 'All levels (Moodle default)';
$string['openlevels_desc'] = 'How many levels of categories are visible when someone opens the Categories page of a question bank. Subcategories below this level start collapsed; users can still expand them with the arrow next to each category.';
$string['openlevels_n'] = '{$a} levels';
$string['openlevels_one'] = 'Top level only';
$string['pluginname'] = 'Collapse categories';
$string['privacy:metadata:preference:state'] = 'Which question categories the user has expanded or collapsed on the question bank Categories page, where that differs from the site default.';
$string['privacy:preference:state'] = 'Question categories you expanded (positive id) or collapsed (negative id) on the Categories page';
$string['remember'] = 'Remember each user\'s choices';
$string['remember_desc'] = 'If enabled, categories a user expands or collapses stay that way the next time they open the Categories page, in any question bank.';
