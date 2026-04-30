# Application Development Plan

This document explains how the Laravel application works from the database up to the UI, and outlines a practical build plan for the Aion Pharmacy project. The scope is intentionally limited to features that can be supported by the current database/domain model.

## 1) Purpose and Scope

The purpose of this plan is to help a developer understand:
- how Laravel moves data from the database to the browser
- how MVC, Blade, controllers, services, and repositories fit together
- which pages and screens should exist for this pharmacy app
- which frontend elements are valid because they are backed by the current database

Scope rules:
- only build screens that are supported by the existing domain model
- do not add UI features without a matching database purpose
- keep the app centered on pharmacy operations: staff, customers, medications, inventory, prescriptions, sales, and stock movements

Reference documents:
- `docs/models.md` for the data model and diagrams
- `docs/technical.md` for setup and environment guidance

---

## 2) How Laravel Works: Database to UI

A Laravel app is usually built in a simple flow:

1. **Database** stores the data.
2. **Eloquent models** represent the tables in PHP.
3. **Controllers** receive the request and coordinate the response.
4. **Services / repositories** contain business rules and data access logic.
5. **Blade views** render the HTML sent to the browser.
6. **Routes** connect URLs to controller actions.

### Example flow

A user opens the medications page:
- the route points to a controller action
- the controller asks a service or model for medication records
- the data is filtered, sorted, or paginated
- the controller sends the result to a Blade view
- the Blade view renders a table, form, or detail page

This means the database is the source of truth, and the UI should always reflect the records available in the system.

---

## 3) MVC in Simple Terms

### Model
The model represents the data and rules around it.

Examples in this app:
- `User`
- `Customer`
- `Medication`
- `Inventory`
- `StockMovement`
- `Prescription`
- `PrescriptionItem`
- `Sale`
- `SaleItem`

### View
The view is the interface the user sees.

In Laravel, this is usually a **Blade** file.

### Controller
The controller receives the request, validates it, and returns a response.

Example responsibilities:
- show a list of records
- show create/edit forms
- save submitted form data
- redirect after success
- return validation errors when something is wrong

### Why this matters
If MVC is clear, the app stays organized:
- models manage data relationships
- controllers stay small
- Blade files stay focused on presentation
- business logic can move into services instead of being repeated everywhere

---

## 4) Working with Blade

Blade is Laravel’s templating engine. It lets you build pages using reusable templates and components.

### Recommended Blade structure
- `layouts/` for page shells
- `components/` for reusable UI pieces
- feature views grouped by module

### What Blade should do
Blade should:
- display data from the controller
- show forms and buttons
- render errors and alerts
- include reusable components like tables, badges, inputs, and modals

Blade should not:
- contain business rules
- query the database directly
- calculate complex workflow logic that belongs in controllers/services

### Useful Blade features
- `@extends`, `@section`, `@yield`
- `@include`
- `@foreach`, `@if`
- components and slots
- form helpers and old input handling
- error display with validation messages

### Example layout pattern
- app shell layout
- sidebar navigation
- top bar with user menu
- content section
- flash message area

---

## 5) Recommended Laravel Application Structure

To keep the codebase maintainable, the project should use a layered structure.

### 5.1 Controllers
Controllers should handle request flow only.

Examples:
- `DashboardController`
- `CustomerController`
- `MedicationController`
- `InventoryController`
- `PrescriptionController`
- `SaleController`
- `StockMovementController`

### 5.2 Services
Services hold business behavior that is used across controllers.

Examples:
- `CustomerService` — create/update customer details
- `MedicationService` — manage medication data rules
- `InventoryService` — adjust quantities and record movements
- `PrescriptionService` — create and confirm prescriptions
- `SaleService` — calculate totals and finalize sales
- `ReceiptService` — format sale receipt information

### 5.3 Repositories
Repositories are useful if you want to separate query logic from services.

Examples:
- `CustomerRepository`
- `MedicationRepository`
- `SaleRepository`
- `StockMovementRepository`

Repositories should be used for:
- reusable query methods
- filtering and searching
- pagination and listing logic
- complex joins

### 5.4 Form Requests
Use form request classes for validation.

Examples:
- `StoreCustomerRequest`
- `StoreMedicationRequest`
- `StorePrescriptionRequest`
- `StoreSaleRequest`

### 5.5 Policies / Authorization
Use policies or gates to protect actions.

Examples:
- admin can manage staff and configuration
- pharmacist can create prescriptions and process sales
- stock edits may be restricted to specific roles

---

## 6) Database-Backed Frontend Scope

Only create frontend features that have matching database tables or derived values.

### Supported modules
- staff users
- customers
- medications
- inventory
- prescriptions
- sales
- stock movements

