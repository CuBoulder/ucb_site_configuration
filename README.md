# CU Boulder Site Settings

This Drupal module provides CU Boulder-specific site-wide settings to a site administrator with the permission `administer ucb site`. This includes exposing specific CU Boulder base theme settings to site administrators outside of the default Drupal theme settings.

This module provides a service `ucb_site_configuration` with function `buildThemeSettingsForm` which the theme can call to build its settings form in the usual place, thereby making it unnecessary to maintain two seperate forms for theme settings.
