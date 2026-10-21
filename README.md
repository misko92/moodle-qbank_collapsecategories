# Collapse categories (qbank_collapsecategories)

On a question bank's **Categories** page, Moodle opens every category and
subcategory. In a big bank that is a long scroll. This plugin starts
subcategories **collapsed** and lets each user's open/closed choices stick.

## Settings

*Site administration → Plugins → Question bank plugins → Collapse categories*

| Setting | Default | What it does |
|---|---|---|
| Levels shown expanded | Top level only | How many levels are visible when the page opens. "All levels" is Moodle's normal behaviour. |
| Remember each user's choices | On | Categories a user expands or collapses stay that way next time, in any question bank. |

Remembered choices are stored in one user preference
(`qbank_collapsecategories_state`), keeping only choices that differ from the
site default. If a category is added or dragged into a collapsed category, that
category opens so you can see the result.

Turning the plugin off under *Manage question bank plugins* restores Moodle's
normal behaviour.

## Requirements

Moodle 5.2 or 5.3.

## Licence

GNU GPL v3 or later.
