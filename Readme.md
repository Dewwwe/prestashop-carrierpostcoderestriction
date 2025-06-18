# Carrier Postcode Restriction Module for PrestaShop

This module allows you to restrict carrier options based on customer delivery address postcodes. It's particularly useful for businesses that only want to offer certain shipping methods to specific geographic areas.

## Features

- Restrict carrier availability based on postcode prefixes
- Set specific carriers to bypass restrictions (always available)
- Easy-to-use admin interface
- Compatible with PrestaShop 1.7+

## Installation

1. Download the latest release from the [Releases page](https://github.com/Dewwwe/prestashop-carrierpostcoderestriction/releases)
2. Go to your PrestaShop back office > Modules > Module Manager
3. Click "Upload a module"
4. Select the downloaded ZIP file
5. The module will install automatically

## Configuration

### Setting Allowed Postcode Prefixes

1. Go to Shipping > Postcode Restriction in your back office
2. Enter the two-digit postcode prefixes that should be allowed, separated by commas (e.g., "75,77,78")
3. Only customers with delivery addresses starting with these prefixes will see the restricted carriers

### Configuring Carrier Restrictions

1. In the same configuration page, you'll see a list of all your carriers
2. For each carrier, you can enable "Bypass restriction" to make it always available to all customers
3. Carriers without "Bypass restriction" will only be shown to customers with matching postcodes
4. Click "Save" to apply your changes

## How It Works

When a customer reaches the checkout page:

1. The module checks the customer's delivery address postcode
2. It compares the postcode against the allowed prefixes you've configured
3. If the postcode starts with an allowed prefix, all carriers are shown (unless restricted by other rules)
4. If the postcode doesn't match any allowed prefix, only carriers with "Bypass restriction" enabled are shown
5. This filtering happens in real-time without page reloads

## Use Cases

- Restrict premium delivery options to specific areas
- Offer local delivery only to nearby postcodes
- Create zone-based shipping restrictions without complex carrier zone setup

## Troubleshooting

**No carriers appear at checkout**
- Make sure you've set at least one carrier to "Bypass restriction" OR
- Ensure the customer's postcode starts with one of your allowed prefixes

**Changes not taking effect**
- Clear your PrestaShop cache (Advanced Parameters > Performance > Clear cache)
- Test with a different browser or in incognito mode

## Contributing

We welcome contributions to improve this module! We're not professional PrestaShop developers, so your expertise is valuable.

### Development Setup

1. Clone this repository to your PrestaShop modules directory
2. Make your changes
3. Test thoroughly
4. Submit a pull request with a clear description of your changes

### Coding Standards

- Follow PrestaShop's coding standards
- Keep compatibility with PrestaShop 1.7+
- Document your code
- Add appropriate comments

## Disclaimer

This module is provided as-is by developers who are not professional PrestaShop experts. While we strive to maintain quality and functionality, we cannot guarantee perfect operation in all environments or with all PrestaShop versions. Always test thoroughly in a staging environment before deploying to production.

We are not affiliated with or endorsed by PrestaShop SA.

## License

This module is released under the [Academic Free License 3.0](https://opensource.org/licenses/AFL-3.0)

## Support

For issues, questions, or feature requests, please [open an issue](https://github.com/Dewwwe/prestashop-carrierpostcoderestriction/issues) on GitHub.

