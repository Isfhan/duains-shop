---

name: aimeos-development
description: Expert development guidance for Aimeos e-commerce projects. Use this skill
whenever working with Aimeos, especially Aimeos Laravel/TYPO3 integrations,
custom extensions, MShop managers and items, database schemas, frontend
clients, JSON APIs, GraphQL APIs, administration, service providers,
decorators, basket plugins, pricing rules, cron jobs, themes, configuration,
dependency injection context, testing, imports, or Aimeos architecture.
Always follow Aimeos extension and customization patterns instead of
modifying Aimeos core or official extensions.
---

# Aimeos Development Skill

## 1. Purpose

This skill provides rules and implementation guidance for developing,
customizing, extending, debugging, reviewing, and explaining Aimeos projects.

Aimeos is not simply a conventional Laravel e-commerce package.

It is a modular commerce framework that can be integrated into a host
application. Its architecture contains multiple layers including:

* Host application integration
* Controllers
* Client components
* MShop managers
* MShop items
* Providers
* Decorators
* Infrastructure
* Administration interfaces
* APIs
* Extensions
* Themes
* Configuration
* Setup/database schema
* Jobs/cron processing

When working with Aimeos, reason from this architecture first.

Do not automatically apply conventional Laravel MVC patterns when an
Aimeos-native mechanism already exists.

---

# 2. Primary Rule

## NEVER modify Aimeos core or official extensions

Do not directly edit files inside:

* `vendor/aimeos/*`
* official Aimeos extension source
* installed package files
* generated vendor code

Do not solve customization problems by modifying package source.

Instead:

1. Identify the correct Aimeos extension point.
2. Create or use the project's own Aimeos extension.
3. Put custom PHP classes in `src/`.
4. Put configuration in `config/`.
5. Put templates in `templates/`.
6. Put translations in `i18n/`.
7. Put database setup in `setup/`.
8. Put tests in `tests/`.
9. Register the extension through Composer/Aimeos configuration.

This preserves upgradeability.

Aimeos explicitly recommends project-specific extensions for customization.

---

# 3. Documentation First Rule

When solving an Aimeos problem:

1. Identify the Aimeos version.
2. Identify the host application:

   * Laravel
   * TYPO3
   * another supported integration
3. Identify which Aimeos layer is involved.
4. Consult the corresponding Aimeos documentation.
5. Prefer documented extension points.
6. Only create custom architecture when Aimeos does not already provide the required mechanism.

Do not assume documentation for one Aimeos version applies identically to another.

Version-sensitive features must be verified against the project's installed version.

Important documentation areas include:

* Architecture
* Laravel
* TYPO3
* Extensions
* Models
* Managers
* Providers
* Frontend
* Administration
* APIs
* Infrastructure
* Jobs
* Themes
* Configuration

---

# 4. Understand the Host Application

Aimeos is designed to live inside a host application.

The host application may provide:

* HTTP routing
* authentication
* sessions
* configuration
* database connections
* cache
* logging
* URL generation
* mail
* filesystem
* dependency injection

Aimeos integrates into these capabilities rather than necessarily replacing them.

For Laravel projects, use the Aimeos Laravel integration instead of recreating Aimeos infrastructure manually.

---

# 5. Aimeos Architecture

Think about Aimeos approximately as:

```text
Host Application
       |
       v
Aimeos Integration
       |
       +----------------------+
       |                      |
       v                      v
   HTTP/API              Administration
       |                      |
       v                      v
    Clients                JQAdm / GraphQL
       |
       v
   Controllers
       |
       v
 MShop Managers
       |
       v
 MShop Items
       |
       v
 Database / Storage
```

There are also provider and infrastructure layers surrounding this architecture.

Aimeos describes controllers as business-logic controllers rather than
traditional MVC controllers. Clients consume those controllers. MShop
managers provide the data access abstraction.

---

# 6. Dependency Injection Context

The Aimeos Context is extremely important.

Do not instantiate Aimeos internals blindly.

The Context provides access to infrastructure such as:

* configuration
* database connections
* sessions
* cache
* translation
* logging
* other runtime services

When calling Aimeos managers/controllers directly, obtain the appropriate
Aimeos context from the host integration.

For Laravel, the documented pattern is:

```php
$context = app('aimeos.context')->get();
```

or, where appropriate:

```php
$context = app('aimeos.context')->get(false);
```

Use the Aimeos context when interacting directly with Aimeos objects.

Do not construct an incomplete fake environment manually unless writing an
isolated test.

---

# 7. Extensions

## Default extension strategy

