# Implemented Features and Staff Workflows

This document describes what the **Aion Pharmacy** web application currently implements and how **Admin** and **Pharmacist** roles are expected to use it day to day.

---

## Roles overview

| Role | Purpose |
|------|---------|
| **Admin** | Full operational access plus inventory adjustments, stock movement history, sales analytics reports, and user (staff) management. |
| **Pharmacist** | Front-desk pharmacy work: customers, medications catalog view, inventory levels, prescriptions, **recording sales (checkout)**, and profile. Does **not** adjust stock manually, view global stock movement reports, run summary reports, or manage users. |

Registration creates accounts with the **pharmacist** role by default. **Admin** accounts are created via the Users module (by an existing admin) or via database seeding / manual DB update (see `DatabaseSeeder`).

---

## Implemented feature areas

### Authentication and profile

- Register, login, logout, password reset (Laravel Breeze-style).
- Email verification routes exist; product behavior depends on `User` model configuration.
- Profile: update name and email; delete own account (with password confirmation).

### Customers

- List, create, edit, delete customers (no separate “show” page).
- Fields include demographics, contact, address, medical notes (allergies, conditions, history).
- Search on listing where implemented in controller.

### Medications (catalog)

- List with search and status filter.
- CRUD for SKU, name, unit type, dosage form, strength, unit price, reorder level, status (e.g. active / inactive).
- SKU uniqueness enforced at application/database level.

### Inventory

- **Everyone (admin + pharmacist):** inventory index — see quantity on hand per medication, low-stock highlighting/filter.
- **Admin only:** manual stock adjustment (in / out / adjustment with direction), with reason notes and automatic **stock movement** log entries.

### Prescriptions

- List with status filter.
- Create prescription for a customer: header data + multiple line items (medication, quantity, dosage instructions); line prices snapshot from medication at creation time.
- View prescription detail.
- Update prescription status (`draft`, `confirmed`, `dispensed`, `cancelled`) from the detail page.

### Sales (checkout)

- **Admin and pharmacist:** list sales, create new sale, complete checkout, view receipt-style detail.
- Checkout supports optional customer, line items (medication + quantity), discount/tax fields, payment method (`cash`, `card`, `mobile`).
- On successful sale: creates sale record, line items, **deducts inventory**, and writes **stock movement** rows linked to the sale.

### Stock movements (read-only log)

- **Admin only:** paginated/filterable list of historical movements (manual adjustments, sale deductions, etc.).

### Reports

- **Admin only:** sales summary (period totals, top sellers, recent sales) and stock summary (inventory snapshot, low stock, movement aggregates).

### Users (staff)

- **Admin only:** list/search/filter staff, create user with role and password, edit, delete (cannot delete self).

### Dashboard

- Role-aware cards: customer/medication/prescription counts for all; **low stock, today’s sales, recent sales** for users who can manage sales (admin and pharmacist); **recent stock movements** for admin; report quick links for admin.

---

## Workflow: Pharmacist (typical shift)

1. **Sign in** → lands on **Dashboard** (operational snapshot).
2. **Customers** — register or find a patient; update details if needed.
3. **Medications** — confirm drug exists, price, and status; escalate to admin if catalog changes are needed.
4. **Inventory** — check **on hand** quantities; cannot run manual adjustments (admin handles receiving / corrections).
5. **Prescriptions** — create or look up prescriptions; advance status as the workflow progresses (per your assignment rules).
6. **Sales** — when the patient purchases OTC or dispensed items:
   - Open **Sales → New sale** (or equivalent).
   - Optionally attach **customer**.
   - Add **line items** (medication + quantity), set **payment method** (and discount/tax if used).
   - Submit → system records the sale and **reduces stock** automatically.
7. **Profile** — update own email/name as needed.

---

## Workflow: Admin (supervisor / back office)

1. Same operational paths as pharmacist **where shared** (customers, medications, inventory view, prescriptions, **sales**).
2. **Inventory → Adjust** — record deliveries, wastage, corrections; each action creates an auditable stock movement.
3. **Stock (movements)** — review history for discrepancies or audits.
4. **Reports** — review sales and stock summaries for the period.
5. **Users** — onboard new pharmacists or admins, reset access by editing users, deactivate accounts by delete (subject to your policy).

---

## Route access summary (high level)

| Area | Pharmacist | Admin |
|------|:----------:|:-----:|
| Dashboard | Yes | Yes |
| Customers CRUD | Yes | Yes |
| Medications CRUD | Yes | Yes |
| Inventory index | Yes | Yes |
| Inventory adjust | No | Yes |
| Prescriptions | Yes | Yes |
| Sales | Yes | Yes |
| Stock movements list | No | Yes |
| Reports | No | Yes |
| Users CRUD | No | Yes |

---

## Technical pointers (for developers)

- Authorization uses Laravel **Gates** in `AppServiceProvider` (`access-pharmacy-operations`, `adjust-inventory`, `manage-sales`, `view-stock-movements`, `view-reports`, `manage-users`) and `can:` middleware on routes in `routes/web.php`.
- Navigation uses `@can(...)` for Sales, Reports, Stock, and Users so it stays aligned with server-side checks.

---

## Related documents

- `docs/IMPROVEMENTS_AND_GAPS.md` — known limitations and improvement backlog.
- `docs/execution-checklist.md` — phased delivery checklist used during build.
