# Woo Extender

> A modular WooCommerce extension framework built on clean architecture principles, designed to enable scalability and maintainable custom development.

![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759B.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-8.0%2B-96588A.svg)
![License](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)
![Build Status](https://img.shields.io/github/actions/workflow/status/github.com/Invisiblec/woo-extender/tests.yml?branch=main&label=build)
![Coverage](https://img.shields.io/codecov/c/github/github.com/Invisiblec/woo-extender?color=success)
![Code Quality](https://img.shields.io/codefactor/grade/github/github.com/Invisiblec/woo-extender?color=success)

---

## Table of Contents

- [Overview](#overview)
- [Vision](#vision)
- [Goals](#goals)
- [Current Features](#current-features)
- [Planned Features](#planned-features)
- [Design Principles](#design-principles)
- [Architecture](#architecture)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Directory Responsibilities](#directory-responsibilities)
- [Source Code Structure](#source-code-structure)
- [Directory Details](#directory-details)
- [Validation](#validation)
- [View Layer](#view-layer)
- [Dependency Injection](#dependency-injection)
- [Service Providers](#service-providers)
- [Hook Registration](#hook-registration)
- [Error Handling](#error-handling)
- [Logging](#logging)
- [Configuration](#configuration)
- [Coding Standards](#coding-standards)
- [Naming Conventions](#naming-conventions)
- [Security](#security)
- [Development Workflow](#development-workflow)
- [Contributing](#contributing)
- [Roadmap](#roadmap)
- [License](#license)
- [Acknowledgements](#acknowledgements)
- [Future Documentation](#future-documentation)

---

## Overview

Woo Extender is a modular WooCommerce plugin designed to extend WooCommerce capabilities while maintaining a structured, scalable, and decoupled architecture.

Unlike traditional WordPress development patterns where logic often becomes tightly coupled to core actions and filters, Woo Extender introduces an application layer between the WordPress environment and the domain business logic. It applies recognized software engineering principles to typical plugin scenarios:

- SOLID Principles
- Separation of Concerns
- Dependency Injection (DI)
- Modular Architecture
- Service-Oriented Design
- PSR Standards Compliance
- WordPress Coding Best Practices

The project provides a maintainable foundation for advanced WooCommerce features without increasing technical debt.

---

## Vision

As WooCommerce business requirements grow, standard custom implementations can become difficult to maintain due to scattered procedural code.

Woo Extender addresses this challenge by introducing a structured application layer. Instead of writing inline callback functions across various hooks, each feature is contained within dedicated domain modules with defined responsibilities.

The long-term vision is to establish a robust foundation for complex systems, such as inventory control and product management, while remaining compatible with the WooCommerce core ecosystem.

---

## Goals

- **Maintainability:** Isolate core business logic from WordPress internals.
- **Extensibility:** Simplify the addition of new features through modular design.
- **Technical Debt Reduction:** Limit tightly-coupled dependencies and global state.
- **Code Clarity:** Organize files logically so that the purpose of each class is evident.
- **DRY (Don't Repeat Yourself):** Centralize shared operations within reusable services.
- **Compatibility:** Ensure complete interoperability with core WordPress and WooCommerce APIs.
- **Modern Standards:** Encourage modern PHP development practices within the WordPress ecosystem.

---

## Current Features

The initial framework implementation includes:

- **Custom Metadata Architecture:** A structured approach for managing product attributes.
- **Advanced Product Options:** API structures to extend standard product options.
- **Extensible Settings Engine:** Clean integration with WordPress and WooCommerce settings pages.
- **Dedicated Validation Layer:** Early request and data validation before processing.
- **Modular Admin Interfaces:** Decoupled administration page handling.
- **Service Container & Registration:** Automatic and manual service binding.
- **Custom Database Migration Schema:** Database-agnostic schema management practices.

---

## Planned Features

The following modules are planned for future iterations.

### Inventory

- **Inventory Batches:** Manage stock items in structured lots.
- **Purchase Lots:** Track batch-level cost of goods.
- **Valuation Methods:** Support for FIFO, LIFO, and Average Cost calculations.
- **Batch Expiration:** Track expiration and shelf life for perishable batches.
- **Production Dates:** Track manufacturing dates at the batch level.

### Supplier Management

- **Supplier Profiles:** Basic supplier directories and contact cards.
- **Purchase History:** Historic tracking of cost changes by supplier.
- **Default Supplier Bindings:** Associate default suppliers with products or variations.
- **Supplier Performance metrics:** Simple metrics on lead times and accuracy.

### Warranty Management

- **Warranty Providers:** Register in-house or third-party warranty entities.
- **Warranty Periods:** Define varying durations (days, months, years).
- **Product Associations:** Assign warranty rules to specific products or categories.
- **Multiple Warranties:** Support tiered or optional extended warranties.

### Product Management

- **Product Notes:** Internal notes for product operations.
- **Procurement Cost Tracking:** Log historical purchase prices.
- **Selling Restrictions:** Impose minimum, maximum, or incremental step quantities on cart operations.
- **Delivery Information:** Custom shipping lead times at the product level.

### Reporting

- **Purchase Analysis:** Costs over time.
- **Inventory Valuation Reports:** Current inventory asset calculations.
- **Profit Margin Tracking:** Analysis of revenue against actual batch costs.

---

## Design Principles

- **Single Responsibility Principle (SRP):** Each class must have a single reason to change.
- **Open/Closed Principle (OCP):** Behavior should be extendable without modifying existing source code.
- **Dependency Injection (DI):** Classes should be passed their dependencies rather than instantiating them internally.
- **Composition over Inheritance:** Build complex behavior by combining simple, focused objects.
- **Convention over Configuration:** Rely on sensible defaults to reduce boilerplate code.
- **Defensive Programming:** Anticipate errors, validate data, and fail gracefully.

---

## Architecture

Woo Extender uses a layered architectural design to mediate between WordPress hooks and data persistence:

```text
          WordPress Core
                │
         WooCommerce Core
                │
        Plugin Bootstrap (Container/Kernel)
                │
        Service Providers
                │
     Hooks (Action / Filter Subscribers)
                │
           Controllers (Request/Response)
                │
         Validation Layer
                │
         Services (Business Logic)
                │
        Repositories (Data Abstraction)
                │
      Database (Custom Tables / WPDB)
```

A technical breakdown of this architecture is available in: `docs/ARCHITECTURE.md`

---

## Performance & Caching

Enterprise-grade operations, particularly inventory valuation and reporting, require strict performance management. Woo Extender incorporates a multi-level caching strategy:

- **WordPress Object Cache:** Repetitive database queries (e.g., fetching supplier details or active batches) are cached using the WP Object Cache API (`wp_cache_set`, `wp_cache_get`).
- **Transients API:** Expensive calculations, such as monthly COGS (Cost of Goods Sold) or total inventory valuation, are stored as transients and invalidated only when underlying data changes.
- **Cache Invalidation:** Services are responsible for clearing related caches when write operations (Insert/Update/Delete) occur. Repositories should never manage cache invalidation directly.

---

## Technology Stack

- PHP: 8.1 or higher (incorporating strict types, constructor promotion, and readonly properties)
- WordPress: 6.0 or higher
- WooCommerce: 8.0 or higher
- Composer: For dependency management and PSR-4 autoloading
- Database: MySQL 8.0 or MariaDB 10.5+

Development tools to be added in future updates:

- PHPUnit for automated testing
- PHPStan for static analysis
- PHP_CodeSniffer / Laravel Pint for style consistency
- GitHub Actions for continuous integration (CI)

---

## Requirements

- PHP 8.1+
- WordPress 6.0+
- WooCommerce 8.0+
- MySQL 8.0+ or MariaDB 10.5+
- Composer 2.x

---

## Installation

### Workflow

```text
┌─────────────────┐
│  Verify Server  │
│  Requirements   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Clone Repository│
│ into plugins/   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Run:            │
│ composer install│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Activate Plugin │
│ in WordPress    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Setup/Configure │
│  WooCommerce    │
└─────────────────┘
```

### Installation Steps

1. Clone the repository into your WordPress plugins directory (wp-content/plugins/):

```bash
git clone <repository-url> woo-extender
```

2. Navigate to the plugin directory and install dependencies:

```bash
cd woo-extender
composer install --no-dev --optimize-autoloader
```

3. Activate Woo Extender via the WordPress Admin Plugins dashboard or WP-CLI:

```bash
wp plugin activate woo-extender
```

## Project Structure

The project is organized into independent modules with clearly defined responsibilities.

```text
woo-extender/
├── assets/                  # Public static assets
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── config/                  # Configuration files
├── database/                # Database migrations, schemas, and seeds
│   ├── migrations/
│   ├── schema/
│   └── seeders/
├── docs/                    # Technical documentation
│   ├── README.md
│   ├── ARCHITECTURE.md
│   ├── DATABASE.md
│   └── API.md
├── languages/               # Internationalization (i18n) files
├── resources/               # Uncompiled views and templates
│   ├── views/
│   ├── templates/
│   └── icons/
├── routes/                  # Routing structures (REST API routes)
├── src/                     # PSR-4 Autoloaded source code
│   ├── Admin/
│   ├── Bootstrap/
│   ├── Controllers/
│   ├── Core/
│   ├── Database/
│   ├── DTO/
│   ├── Exceptions/
│   ├── Helpers/
│   ├── Hooks/
│   ├── Interfaces/
│   ├── Models/
│   ├── Providers/
│   ├── Repositories/
│   ├── Services/
│   ├── Traits/
│   ├── Validation/
│   └── View/
├── storage/                 # Local workspace (cache, lock files, internal logs)
├── tests/                   # Automated tests suite
├── vendor/                  # Third-party dependencies (Composer)
├── composer.json            # Composer configuration
├── woo-extender.php         # Main plugin entrypoint file
└── README.md                # General project documentation
```

---

## Directory Responsibilities

Every directory serves a single, specific purpose within the project structure.

### assets/

Contains static resources that are directly requested by the client browser.

Examples:

- CSS stylesheets
- Client-side JavaScript files
- Publicly accessible images and web fonts

Note: No PHP execution or business logic is permitted in this directory.

---

### config/

Contains native PHP array files returning configuration data.

Examples:

- Feature flags
- Default setting arrays
- Non-sensitive operational constants

Note: Configuration files should return data arrays and should not contain complex logic.

---

### database/

Handles schema modeling and data structures.

Includes:

- Custom table creation scripts
- Database migration definitions
- Seeder files for populating testing tables

Note: This directory represents structure and initial states, not query-time data manipulation.

---

### docs/

Houses system documentation for developers, system administrators, and integration teams.

Examples:

- Technical explanations of system design
- Database relationships schemas
- Local API specifications

Note: Documentation should always be updated alongside code changes.

---

### resources/

Contains uncompiled presentation templates and views.

Examples:

- PHP/HTML layout scripts for the administration area
- Core templates for custom components
- Shared SVG vector assets

Note: Views should only render data and perform basic loops/conditionals; business logic must be resolved beforehand.

---

### routes/

Manages endpoint registrations for modern communication APIs. In a WordPress context, this directory houses registrations for custom namespaces within the WP REST API.

---

### src/

The core source directory containing the application logic. All classes inside this directory follow the PSR-4 namespace configuration (WooExtender\).

---

### storage/

Designed to temporarily store runtime file output, such as custom diagnostics, exported CSV operations, or temporary caches.
Note: Because plugin-specific directories may have write restrictions on specialized WordPress hosts (such as WP Engine or Pantheon), this directory should be used for temporary local compilation, with critical runtime file storage routed safely to the standard WordPress uploads path (wp-content/uploads/woo-extender/) via the WordPress Filesystem API.

Examples:

- Logs
- Cache
- Temporary files

Nothing inside this directory should be committed to Git unless explicitly required.

---

### tests/

Houses standard testing suites.

- PHPUnit configurations and test cases
- Mock data and environment assertions
- Directory divisions for unit, integration, and functional tests

---

## Source Code Structure

The src directory follows a modular architecture.

Each namespace has a clearly defined responsibility.

```text
src/
├── Admin/         # Admin screen integration, menus, and meta-boxes
├── Bootstrap/     # Plugin startup, Composer loading, and system initialization
├── Controllers/   # Request extraction, translation, and delegation
├── Core/          # DIC, kernel definitions, and base architectural classes
├── Database/      # Core SQL builders and schema migrations runner
├── DTO/           # Data Transfer Objects (immutable structures)
├── Exceptions/    # Specialized domain Exception classes
├── Helpers/       # Reusable, stateless utility structures
├── Hooks/         # WordPress Actions and Filters listeners
├── Interfaces/    # System contracts
├── Models/        # Domain entity representations
├── Providers/     # Service providers for dependency mapping
├── Repositories/  # Persistence layer abstractions (WPDB or custom tables)
├── Services/      # Standard business domain implementations
├── Traits/        # Shared cross-class helper routines
├── Validation/    # Input assertion rules
└── View/          # View management utilities and wrappers
```

---

## Directory Details

### Admin

Handles integration with the standard WordPress admin panel.

Examples:

- Rendering menu lists
- Creating core settings tabs
- Initializing custom product option meta-boxes

Note: Admin classes function as coordinators. They extract configuration, receive visual actions, and trigger the layout engine.

---

### Bootstrap

Executes early initialization sequences when the plugin loader file executes.

Typical responsibilities:

- Load Composer
- Register services
- Initialize providers
- Load configuration
- Start application

This directory should execute only once during plugin initialization.

---

### Controllers

Controllers receive requests and delegate work to Services.

Controllers must remain thin.

Controllers should never contain business rules.

A controller should:

- Validate incoming data
- Call a Service
- Return a response

Nothing more.

---

### Core

Contains the framework layer of Woo Extender.

Examples:

- Application
- Container
- Kernel
- Base Classes
- Abstract Components

Every other module depends on Core.

Core should depend on nothing.

---

### Database

Contains database abstractions.

Examples:

- Query Builders
- Database Managers
- Connection Helpers

SQL should never be scattered throughout the project.

---

### DTO

Contains Data Transfer Objects.

DTOs transport structured data between layers.

DTOs must remain immutable whenever possible.

They should never contain business logic.

---

### Exceptions

Contains all custom exception classes.

Each exception should represent one specific problem.

Avoid generic exceptions whenever possible.

---

### Helpers

Small reusable utility classes.

Examples:

- String Helpers
- Array Helpers
- Date Helpers

Helpers should never become a dumping ground for random functions.

---

### Hooks

Responsible for WordPress hooks.

Instead of registering hooks everywhere, all hooks are centralized here.

This makes the application easier to understand and debug.

---

### Interfaces

Contains contracts shared across the application.

Programming against interfaces improves flexibility and testing.

---

### Models

Represents domain objects.

Models describe data.

Business logic belongs in Services.

Database access belongs in Repositories.

Models should not directly query the database.

---

### Services

Services contain the application's business logic.

This is where the actual behavior of the system is implemented.

A Service should answer questions like:

- What should happen?
- In what order should it happen?
- What business rules should be applied?

A Service should **not** answer questions like:

- How should HTML be rendered?
- Which admin page should display the data?
- Which database table stores the information?

Those responsibilities belong to other layers.

---

### Service Responsibilities

A Service MAY:

- Execute business rules
- Coordinate multiple repositories
- Validate domain constraints
- Dispatch events
- Call external APIs
- Manage transactions

A Service MUST NOT:

- Render HTML
- Echo output
- Access `$_POST` directly
- Access `$_GET` directly
- Access `$_SERVER` directly
- Register WordPress hooks
- Build SQL queries
- Perform direct database operations

---

### Example

Correct Architecture:

```text
Controller (Validates & Maps Request)
       │
       ▼
Domain Service (Applies Business Rules)
       │
       ▼
Data Repository (Handles Database Access)
       │
       ▼
Database Engine (Custom tables, WPDB, or WC CRUD)
```

Incorrect Coupling:
`text Controller (Accepts Request)  ───✕───  Database (Executes SQL Directly)`

---

## Repositories

Repositories provide an abstraction over the persistence layer.

Every database interaction should pass through a Repository.

Repositories isolate SQL from business logic.

This makes the application easier to maintain, test and refactor.

---

### Repository Responsibilities

Repositories MAY:

- Read records
- Insert records
- Update records
- Delete records
- Build queries

Repositories MUST NOT:

- Validate business rules
- Render HTML
- Register hooks
- Call admin pages
- Perform UI operations

---

### Naming Convention

Examples:

`ProductRepository`
`SupplierRepository`
`BatchRepository`
`WarrantyRepository`
`InventoryRepository`

Every repository should represent a single aggregate or entity.

---

## Models

Models represent domain entities.

A Model describes the shape of the data.

Models should remain lightweight.

Example:

`Product`
`Supplier`
`Batch`
`Warranty`
`Inventory`

A Model MUST NOT know where its data came from.

It simply represents the data.

---

## Validation

Validation is a dedicated layer.

Validation should never be mixed with Controllers.

Likewise, validation should never be scattered across Services.

All validation logic belongs inside the Validation namespace.

---

### Validation Rules

Validation classes should:

- Validate user input
- Validate request payloads
- Validate settings
- Return meaningful errors

Validation classes should not:

- Save data
- Query the database
- Render output

---

## View Layer

Views are responsible only for presentation.

Views should never contain business logic.

Views may contain:

- HTML
- Escaped output
- Small presentation helpers

Views must never:

- Query the database
- Perform calculations
- Save records

---

## Dependency Injection

Woo Extender follows Dependency Injection wherever possible.

Dependencies should be injected instead of created manually.

Good:

```php
class ProductService
{
    public function __construct(
        ProductRepository $repository
    ) {}
}
```

Bad:

```php
class ProductService
{
    public function __construct()
    {
        $this->repository = new ProductRepository();
    }
}
```

Creating dependencies manually increases coupling and reduces testability.

---

## Service Providers

Service Providers are responsible for registering application services.

Every Provider should have one responsibility.

Typical providers include:

- HookProvider
- AdminProvider
- RouteProvider
- DatabaseProvider
- EventProvider

Providers should register services.

Providers should not execute business logic.

---

## Hook Registration

One of the project's architectural goals is to centralize WordPress hooks.

Hooks must never be scattered randomly across the application.

Instead, Hooks should be grouped logically.

Example:

`Product Hooks`
`Inventory Hooks`
`Admin Hooks`
`Order Hooks`
`Settings Hooks`

This greatly improves discoverability.

---

## Error Handling

Errors should never fail silently.

Expected errors should become Exceptions.

Unexpected errors should be logged.

The application should always fail gracefully.

---

### Exception Guidelines

Create specific exceptions whenever possible.

Examples:

`ProductNotFoundException`
`BatchNotFoundException`
`InventoryException`
`WarrantyException`

Avoid generic Exception unless absolutely necessary.

---

## Logging

Logging should help developers diagnose issues.

Logs should never expose stack traces in production environments.

Detailed exception traces should only be available in debug mode.

Logs should contain:

- Timestamp
- Context
- Exception
- Stack trace
- User ID (when available)
- Product ID (when applicable)

Sensitive information must never be logged.

Examples:

`Passwords`
`Tokens`
`Nonces`
`Personal customer information`

---

## Configuration

Configuration values belong inside config/.

Magic numbers should never appear throughout the codebase.

Correct Pattern

```php
if ($stock < config('inventory.low_stock_threshold')) {
    // Execute low stock routines
}

```

Incorrect Pattern

```php
if ($stock < 5) {
    // Hardcoded threshold limits flexibility
}
```

---

## Coding Standards

Woo Extender follows modern PHP standards.

Primary standards include:

- PSR-1: Basic Coding Standard.
- PSR-4: Autoloader Standards.
- PSR-12: Extended Coding Style Guide.
- WordPress Coding Standards: Applied where compatibility is required (e.g., naming hooks, templates, or sanitization helpers).

Note: In the event of a conflict between modern object-oriented architecture and legacy procedural WordPress patterns, priority is given to clean architecture and testability, provided it does not break core WordPress compatibility.

---

## Naming Conventions

Consistency in class and file naming makes the codebase predictable and easier to navigate.

### Class File Names

Classes must use `PascalCase` and match their file name.

### Domain Models

Singular domain names without suffixes. (e.g., `Product`, `Batch`, `Supplier`, `Warranty`)

### Repositories

Suffix: `Repository` (e.g., `BatchRepository`, `SupplierRepository`)

### Services

Suffix: `Service` (e.g., `InventoryService`, `WarrantyService`)

### Interfaces

Suffix: `Interface` (e.g., `ProductRepositoryInterface`)

### Data Transfer Objects

Suffix: `Data` or `Request` (e.g., `CreateBatchData`)

### Controllers

Suffix: `Controller` (e.g., `ProductController`)

### Exceptions

Suffix: `Exception` (e.g., `InventoryException`, `SupplierNotFoundException`)

### Providers

Suffix: `Provider` (e.g., `HookProvider`, `AdminProvider`)

### Validators

Suffix: `Validator` (e.g., `BatchValidator`)

---

## Coding Standards

Consistency is one of the key goals of Woo Extender.

A predictable codebase is easier to maintain, easier to debug, and significantly easier for new contributors to understand.

The following standards apply to every part of the project.

### General Rules

- Always declare strict types whenever possible.
- Follow PSR-12 formatting rules.
- Keep methods short and focused.
- Prefer composition over inheritance.
- Favor dependency injection over object creation.
- Avoid duplicated logic.
- Keep classes highly cohesive.
- Keep coupling as low as possible.
- Write self-documenting code.

---

### Class Design

A class should have one clear responsibility.

Avoid creating "God Classes" that manage unrelated concerns.

Whenever a class starts handling multiple responsibilities, consider splitting it into smaller classes.

---

### Method Design

Methods should be:

- Small
- Predictable
- Readable
- Easy to test

A method should ideally perform one logical operation.

Avoid methods with excessive branching or deeply nested conditions.

---

### Dependency Rules

Dependencies should always point inward.

The following dependency flow should be respected:

```text
Controller ──► Service ──► Repository ──► Database
```

Never bypass intermediate layers without a valid architectural reason.

---

### Forbidden Practices

The following practices are strongly discouraged:

- Implementing business rules inside Controller classes.
- Executing raw SQL queries inside Services or Controllers.
- Generating HTML markup inside Repository classes.
- Referencing `$_POST`, `$_GET`, or `$_SERVER` superglobals directly outside of Controllers.
- Scattering hook calls across the codebase.
- Duplicate validation code in multiple files.
- Hardcoding application configurations or parameters.

---

### Documentation

Public classes and methods should include meaningful PHPDoc comments when they improve readability.

Documentation should explain **why**, not merely **what**.

Poor Documentation:

```php
// Save product.
```

Good Documentation:

```php
/**
 * Persists the validated product data and updates all related inventory records.
 *
 * @param ProductData $data Object containing validated product values.
 * @throws ProductStorageException If database operations fail.
 */
```

---

### Internationalization (i18n)

Woo Extender is designed for a global user base. All user-facing strings must be fully translatable.

- **Text Domain:** Always use the `woo-extender` text domain.
- **Proper Escaping:** Combine translation and escaping whenever outputting to the browser.
  - Good: `esc_html__( 'Settings saved.', 'woo-extender' )`
  - Bad: `echo __( 'Settings saved.', 'woo-extender' )`
- **Dynamic Strings:** Never use variables directly inside translation functions. Use `sprintf()`.
  - Good: `sprintf( esc_html__( 'Batch %s created.', 'woo-extender' ), $batch_id )`
  - Bad: `__( 'Batch ' . $batch_id . ' created.', 'woo-extender' )`

---

### Code Reviews

All contributions must be reviewed before being merged. Reviewers should check:

- Consistent application architecture.
- Proper naming conventions.
- Performance implications (e.g., avoiding database queries inside loops).
- Security considerations (e.g., input validation and output escaping).
- Updated documentation matching the code changes.
- Backward compatibility.

---

## Security

Security is considered a first-class concern throughout the project.

Every new feature should be designed with security in mind rather than adding security afterwards.

---

### Input Validation

All external input must be validated and sanitized before it is used. Validation should happen as early as possible in the execution cycle.

---

### Output Escaping

Ensure all data rendered in HTML templates is properly escaped based on its context.

Common WordPress Escaping Functions:

- `esc_html()`: For standard text output.
- `esc_attr()`: For HTML attributes.
- `esc_url()`: For URL outputs.
- `wp_kses_post()`: For strings containing HTML.

---

### Authorization

Every request must verify user capabilities before performing administrative or state-altering actions. Do not rely on page context alone to determine user permissions.

---

### Nonce Verification

Include and verify WordPress nonces for all state-changing requests to protect against Cross-Site Request Forgery (CSRF).

---

### Database Security

All database queries must be prepared to prevent SQL injection vulnerabilities. Repositories must utilize proper `$wpdb->prepare()` statements when running database operations.

---

### Sensitive Information

The following information must never be logged, displayed or exposed:

- Passwords
- Access tokens
- Security nonces
- Customer personal information
- Payment credentials
- API secrets

---

### Principle of Least Privilege

Ensure database components and user actions operate with only the minimum level of access required to complete their tasks.

---

## Development Workflow

The typical path for implementing new features:

```text
┌─────────────────┐
│ Define Feature  │
│  Requirements   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Create DTO    │
│  Representations│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Create Data   │
│   Validators    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Create Domain   │
│   Services      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│Create Repository│
│ Implementations │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│Register Service │
│ in Container    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Register Hooks/ │
│ REST Endpoints  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Develop View   │
│    Templates    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Write Tests   │
│  (Unit/Integr.) │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Update System  │
│  Documentation  │
└─────────────────┘
```

---

## Contributing

We welcome community contributions. To help keep the codebase maintainable, please ensure all contributions follow the project's architectural guidelines and coding standards.

When opening an issue, please include:

- A clear description of the expected behavior.
- The actual behavior observed.
- Step-by-step instructions to reproduce the issue.
- Your local environment details (PHP, WordPress, and WooCommerce versions).

---

### Before Opening a Pull Request

Verify that:

- Your code conforms to the project's architecture and coding standards.
- Appropriate validation handles all incoming user inputs.
- Relevant documentation is updated to reflect your changes.
- All automated tests pass, and new tests are added for new features.
- No unnecessary external dependencies are introduced.

---

### Pull Request Guidelines

- Focused Scope: Each PR should address a single issue or implement one specific feature.
- Detailed Descriptions: Explain the reasoning behind your implementation and any architectural decisions.
- Clean Commits: Use logical, descriptive commit messages.

---

### Commit Messages

Use clear, descriptive commit messages to keep the project history readable.

Good examples:

`Add batch repository implementation`

`Fix inventory calculation`

`Refactor supplier validation`

Avoid generic messages such as:

`Update`

`Fix bug`

`Changes`

`Misc fixes`

---

### File Access

Every PHP file that can be accessed directly should prevent direct execution.

Example:

```php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
```

---

### Data Sanitization

- Sanitize Early: Clean input data as soon as it is received.
- Escape Late: Escape data at the point of output to ensure context-appropriate escaping.

---

## Roadmap

This roadmap outlines the planned development path for Woo Extender. Features may be adjusted as the project and community requirements evolve.

### Version 1.x: Core Foundation

- Core dependency injection container.
- Service provider infrastructure.
- Centralized Hook system.
- Basic product metadata management.

### Version 2.x: Inventory Management

- Inventory batch and lot management.
- Valuation models (FIFO, LIFO, Average Cost).
- Lot tracking (expiration and production dates).

### Version 3.x: Suppliers & Warranties

- Supplier directories and performance metrics.
- Warranty term management.
- Multi-warranty options for products.

### Version 4.x: Reporting & Analytics

- Inventory asset valuations.
- Cost of Goods Sold (COGS) reporting.
- Profit margin metrics.

### Version 5.x: Developer API & Integrations

- Core REST API endpoints.
- Integration tools for third-party ERP platforms.
- Developer SDK for sub-extension creation.

---

## License

Woo Extender is licensed under the GPL-2.0-or-later license. This matches the licensing model of WordPress and WooCommerce, allowing the community to safely use, modify, and distribute the software.

Please see the `LICENSE` file for the full license text.

---

## Acknowledgements

Woo Extender is built on top of the WordPress and WooCommerce open-source platforms. We appreciate the work of the core contributors and community members who make these projects possible.

---

## Future Documentation

As development progresses, detailed guides on specific modules will be maintained in the `docs/` directory:

Planned documents include:

- ARCHITECTURE.md: Deep dive into container registration and data flows.
- DATABASE.md: Schema descriptions and relationship diagrams.
- API.md: Specifications for the REST API and internal services.
- DEVELOPMENT.md: Local development environment setup instructions.
- TESTING.md: Guidelines for writing unit and integration tests.
- CHANGELOG.md: Detailed release history.
