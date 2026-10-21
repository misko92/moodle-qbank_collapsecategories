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
 * Collapse question categories on the question bank Categories page.
 *
 * Core renders every subcategory list open (Bootstrap "collapse show"). On load we
 * close the lists below the configured depth, then apply the user's own remembered
 * choices. Bootstrap's own toggle keeps working because we only change the same
 * classes and attributes it reads.
 *
 * @module     qbank_collapsecategories/collapse
 * @copyright  2026 Lukasz Miskowicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {setUserPreference} from 'core_user/repository';

const PREFERENCE = 'qbank_collapsecategories_state';
// The user_preferences.value column holds 1333 characters.
const MAXLENGTH = 1300;

const SELECTORS = {
    ROOT: '#categoryroot',
    ITEM: 'li.qbank_managecategories-item[data-categoryid]',
    CHILD_ITEM: ':scope > ul > li.qbank_managecategories-item',
};

/**
 * Get a category's child list container.
 *
 * @param {String} id Category id.
 * @returns {HTMLElement|null}
 */
const getContainer = id => document.getElementById(`childlist${id}`);

/**
 * Get the expand/collapse button for a category.
 *
 * @param {String} id Category id.
 * @returns {HTMLElement|null}
 */
const getButton = id => document.querySelector(`[data-bs-target="#childlist${id}"]`);

/**
 * How deep a category is: top-level categories are 1.
 *
 * @param {HTMLElement} item The category's li element.
 * @returns {Number}
 */
const getDepth = item => {
    let depth = 0;
    for (let node = item; node; node = node.parentElement?.closest(SELECTORS.ITEM)) {
        depth++;
    }
    return depth;
};

/**
 * Make the button's screen-reader label match its state.
 *
 * Core always labels it "Collapse <name>", even when the category is closed.
 *
 * @param {String} id Category id.
 * @param {Boolean} open
 */
const updateLabel = async(id, open) => {
    const button = getButton(id);
    const name = document.getElementById(`category-${id}`)?.dataset.categoryname;
    if (button && name !== undefined) {
        button.setAttribute('aria-label', await getString(open ? 'collapsecategory' : 'expandcategory', 'core', name));
    }
};

/**
 * Open or close a category's child list without animation.
 *
 * @param {String} id Category id.
 * @param {Boolean} open
 */
const setOpen = (id, open) => {
    const container = getContainer(id);
    const button = getButton(id);
    if (!container || !button) {
        return;
    }
    container.classList.toggle('show', open);
    button.classList.toggle('collapsed', !open);
    button.setAttribute('aria-expanded', open ? 'true' : 'false');
    updateLabel(id, open);
};

/**
 * Does this category have any subcategories?
 *
 * @param {String} id Category id.
 * @returns {Boolean}
 */
const hasChildren = id => !!getContainer(id)?.querySelector(SELECTORS.CHILD_ITEM);

/**
 * Initialise the page.
 *
 * @param {Number} openLevels How many levels start visible; 0 means all.
 * @param {Boolean} remember Whether to remember each user's choices.
 * @param {Number[]} saved Remembered choices: positive id = opened, negative id = closed.
 */
export const init = (openLevels, remember, saved) => {
    const root = document.querySelector(SELECTORS.ROOT);
    if (!root) {
        return;
    }

    /**
     * Should this category be open by default?
     *
     * @param {HTMLElement} item
     * @returns {Boolean}
     */
    const isOpenByDefault = item => openLevels === 0 || getDepth(item) < openLevels;

    // Remembered choices, keyed by category id, in the order they were made (oldest first).
    const choices = new Map();
    (saved || []).forEach(signedId => choices.set(String(Math.abs(signedId)), signedId > 0));

    root.querySelectorAll(SELECTORS.ITEM).forEach(item => {
        const id = item.dataset.categoryid;
        if (!hasChildren(id)) {
            return;
        }
        const open = choices.has(id) ? choices.get(id) : isOpenByDefault(item);
        if (!open) {
            setOpen(id, false);
        }
    });

    // If a category is added or dragged into a closed category, open it so the user can see it.
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (!(node instanceof HTMLElement) ||
                        !(node.matches(SELECTORS.ITEM) || node.querySelector(SELECTORS.ITEM))) {
                    return;
                }
                for (let item = node.parentElement?.closest(SELECTORS.ITEM); item;
                        item = item.parentElement?.closest(SELECTORS.ITEM)) {
                    if (!getContainer(item.dataset.categoryid)?.classList.contains('show')) {
                        setOpen(item.dataset.categoryid, true);
                    }
                }
            });
        });
    });
    observer.observe(root, {childList: true, subtree: true});

    // Keep the button labels right when the user opens or closes a category.
    const relabel = e => {
        const match = e.target.id?.match(/^childlist(\d+)$/);
        if (match) {
            updateLabel(match[1], e.type === 'shown.bs.collapse');
        }
    };
    root.addEventListener('shown.bs.collapse', relabel);
    root.addEventListener('hidden.bs.collapse', relabel);

    if (!remember) {
        return;
    }

    /**
     * Save the user's choice when they open or close a category.
     *
     * @param {Event} e A Bootstrap shown/hidden collapse event.
     */
    const saveChoice = e => {
        const match = e.target.id?.match(/^childlist(\d+)$/);
        if (!match) {
            return;
        }
        const id = match[1];
        const item = document.getElementById(`category-${id}`);
        if (!item) {
            return;
        }
        const open = e.type === 'shown.bs.collapse';
        choices.delete(id);
        if (open !== isOpenByDefault(item)) {
            choices.set(id, open);
        }

        let ids = Array.from(choices, ([key, isOpen]) => (isOpen ? '' : '-') + key);
        while (ids.join(',').length > MAXLENGTH) {
            ids.shift();
        }
        setUserPreference(PREFERENCE, ids.join(','));
    };
    root.addEventListener('shown.bs.collapse', saveChoice);
    root.addEventListener('hidden.bs.collapse', saveChoice);
};
