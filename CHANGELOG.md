# 3.0.5

-   Fixed a TypeError in the administration dashboard caused by missing stateMachineState association in the order criteria, preventing the dashboard from loading when recovered cart orders were present.

# 3.0.4

-   Resolved incompatibility with Shopware Commercial / B2B Components in Abandoned Cart flows.

# 3.0.3
  
-   Added configuration option to restrict reminder emails for guest customers.

# 3.0.2
  
-   Fixed null pointer exception in customerGroupId retrieval by adding null safe operator.
-   Add Knowledge base configuration button link.

# 3.0.1
  
- Fixed: Resolved a fatal error caused by calling the undefined exec() method on the Doctrine\DBAL\Connection class. Replaced with the appropriate executeStatement() method to ensure compatibility with the current Doctrine DBAL version.

# 3.0.0

-   Added Shopware 6.7 compatibility

# 2.0.7

-   Resolved Email Delivery Failure Due to Invalid Recipient Domain.

# 2.0.6

-   Added CC email configuration option for abandoned cart emails.

# 2.0.5

-   Resolved the issue where an empty promotion code would cause an error.

# 2.0.4

-   Resolved the issue where scheduled line items could not be removed.

# 2.0.3

-   Added dependency on AsMessageHandler.

# 2.0.2

-   Resolved the issue with the scheduled task.

# 2.0.1

-   Added the option to exclude customer groups from the abandoned cart promotion.

# 2.0.0

-   Added compatibility for Shopware 6.6

# 1.5.10

-   Date added at abandoned cart Conversion dashboard for more informative purpose.

# 1.5.9

-   Resolved the error of the "getVariantFromOrderState" method not being declared in the dashboard.
     
# 1.5.8

-   Resolved the issue of Duplicate entry '%s%s%s%s%s' for key individual_code_pattern

# 1.5.7

-   Added default Abandoned Cart promotion which gives customers discount upon restoring their abandoned cart

# 1.5.6

-   Replaced CartPersister with AbstractCartPersister for redis compatibility

# 1.5.5

-   Added better converting base mail template in Dutch, German and English locales

# 1.5.4

-   Resolved bug of mail-template sender null.

# 1.5.3

-   Compatibility patch with Shopware 6.5.4

# 1.5.2

-   Added cart data and total price in the email template.

# 1.5.1

-   Resolved bug of getEmail null.

# 1.5.0

-   Introduced compatibility for Shopware 6.5.0

# 1.4.2

-   Removed !important statement

# 1.4.1

-   Minor patch in cart recovery service

# 1.4.0

-   Refactor of CartService to support Redis cart persister

# 1.3.13

-   Added a notification batch size configuration to avoid spamming the mailserver resulting in failed deliveries.

# 1.3.12

-   Resolved bug occurring at rare occasions when loading the admin dashboard.

# 1.3.11

-   Added abandoned cart conversion metrics to Administration dashboard

# 1.3.10

-   Rebuild administration JS files under v6.4.5.0 to fix issue https://github.com/shopware/platform/issues/2420
-   Added customer preview data

# 1.3.9

-   Added compatibility with v6.4.10.0

# 1.3.8

-   Fixed translation of mail template header and footer

# 1.3.7

-   Added compatibility patch for plugins that rely on extensions being set in the cart.

# 1.3.6

-   Hotfix for error on order creation via API without providing an order customer.

# 1.3.5

-   We now clear the abandoned cart when all line items have been deleted from the original cart.

# 1.3.4

-   Made abandoned cart more persistent. It only gets deleted when a cart gets converted to an order or when the thrash delay has been reached.
-   Added option to unsubscribe from abandoned cart mails from the user account in the Storefront.
-   Added extra field to config to make it easier to debug if the abandoned cart processor is actually running.
-   Moved abandoned cart resolving to OrderSubscriber to avoid sending abandoned cart emails to people who already performed an order but never reach the order finish page
-   Added template data to mail template to allow previewing the email and browsing the available variables.

# 1.3.3

-   Added support for custom products
-   Added extra touch points for abandoned carts to be updated
-   Improved UX in abandoned cart listing in the administration

# 1.3.2

-   Added scheduling capabilities with custom mail templates to abandoned cart mailing

# 1.3.1

-   Fixed decoration issue in AbandonedCartService

# 1.3.0

-   Made plugin compatible to 6.4.0

# 1.2.9

-   Backwards compatibility fix, bringing scheduling capabilities to Shopware 6.3

# 1.2.8

-   Fixed bug in timing function causing abandoned carts to never be trashed or reminders to never be sent

# 1.2.7

-   Fixed translation issue in mail templates

# 1.2.6

-   Added error prevention to scheduled task to ensure the continuation of abandoned cart mails

# 1.2.5

-   Improved entity definition

# 1.2.4

-   Fixed issue where mail header and footer would not always be attached to the email

# 1.2.3

-   Fixed bug in abandoned cart details modal which hindered navigating to the referenced product

# 1.2.2

-   Improved extendability

# 1.2.1

-   Compatibility patch with ZeobvReorder plugin

# 1.2.0

-   Backend order compatibility added
-   Added function to view items in abandoned cart

# 1.1.1

-   v6.3.0.0 compatibility update

# 1.1.0

-   Added anonymous cart option. Via the configuration it's now possible to allow the abandoned cart mail to also recover the customer data for checkout or to make the cart recovery only restore the cart contents.
-   Fixed issue where abandoned cart would not always be removed correctly on recovery or deprecation.

# 1.0.4

-   Recovered cart items are now removable.

# 1.0.3

-   Patched issue where not all abandoned carts would be processed if a sales channel didn't have any abandoned carts.

# 1.0.2

-   Patched issue where no new cart would be created using the link in the abandoned cart email
-   Added logo

# 1.0.1

-   Compatibility fixes for Shopware 6.1.0-rc3

# 1.0.0

-   First version of the Zeo Abandoned Cart for Shopware 6
