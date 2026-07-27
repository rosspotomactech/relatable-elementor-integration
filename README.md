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
1. In your WordPress admin dashboard, go to **Settings > Relatable CRM**.
2. Enter your Relatable API Key.
3. Click **Save Settings**.

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

## 🛠️ Troubleshooting

* **Contacts are not being created:** Ensure your API Key is correct in the WordPress settings. Also, verify that the Field ID entered in the Elementor mapping matches the exact Field ID in the form's Advanced tab (case-sensitive).
* **Updates aren't pulling from GitHub:** Ensure the `plugin-update-checker` directory is present in the root folder of the plugin. 

## 📝 License
This project is licensed under the GPL-3.0 License.