Create one project-specific extension for custom Aimeos functionality where
possible.

The extension can contain:

```text
my-extension/
├── config/
├── i18n/
├── setup/
├── src/
├── templates/
├── themes/
├── tests/
├── manifest.php
├── composer.json
└── phing.xml
```

The exact directories depend on which Aimeos features the project uses.

Aimeos recommends keeping project-specific modifications in the project's own
extension.

---

# 8. Extension Naming

Do not use the official `ai-` prefix for a custom extension.

Use a project/vendor-specific name.

Example:

```text
myshop
```

or:

```text
acme-commerce
```

Avoid:

```text
ai-myextension
```

The `ai-` namespace/prefix is reserved for official Aimeos extensions.

---

# 9. Extension Manifest

The extension's `manifest.php` describes the extension.

Typical structure:

```php
<?php

return [
    'name' => 'vendor-extension',
    'depends' => [
        // dependencies
    ],
    'config' => [
        // configuration directories
    ],
    'include' => [
        // source directories
    ],
    'i18n' => [
        // translations
    ],
    'setup' => [
        // setup directories
    ],
    'custom' => [
        // custom configuration
    ],
];
```

Do not invent manifest properties.

Use the structure required by the Aimeos version being used.

---

# 10. Composer

Aimeos extensions should be installable through Composer.

For local Laravel development, Aimeos documents the use of:

```text
packages/
```

for local project-specific extensions.

Composer path repositories may be used when appropriate.

After adding new classes, if Composer does not discover them:

```bash
composer dump-autoload
```

Do not manually modify generated Composer autoload files.

---

# 11. MShop

MShop is the core data-access/domain layer.

It provides domains such as:

* Product
* Category
* Customer
* Order
* Supplier
* Stock
* Price
* Text
* Media
* Service
* Coupon
* Subscription
* Site
* Locale
* and other commerce domains

The exact available domains depend on the installed Aimeos version/extensions.

MShop uses managers and items.

Think of them as:

```text
Manager
   |
   +---- find/search
   +---- get
   +---- save
   +---- delete
   +---- filter
   |
   v
 Item
```

Managers handle persistence and querying.

Items represent domain data.

---

# 12. Managers

Managers are the primary way to access MShop data.

Do not directly query Aimeos tables from application code unless there is a
specific documented reason.

Prefer:

```text
Manager -> Item
```

over:

```text
Application -> raw SQL -> Aimeos table
```

Managers hide storage implementation details.

This abstraction allows Aimeos to support different storage strategies without
forcing application code to understand the underlying implementation.

---

# 13. Items

Items represent individual domain objects.

Typical operations include:

```php
$item->get('code');
$item->get('label');
$item->get('status');
```

Use the item APIs instead of assuming database columns are always the public
contract.

When custom data is needed, investigate whether the item already supports:

* properties
* attributes
* texts
* list relations
* other existing extension mechanisms

before adding a database column.

---

# 14. Before Extending a Database Table

When a developer requests:

> "Add a new field to products"

Do NOT immediately add a database column.

First ask:

1. Can this be stored as a property?
2. Can it be represented by an attribute?
3. Can it use a text?
4. Can it use a list relation?
5. Does an existing Aimeos extension already support it?
6. Is a custom domain actually required?

Only add a new database column when the data genuinely belongs in the
existing table and there is no better Aimeos-native representation.

Aimeos explicitly notes that extending existing managers/tables is often
unnecessary because data can frequently be stored using attributes, texts,
properties, or list tables.

---

# 15. Manager Decorators

Prefer decorators when you need to modify or augment existing manager
behavior without inheriting the manager directly.

Conceptually:

```text
Original Manager
      |
      v
Decorator A
      |
      v
Decorator B
      |
      v
Application
```

A decorator can:

* modify parameters
* modify results
* execute logic before a method
* execute logic after a method
* stop execution
* delegate to the wrapped manager

This allows multiple extensions to modify the same manager without creating a
large inheritance hierarchy.

Aimeos specifically documents manager decorators as an easy way to extend
existing managers.

---

# 16. Manager Decorator Location

A custom decorator normally follows the original manager namespace.

Example:

```text
src/
└── MShop/
    └── Product/
        └── Manager/
            └── Decorator/
                └── Myproject.php
```

Then configure it in the appropriate Aimeos configuration.

Example pattern:

```php
return [
    'product' => [
        'manager' => [
            'decorators' => [
                'local' => [
                    'Myproject',
                ],
            ],
        ],
    ],
];
```

Do not assume this exact configuration is correct for every domain/version.
Verify the configuration path against the installed Aimeos version.

