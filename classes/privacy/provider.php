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

namespace qbank_collapsecategories\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use qbank_collapsecategories\local\state;

/**
 * Privacy provider: the plugin stores one user preference.
 *
 * @package    qbank_collapsecategories
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {
    /**
     * Describe the stored data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(state::PREFERENCE, 'privacy:metadata:preference:state');
        return $collection;
    }

    /**
     * Export the user's preference.
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid) {
        $value = get_user_preferences(state::PREFERENCE, null, $userid);
        if ($value !== null && $value !== '') {
            writer::export_user_preference(
                'qbank_collapsecategories',
                state::PREFERENCE,
                $value,
                get_string('privacy:preference:state', 'qbank_collapsecategories')
            );
        }
    }
}
