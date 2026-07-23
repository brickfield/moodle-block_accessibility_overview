@block @block_accessibility_overview
Feature: Accessibility overview block visibility, placement, and structure

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: An editing teacher sees the accessibility overview block on a course page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Accessibility Overview" block
    When I am on "Course 1" course homepage
    Then I should see "Brickfield starter"

  Scenario: A student does not see the accessibility overview block on a course page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Accessibility Overview" block
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see "Brickfield starter"
    And ".block_accessibility_overview" "css_element" should not exist

  Scenario: The accessibility overview block can be added to a course page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    Then the add block selector should contain "Accessibility Overview" block

  Scenario: The accessibility overview block cannot be added to an activity page
    Given the following "activities" exist:
      | activity | course | name   |
      | page     | C1     | Page 1 |
    And I log in as "teacher1"
    And I am on the "Page 1" "page activity" page
    And I turn editing mode on
    Then the add block selector should not contain "Accessibility Overview" block

  Scenario: Section titles in the accessibility overview block are real headings
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Accessibility Overview" block
    When I am on "Course 1" course homepage
    Then "h4" "css_element" should exist in the ".block_accessibility_overview" "css_element"

  Scenario: Social links in the accessibility overview block have a discernible accessible name
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Accessibility Overview" block
    When I am on "Course 1" course homepage
    Then "a[aria-label='Facebook']" "css_element" should exist in the ".block_accessibility_overview" "css_element"
    And "a[aria-label='Twitter']" "css_element" should exist in the ".block_accessibility_overview" "css_element"
    And "a[aria-label='Linkedin']" "css_element" should exist in the ".block_accessibility_overview" "css_element"
    And "a[aria-label='Instagram']" "css_element" should exist in the ".block_accessibility_overview" "css_element"
