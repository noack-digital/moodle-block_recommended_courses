@block_recommended_courses
Feature: Recommended courses block can be added
  In order to recommend courses
  As an administrator
  I need to be able to add the Recommended Courses block

  Scenario: Empty block shows the no-courses message
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Recommended Courses" block
    Then I should see "Recommended Courses"
    And I should see "No courses available to display"