---

# 17. Creating a New Manager

Create a new manager when the application needs a genuine new domain or a
data structure that does not fit existing MShop domains.

Typical process:

```text
1. Define the domain
2. Define the database schema
3. Create setup/schema files
4. Create the manager
5. Define save attributes
6. Implement required manager behavior
7. Register/configure the manager
8. Add tests
```

Aimeos managers generally derive from the common manager infrastructure and
implement the appropriate manager interface.

---

# 18. Database Schema

Database changes belong in the Aimeos extension.

Do not manually modify production database schemas without a corresponding
Aimeos setup/migration mechanism.

Typical location:

```text
setup/
└── default/
    └── schema/
```

Schema definitions should use the Aimeos-supported schema mechanisms.

After creating/updating schema definitions, run the appropriate Aimeos setup
command for the host application.

Laravel example:

```bash
php artisan aimeos:setup
```

Do not assume a raw Laravel migration is interchangeable with an Aimeos
setup migration.

---

# 19. Nullable Custom Columns

When extending an existing Aimeos table with an additional custom column,
follow Aimeos's documented schema rules.

For example:

```php
return [
    'table' => [
        'mshop_product' => function (
            \Aimeos\Upscheme\Schema\Table $table
        ) {
            $table->string('mycolumn', 32)->null(true);
        },
    ],
];
```

The custom column should be nullable when extending an existing Aimeos table,
as documented by Aimeos.

---

# 20. Frontend Architecture

Do not treat Aimeos frontend functionality as a collection of random Laravel
views.

Aimeos has its own frontend client/component architecture.

Important frontend concepts include:

* clients
* components
* subparts
* templates
* parameters
* view helpers
* themes

When modifying frontend behavior, first determine whether the requirement
belongs to:

```text
Template
Component
Subpart
Client
Controller
Theme
Configuration
```

Choose the smallest appropriate extension point.

---

# 21. Blade Templates

For Laravel integration, Aimeos uses Blade templates.

Do not modify vendor templates directly.

Instead:

1. Identify the original template.
2. Copy/override it through the project's customization mechanism.
3. Keep the customization inside the project's extension/theme.
4. Upgrade safely later.

Aimeos documents template customization as part of its Laravel extension
workflow.

---

# 22. Themes

A theme should be treated separately from business logic.

Use a theme for:

* visual design
* HTML/CSS
* frontend assets
* shared JavaScript
* presentation changes

Do not put business/domain logic inside a theme.

Portable Aimeos themes can be distributed as Composer packages.

---

# 23. Frontend Components

When adding or modifying a page:

First determine whether you can:

* reorder an existing component
* add an existing component
* remove a component
* configure a component
* extend an existing component
* create a new component

Do not create a new Laravel controller if the requirement can be solved by
an Aimeos client/component.

---

# 24. Controllers

Aimeos controllers are business-logic controllers.

They are not necessarily equivalent to:

```text
Laravel Controller
```

Do not automatically create:

```text
ProductController
OrderController
CheckoutController
```

inside Laravel when Aimeos already provides the required business logic.

Use Aimeos controllers/components where appropriate.

---

# 25. APIs

Aimeos has different API layers.

Important distinction:

```text
Customer/frontend API
        |
        v
JSON API

Administration API
        |
        v
GraphQL / administration interfaces
```

Always determine whether the consumer is:

* storefront
* mobile app
* external integration
* administrator
* internal automation

before choosing an API.

---

# 26. JSON API

The Aimeos JSON API is intended for frontend/custom client use cases.

Use it when a custom frontend needs access to Aimeos commerce functionality.

Do not expose administrative functionality through a frontend API merely
because both use JSON.

Authentication and authorization must be considered.

---

# 27. Administration API

Aimeos historically provided a JSON administration API.

However, the current documentation marks the JSON administration API as
deprecated and states that it should be replaced by the GraphQL administration
API.

Therefore:

```text
New administration integration
        |
        v
Prefer GraphQL
```

Do not introduce new dependencies on the deprecated administration JSON API
unless there is a specific compatibility requirement.

---

# 28. GraphQL

Use GraphQL when the project's Aimeos version and installed extensions provide
the required administration/API functionality.

Before implementation:

1. Verify GraphQL extension availability.
2. Verify authentication.
3. Verify resource support.
4. Verify mutations/operations required.
5. Verify permissions.
6. Verify the API endpoint for the host application.

Do not assume every Aimeos installation exposes every GraphQL resource.

---

# 29. Authentication

Authentication belongs to the host application/integration unless Aimeos
specifically provides the required authentication mechanism.

