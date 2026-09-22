# Relatable CRM Integration for Elementor Pro

This WordPress plugin bridges the gap between **Elementor Pro Forms** and **Relatable CRM**. It adds a custom "Action After Submit" to your Elementor forms, allowing you to seamlessly map form submissions directly to your Relatable CRM contacts. 

The plugin uses an intelligent "upsert" (update or insert) method: it searches for an existing contact by their email address. If a match is found, their profile is updated. If not, a new contact is created.

## ✨ Features
* **Seamless Elementor Integration:** Appears natively within the Elementor Pro Form builder under "Actions After Submit".
* **Visual Field Mapping:** Easily map Elementor Form Field IDs to Relatable CRM data fields (First Name, Last Name, Email, Phone, Location, Company).
* **Smart Upsert Logic:** Automatically prevents duplicate entries by looking up contacts via email before creating a new record.
* **Automatic Updates:** Integrated with `plugin-update-checker`, allowing this plugin to automatically receive updates directly from its public GitHub repository.

## 📋 Prerequisites
* **WordPress** (Tested up to 7.0.2)
* **Elementor Pro** (Requires the Forms widget)
* **Relatable CRM Account & API Key**

## 🚀 Installation

1. Download the latest release from the [GitHub Repository](https://github.com/rosspotomactech/relatable-elementor-integration).
2. Ensure the `plugin-update-checker` folder is included inside the main plugin folder (if cloning directly from the repo, make sure dependencies are fetched).
3. Upload the `relatable-elementor-integration` folder to your `/wp-content/plugins/` directory, OR upload the `.zip` file via **Plugins > Add New > Upload Plugin** in your WordPress admin dashboard.
4. Activate the plugin through the **Plugins** menu in WordPress.

## ⚙️ Configuration & Usage

### 1. Set Your Relatable API Key
Before using the plugin, you must authenticate it with your Relatable CRM account.

**Preferred:** define the key as a constant in `wp-config.php`. When the constant is defined, the settings field is disabled and the key is never stored in the database.

```php
define( 'RELATABLE_API_KEY', 'your_relatable_api_key' );
```

**Alternative:** store the key in the database.
1. In your WordPress admin dashboard, go to **Settings > Relatable CRM**.
2. Enter your Relatable API Key.
3. Click **Save Settings**.

The stored key is never displayed after saving. Leave the field blank to keep the current key, or enter a new value to replace it. WP-CLI: `wp option update relatable_api_key "your_relatable_api_key"`.

### 2. Configure Your Elementor Form
1. Open a page or template with Elementor.
2. Add or select an **Elementor Form** widget.
3. In the Elementor sidebar, go to the **Content** tab and open the **Actions After Submit** section.
4. Click the `+` icon and select **Relatable CRM**.

### 3. Map the Form Fields
Once the action is added, a new **Relatable CRM Field Mapping** section will appear in the Elementor sidebar.
1. Expand the **Relatable CRM Field Mapping** section.
2. For each field, input the corresponding **Elementor Field ID**. 
   *(Note: You can find a field's ID by clicking on the form field in the Elementor sidebar, navigating to its **Advanced** tab, and copying the value in the "ID" box).*
3. **Mandatory:** The "Email Field ID" must be mapped for the integration to work, as it is used to search for existing contacts.
4. Save/Update your Elementor page.

## 🔒 Security considerations

* All form values are sanitized before being sent to Relatable, and the email address must pass WordPress validation.
* The API key is sent over HTTPS in the `Api-Key` request header.
* Abuse control (CAPTCHA, honeypot, rate limiting) is applied at the form or hosting level and is not part of this plugin.
* Updates are downloaded from the public GitHub repository over HTTPS. Anyone with write access to the repository can publish code to every site running the plugin, so the repository is protected with two-factor authentication and protected branches and tags.

## 🛠️ Troubleshooting

API failures are written to the PHP error log and shown to logged-in editors in the form's response as **Relatable CRM API Error**. Visitors never see these messages.

* **Contacts are not being created:** Ensure your API Key is correct in the WordPress settings. Also, verify that the Field ID entered in the Elementor mapping matches the exact Field ID in the form's Advanced tab (case-sensitive).
* **"Relatable CRM: API key is not configured."** No key is defined in `wp-config.php` or saved in the settings page.
* **Updates aren't pulling from GitHub:** Ensure the `plugin-update-checker` directory is present in the root folder of the plugin. 

## 📝 Changelog

### 1.0.1
* The API key can be defined as the `RELATABLE_API_KEY` constant in `wp-config.php`; the stored key is no longer echoed into the settings form.
* Failed API calls are logged and reported to editors instead of failing silently.
* Email addresses are URL-encoded in the contact lookup, so addresses containing `+` match correctly.
* Contact IDs returned by the API are validated and encoded before use.

### 1.0.0
* Initial release.

## 📝 License
This project is licensed under the GPL-3.0 License.
