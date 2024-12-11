# CU Boulder Site Configuration

All notable changes to this project will be documented in this file.

Repo : [GitHub Repository](https://github.com/CuBoulder/ucb_site_configuration)

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- ### Adds Sidebar color options
  Options for sidebar menu styles added. Default is the current D10 Gold, and the light gray option mimics the D7 style
  
  Sister PR: https://github.com/CuBoulder/tiamat-theme/pull/1544
---

- ### Increases the maximum length of the "Content" field on third-party service entities (v2.9.1)
  Previously, a site wasn't able to include or exclude all the pages they wanted to due to a default character limit on the "Content" field on third-party services entities. This update increases the character limit to 10,000.
  
  Resolves CuBoulder/ucb_site_configuration#72
---

- ### Update SiteConfiguration.php
  Update for the new homepage footer option as well as background color choices for the above and below content regions.
  
  Sister PR: https://github.com/CuBoulder/tiamat-theme/pull/1456
---

- ### Renames Infrastructure and Sustainability to Infrastructure and Resilience (v2.9)
  [change] This update renames Infrastructure and Sustainability to Infrastructure and Resilience. This update contains an update hook.
  
  Resolves CuBoulder/ucb_site_configuration#65
---

- ### Prepends site base URL to site-relative search page path (v2.8.6)
  [bug, severity:moderate] An issue existed where the site-relative search page path would be treated as root-relative by the browser due to a missing prefixing of the site's URL, preventing custom site search from working as expected. This update resolves the issue by correctly prefixing the site's full URL to the search page path. Resolves CuBoulder/ucb_site_configuration#69
---

- ### Updates linter workflow
  Updates the linter workflow to use the new parent workflow in action-collection.
  
  CuBoulder/action-collection#7
  
  Sister PR in: All the things
---

- ### Create developer-sandbox-ci.yml
  new ci workflow
---

- ### Adds 404 page setting to General (2.8.5)
  Resolves CuBoulder/ucb_site_configuration#62
---

- ### Fixes People List Filter labels
  Previously there was an error in the site configuration where the set Filter 1 label would be presented for Filter 2 and Filter 3. This has been corrected and allows the People List Page to access the correct term labels.
  
  Resolves #60 
---

- ### Fixes bug with site frontpage setting (v2.8.4)
  [Bug] Resolves CuBoulder/ucb_site_configuration#56
---

- ### Removes "social share position" setting (v2.8.3)
  [Remove] CuBoulder/tiamat-theme#1073
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/1078)
---

- ### Renames menu styles (v2.8.2)
  This update renames menu styles to more generic names.
  
  - Highlight -> Light 1
  - Ivory -> Light 2
  - Layers -> Dark 1
  - Minimal -> Light 3
  - Modern -> Dark 2
  - Rise -> Light 4
  - Shadow -> Dark 3
  - Simple -> Dark 4
  - Spirit -> Light 5
  - Swatch -> Dark 5
  - Tradition -> Light 6
  
  Resolves CuBoulder/ucb_site_configuration#53
---

- ### Fixes third-party services set to "all content" appearing on content in the exclude field (v2.8.1)
  Apparently Drupal's query API is so limiting that they actively encourage [duplicating the exact same code 3 times](https://www.drupal.org/docs/8/api/database-api/dynamic-queries/conditions#s-using-not-in-with-multi-value-field-like-roles-user-entity) in the documentation, big yikes. Instead of a query condition this update just grabs all of them and uses PHP to filter some out.
  
  Resolves CuBoulder/ucb_site_configuration#51
---

- ### CU Boulder Site Configuration v2.8
  This update:
  - [New] Adds Service Cloud third-party service and associated configuration. Resolves CuBoulder/ucb_site_configuration#46
  - [Bug] Refactors third-party services yet again to fix a bug with the previous implementation.
  - [Bug] Prevents third-party services from being attached to admin pages. Resolves CuBoulder/ucb_site_configuration#50
  - [Bug] Fixes Shortcodes not rendering correctly in the site contact info block. Resolves CuBoulder/ucb_migration_shortcodes#15
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/702)
---

- ### Third-party services update (v2.7)
  This update:
  - Changes the behavior of the "All content" option on third-party service entities:
     - When selected, the content field no longer hides, but instead becomes an exclude field.
     - The third-party service is excluded from content in this field, the inverse of the include behavior.
  - Refactors third-party services by moving all associated code into the Site Configuration module. In the future, a sister PR in tiamat-theme is unlikely to be necessary.
  
  Resolves CuBoulder/ucb_site_configuration#47
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/671)
---

