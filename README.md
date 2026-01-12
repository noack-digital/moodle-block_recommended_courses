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
- Moodle 4.5 or higher (compatible with Moodle 5.0)
- PHP 7.4 or higher

## Changelog

### Version 2.0.0 (2025-10-10) - STABLE RELEASE - RENAMED PLUGIN

**Major Changes:**
- 🔄 **Plugin renamed:** from `block_empfohlene_kurse` to `block_recommended_courses`
- 🌍 **Multilingual:** German, English, Ukrainian language support
- 🆔 **Component name:** Changed to `block_recommended_courses`

**Features from v1.3.1:**
- 👤 **Main contact person:** Shows course teacher with profile picture
- 📅 **Last modification date:** Shows when the course was last updated (format: 09.10.25)
- ⚙️ **Flexible course information:** Category, contact person, and date individually toggleable
- 🖼️ **Profile picture option:** Contact person profile picture optionally displayable
- 🎨 **Modern meta tags:** Information in clear badges with icons
- 💡 **Tooltips:** Explanations displayed on mouseover of meta information

**Previous Features:**
- 🎯 **Indicator dots:** Dots below main course show number and position of slides
- 💡 **Course name tooltips:** Hover over indicator dot displays course name
- 🖱️ **Direct navigation:** Click on indicator dot jumps directly to course
- 🎨 **Optimized navigation arrows:** Outside of content at edge, Moodle blue with white icons
- 🖼️ **Full image width:** Main course images use full width with proportional height
- 🔗 **Clickable course titles:** Titles in main slider lead directly to course
- 🎨 **4 layout modes:** Vertical, Horizontal, Card (centered), Minimal (image + title only)
- ⏱️ **Automatic sliding:** 3-10 seconds configurable, pauses on hover
- 👁️ **Toggleable elements:** Course cards and button individually configurable
- 📱 **Responsive:** Navigation arrows adjust on mobile devices

**Improvements:**
- Automatic detection of main contact person (editingteacher/teacher)
- Flexgroup layout for meta information with automatic wrapping
- Clickable contact names lead to user profile
- Hover effects on meta badges (background color changes)
- Cursor: help for tooltips for better UX
- Responsive display on mobile devices

**Tested on:**
- ✅ Moodle 5.0.2+ (Build: 20250923)
- ✅ Moodle 4.5+
- ✅ PHP 8.4.5
- ✅ MariaDB 11.4.7

See [DEMO_KURSE.md](DEMO_KURSE.md) for example courses with sustainability themes

## Author
- Alexander Noack - Hochschule für nachhaltige Entwicklung Eberswalde (HNEE)

## License
GPL v3 - See [LICENSE](LICENSE) for more information