Never invent a second authentication system unnecessarily.

Before adding authentication:

1. Determine who the user is.
2. Determine whether they are a customer or administrator.
3. Determine which host application authentication is already available.
4. Determine what Aimeos expects.
5. Add only the missing authorization logic.

Never bypass Aimeos authorization simply because an internal API is being
called.

---

# 30. Service Providers

Aimeos service providers are used for external services such as:

* payment
* delivery/shipping
* other service integrations

A provider should encapsulate integration-specific behavior.

Do not put payment-provider-specific logic into generic checkout code.

Use the provider abstraction.

---

# 31. Payment Providers

When implementing a payment integration:

```text
Checkout
   |
   v
Aimeos payment service
   |
   v
Payment provider
   |
   v
External payment gateway
```

Keep gateway-specific details inside the provider.

Do not scatter:

```text
Stripe API calls
PayPal API calls
Bank API calls
```

throughout checkout/business logic.

---

# 32. Delivery Providers

Use Aimeos delivery/service-provider abstractions for shipping integrations.

Keep:

* API credentials
* request construction
* external status mapping
* shipping labels
* tracking logic

inside the appropriate provider/integration layer.

Do not put carrier-specific code into generic order logic.

---

# 33. Decorators

Decorators are a major Aimeos extension mechanism.

Use decorators when functionality should wrap an existing provider/service.

Conceptually:

```text
Application
    |
    v
Decorator A
    |
    v
Decorator B
    |
    v
Actual Provider
```

Decorators can:

* validate conditions
* modify behavior
* add logging
* enforce business rules
* stop execution
* delegate to the wrapped provider

Aimeos supports stacking decorators, allowing multiple behaviors to be
combined.

---

# 34. Basket Plugins

Use basket plugins for logic that should participate in basket/order
processing.

Examples can include:

* validation
* automatic basket modifications
* additional charges
* custom business rules
* order constraints

Do not add basket logic directly to unrelated controllers.

---

# 35. Pricing Rules

Pricing logic should use Aimeos pricing mechanisms when the requirement fits
them.

Before implementing custom price calculations:

1. Check existing price rules.
2. Check product/price configuration.
3. Check service pricing.
4. Check coupon functionality.
5. Determine whether a custom pricing rule/provider is required.

Avoid duplicating Aimeos pricing logic in application controllers.

---

# 36. Jobs / Cron

Background processing should use Aimeos job controllers where applicable.

Typical jobs include:

* imports
* exports
* order processing
* order cleanup
* email processing
* subscription processing
* stock updates
* index rebuilding
* cache maintenance
* media processing

Do not create a Laravel scheduled job that duplicates an existing Aimeos job
without a reason.

First inspect available Aimeos jobs.

---

# 37. Imports

For external data imports:

```text
External data
     |
     v
Import job
     |
     v
Validation/mapping
     |
     v
MShop manager
     |
     v
Aimeos storage
```

Do not bypass managers and write directly into Aimeos tables unless the
specific operation explicitly requires it and has been designed accordingly.

---

# 38. Configuration

Aimeos is highly configuration-driven.

Do not hard-code values that Aimeos expects to be configurable.

Examples:

* manager names
* decorators
* providers
* service configuration
* templates
* client settings
* cache
* database resources
* frontend behavior

When changing behavior, first search the Aimeos configuration tree.

Prefer:

```text
Configuration
```

over:

```text
Hard-coded behavior
```

when Aimeos provides a configuration option.

---

# 39. Configuration Override Rule

Custom configuration should live in the project's extension.

Do not edit:

```text
vendor/aimeos/...
```

configuration files.

A project extension can override/extend configuration through the Aimeos
extension mechanism.

Ensure extension dependencies/loading order are correct so custom
configuration is applied at the intended point.

---

# 40. Templates

When overriding templates:

1. Find the original template.
2. Understand which client/component renders it.
3. Copy it into the appropriate custom extension/theme location.
4. Change only what is required.
5. Avoid copying huge templates when a smaller customization mechanism
   exists.

Do not edit the vendor template.

---

# 41. Translation

Project-specific translations belong in the project extension.

Do not modify Aimeos translation files directly.

When adding user-facing text:

```text
Source code
     |
     v
Translation key
     |
     v
i18n
```

Avoid hard-coded UI strings when the surrounding Aimeos component is
translation-aware.

---

# 42. Testing

Every custom Aimeos feature should have an appropriate test strategy.

Consider:

```text
Unit tests
Integration tests
Manager tests
Provider tests
Component tests
API tests
End-to-end tests
```