- ### CU Boulder Site Configuration v2.6.4
  This update:
  - Fixes an error in Drupal 10.2 caused by a dependency on a `ThemeSettingsForm` class marked as internal. This dependency has been removed. Resolves CuBoulder/ucb_site_configuration#43
  - Moves "Sidebar position" under Advanced in Appearance and layout, with the setting now expecting a value of either `left` or `right`. Updates are needed in the theme and profile to make the setting work and will be tagged as sister PRs, but aren't required to merge this PR. CuBoulder/tiamat-theme#633.
  
  @jnicholCU assigned as the reviewer to test the primary fix of this update.
---

- ### Adds label fields for "Department" and "Job Type" on People List Pages
  CuBoulder/tiamat-theme#626
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/636)
---

- ### CU Boulder Site Configuration v2.6.2
  This update changes "Display links in the secondary menu as buttons" to "Secondary menu button display" with options for none, blue, gold, and gray. The setting only appears if "Position of the secondary menu" is set to "Above the main navigation". CuBoulder/tiamat-theme#551
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/583)
---

## [20231212] - 2023-12-12

-   ### CU Boulder Site Configuration v2.6.1

    This update:

    -   Reorganizes the _Appearance and layout_ section of CU Boulder site settings into three categories: _Header and navigation_, _Page content_, and _Miscellaneous_.
    -   Adds a _Heading font_ setting. The setting defaults to _Bold_ but can also be set to _Normal_. CuBoulder/tiamat-theme#516.
    -   Swaps the location of _Inline_ and _Above_ when choosing the secondary menu position. CuBoulder/tiamat-theme#551

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/578)

* * *

-   ### CU Boulder Site Configuration 2.6

    This update:

    -   Moves all settings from "Pages and Search" into "General". Search settings are now advanced settings.
    -   Replaces the "Pages and search" and "Related articles" tabs with a brand new "Content types" tab. All "Related articles" settings have been moved into "Content types".
    -   Replaces the `edit ucb pages` and `configure ucb related articles` permissions with a new `edit ucb content types` permission.
    -   Moves the People List filter labels and Article date format into "Content types".
    -   Moves the GTM account setting into "General" as an advanced setting.
    -   Changes header color labels (Light -> Light Gray, Dark -> Dark Gray). Resolves CuBoulder/ucb_site_configuration#37

    Resolves CuBoulder/ucb_site_configuration#36

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/576), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/53)

* * *

