# Improvements, Gaps, and Technical Debt

This document is a structured review of the **aion-laravel** codebase: database design, business logic, authorization, security, UI/UX, testing, and operations. It is written for a future improvement pass after the class presentation.

**Severity legend**

| Level | Meaning |
|-------|---------|
| Critical | Data loss, security breach, or incorrect financial/stock state under normal use |
| High | Important correctness, security, or compliance risk |
| Medium | Reliability, maintainability, or user-trust issues |
| Low | Polish, consistency, or minor optimizations |

---

## 1. Database schema and integrity

### 1.1 Unconstrained enumerations (Medium)

String columns carry business state without database-level enforcement:

- `medications.status`
- `prescriptions.status`
- `sales.status`, `sales.payment_method`
- `stock_movements.movement_type`, `stock_movements.reference_type`

**Risk:** Invalid values can appear if code paths bypass FormRequests or if raw SQL / future imports are used.

**Improvement:** Use `ENUM` (MySQL) sparingly, or a dedicated lookup table, or at least CHECK constraints where supported; align with a single domain enum layer in PHP.

### 1.2 Cascade deletes and audit trail (High)

Foreign keys use `cascadeOnDelete()` in several places (e.g. `stock_movements.user_id`, `prescriptions.user_id`, `sales.user_id`). Deleting a user removes linked prescriptions, sales, and movements.

**Risk:** Loss of historical records for compliance and reconciliation.

**Improvement:** Prefer `restrict` or `nullOnDelete` on user references for immutable records; soft-delete users; archive tables for legal retention.

### 1.3 Medication deletion (High)

Deleting a `medication` cascades to inventory, stock movements, prescription items, and sale items (via FK definitions).

**Risk:** Destructive removal of historical sales/prescription line history.

**Improvement:** Soft-delete medications (`deleted_at`); block delete if referenced by completed sales; or mark `status = discontinued` only.

### 1.4 `reserved_quantity` unused (Medium)

`inventory.reserved_quantity` exists in the schema but is not decremented/incremented during checkout or prescriptions.

**Risk:** Future features may assume reservations are accurate; current logic ignores them.

**Improvement:** Either implement reservation flow or remove the column until needed.

### 1.5 Numeric ranges (Low–Medium)

Quantities are integers; prices use `decimal(12,2)`. There is no DB-level check that `quantity_on_hand >= 0` or that totals match line items.

**Improvement:** CHECK constraints or guarded updates only through domain services; periodic reconciliation reports.

### 1.6 Customer PII (Medium)

`customers` stores DOB, medical history, allergies, etc. There is no encryption-at-rest in the application layer and no field-level masking in exports.

**Improvement:** Document GDPR/HIPAA-style handling; encrypt sensitive columns or use DB TDE; audit access logs.

---

## 2. Concurrency and stock correctness

### 2.1 Sale checkout without row locking (High)

`SaleController::store` runs in a transaction and checks `quantity_on_hand` on an in-memory `Inventory` model, then decrements. Concurrent requests can pass the check before either commits, leading to **overselling** (negative stock if checks were weaker; here stock could go negative only if another path bypassed validation—still a race between two sales).

**Improvement:** `lockForUpdate()` on `inventory` (or `medications`) rows inside the transaction in deterministic order; or optimistic locking with `version` column.

### 2.2 Inventory adjustment race (Medium)

Same pattern: read-modify-write without `lockForUpdate()` for `storeAdjustment`.

**Improvement:** Pessimistic lock for the inventory row being adjusted.

### 2.3 Sale number / prescription number generation (Low)

Random suffix with existence check is fine at small scale; under extreme concurrency duplicate retries are possible but unlikely.

**Improvement:** DB sequence or atomic counter table if volume grows.

---

## 3. Business rules and domain logic

### 3.1 Prescription status is a free graph (High)

