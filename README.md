# CU Boulder Site Settings

This Drupal module provides CU Boulder-specific site-wide settings to a site administrator with the permission `administer ucb site`. This includes exposing specific CU Boulder base theme settings to site administrators outside of the default Drupal theme settings.

This module provides a service `ucb_site_configuration` with function `buildThemeSettingsForm` which the theme can call to build its settings form in the usual place, thereby making it unnecessary to maintain two seperate forms for theme settings.

This module also provides site contact information as a block for the site footer. The information is editable via a seperate adminstration form at `/admin/config/system/ucb-site-contact-info` (same permission required).

The block provides information about a given site including:

- Address
- Email
- Fax
- Phone

The templates for rendering this block are located in the theme. This module simply installs the block itself and an admin form for populating the information.

**Theme dependency: `boulder_d9_base`**
