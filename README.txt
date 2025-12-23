=== Mailing Manager - PN ===
Contributors: felixmartinez, hamlet237
Donate link: https://padresenlanube.com/
Tags: email, mailing, notifications, sender, mail address
Requires at least: 3.0
Tested up to: 6.8
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Effortlessly manage your email campaigns. Schedule, send, and track emails directly from your dashboard to engage your audience like never before.

== Description ==

Transform your WordPress site into a powerful email management hub with our intuitive plugin. Whether you're running newsletters, promotional campaigns, or customer outreach, this tool empowers you to Schedule Emails (plan campaigns in advance with an easy-to-use scheduler), Personalize Content (Create tailored messages with dynamic content fields), Track Performance (Monitor open rates, click-through rates, and engagement metrics in real time), Seamless Integration: (Connect with popular email services or use your SMTP server), Automation Features: (Set up automated responses and drip campaigns to save time and boost engagement). Perfect for bloggers, small businesses, and marketers, this plugin combines simplicity with robust functionality to ensure your emails get delivered and make an impact. Start growing your audience today!

= Core Features =

* **Email Template Management**: Create and manage unlimited email templates using WordPress's familiar post editor. Each template supports rich HTML content, custom styling, and dynamic shortcodes for personalized messaging.

* **SMTP Configuration**: Full SMTP support with authentication, allowing you to connect to any SMTP server (Gmail, Outlook, custom servers). Configure SMTP host, port, security (TLS/SSL), authentication credentials, and custom sender information. Includes Gmail-specific optimizations for better deliverability.

* **Email Queue System**: Intelligent email queue management that processes emails in controlled batches. Configure sending rates (emails per 10 minutes and daily limits) to prevent server overload and ensure optimal deliverability. Automatic queue pausing when daily limits are reached, with automatic reset after 24 hours.

* **Scheduled Email Delivery**: Schedule emails to be sent at specific times in the future. Perfect for welcome emails, follow-ups, and time-sensitive campaigns. Includes delayed welcome email functionality with configurable delays.

* **Email Tracking & Analytics**: Comprehensive tracking system including:
  - **Open Tracking**: Track email opens using invisible tracking pixels. Monitor when recipients open your emails with timestamps.
  - **Click Tracking**: Track all link clicks in emails. See which links are clicked most, track unique clicks per user, and analyze click patterns.
  - **Detailed Statistics**: View click statistics by URL, total clicks, unique users who clicked, and detailed click history with timestamps and IP addresses.

* **Email Types & Automation**:
  - **Welcome Emails**: Automated welcome emails for new users with configurable delays
  - **One-Time Emails**: Send emails that are only sent once per recipient
  - **Published Content Emails**: Automatically send emails when new content is published. Configure to send notifications about new posts, pages, or custom post types
  - **Coded Emails**: Special emails with unique codes (e.g., verification codes)
  - **Password Reset Emails**: Customizable password reset emails with branded templates
  - **New User Notifications**: Automated emails sent when new users register

* **WooCommerce Integration**: Seamless integration with WooCommerce for e-commerce email automation:
  - **Purchase Emails**: Automatically send emails after purchase completion with configurable delays
  - **Abandoned Cart Emails**: Detect and send emails to users who abandon their shopping carts. Configurable delay periods (minutes, hours, or days)
  - **Cart Tracking**: Monitor cart activity and send targeted recovery emails

* **Email Distribution Options**: Flexible recipient targeting:
  - Send to all users
  - Send to specific user roles
  - Send to individual selected users
  - Support for custom user queries

* **Exception Management**: Advanced email filtering system:
  - Exclude specific email domains from receiving emails
  - Exclude individual email addresses
  - Perfect for testing environments or excluding internal accounts

* **Email Records & History**: Complete audit trail of all sent emails:
  - Track every email sent with full details (recipient, subject, content, attachments, timestamps)
  - View email status (sent, queued, failed)
  - Detailed error logging for failed sends
  - Email content stored in both HTML and plain text formats
  - Server information and IP tracking

* **Dashboard & Statistics**: Comprehensive dashboard providing:
  - Recent sent emails count (last 7 days)
  - Pending scheduled emails count
  - Detailed email history with filtering options
  - Visual statistics and progress tracking
  - Email queue status monitoring

* **Email Templates & Branding**: Professional email template system:
  - Customizable header images
  - Customizable footer images
  - Configurable maximum email width
  - Legal information footer (company name, address)
  - Custom footer messages
  - Social media links support
  - Responsive design for mobile devices

