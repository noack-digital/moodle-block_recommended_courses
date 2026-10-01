# Recommended Courses Block

## Description
This Moodle plugin provides a block that displays selected courses in which the user is not yet enrolled. The block can be added to the Moodle Dashboard and shows recommended courses in an interactive slider.

## Features
- Display of selected courses in a slider
- Main course displayed with image, title, description, and enrollment button
- Additional three courses displayed as course cards
- Navigation via left and right arrows
- Configurable by administrators:
  - Selection of courses to display via search function
  - Customization of block title
  - Selection of title alignment (left, right, centered)
  - Customization of button text
  - 4 layout modes (Vertical, Horizontal, Card, Minimal)
  - Automatic sliding functionality
  - Show/hide course cards and enrollment button
  - Course information (category, contact person, last modified date)
- Multilingual support (German, English, Ukrainian)
- Optional per-user filter: show only courses without an active enrolment

## Installation
1. Upload the contents of the repository to the directory `/blocks/recommended_courses/` of your Moodle installation.
2. As an administrator, visit the "Site Administration" > "Notifications" page to complete the installation.
3. Add the block to your dashboard or another page.

## Configuration
1. As an administrator, you can add the block to your dashboard and then click on the gear icon to open the block settings.
2. In the configuration menu, you can:
   - Customize the block title
   - Select the title alignment
   - Change the text for the enrollment button
   - Select courses for the slider
   - Choose layout mode
   - Configure auto-slide interval
   - Toggle visibility of course cards and buttons
   - Configure course information display (category, contact, date)

## Requirements
- Moodle 4.5 or higher (compatible with Moodle 5.0 and 5.1)
- PHP 8.1 or higher (PHP 8.2+ required for Moodle 5.0+)

## Bug tracker
https://github.com/noack-digital/moodle-block_recommended_courses/issues

## Changelog

### Version 2.1.0 (2026-07-23)
- Enrolment filter toggle in the block header: show only courses the user is not enrolled in
- Preference is stored per user and applied after reload
- When the filter is off, enrolled courses show a **Go to course** action instead of enrol
- Sidebar and responsive layout improvements (course counter, navigation row)
- Fixed enrol button / meta labels; category meta label renamed to **Semester / Area**
- Privacy API documents the enrolment-filter user preference
- CI updated for Moodle 4.5 / 5.0 / 5.1 (PostgreSQL and MariaDB)

### Version 2.0.8 (2026-07-21)
- Security and access: hide invisible courses, respect course visibility capabilities, filter active enrolments only
- Use site course-contact roles (`$CFG->coursecontact`) instead of hard-coded teacher roles
- Escape JSON for slider bootstrap (`JSON_HEX_*`)
- Format titles/summaries with Moodle APIs (`format_string`, `content_to_text`, `userdate`)
- Apply configured title alignment CSS class
- Remove empty global settings page (`has_config` = false)
- AMD accessibility improvements (keyboard dots, `prefers-reduced-motion`)
- Add PHPUnit and Behat coverage
- Align version metadata, copyright headers, and coding guidelines

### Version 2.0.0 (2025-10-10) - STABLE RELEASE - RENAMED PLUGIN

**Major Changes:**
- Plugin renamed: from `block_empfohlene_kurse` to `block_recommended_courses`
- Multilingual: German, English, Ukrainian language support
- Component name: Changed to `block_recommended_courses`

**Features from v1.3.1:**
- Main contact person with optional profile picture
- Last modification date
- Flexible course information toggles
- Indicator dots with tooltips and direct navigation
- 4 layout modes, auto-slide, responsive navigation

**Tested on:**
- Moodle 5.0.2+
- Moodle 4.5+
- PHP 8.1–8.4
- MariaDB / PostgreSQL

## Author
- Alexander Noack - Hochschule für nachhaltige Entwicklung Eberswalde (HNEE)

## License
GPL v3 - See LICENSE for more information
