@qbank @qbank_collapsecategories @javascript
Feature: Question categories start collapsed on the Categories page
  In order to find my way around a large question bank
  As a teacher
  I want subcategories to start collapsed and stay how I left them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name    | course | idnumber |
      | qbank    | Qbank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | questioncategory    | name          |
      | Activity module | qbank1    | Default for Qbank 1 | Child cat     |
      | Activity module | qbank1    | Child cat           | Grandchild    |
    And I log in as "teacher1"

  Scenario: Subcategories start collapsed and can be expanded
    When I am on the "Qbank 1" "core_question > question categories" page
    Then I should see "Default for Qbank 1"
    And I should not see "Child cat"
    And I press "Expand Default for Qbank 1"
    And I should see "Child cat"
    And I should not see "Grandchild"

  Scenario: Expanded categories are remembered
    Given I am on the "Qbank 1" "core_question > question categories" page
    And I press "Expand Default for Qbank 1"
    And I should see "Child cat"
    # Give the preference save a moment to finish.
    And I wait "1" seconds
    When I reload the page
    Then I should see "Child cat"
    And I should not see "Grandchild"

  Scenario: The Moodle default can be restored by the administrator
    Given the following config values are set as admin:
      | openlevels | 0 | qbank_collapsecategories |
    When I am on the "Qbank 1" "core_question > question categories" page
    Then I should see "Child cat"
    And I should see "Grandchild"

  Scenario: Showing two levels keeps the second level open
    Given the following config values are set as admin:
      | openlevels | 2 | qbank_collapsecategories |
    When I am on the "Qbank 1" "core_question > question categories" page
    Then I should see "Child cat"
    And I should not see "Grandchild"