The appropriate level depends on the change.

For manager/provider changes, test the Aimeos abstraction rather than only
testing the final HTTP response.

Aimeos documents extension testing using its development/test environment and
Phing.

---

# 43. Testing Database Changes

For schema-related functionality:

1. Start with a clean test database.
2. Run Aimeos setup.
3. Verify schema creation.
4. Test manager operations.
5. Test update paths.
6. Test rollback/reinstallation behavior where applicable.

Do not assume that because a migration runs successfully the manager is
correct.

---

# 44. Debugging

When debugging an Aimeos issue, identify the layer first.

Use this sequence:

```text
HTTP request
    ↓
Host application
    ↓
Aimeos client
    ↓
Aimeos controller
    ↓
Manager
    ↓
Item
    ↓
Provider / infrastructure
    ↓
Database / external service
```

Ask:

> At which layer does the behavior become incorrect?

Then debug that layer.

---

# 45. Do Not Guess Class Names

Aimeos uses factories, managers, decorators, clients and configuration.

Do not invent class names such as:

```text
ProductService
OrderRepository
AimeosProductService
CheckoutManager
```

just because they sound reasonable.

First identify the actual Aimeos manager/client/controller/provider.

---

# 46. Do Not Invent Configuration Keys

This is critical.

Never create configuration such as:

```php
'aimeos.foo.bar'
```

without verifying that the Aimeos version supports it.

Search the documentation/source/configuration for the real key.

Aimeos has extensive configuration and small naming differences can cause
silent or confusing failures.

---

# 47. Do Not Bypass Managers

Avoid:

```php
DB::table('mshop_product')->...
```

for normal Aimeos domain operations.

Prefer the relevant MShop manager.

Direct SQL/database access should only be considered for:

* infrastructure-level operations
* special reporting
* migration/schema work
* performance-critical operations with an explicit design
* operations not represented by Aimeos APIs

Even then, understand the implications for indexes, relations, caches and
Aimeos expectations.

---

# 48. Existing Domain vs New Domain

Use this decision tree.

```text
Do we need new data?
       |
       +-- Can existing item properties handle it?
       |          |
       |          +-- YES → use properties
       |
       +-- Can attributes/text/list relations handle it?
       |          |
       |          +-- YES → use existing mechanism
       |
       +-- Is it logically part of an existing domain?
       |          |
       |          +-- YES → extend existing domain carefully
       |
       +-- Is it a genuinely new business domain?
                  |
                  +-- YES → create new manager/domain
```

This prevents unnecessary schema and manager complexity.

---

# 49. Extending Existing Managers

When an existing manager must be extended:

1. Determine whether a decorator is enough.
2. If yes, use a decorator.
3. If inheritance is genuinely necessary, extend the manager in the custom
   extension.
4. Add schema changes if required.
5. Add tests.
6. Configure the custom manager/decorator.

Aimeos documents both the decorator approach and the custom manager approach.

---

# 50. External Integrations

When integrating an external system:

```text
Aimeos
   |
   v
Integration layer
   |
   v
External API
```

Keep external API concerns isolated.

Recommended responsibilities:

```text
Provider/client
    - authentication
    - HTTP communication
    - request mapping
    - response mapping
    - error translation

Aimeos domain
    - business rules
    - persistence
    - commerce state
```

Do not mix HTTP client details with MShop domain logic.

---

# 51. Error Handling

When handling errors:

1. Preserve the Aimeos abstraction.
2. Translate external exceptions where appropriate.
3. Do not leak credentials or sensitive API responses.
4. Log useful diagnostic information.
5. Avoid exposing internal stack traces to customers.
6. Preserve enough context for debugging.

For external providers, distinguish:

```text
Validation error
Authentication error
External API error
Timeout
Temporary failure
Permanent failure
Business rejection
```

---

# 52. Performance

Aimeos is designed with performance and scalability in mind.

Do not optimize by immediately bypassing Aimeos architecture.

First investigate:

* query filters
* indexes
* manager usage
* caching
* configuration
* batching
* pagination
* search/index mechanisms
* unnecessary object loading
* repeated API calls

Only bypass an abstraction when profiling demonstrates that it is actually
the bottleneck.

---

# 53. Caching

Before implementing custom caching:

1. Determine whether Aimeos already caches the relevant data.
2. Understand cache invalidation.
3. Determine whether the data is site/user/language/currency-specific.
4. Avoid caching personalized or permission-sensitive information globally.

Never add a global cache to customer-specific data without considering
isolation.

---

# 54. Multi-site / Locale

Aimeos supports concepts such as:

