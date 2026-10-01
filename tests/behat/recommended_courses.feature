@block @block_recommended_courses @javascript
Feature: Recommended courses block can be added and filtered
  In order to recommend courses
  As a user
  I need to see recommended courses and control the enrolment filter

  Background:
    Given the following "courses" exist:
      | fullname           | shortname | category |
      | Recommended Alpha  | RA1       | 0        |
      | Recommended Beta   | RB1       | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | RA1    | student |

  Scenario: Empty block shows the no-courses message
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Recommended Courses" block
    Then I should see "Recommended Courses"
    And I should see "No courses available to display"

  Scenario: Enrolment filter hides enrolled courses by default
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Recommended Courses" block
    And the Recommended Courses block is configured with courses "RA1, RB1"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    Then I should see "Show only courses I am not enrolled in" in the "Recommended Courses" "block"
    And I should see "Recommended Beta" in the "Recommended Courses" "block"
    And I should not see "Recommended Alpha" in the "Recommended Courses" "block"
    And I should see "Enrol now" in the "Recommended Courses" "block"

  Scenario: Turning the enrolment filter off shows enrolled courses with go-to-course
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Recommended Courses" block
    And the Recommended Courses block is configured with courses "RA1, RB1"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    When I toggle the recommended courses enrolment filter
    Then I should see "Recommended Alpha" in the "Recommended Courses" "block"
    And I should see "Recommended Beta" in the "Recommended Courses" "block"
    And I should see "Go to course" in the "Recommended Courses" "block"
