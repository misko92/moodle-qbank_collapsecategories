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

namespace qbank_collapsecategories\local;

/**
 * Reads and cleans the per-user "which categories are open" preference.
 *
 * The preference is a comma-separated list of category ids, most recent last.
 * A positive id means the user expanded that category, a negative id means they
 * collapsed it. Only choices that differ from the site default are stored.
 *
 * @package    qbank_collapsecategories
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state {
    /** @var string User preference name. */
    const PREFERENCE = 'qbank_collapsecategories_state';

    /** @var int Longest value we store (user_preferences.value holds 1333 chars). */
    const MAXLENGTH = 1300;

    /**
     * Turn a stored preference into a list of signed category ids.
     *
     * @param string|null $value
     * @return int[]
     */
    public static function parse(?string $value): array {
        $ids = [];
        foreach (explode(',', (string) $value) as $part) {
            $part = trim($part);
            if (preg_match('/^-?[1-9][0-9]{0,17}$/', $part)) {
                // Later entries win, so drop any earlier entry for the same category.
                $id = (int) $part;
                unset($ids[abs($id)]);
                $ids[abs($id)] = $id;
            }
        }
        return array_values($ids);
    }

    /**
     * Clean callback for the preference: keep only valid ids, dropping the oldest if too long.
     *
     * @param string|null $value
     * @return string
     */
    public static function clean_preference(?string $value): string {
        $ids = self::parse($value);
        $out = implode(',', $ids);
        while (strlen($out) > self::MAXLENGTH) {
            array_shift($ids);
            $out = implode(',', $ids);
        }
        return $out;
    }
}