* sites
* languages
* currencies

Never assume that an application has only:

```text
one site
one language
one currency
```

When building URLs, queries, imports or API integrations, verify the active
site/locale context.

---

# 55. Security

Always consider:

* authentication
* authorization
* tenant/site boundaries
* customer ownership
* administrator permissions
* input validation
* output encoding
* CSRF where applicable
* API credentials
* webhook verification
* sensitive logs

Never expose administration APIs to unauthenticated customers.

Never trust IDs supplied by the frontend without authorization checks.

---

# 56. API Security

For API work:

```text
Authentication
     ↓
Authorization
     ↓
Validation
     ↓
Aimeos operation
     ↓
Response
```

Do not assume authentication automatically means authorization.

A logged-in customer must not automatically gain access to:

* another customer's orders
* administrator data
* internal configuration
* supplier information
* unrestricted shop data

---

# 57. Code Style

Prefer code that follows the existing Aimeos conventions.

Do not introduce unnecessary framework abstractions.

Avoid:

```text
Repository
Service
Manager
Factory
Adapter
Handler
Utility
Helper
```

layers unless they solve a real problem.

Aimeos already has abstractions for many of these concerns.

---

# 58. PHP Version Compatibility

Before using PHP features:

1. Check the project's PHP version.
2. Check the installed Aimeos version.
3. Check Composer constraints.
4. Follow the project's existing coding style.

Do not use the newest PHP syntax simply because the local machine supports
it.

---

# 59. Version Awareness

Aimeos documentation is versioned.

Always distinguish:

```text
latest
2026.x
2025.x
master
older versions
```

Do not blindly copy code from an older version.

When answering implementation questions, state the relevant version if known.

If the project version is unknown and the behavior is version-sensitive,
inspect the project's Composer configuration/package versions before giving
specific implementation instructions.

---

# 60. Upgrade Safety

Every customization should answer:

> "Will this survive an Aimeos upgrade?"

If the answer is no, redesign it.

Preferred:

```text
Custom extension
     ↓
Aimeos extension point
     ↓
Aimeos core
```

Avoid:

```text
Edit vendor package
     ↓
Hope Composer doesn't overwrite it
```

---

# 61. Change Review Checklist

Before approving an Aimeos change, check:

* [ ] Is the Aimeos version known?
* [ ] Is the host application known?
* [ ] Is the correct Aimeos layer identified?
* [ ] Is Aimeos core untouched?
* [ ] Are official extensions untouched?
* [ ] Is custom code inside the project extension?
* [ ] Could existing properties/attributes/list relations solve the problem?
* [ ] Could a decorator solve the problem?
* [ ] Is a new manager actually necessary?
* [ ] Are database changes represented in Aimeos setup/schema?
* [ ] Are configuration changes in the custom extension?
* [ ] Are templates overridden safely?
* [ ] Are APIs appropriate for the consumer?
* [ ] Is authentication/authorization correct?
* [ ] Are tests included?
* [ ] Is the implementation upgrade-safe?

---

# 62. Implementation Decision Tree

When given an Aimeos requirement, follow this process.

```text
START
  |
  v
What is being changed?
  |
  +-- UI
  |    |
  |    +-- Existing template → override template
  |    +-- Existing component → configure/extend component
  |    +-- New behavior → create component/client
  |    +-- Visual only → theme
  |
  +-- Data
  |    |
  |    +-- Existing property → use property
  |    +-- Existing attribute/text/list → use it
  |    +-- Existing domain → extend domain
  |    +-- New domain → create manager
  |
  +-- Business logic
  |    |
  |    +-- Existing controller → extend/use controller
  |    +-- Manager behavior → decorator/manager
  |    +-- Service behavior → provider/decorator
  |
  +-- External service
  |    |
  |    +-- Payment → payment provider
  |    +-- Delivery → delivery provider
  |    +-- Other service → appropriate provider/integration
  |
  +-- API
  |    |
  |    +-- Customer/frontend → client JSON API where appropriate
  |    +-- Administration → prefer supported GraphQL API
  |
  +-- Background processing
       |
       +-- Existing job → configure/use it
       +-- New recurring behavior → create job controller
```

---

# 63. Preferred Development Workflow

For every non-trivial Aimeos feature:

## Step 1 — Understand the requirement

Write down:

```text
Actor:
Action:
Data:
Business rule:
Expected result:
```

## Step 2 — Identify the Aimeos domain

Examples:

```text
Product
Customer
Order
Catalog
Basket
Checkout
Service
Payment
Delivery
Subscription
```

## Step 3 — Identify the layer