`UpdatePrescriptionStatusRequest` allows any transition among `draft`, `confirmed`, `dispensed`, `cancelled`. There is no state machine (e.g. cannot go from `cancelled` back to `dispensed`; cannot skip `confirmed`).

**Risk:** Incorrect workflow, inventory mismatches if dispensing is later tied to stock, and audit confusion.

**Improvement:** Explicit allowed transitions per role; model methods `canTransitionTo()`; reject illegal transitions with 422.

### 3.2 Sales always created as `paid` (Medium)

`store` sets `status` to `paid` immediately. There is no `pending` / `void` / `refunded` lifecycle despite `status` existing on the table.

**Risk:** Reporting and refunds are harder; accidental submission is irreversible.

**Improvement:** Multi-step checkout or admin void with compensating stock movements.

### 3.3 Inactive medications can still be sold (Medium)

`StoreSaleRequest` validates `exists:medications,id` but not `status = active`. Checkout uses live `unit_price` from `Medications` table.

**Risk:** Selling discontinued items; price changes retroactively affect interpretation of past sales (line items snapshot unit price—good for history; still allows wrong product class).

**Improvement:** Rule `Rule::exists('medications', 'id')->where('status', 'active')` and optionally lock price at add-to-cart time in session.

### 3.4 Prescriptions vs inventory (Medium)

Creating/updating prescriptions does not reserve or deduct stock. Dispensing is not integrated with inventory.

**Risk:** Operational gap between clinical and stock reality.

**Improvement:** On `dispensed`, deduct inventory with movements; tie to prescription ID.

### 3.5 Discount and tax (Medium)

`StoreSaleRequest` accepts `discount` and `tax` without upper bounds relative to subtotal. `total = max(subtotal - discount + tax, 0)` can produce **zero-revenue sales** with arbitrary discount.

**Risk:** Financial abuse if an attacker compromises an admin session.

**Improvement:** Cap discount percentage; require manager approval over threshold; server-side pricing rules.

---

## 4. Authorization and access control

### 4.1 Gates vs routes (Low)

Fine-grained abilities are defined in `AppServiceProvider` (`adjust-inventory`, `manage-sales`, etc.) and used on routes. `EnsureUserHasRole` middleware exists but route file primarily uses `can:`.

**Improvement:** Standardize on policies per model (`SalePolicy`, `PrescriptionPolicy`) for `view`, `update`, `delete` to avoid drift.

### 4.2 No per-record authorization (Medium)

Example: any user with `access-pharmacy-operations` can open any prescription by ID if they guess the URL (`PrescriptionController::show` / `updateStatus`).

**Risk:** In a multi-branch setting, pharmacists could view others’ records; for class scope may be acceptable.

**Improvement:** Policies scoped by branch/team; or at least audit `view` events.

### 4.3 Admin user deletion (Medium)

`UserController::destroy` prevents self-delete but does not prevent deleting the **last** admin account.

**Risk:** Lockout from admin features.

**Improvement:** Query count of admins before delete; forbid when `<= 1`.

### 4.4 Registration role (Low–Medium)

`RegisteredUserController` does not set `role`; DB default is `pharmacist`. `User::$fillable` includes `role`, but public registration does not expose it (good). Admin-only user creation sets role via `StoreUserRequest`.

**Residual risk:** Any future mass-assignment endpoint that accepts `role` without validation could elevate privileges—always use dedicated FormRequests and `$request->only([...])`.

---

## 5. Security

### 5.1 Default seeded credentials (High in production)

`DatabaseSeeder` creates `admin@example.com` / `password` and `pharmacist@example.com` / `password`.

**Risk:** Trivial compromise if seeding reaches production unchanged.

**Improvement:** Never seed default passwords in production; use env-driven one-time admin; force password reset on first login.

### 5.2 Email verification disabled (Medium)

`User` model does not implement `MustVerifyEmail`; dashboard uses `verified` middleware—verify behavior matches product intent.

**Improvement:** Enable verification for internet-facing deployments.

### 5.3 HTTPS and cookies (Medium)

