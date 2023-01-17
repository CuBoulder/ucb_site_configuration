# CU Boulder Site Configuration

**IMPORTANT:** CU Boulder Site Configuration is considered a companion module to [CU Boulder Base Theme](https://github.com/CuBoulder/tiamat-theme). The two are intended to be installed together and must be updated together when changes are made to theme settings.

---
## Functionality

CU Boulder Site Configuration is a module compatible with Drupal 9+ that allows CU Boulder-provided site-wide settings and information to be modified by users with the approprate permissions. The primary functionality provided by the module is grouped into three sections, and a matching three-item submenu is placed in the default Drupal configuration menu.

### Appearance

Path: `/admin/config/cu-boulder/appearance`

User permission: `edit ucb site appearance` 

The "Appearance" administration form exposes supported theme settings to users with the permission above. It is separate from the default Drupal theme settings form of CU Boulder Base Theme and requires a different permission to access, however both forms are built using `SiteConfiguration::buildThemeSettingsForm`.

Theme settings to expose on the "Appearance" administration form are defined in `/config/install/ucb_site_configuration.configuration.yml`. `SiteConfiguration::buildThemeSettingsForm` should output a form with fields for all CU Boulder Base Theme settings, including any not exposed on the "Appearance" administration form.

### Contact info

Path: `/admin/config/cu-boulder/contact-info`

User permission: `edit ucb site contact info` 

Site contact information is editable in the "Contact info" administration form and provided as a Site Contact Info block intended for the footer region (the [install profile](https://github.com/CuBoulder/tiamat-profile) handles block configuration and region placement at install time). The block provides information about a given site including:

- Address
- Email
- Fax
- Phone

### Third-party services

Path: `/admin/config/cu-boulder/services`

User permission: `administer ucb external services` 

Users with the permission above may choose to add selected client-side third-party services (JavaScript) to their site in the "Third-party services" administration page. Supported services are:

- [Mainstay](https://mainstay.com) chat widget
- [LiveChat](https://www.livechat.com) chat widget
- [StatusPage](https://www.atlassian.com/software/statuspage) status widget

These services can be configured and added to pages as desired, with options for specific pages or all pages on the site. Additionally, they can be configured to appear as options when creating or editing content, enabling them to be added or removed by content authors who don't have permission to `administer ucb external services`.

Some required configuration for these services isn't provided by default, and the responsibility for ensuring they are configured correctly falls to the site administrator.

---
## Maintenance

CU Boulder Site Configuration adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html), or the familiar major.minor.patch.

### Configuration updates

For simplicity, most of the module's static configuration can be found in [/config/install/ucb_site_configuration.configuration.yml](/config/install/ucb_site_configuration.configuration.yml), while [/config/install/ucb_site_configuration.settings.yml](/config/install/ucb_site_configuration.settings.yml) is intended to contain defaults for user-modifiable settings. These files are only read at install time, and updates must be possible *without* reinsitalling the module as this will cause data loss on existing sites. [Update hooks](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Extension%21module.api.php/function/hook_update_N/8.2.x) can be used to remedy the issue.

Below is an example workflow for adding a theme setting to change the header text size with options for `small`, `medium`, or `large`, defaulting to `medium`, and exposing via the "Appearance" administration form, for both new and existing sites. It doesn't include additional steps in CU Boulder Base Theme such as modifying the template to apply the change.

 - **Step 1:** Increment the minor version number of CU Boulder Base Theme. In CU Boulder Base Theme, add the following line to `/config/install/boulderD9_base.settings.yml`:
	```yaml
	ucb_header_text_size: 'medium'
	```

 - **Step 2:** In CU Boulder Site Configration, add the form field to edit the setting in `SiteConfiguration::buildThemeSettingsForm`. Your code will look something like this:
	```php
	$form['ucb_header_text_size'] = [
				'#type'           => 'select',
				'#title'          => $this->t('Site header text size'),
				'#default_value'  => theme_get_setting('ucb_header_text_size', $themeName) ?? 'medium',
				'#options'        => [
					'small'  => $this->t('Small'),
					'medium' => $this->t('Medium'),
					'large'  => $this->t('Large')
				],
				'#description'    => $this->t('Select the text size for the header at the top of the page.')
	];
	```

 - **Step 3:** Increment the minor version number of CU Boulder Site Configuration. In CU Boulder Site Configuration, add the following line to `/config/install/ucb_site_configuration.configuration.yml` under `editable_theme_settings`:
	```yaml
	- ucb_header_text_size
	```

 - **Step 4:** In CU Boulder Site Configration, create an update hook in `ucb_site_configuration.install` to run when updating existing sites. Your code will look something like this:
	```php
	/**
	 * Adds theme setting for header text size.
	 */
	function ucb_site_configuration_update_95xx() {
		// Ensures the default value of `medium` is set for the theme setting
		\Drupal::configFactory()->getEditable(\Drupal::service('ucb_site_configuration')->getThemeName() . '.settings')->set('ucb_header_text_size', 'medium');
		// Exposes the theme setting on the "Appearance" adminstration form by updating the module configuration
		$config = \Drupal::configFactory()->getEditable('ucb_site_configuration.configuration');
		$editableThemeSettings = $config->get('editable_theme_settings');
		$editableThemeSettings[] = 'ucb_header_text_size';
		$config->set('editable_theme_settings', $editableThemeSettings)->save();
	}
	```

 - **Step 5:** Release and deploy the new versions of CU Boulder Base Theme and CU Boulder Site Configration together. After clearing the cache the new theme setting should now be present.
