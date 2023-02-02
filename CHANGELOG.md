# CU Boulder Site Configuration

All notable changes to this project will be documented in this file.

Repo : [GitHub Repository](https://github.com/CuBoulder/ucb_site_configuration)

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- ### Adds site type, site affiliation
  This update to CU Boulder Site Configuration introduces a new "General" configuration form accessible for users with the "Edit the site general settings" permission. The form allows editing of site information:
  - Site name
  - Site type (optional, selected from a list of presets)
  - Site affiliation (optional, selected from a list of presets or custom defined)
  
  The site affiliation is always displayed underneath the site name in the header of the site.
  
  CuBoulder/tiamat-theme#210
  CuBoulder/tiamat-theme#211
  
  Sister PR in: [tiamat-theme](https://github.com/CuBoulder/tiamat-theme/pull/215)
---
 
Updates `README.md`