```text
Frontend
Client
Controller
Manager
Item
Provider
Decorator
Infrastructure
API
Admin
Job
Theme
```

## Step 4 — Check existing functionality

Before writing code, search:

* Aimeos docs
* existing project extension
* installed Aimeos extensions
* manager methods
* configuration
* decorators
* providers
* existing components

## Step 5 — Select the smallest extension point

Prefer:

```text
Configuration
    >
Template override
    >
Decorator
    >
Existing component extension
    >
Existing manager extension
    >
New component
    >
New manager/domain
```

Only use a more invasive mechanism when necessary.

## Step 6 — Implement in the project extension

Do not modify vendor code.

## Step 7 — Add setup/schema if required

Use Aimeos-supported setup mechanisms.

## Step 8 — Add tests

Test the actual layer being changed.

## Step 9 — Verify upgrade safety

Make sure Composer updates won't destroy the implementation.

---

# 64. Common Bad Approaches

Avoid these patterns.

## Bad

```text
Edit vendor/aimeos/...
```

## Correct

```text
Create project-specific extension
```

---

## Bad

```text
DB::table('mshop_product')
```

for normal product operations.

## Correct

```text
Product manager
```

---

## Bad

```text
Create ProductRepository
Create ProductService
Create ProductController
```

without checking existing Aimeos functionality.

## Correct

```text
Use/extend MShop manager and Aimeos client/controller
```

---

## Bad

```text
Add a new product column immediately
```

## Correct

```text
Check property/attribute/text/list mechanisms first
```

---

## Bad

```text
Put payment gateway logic inside CheckoutController
```

## Correct

```text
Payment service provider
```

---

## Bad

```text
Create custom middleware to reproduce Aimeos behavior
```

## Correct

```text
Use the appropriate Aimeos layer
```

---

## Bad

```text
Use deprecated JsonAdm API for a brand-new admin integration
```

## Correct

```text
Check and prefer the current GraphQL administration API
```

The current documentation explicitly marks JsonAdm as deprecated.

---

# 65. Explaining Aimeos to Developers

When explaining Aimeos to someone unfamiliar with it, use simple language.

Explain:

```text
Host application
=
The Laravel/TYPO3/etc. application containing Aimeos.

Extension
=
Your safe customization package.

Client
=
The part that prepares frontend/API output.

Controller
=
Business logic.

Manager
=
How Aimeos reads/writes domain data.

Item
=
The actual domain object/data.

Provider
=
Integration with external services.

Decorator
=
A wrapper that adds behavior around an existing object.

Theme
=
Presentation/UI customization.

Setup
=
Database/schema installation and updates.

Context
=
The Aimeos runtime/DI environment.
```

Avoid explaining Aimeos as if it were a conventional Laravel CRUD package.

---

# 66. When Asked to "Add an API"

First determine:

```text
Who consumes it?
```

Then:

```text
Customer frontend?
Mobile app?
External integration?
Admin dashboard?
Internal automation?
```

Then select the appropriate Aimeos API/client.

Do not automatically create:

```text
routes/api.php
ApiController.php
```

before checking whether the Aimeos API/client architecture already provides
the required functionality.

---

# 67. When Asked to "Add a Field"

Use this order:

```text
Existing property?
        ↓
Existing attribute?
        ↓
Existing text?
        ↓
Existing list relation?
        ↓
Existing table extension?
        ↓
New domain?
```

Do not jump directly to schema modification.

---

# 68. When Asked to "Change Checkout"

Determine whether the requirement is:

```text
UI change
Business rule
Basket behavior
Payment behavior
Delivery behavior
Order processing
```

Then route it to the appropriate Aimeos layer.

For example:

```text
Payment restriction
→ payment provider/decorator

Basket restriction
→ basket plugin

Visual checkout change
→ client/component/template

Order processing
→ controller/job/provider as appropriate
```

---

# 69. When Asked to "Add a Payment Gateway"

Recommended reasoning:

```text
1. Identify existing provider architecture.
2. Determine whether an existing payment provider/extension can be reused.
3. If not, implement a payment provider.
4. Keep gateway API communication inside the provider.
5. Add configuration for credentials/settings.
6. Add error handling.
7. Add tests.
8. Register/configure the provider.
```

Do not modify checkout internals merely to communicate with the gateway.

---

# 70. When Asked to "Add Shipping"

Use the delivery provider architecture.

Determine:

```text
Rates?
Availability?
Shipment creation?
Tracking?
Labels?
Webhook/status updates?
```

Map those requirements to the Aimeos delivery/provider mechanisms.

---