### Supported derived data
These can appear on dashboard cards or summary widgets because they come from database records:
- total customers
- total medications
- low-stock medications
- today’s sales total
- number of prescriptions created today
- recent stock movements
- recent sales

### Not allowed unless the database later supports them
- chat system
- unrelated marketing pages
- notifications with no stored source
- appointment booking without a table for it
- insurance claims without a claims model
- supplier purchasing screens without supplier and purchase tables

---

## 7) Pages, Screens, and Elements to Build

The app should be developed module by module.

### 7.1 Authentication and access
Supported because Laravel already provides user authentication.

Pages/screens:
- login
- logout
- profile
- password change

Elements:
- email/password form
- remember me checkbox
- validation errors
- user menu

### 7.2 Dashboard
A central landing page for staff.

Use only data from the database.

Elements:
- summary cards
- low-stock alert list
- recent sales table
- recent prescriptions table
- recent stock movements table
- optional chart for sales by day if backed by sales data

### 7.3 Users / staff management
For admins only.

Pages/screens:
- users list
- create user
- edit user
- user detail view

Elements:
- search and filter
- role selector
- active/inactive status
- password reset or set password form

### 7.4 Customers
Patient/customer management.

Pages/screens:
- customer list
- customer create form
- customer edit form
- customer detail page
- customer history view

Elements:
- name fields
- DOB
- sex
- contact details
- medical history
- allergies
- conditions
- customer search
- recent prescriptions and sales for that customer

### 7.5 Medications
Medicine catalog management.

Pages/screens:
- medication list
- medication create form
- medication edit form
- medication detail page

Elements:
- SKU
- name
- unit type
- dosage form
- strength
- unit price
- reorder level
- status
- low-stock badge

### 7.6 Inventory
Stock tracking.

Pages/screens:
- inventory list
- inventory detail page
- adjust stock form
- stock movement history

Elements:
- current stock quantity
- reserved quantity
- adjustment reason
- movement type
- reference type
- date filters

### 7.7 Prescriptions
Prescriptions are part of the sale/dispensing workflow.

Pages/screens:
- prescription list
- prescription create form
- prescription detail page
- confirm prescription page

Elements:
- customer selector
- prescribing staff user
- medication rows
- quantity per item
- dosage instructions
- status badge
- notes

### 7.8 Sales
Completed sales and receipts.

Pages/screens:
- sales list
- sale create form
- sale detail page
- receipt view/print page

Elements:
- customer selector
- medication line items
- quantity
- unit price
- subtotal
- discount
- tax
- total
- payment method
- payment status

### 7.9 Stock movements
Inventory audit trail.

Pages/screens:
- stock movement list
- stock movement detail page

Elements:
- medication
- user who recorded it
- movement type
- quantity
- reference type
- notes
- created at time

---

## 8) Recommended Build Order

Build the app in this order so each step has a working database-backed result.

### Phase 1: Foundation
- confirm models and relationships
- set up layouts and navigation
- create shared Blade components
- add role-based access rules

### Phase 2: Master data
- users
- customers
- medications

### Phase 3: Inventory workflow
- inventory list
- stock movement logging
- low-stock indicators

### Phase 4: Operational workflow
- prescriptions
- sales
- receipt rendering

### Phase 5: Reporting and summaries
- dashboard cards
- recent activity panels
- sales summaries
- stock alerts

---

## 9) Suggested View Structure

A practical Blade structure could look like this:

- `resources/views/layouts/`
- `resources/views/components/`
- `resources/views/dashboard.blade.php`
- `resources/views/customers/`
- `resources/views/medications/`
- `resources/views/inventory/`
- `resources/views/prescriptions/`
- `resources/views/sales/`
- `resources/views/stock-movements/`
- `resources/views/users/`

### Reusable frontend elements
- table component
- page header component
- breadcrumb component
- alert/flash component
- modal component
- form input component
- select component
- badge/status component
- confirmation dialog component

These elements keep the frontend consistent and reduce repeated markup.

---

## 10) Development Rules

When adding a new screen, always ask:
1. Is there a database table behind this screen?
2. Does the screen need a list, form, or detail view?
3. Which controller action serves the page?
4. Which service handles the business logic?
5. Which Blade components can be reused?
6. Does the screen fit the pharmacy scope?

If the answer to question 1 is no, the screen should not be added yet.

---

## 11) Summary

Laravel works best when the flow is clear:
- database stores the truth
- models represent the data
- controllers coordinate requests
- services and repositories manage logic and queries
- Blade renders the UI

For Aion Pharmacy, the frontend should stay focused on the current data model and the pharmacy workflow: customers, medications, inventory, prescriptions, sales, and stock movement history.

