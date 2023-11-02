# CU Boulder Site Configuration

All notable changes to this project will be documented in this file.

Repo : [GitHub Repository](https://github.com/CuBoulder/ucb_site_configuration)

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- ### Adds search frontend and settings
  This update:
  - Adds a search modal which appears when clicking on the search icon in the top bar.
  - Adds a new "Pages and search" tab to CU Boulder site settings (`/admin/config/cu-boulder/pages`). This tab contains settings accessible to Architect, Developer, and Site Manager:
    - The home page setting (moved from "General").
    - Options to enable site search, all of Colorado.edu search (default), both, or neither.
    - Configuration for the site search label, placeholder, and URL.
  - Renames "Appearance" to "Appearance and layout" and alters the descriptions of menu items.
  - Adds the [Google Programmable Search Engine](https://www.drupal.org/project/google_cse) module, which allows creating custom search pages to use with site search.
  
  CuBoulder/tiamat-theme#266
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/527), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/43), [tiamat10-project-template](https://github.com/CuBoulder/tiamat10-project-template/pull/17)
---

- ### Adds home page setting to CU Boulder site settings
  This update to CU Boulder Site Configuration:
  - Adds a new home page setting to CU Boulder site settings. Resolves CuBoulder/tiamat-theme#506
  - Adds a new `edit ucb site pages` permission:
    - Architect, Developer, and Site Manager have been given this new permission. 
    - While "Pages" could eventually be split off into its own tab, the settings are found under "General", at least for now. The existing settings in "General" still require `edit ucb site general` to access.
  - Cleans up PHP files in CU Boulder Site Configuration according to Drupal coding standards. Full compliance has not yet been achieved. CuBoulder/ucb_site_configuration#27
  
  Sister PR in: [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/30)
---

- ### Removes "D9" from theme name and the theme, custom entities Composer package names
  CuBoulder/tiamat-theme#435
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/452), [tiamat-custom-entities](https://github.com/CuBoulder/tiamat-custom-entities/pull/70), [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/52), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/13), [tiamat-project-template](https://github.com/CuBoulder/tiamat-project-template/pull/28), [tiamat10-project-template](https://github.com/CuBoulder/tiamat10-project-template/pull/8)
---

- ### Adds "Menu style" setting to Appearance
  CU Boulder Site Settings → Appearance features a new "Menu style" menu. Resolves CuBoulder/ucb_site_configuration#24.
  
  Sister issue as: CuBoulder/tiamat-theme#330 (PR [here](https://github.com/CuBoulder/tiamat-theme/pull/416))
---

- ### Adds a global "Related articles" configuration form to CU Boulder Site Settings
  This update adds a "Related articles" configuration form to CU Boulder Site Settings, accessible via the menu or `/admin/config/cu-boulder/related-articles`. Here users with permission can exclude articles with specific categories or tags from appearing in "related articles" sections. Resolves CuBoulder/ucb_site_configuration#22
  
  This update also fixes a bug which caused warnings to appear when configuring a third-party service. Resolves CuBoulder/ucb_site_configuration#21
  
  Sister PR in: [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/47), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/7)
---

- ### Adds sticky menu
  This update adds an optional "sticky menu" component to all pages on a site, enabled by visiting CU Boulder site settings → Appearance and toggling on _Show sticky menu_. The menu appears automatically when a user scrolls down passed the main website header, and only on large screen devices (at least 960 pixels wide).
  
  CuBoulder/tiamat-theme#247
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/271)
---

- ### Adds "Advanced" appearance settings and custom site logos; modifies contact info settings
  This update:
  - Adds an _Advanced_ view at the bottom of the _Appearance_ settings, collapsed by default and visible only to those with the _Edit advanced site settings_ permission.
  - Moves all theme settings previously restricted to Drupal's default theme settings into the _Advanced_ view.
  - Adds site-specific custom logos (CuBoulder/tiamat-theme#264) and places the settings for custom logos into the _Advanced_ view:
    - Custom logo requires _white text on dark header_ and _dark text on white header_ variants.
    - An image can be uploaded or a path can be manually specified for each.
    - ~~A scale can be specified, which defaults to _2x_ (Retina) but also allows _1x_ (standard) or _3x_ (enhanced Retina)~~.
  - Assigns the _Architect_ and _Developer_ user roles the _Edit advanced site settings_ permission.
  - Replaces address fields with general field and WYSIWYG editor in site contact info; removes colons from site contact info footer (CuBoulder/tiamat-theme#269)
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/270), [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/34)
---

- ### Hides administration items that a user doesn't have access to
  This update includes these changes across several repos:
  - Hides inaccessible items from the Admin Toolbar by installing Admin Toolbar Links Access Filter
  - Hides inaccessible items from "Add content" and "CU Boulder site settings"
  
  CuBoulder/tiamat-theme#240; Author @TeddyBearX
  
  Sister PRs in: [ucb_admin_menus](https://github.com/CuBoulder/ucb_admin_menus/pull/6), [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/32)
---

- ### Removes the Admin Helpscout Beacon and help page redirects
  Admin Helpscout Beacon and help page redirects moved to ucb_admin_menus (CuBoulder/ucb_admin_menus#2).
  
  Sister PR in: [ucb_admin_menus](https://github.com/CuBoulder/ucb_admin_menus/pull/5)
---

## [20230209] - 2023-02-09

-   ### Removes "Be Boulder slogan" from Appearance settings
    Resolves CuBoulder/tiamat-theme#230

* * *

-   ### Adds site type, site affiliation

    This update to CU Boulder Site Configuration introduces a new "General" configuration form accessible for users with the "Edit the site general settings" permission. The form allows editing of site information:

    -   Site name
    -   Site type (optional, selected from a list of presets)
    -   Site affiliation (optional, selected from a list of presets or custom defined)

    The site affiliation is always displayed underneath the site name in the header of the site.

    CuBoulder/tiamat-theme#210
    CuBoulder/tiamat-theme#211

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/215)

* * *

Updates `README.md`

[Unreleased]: https://github.com/CuBoulder/ucb_site_configuration/compare/20230209...HEAD

[20230209]: https://github.com/CuBoulder/ucb_site_configuration/compare/4ed9af76cb1d44efe6155803a80aba43b7d0a448...20230209