# 71. When Asked to "Add a Custom Admin Screen"

Determine:

```text
Does it belong in existing JQAdm?
Can existing admin resources represent it?
Is GraphQL needed?
Is a completely new admin client required?
```

Do not automatically create a separate Laravel admin panel.

---

# 72. When Asked to "Import Products"

First check existing Aimeos import jobs.

If the source is custom:

```text
Source
  ↓
Mapping
  ↓
Validation
  ↓
Aimeos manager
  ↓
Product/item relationships
```

Do not directly insert product rows without understanding related:

* texts
* prices
* media
* categories
* attributes
* lists
* stock
* sites

---

# 73. When Asked to "Sync Orders"

Determine:

```text
Direction:
Aimeos → external system
external system → Aimeos
bidirectional
```

Then define:

```text
source of truth
identifier mapping
status mapping
retry strategy
idempotency
failure handling
```

Use Aimeos managers/controllers/providers/jobs as appropriate.

---

# 74. External API Reliability

For integrations:

```text
Request
  |
  +-- timeout
  +-- retry
  +-- validation
  +-- authentication
  +-- response mapping
  +-- error mapping
```

Never blindly retry payment operations.

For non-idempotent operations, explicitly consider idempotency before retrying.

---

# 75. Logging

Logs should answer:

```text
What happened?
Which entity?
Which operation?
Which external system?
What was the result?
Why did it fail?
```

Do not log:

* passwords
* API secrets
* tokens
* payment credentials
* sensitive customer data unnecessarily

Use the host/Aimeos logging infrastructure rather than creating random
logging systems.

---

# 76. Code Review Questions

When reviewing Aimeos code, ask:

### Architecture

* Is this the correct Aimeos layer?
* Is an existing mechanism being ignored?

### Extension

* Is vendor code untouched?
* Is the customization inside the project extension?

### Data

* Is a manager being used?
* Was schema modification actually necessary?

### Configuration

* Are configuration keys valid?
* Are they version-compatible?

### Security

* Is authorization correct?
* Can one customer access another customer's data?

### Performance

* Is there unnecessary querying?
* Is pagination used?
* Are repeated manager/API calls avoidable?

### Upgradeability

* Will the change survive a package update?

---

# 77. Minimality Rule

Prefer the smallest correct Aimeos-native implementation.

If the requirement can be solved with:

```text
1 configuration change
```

do not create:

```text
3 classes + 2 services + 1 controller + middleware
```

If a decorator is enough, don't replace the manager.

If a property is enough, don't create a new database domain.

If a template override is enough, don't create a new frontend architecture.

---

# 78. Source-of-Truth Rule

When implementing Aimeos functionality:

Priority should generally be:

```text
Installed project version
        ↓
Official Aimeos documentation for that version
        ↓
Official Aimeos source code
        ↓
Official extension documentation
        ↓
Existing project implementation
        ↓
Community examples
```

Do not treat random blog posts or Stack Overflow snippets as authoritative.

---

# 79. Documentation References

Use the official Aimeos documentation:

* Aimeos documentation:
  https://aimeos.org/docs/latest/

* Architecture:
  https://aimeos.org/docs/master/developer/architecture/

* Create extensions:
  https://aimeos.org/docs/master/developer/extensions/

* Extend managers:
  https://aimeos.org/docs/2026.x/models/extend-managers/

* Create managers:
  https://aimeos.org/docs/master/models/create-managers/

* Laravel extension:
  https://aimeos.org/docs/master/laravel/

* Laravel extension/customization:
  https://aimeos.org/docs/2025.x/laravel/extend/

* Themes:
  https://aimeos.org/docs/latest/frontend/html/create-themes/

* Administration JSON API:
  https://aimeos.org/docs/2026.x/admin/jsonadm/

Use the documentation version matching the project's installed Aimeos version
whenever possible.

---

# 80. Final Rule

When working with Aimeos, always think:

```text
Don't modify core.
        ↓
Find the correct extension point.
        ↓
Use Aimeos architecture.
        ↓
Prefer existing managers/items.
        ↓
Prefer configuration and decorators.
        ↓
Use custom extensions for project code.
        ↓
Use setup mechanisms for schema.
        ↓
Test the appropriate layer.
        ↓
Keep everything upgrade-safe.
```

The goal is not merely to make the feature work.

The goal is to make it work **the Aimeos way**.

That means the resulting implementation should be:

* Aimeos-native
* upgrade-safe
* modular
* testable
* configurable
* maintainable
* compatible with the existing architecture
* free from unnecessary custom abstractions
* free from vendor modifications