-   ### People List Filter Labels as a Global Setting

    Changes the People List `Filter 1`, `Filter 2`, and `Filter 3` custom labels to a Global Setting in Site Configuration, rather than being set per-page. These labels will be set under Configuration => Cu Boulder Site Settings => Appearance and Layout.

    Resolves [#543 ](https://github.com/CuBoulder/tiamat-theme/issues/543)

    Includes:

    -   `ucb_site_configuarion` => <https://github.com/CuBoulder/ucb_site_configuration/pull/35>
    -   `tiamat-theme` => <https://github.com/CuBoulder/tiamat-theme/pull/560>
    -   `ucb_custom_entities` => <https://github.com/CuBoulder/tiamat-custom-entities/pull/87>

* * *

-   ### CU Boulder Site Configuration 2.5.2

    This update:

    -   Places "Type" and "Affiliation" under  an "Advanced" section in the "General" settings. This section behaves identically to the one in "Appearance and layout", requiring the same special permission to access.
    -   Gives the "Site Manager" role the `edit ucb site general` permission to access the "General" settings.

    Sister PR in: [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/52)
    Resolves CuBoulder/ucb_site_configuration#33

* * *

-   ### CU Boulder site configuration 2.5.1
    This update:
    -   Moves campus alerts setting into "Advanced". Resolves CuBoulder/ucb_site_configuration#31
    -   Sets the weight of "Third-party services" in node sidebars to 35, placing it below "URL alias". Resolves CuBoulder/ucb_site_configuration#30

* * *

-   ### Adds search frontend and settings

    This update:

    -   Adds a search modal which appears when clicking on the search icon in the top bar.
    -   Adds a new "Pages and search" tab to CU Boulder site settings (`/admin/config/cu-boulder/pages`). This tab contains settings accessible to Architect, Developer, and Site Manager:
        -   The home page setting (moved from "General").
        -   Options to enable site search, all of Colorado.edu search (default), both, or neither.
        -   Configuration for the site search label, placeholder, and URL.
    -   Renames "Appearance" to "Appearance and layout" and alters the descriptions of menu items.
    -   Adds the [Google Programmable Search Engine](https://www.drupal.org/project/google_cse) module, which allows creating custom search pages to use with site search.

    CuBoulder/tiamat-theme#266

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/527), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/43), [tiamat10-project-template](https://github.com/CuBoulder/tiamat10-project-template/pull/17)

* * *

-   ### Adds home page setting to CU Boulder site settings

    This update to CU Boulder Site Configuration:

    -   Adds a new home page setting to CU Boulder site settings. Resolves CuBoulder/tiamat-theme#506
    -   Adds a new `edit ucb site pages` permission:
        -   Architect, Developer, and Site Manager have been given this new permission. 
        -   While "Pages" could eventually be split off into its own tab, the settings are found under "General", at least for now. The existing settings in "General" still require `edit ucb site general` to access.
    -   Cleans up PHP files in CU Boulder Site Configuration according to Drupal coding standards. Full compliance has not yet been achieved. CuBoulder/ucb_site_configuration#27

    Sister PR in: [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/30)

* * *

-   ### Removes "D9" from theme name and the theme, custom entities Composer package names

    CuBoulder/tiamat-theme#435

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/452), [tiamat-custom-entities](https://github.com/CuBoulder/tiamat-custom-entities/pull/70), [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/52), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/13), [tiamat-project-template](https://github.com/CuBoulder/tiamat-project-template/pull/28), [tiamat10-project-template](https://github.com/CuBoulder/tiamat10-project-template/pull/8)

* * *

-   ### Adds "Menu style" setting to Appearance

    CU Boulder Site Settings → Appearance features a new "Menu style" menu. Resolves CuBoulder/ucb_site_configuration#24.

    Sister issue as: CuBoulder/tiamat-theme#330 (PR [here](https://github.com/CuBoulder/tiamat-theme/pull/416))

* * *

-   ### Adds a global "Related articles" configuration form to CU Boulder Site Settings

    This update adds a "Related articles" configuration form to CU Boulder Site Settings, accessible via the menu or `/admin/config/cu-boulder/related-articles`. Here users with permission can exclude articles with specific categories or tags from appearing in "related articles" sections. Resolves CuBoulder/ucb_site_configuration#22

    This update also fixes a bug which caused warnings to appear when configuring a third-party service. Resolves CuBoulder/ucb_site_configuration#21

    Sister PR in: [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/47), [tiamat10-profile](https://github.com/CuBoulder/tiamat10-profile/pull/7)

* * *

-   ### Adds sticky menu

    This update adds an optional "sticky menu" component to all pages on a site, enabled by visiting CU Boulder site settings → Appearance and toggling on _Show sticky menu_. The menu appears automatically when a user scrolls down passed the main website header, and only on large screen devices (at least 960 pixels wide).

    CuBoulder/tiamat-theme#247

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/271)

* * *

-   ### Adds "Advanced" appearance settings and custom site logos; modifies contact info settings

    This update:

    -   Adds an _Advanced_ view at the bottom of the _Appearance_ settings, collapsed by default and visible only to those with the _Edit advanced site settings_ permission.
    -   Moves all theme settings previously restricted to Drupal's default theme settings into the _Advanced_ view.
    -   Adds site-specific custom logos (CuBoulder/tiamat-theme#264) and places the settings for custom logos into the _Advanced_ view:
        -   Custom logo requires _white text on dark header_ and _dark text on white header_ variants.
        -   An image can be uploaded or a path can be manually specified for each.
        -   ~~A scale can be specified, which defaults to _2x_ (Retina) but also allows _1x_ (standard) or _3x_ (enhanced Retina)~~.
    -   Assigns the _Architect_ and _Developer_ user roles the _Edit advanced site settings_ permission.
    -   Replaces address fields with general field and WYSIWYG editor in site contact info; removes colons from site contact info footer (CuBoulder/tiamat-theme#269)

    Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/270), [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/34)

* * *

-   ### Hides administration items that a user doesn't have access to

    This update includes these changes across several repos:

    -   Hides inaccessible items from the Admin Toolbar by installing Admin Toolbar Links Access Filter
    -   Hides inaccessible items from "Add content" and "CU Boulder site settings"

    CuBoulder/tiamat-theme#240; Author @TeddyBearX

    Sister PRs in: [ucb_admin_menus](https://github.com/CuBoulder/ucb_admin_menus/pull/6), [tiamat-profile](https://github.com/CuBoulder/tiamat-profile/pull/32)

* * *

-   ### Removes the Admin Helpscout Beacon and help page redirects

    Admin Helpscout Beacon and help page redirects moved to ucb_admin_menus (CuBoulder/ucb_admin_menus#2).

    Sister PR in: [ucb_admin_menus](https://github.com/CuBoulder/ucb_admin_menus/pull/5)

* * *

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

[Unreleased]: https://github.com/CuBoulder/ucb_site_configuration/compare/20231212...HEAD

[20231212]: https://github.com/CuBoulder/ucb_site_configuration/compare/20230209...20231212

[20230209]: https://github.com/CuBoulder/ucb_site_configuration/compare/4ed9af76cb1d44efe6155803a80aba43b7d0a448...20230209
