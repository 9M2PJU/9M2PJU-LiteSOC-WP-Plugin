=== 9M2PJU LiteSOC ===
Contributors: piju9m2pju
Tags: security, litesoc, threat-detection, cyber-security, brute-force
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.3.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://buymeacoffee.com/9m2pju

LiteSOC security event tracking with Behavioral AI & Geo-IP Intelligence for WordPress.

== Description ==

9M2PJU LiteSOC is a WordPress security plugin that integrates the power of [LiteSOC](https://litesoc.io) real-time threat detection, Behavioral AI, and Geo-IP Intelligence into your WordPress site.

= Key Features =

* **Real-time Event Ingestion**: Automatically tracks authentication, user management, and admin activities.
* **Behavioral AI & Geo-IP Intelligence**: Identifies Geo-Anomalies, Impossible Travel, and Advanced Brute-force attacks using integrated AI models.
* **Hardened Security**: Includes IP validation (X-Forwarded-For support) and input sanitization.
* **Admin Dashboard**: Sleek interface with integrated logo and real-time security logs.
* **Standardized Schema**: Uses the official LiteSOC event schema for maximum compatibility.

== Installation ==

1. Upload the `9m2pju-litesoc` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the 9M2PJU LiteSOC menu and enter your API Key from the LiteSOC Dashboard.

== Frequently Asked Questions ==

= Does this plugin work with reverse proxies? =
Yes, it includes X-Forwarded-For support for accurate IP tracking.

== Screenshots ==

1. The 9M2PJU LiteSOC Admin Dashboard.

== Changelog ==

= 1.3.4 =
* UX: Renamed test button to "Test API Key" for better clarity.

= 1.3.3 =
* UX: Removed "Connection Failed" prefix for cleaner, more direct error messages.
* UX: Consistent success/error feedback terminology.

= 1.3.2 =
* UX: Updated success message to "Valid API Key" for better clarity.
* Polish: Consistent feedback labels in settings.

= 1.3.1 =
* Improvement: Better error messages for API Connection Test (e.g. "Invalid API Key").
* Fix: Handled 401/403 status codes with descriptive feedback.

= 1.3.0 =
* Feature: Added API Connection Test button in settings.
* UX: Immediate feedback for API key validity.
* Docs: Updated screenshots in README.md.

= 1.2.9 =
* Security: Added simulation script for Impossible Travel verification.
* Docs: Finalized documentation for all security scenarios.

= 1.2.8 =
* Docs: Added step-by-step WordPress dashboard installation instructions.
* Docs: Improved Installation section in README.md.

= 1.2.7 =
* Fix: Added missing translators comment to satisfy WordPress standards.
* UI: Finalized branding and spacing refinements.

= 1.2.6 =
* UX: Refined API key dashboard instructions.
* UI: Optimized branding and header visibility.

= 1.2.5 =
* UI: Maximized branding with larger logo and header for better screen coverage.
* UI: Further improved spacing for a more premium, filled look.

= 1.2.4 =
* UX: Improved API key registration guidance in settings.
* UI: Refined balanced layout for standard and high-resolution screens.

= 1.2.3 =
* UI: Balanced responsive design for universal one-page fit.
* UI: Larger logo and header banner on high-resolution screens.
* UI: Improved spacing and vertical alignment.

= 1.2.2 =
* UI: Optimized settings page for Ultra-Compact layout.
* UI: Reduced header and form vertical spacing.
* UI: Scaled "Buy Me A Coffee" button for better visibility.

= 1.2.1 =
* Added "Source" and "Environment" fields to settings.
* Included source and environment metadata in event tracking.
* Fixed missing plugin header tags for better compatibility.
* Refined UI branding and field descriptions.

= 1.2.0 =
* Final production release for official WordPress.org submission.
* Optimized event tracking logic and refined UI components.
* Refreshed documentation and metadata.

= 1.1.9 =
* Compliance fix: Removed 'WP' and 'Plugin' from official headers as per WordPress.org requirements.
* Retained internal branding for settings page.
* Bumping version for official submission.

= 1.1.8 =
* Rebranded to '9M2PJU LiteSOC WP Plugin' for consistent naming.
* Bumping version for official submission.

= 1.1.7 =
* Final polish for official WordPress.org submission.
* Refined version metadata across all files.

= 1.1.6 =
* Updated marketing copy to highlight Behavioral AI & Geo-IP Intelligence.
* Bumping version for official submission.

= 1.1.5 =
* WordPress.org compliance fixes: localizing assets, updating prefixes, and removing restricted term 'wp' from slugs.
* Reinforcing centering for internal footer and donation button.
* Bumping version for official submission.

= 1.1.4 =
* Refined footer links and version display.

= 1.1.2 =
* Finalized WordPress.org compliance fixes.
* Added GitHub Sponsorship support.
* Stable release for submission.

= 1.1.1 =
* Initial release for WordPress.org submission.
* Updated to GPLv3 (latest GPL) for compliance.
* Fixed license declaration headers in readme.txt.

== Upgrade Notice ==

= 1.1.2 =
Final compliant release with full security hardening and sponsorship support.
