# CU Boulder Site Configuration

All notable changes to this project will be documented in this file.

Repo : [GitHub Repository](https://github.com/CuBoulder/ucb_site_configuration)

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
