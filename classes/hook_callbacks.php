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

namespace qbank_collapsecategories;

use qbank_collapsecategories\local\state;

/**
 * Hook callbacks.
 *
 * @package    qbank_collapsecategories
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /** @var string Page type of the question bank "Categories" page. */
    const PAGETYPE = 'question-bank-managecategories-category';

    /**
     * Load the collapse script on the question bank Categories page.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(
        \core\hook\output\before_footer_html_generation $hook
    ): void {
        global $PAGE;

        if (during_initial_install() || $PAGE->pagetype !== self::PAGETYPE) {
            return;
        }
        if (!\core\plugininfo\qbank::is_plugin_enabled('qbank_collapsecategories')) {
            return;
        }

        $openlevels = (int) get_config('qbank_collapsecategories', 'openlevels');
        $remember = (bool) get_config('qbank_collapsecategories', 'remember');
        $saved = $remember ? state::parse(get_user_preferences(state::PREFERENCE, '')) : [];

        $PAGE->requires->js_call_amd('qbank_collapsecategories/collapse', 'init', [$openlevels, $remember, $saved]);
    }
}
