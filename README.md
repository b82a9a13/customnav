# customnav
This is a block plugin for Moodle which allows for an administrator to create custom navigation for specific roles.

Requirements for use and installation:
  1) To install this plugin and add the block to the dashboard you will need an "Administator" role.
  2) To configure this plugin you will need the "Manager" role.

How to download and install:
  1) Download the repository as a zip file.
  2) Extract the contents of the folder.
  3) Rename the folder "customnav_main" to "customnav".
  4) Compress the "customnav" folder into a zip folder.
  5) Upload the "customnav.zip" to your moodle site by:
    1) Navigate to site administration->plugins->install plugins.
    2) Choose the "customnav.zip".
    3) Click "install plugin from the ZIP file".
    4) Then click the "continue" button.
    5) Then click the "continue" button which is located at the bottom of the page.
    6) Finally click the "Upgrade Moodle database now" button.
  6) A success message should appear which means the installation was successful, and you can just click continue to finish the installation process.

How to add the block to the dashboard:
  1) Navigate to site administration->appearance->Default Dashboard Page
  2) Click the button at the top of the page "Blocks editing on" so it displays the text "Blocks editing off"
  3) Make sure that the button in the navigation bar at the top displays "Hide blocks" if it shows "Show blocks" click it to change it.
  4) Scroll down to the bottom of the page and in the blocks navigation which is located on the left or right of the page.
  5) The in the "Add a block" select "Custom Navigation" from the drop down box.
  6) Use the 4 arrows shaped as a cross to drag the "Custom Navigation" block to your desired location.
  7) Once you have followed the previous steps you can click "Reset Dashboard for all users" in the navigation bar at the top of the page. Now the block will appear on your dashboard page.

To configurew the custom navigation:
  1) You will need to of followed "How to add the block to the dashboard" steps.
  2) Once you have completed the first step you can navigate to the dashboard and click "Custom Navigation Configuration".
  3) You will need to click the "Settings" button and the click the role you want to create navigation for and then fill out the form and submit it.
  4) Then click the "Displays" button and click the role you created settings for.
  5) You can now fill out a navigation element you want to add and to add more click the "Add New Icon" button. Although if you want to remove a icon you can click the X above the icon number.
  6) Once you have created all your elements you can click the "Submit" button.
  Tip:
    1) You can have a image or text but not both for a icon.
    2) A URL or Text is required
    3) URL and Alt text is required.

Extra Information:
  1) When you submit new icons or changes to the icons. The block will automatically change. However if it hasn't changed for a user all they need to do is refresh the page
  2) The block is only used for the roles "manager", "teacher", "editing teacher" and "student".
  3) This plugin will only work if the archetype in the "role" table is kept as its default value.