* **Dynamic Content & Shortcodes**: Powerful shortcode system for personalization:
  - `[user-name]` - Display recipient's name
  - `[post-name]` - Display post titles with links
  - `[new-contents]` - Display recently published content
  - Support for user data (first name, last name, email, nickname, ID)
  - Post-specific shortcodes
  - Custom content filters

* **Test Email Functionality**: Send test emails to verify templates before sending to all recipients. Test emails bypass queue system and restrictions for immediate delivery.

* **Error Handling & Logging**: Robust error management:
  - Detailed error messages for failed sends
  - SMTP error reporting
  - Option to email admin on send failures
  - Error retry functionality
  - Comprehensive error logs with timestamps and details

* **Role-Based Permissions**: Fine-grained access control:
  - Custom capabilities for email management
  - Role-specific permissions for creating, editing, and sending emails
  - Taxonomy capabilities for email categories
  - Secure permission system following WordPress standards

* **Email Queue Management**: Advanced queue control:
  - View and manage pending emails
  - Pause/resume queue functionality
  - Progress tracking for bulk sends
  - Automatic cleanup of processed items
  - Queue status indicators

* **Welcome Email Management**: Dedicated interface for managing welcome emails:
  - View pending welcome email registrations
  - Manage scheduled welcome emails
  - Cleanup tools for old or stuck registrations
  - Unified management interface

* **Notifications System**: Built-in notification management:
  - User notification preferences
  - Subscription management links in emails
  - Unsubscribe functionality
  - Integration with USERSPN plugin for enhanced user management

* **Multilingual Support**: Fully translation-ready:
  - Translation files included for Spanish (ES), Catalan (CA), Basque (EU), Galician (GL), Italian (IT), and Portuguese (PT)
  - Uses WordPress i18n standards
  - Easy to translate with Loco Translate or similar tools

* **Security Features**:
  - Nonce verification for all AJAX requests
  - Input sanitization and validation
  - KSES filtering for HTML content
  - Secure SMTP password storage
  - Permission checks throughout

* **Cron Job Management**: Automated background processing:
  - Daily cleanup tasks (removed users, old logs)
  - Every 10 minutes email queue processing
  - Weekly maintenance tasks
  - Scheduled email processing
  - WooCommerce automated email processing

* **Form Builder Integration**: Advanced form building capabilities:
  - Multiple input types (text, email, select, textarea, file uploads, images, videos, audio)
  - Conditional fields
  - Multi-field groups
  - Password strength checker
  - Range inputs with visual feedback
  - Star rating inputs

* **Public-Facing Features**:
  - Email subscription management popups
  - Unsubscribe functionality
  - Click tracking redirects
  - Open tracking endpoints
  - Public shortcodes for notifications

* **Developer-Friendly**:
  - Well-structured codebase following WordPress coding standards
  - Extensible with filters and hooks
  - Custom post types for emails and records
  - Custom taxonomies for organization
  - REST API endpoints for tracking

Perfect for bloggers, small businesses, e-commerce stores, and marketers who need a comprehensive email management solution without the complexity of external services. The plugin integrates seamlessly with WordPress and provides all the tools you need to create, send, track, and manage your email campaigns effectively.


== Credits ==
This plugin stands on the shoulders of giants

Tooltipster v4.2.8 - A rockin' custom tooltip jQuery plugin
Developed by Caleb Jacob and Louis Ameline
MIT license
https://calebjacob.github.io/tooltipster/
https://github.com/calebjacob/tooltipster/blob/master/dist/js/tooltipster.main.js
https://github.com/calebjacob/tooltipster/blob/master/dist/css/tooltipster.main.css

Owl Carousel v2.3.4
Licensed under: SEE LICENSE IN https://github.com/OwlCarousel2/OwlCarousel2/blob/master/LICENSE
Copyright 2013-2018 David Deutsch
https://owlcarousel2.github.io/OwlCarousel2/
https://github.com/OwlCarousel2/OwlCarousel2/blob/develop/dist/owl.carousel.js

Select2 4.0.13
License MIT - https://github.com/select2/select2/blob/master/LICENSE.md
https://github.com/select2/select2/tree/master
https://github.com/select2/select2/blob/master/dist/js/select2.js
https://github.com/select2/select2/blob/master/dist/css/select2.css

