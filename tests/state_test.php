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
 * Tests for the remembered-state preference.
 *
 * @package    qbank_collapsecategories
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qbank_collapsecategories\local\state
 */
final class state_test extends \advanced_testcase {
    /**
     * Valid ids are kept in order; junk is dropped.
     */
    public function test_parse(): void {
        $this->assertSame([], state::parse(''));
        $this->assertSame([], state::parse(null));
        $this->assertSame([12, -34, 5], state::parse('12,-34, 5'));
        $this->assertSame([7], state::parse('0,-0,abc,7,1e5,<b>,--3,-'));
    }

    /**
     * A later choice for the same category replaces the earlier one and moves to the end.
     */
    public function test_parse_duplicates(): void {
        $this->assertSame([-34, 12], state::parse('12,-34,-12,12'));
    }

    /**
     * Over-long values lose their oldest entries.
     */
    public function test_clean_preference_trims_oldest(): void {
        $ids = range(100000, 100400);
        $cleaned = state::clean_preference(implode(',', $ids));
        $this->assertLessThanOrEqual(state::MAXLENGTH, strlen($cleaned));
        $this->assertStringEndsWith(',100400', $cleaned);
        $this->assertStringStartsNotWith('100000,', $cleaned);
    }

    /**
     * The preference can be set through core's preference API and is cleaned on the way in.
     */
    public function test_preference_definition(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->assertSame('3,-4', \core_user::clean_preference('3,x,-4', state::PREFERENCE));
    }

    /**
     * The script is only added on the Categories page.
     */
    public function test_hook_only_on_categories_page(): void {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/course/view.php');
        $PAGE->set_pagetype('course-view');
        $PAGE->set_context(\context_system::instance());
        hook_callbacks::before_footer_html_generation(
            new \core\hook\output\before_footer_html_generation($PAGE->get_renderer('core'))
        );
        $this->assertStringNotContainsString('qbank_collapsecategories/collapse', $PAGE->requires->get_end_code());

        $PAGE = new \moodle_page();
        $PAGE->set_url('/question/bank/managecategories/category.php');
        $PAGE->set_pagetype(hook_callbacks::PAGETYPE);
        $PAGE->set_context(\context_system::instance());
        hook_callbacks::before_footer_html_generation(
            new \core\hook\output\before_footer_html_generation($PAGE->get_renderer('core'))
        );
        $this->assertStringContainsString('qbank_collapsecategories/collapse', $PAGE->requires->get_end_code());
    }
}
