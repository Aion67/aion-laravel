# Aion Pharmacy Execution Checklist

This checklist enforces strict phase-by-phase delivery.

## Phase 1 - Foundation

- [x] Core domain tables created with constraints and indexes.
- [x] Core Eloquent models and relationships added.
- [x] Shared module routes and placeholders added.
- [x] Base layout navigation aligned to project modules.
- [x] Reusable Blade components added (page header, stat card, status badge).
- [x] Role-based access baseline added (admin and pharmacist).
- [x] Role access feature tests added.
- [x] Baseline seed users for admin/pharmacist added.

Exit criteria:
- migration succeeds from clean database
- authentication tests pass
- role access tests pass
- module placeholder pages are reachable by authenticated users

## Phase 2 - Master Data

- [x] Users management CRUD (admin only)
- [x] Customers CRUD with validation and search
- [x] Medications CRUD with SKU uniqueness and status management

Exit criteria:
- user/customer/medication module CRUD routes are functional
- validation failures are surfaced correctly in forms
- search and status filters are functional on index pages
- feature tests for Phase 2 modules pass

## Phase 3 - Inventory Workflow

- [x] Inventory list with low-stock signals
- [x] Stock adjustments with reason capture
- [x] Stock movement logging from inventory operations

Exit criteria:
- inventory list and low-stock filters are functional
- manual stock adjustments create stock movement records
- stock-out cannot reduce inventory below zero
- stock movement listing and movement-type filter are functional
- feature tests for Phase 3 workflows pass

## Phase 4 - Operational Workflow

- [x] Prescriptions creation and status transitions
- [x] Sales checkout flow and receipt output
- [x] Inventory deduction integration on completed sales

## Phase 5 - Reporting and Summaries

- [ ] Dashboard cards backed by live aggregates
- [ ] Recent activity panels (sales, prescriptions, stock movements)
- [ ] Sales and stock summary reporting views