Trumbowyg v2.27.3 - A lightweight WYSIWYG editor
alex-d.github.io/Trumbowyg/
License MIT - Author : Alexandre Demode (Alex-D)
https://github.com/Alex-D/Trumbowyg/blob/develop/src/ui/sass/trumbowyg.scss
https://github.com/Alex-D/Trumbowyg/blob/develop/src/ui/sass/trumbowyg.scss
https://github.com/Alex-D/Trumbowyg/blob/develop/src/trumbowyg.js


== Installation ==

1. Upload `mailpn.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I install the Mailing Manager - PN plugin? =

To install the Mailing Manager - PN plugin, you can either upload the plugin files to the /wp-content/plugins/mailpn directory, or install the plugin through the WordPress plugins screen directly. After uploading, activate the plugin through the 'Plugins' screen in WordPress.

= Can I customize the look and feel of my recipe listings? =

Yes, you can customize the appearance of your recipe listings by modifying the CSS styles provided in the plugin. Additionally, you can enqueue your own custom styles to override the default plugin styles.

= Where can I find the uncompressed source code for the plugin's JavaScript and CSS files? =

You can find the uncompressed source code for the JavaScript and CSS files in the src directory of the plugin. You can also visit our GitHub repository for the complete source code.

= How do I add a new recipe to my site? =

To add a new recipe, go to the 'Mail' section in the WordPress dashboard and click on 'Add New'. Fill in the required details for your recipe, including the title, ingredients, steps, and any other custom fields provided by the plugin. Once you're done, click 'Publish' to make the recipe live on your site.

= Can I use this plugin with any WordPress theme? =

Yes, the Mailing Manager - PN plugin is designed to be compatible with any WordPress theme. However, some themes may require additional customization to ensure the plugin's styles integrate seamlessly.

= Is the plugin translation-ready? =

Yes, the Mailing Manager - PN plugin is fully translation-ready. You can use translation plugins such as Loco Translate to translate the plugin into your desired language.

= How do I update the plugin? =

You can update the plugin through the WordPress plugins screen just like any other plugin. When a new version is available, you will see an update notification, and you can click 'Update Now' to install the latest version.

= How do I backup my recipes before updating the plugin? =

To backup your recipes, you can export your posts and custom post types from the WordPress Tools > Export menu. Choose the 'Mail' post type and download the export file. You can import this file later if needed.

= How do I add ratings and reviews to my recipes? =

The plugin don't include a built-in ratings and reviews system yet. You can integrate third-party plugins that offer these features or customize the plugin to include them.

= How do I optimize my recipes for SEO? =

To optimize your recipes for SEO, ensure that you use relevant keywords in your recipe titles, descriptions, and content. You can also use SEO plugins like Yoast SEO to further enhance your recipe posts' search engine visibility.

= How do I get support for the Mailing Manager - PN plugin? =

For support, you can visit the plugin's support forum on the WordPress.org website or contact the plugin author directly through our contact information info@padresenlanube.com.

= Is the plugin compatible with the latest version of WordPress? =

The Mailing Manager - PN plugin is tested with the latest version of WordPress. However, it is always a good practice to check for any compatibility issues before updating WordPress or the plugin.

= How do I uninstall the plugin? =

To uninstall the plugin, go to the 'Plugins' screen in WordPress, find the Mailing Manager - PN plugin, and click 'Deactivate'. After deactivating, you can click 'Delete' to remove the plugin and its files from your site. Note that this will not delete your recipes, but you should back up your data before uninstalling any plugin.


== Changelog ==

= 1.0.1 =

Update version to 1.0.1 and reflect changes in README
Update plugin requirements and refactor function names for consistency
Add test email functionality and refactor sanitization methods
Update README and enhance AJAX handling in mailpn
Add popup functionality and related styles
Remove fancyBox assets and enhance AJAX nonce verification
Refactor AJAX handling and improve plugin initialization
Refactor post insertion methods for consistency
Refactor post insertion methods for consistency
Enhance email tracking and popup functionality
Update version and enhance plugin structure
Enhance security and improve code readability
Revert version number to 1.0.0 and remove outdated screenshots
Remove mailpn.zip and enhance email exception handling
Refactor post handling and enhance email exception logic
Enhance popup styling and functionality
Refactor email handling and enhance SMTP configuration
Implement delayed welcome email functionality and enhance email processing
Remove deprecated debug scripts and cron status check files
Refactor role capabilities and enhance post type registration


= 1.0.0 =

Hello mailing world!


