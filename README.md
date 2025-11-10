# 🏡 House and Land Search Plugin

**Version:** 1.1.0  
**Author:** KO  
**Requires WordPress:** 5.0+  
**Tested up to:** 6.7  
**License:** GPL-2.0-or-later  

---

## 📖 Description

The **House and Land** plugin enables users to search for **House and Land packages** dynamically via AJAX.  
It allows filtering results by price, lot width, bedrooms, bathrooms, and sorting order — all without reloading the page.

The plugin uses `WP_Query` with meta queries for precise filtering, and returns live search results formatted with images, prices, and details.

---

## ✨ Features

- 🔍 **AJAX-powered search results**
- 💰 Filter by **price**, **lot width**, **bedrooms**, and **bathrooms**
- ⚙️ Supports **ordering** and **pagination**
- 🧱 Clean and semantic HTML output
- 🔒 Secure with **nonce verification** and sanitized inputs
- ⚡ Optimized with minimal database load and cached queries
- 📱 Fully compatible with any modern WordPress theme

---

## 📦 Installation

1. Download or clone this repository into your WordPress `/wp-content/plugins/` directory.

   ```bash
   cd wp-content/plugins
   git clone https://github.com/yourusername/house-and-land.git
Activate the plugin from your WordPress Admin Dashboard → Plugins.

The plugin will automatically enqueue the JavaScript and handle AJAX requests.

🚀 Usage
1. Frontend Button Trigger
Add a clickable button or link with the class .house_and_land_link to trigger AJAX filtering.

html
Copy code
<button class="house_and_land_link" value="12">Search Houses with 12m Lot Width</button>
<div class="result_house_and_land"></div>
The result will be dynamically loaded into .result_house_and_land.

2. JavaScript Event (AJAX Request)
The plugin automatically localizes AJAX variables.
If you want to manually trigger an AJAX request, use:

js
Copy code
jQuery(document).trigger('click', '.house_and_land_link');
or define custom parameters:

js
Copy code
$.get(handl.ajax_url, {
  action: 'house_and_land_post',
  nonce: handl.nonce,
  price: '100000,400000',
  lotwidth: '10,20',
  bedroomrange: '2,4',
  bathroomrange: '1,3',
  orderby: 'meta_value_num',
  order: 'ASC'
});
⚙️ Configuration
The plugin supports the following query parameters:

Parameter	Type	Example	Description
price	string	100000,500000	Price range in dollars
lotwidth	string	10,25	Lot width range in meters
bedroomrange	string	2,5	Bedroom count range
bathroomrange	string	1,3	Bathroom count range
orderby	string	meta_value_num	Sort key
order	string	ASC or DESC	Sorting direction
paged	int	1	Pagination number

🧩 File Structure
bash
Copy code
house-and-land/
├── house-and-land.php     # Main plugin file
├── script.js              # AJAX frontend script
├── README.md              # Plugin documentation
🧠 Developer Notes
The plugin uses wp_send_json_success() for consistent JSON response handling.

Results are generated with WP_Query and Advanced Custom Fields (ACF) keys like:

price

lot_width

bedroom

bathroom

garage

builder_logo

Images and icons are fetched from the active theme’s /assets/img/ directory.

To integrate with your theme, ensure that your house_and_land post type and ACF fields are properly configured.

🧰 Dependencies
jQuery (bundled with WordPress)

Advanced Custom Fields (ACF) plugin (for custom field data)

🛡️ Security
All input parameters are sanitized using WordPress functions like sanitize_text_field() and absint().

Nonce validation is enforced via check_ajax_referer('handl_nonce', 'nonce').

🧪 Example AJAX Response
json
Copy code
{
  "success": true,
  "data": "<div class=\"results\"> ... </div>"
}
📸 Screenshots
1️⃣ Search Filters in Action
2️⃣ AJAX Loading Animation
3️⃣ Filtered House and Land Results

🧑‍💻 Contributing
Pull requests are welcome!
If you find a bug or want to propose an enhancement, please open an issue on GitHub.

📜 License
This plugin is licensed under the GPL-2.0-or-later license.

Developed with ❤️ by KO