Documented elsewhere: `SESSION_SECURE_COOKIE` must align with `APP_URL` scheme to avoid 419 / rejected cookies.

**Improvement:** Enforce HTTPS at reverse proxy; HSTS; secure cookies.

### 5.4 Rate limiting (Low)

Login uses `LoginRequest` rate limiting (good). API-style abuse of other endpoints is not uniformly throttled.

**Improvement:** `throttle` middleware on sensitive POST routes (registration, password reset already partially covered in `routes/auth.php`).

---

## 6. Application and UI layer

### 6.1 Navigation vs authorization (Low)

Desktop nav hides admin links for non-admins (`Auth::user()->isAdmin()`). Mobile menu should mirror the same rules (verify parity to avoid confusing 403s).

### 6.2 Pharmacist dashboard cards (Low)

Pharmacists still see aggregate counts (customers, medications, today’s prescriptions) which may be desirable or too broad depending on policy.

**Improvement:** Role-specific dashboards and widgets.

### 6.3 Reports time window consistency (Low)

`ReportController::sales` uses “last 10 sales” without the same month filter as aggregates; could confuse users.

**Improvement:** Single coherent filter (date range query params).

### 6.4 Search `LIKE` patterns (Low)

User-supplied search is passed to `LIKE "%{$search}%"`—special characters `%` and `_` can broaden results unexpectedly.

**Improvement:** Escape wildcards or use full-text search.

### 6.5 Accessibility (Low)

Forms and tables should be audited for labels, focus order, and live regions for validation—typical Blade/Breeze baseline is okay but not comprehensive.

---

## 7. Performance and scalability

### 7.1 Inventory index page (Medium at scale)

`InventoryController::index` loads all medications with inventory into memory, then filters low stock in collection.

**Improvement:** Push filters to SQL `whereExists` / `having` with pagination.

### 7.2 Dashboard (Low)

Multiple aggregate queries per request; acceptable for small data.

**Improvement:** Cache aggregates for N minutes or use materialized summaries.

---

## 8. Testing and quality gates

### 8.1 Coverage gaps (Medium)

Feature tests exist for major flows, but gaps likely include:

- Concurrent sale / double-submit behavior
- Prescription illegal transitions
- Last-admin deletion
- Medication delete with historical sales

**Improvement:** Add integration tests with database transactions and `lockForUpdate` tests where relevant.

### 8.2 CI vs production PHP (Low)

CI was aligned to PHP 8.4 to match framework requirements; keep Dockerfile, `composer.json` `require.php`, and CI in sync when upgrading.

---

## 9. Operations and deployment

### 9.1 Deploy script destructive git reset (High awareness)

Production deploy uses `git reset --hard origin/main`. Uncommitted changes on the server are wiped.

**Improvement:** Server should never hold local edits; document clearly.

### 9.2 Migrations on every deploy (Medium)

Running `migrate --force` on each deploy is standard; ensure backward-compatible migrations and backups before risky changes.

### 9.3 Environment-specific config (Medium)

Cached config (`config:cache`) bakes env at build time—ensure `.env` on server is correct before caching; document `APP_KEY` rotation procedure.

---

## 10. Suggested priority roadmap

1. **Stock correctness:** `lockForUpdate()` on inventory during sale and adjustment; validate active medications only.
2. **Prescription workflow:** State machine for status; optional stock tie-in on dispense.
3. **Safety on deletes:** Soft-delete medications; restrict user delete impact on historical data.
4. **Security hygiene:** Remove or gate default seed users in production; enable HTTPS + secure cookies.
5. **Authorization polish:** Policies per resource; last-admin guard.
6. **UX/reporting:** Align report filters; pagination on heavy index pages.
7. **Compliance:** PII handling documentation and encryption strategy for customer health fields.

---

## Document maintenance

When you fix an item, either remove it from this list or mark it with a date and PR reference so the document stays trustworthy for reviewers and future contributors.
