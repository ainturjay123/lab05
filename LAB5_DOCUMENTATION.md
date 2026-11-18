# Laboratory Exercise 5 — Inventory Management System

**Student:** 2411600350  
**Project folder:** `C:\xampp\htdocs\lab05_2411600350\lab05_2411600350`  
**Database:** `lab05_inventory` (MySQL)  
**Framework:** Laravel 12.69.3  
**Login:** `admin` / `password123` (Breeze-style session authentication)

---

## 1. Overview

This project extends the existing Laravel **Student Portal** with the Lab 4 inventory dashboard.  
All inventory data now comes from **MySQL**, not from hardcoded JavaScript arrays or `localStorage`.

The original Student Portal was **preserved**:

| Original feature | Still available at |
|---|---|
| Student Portal dashboard | `/student-portal` |
| Student activity CRUD | `/products` |
| Original user account | `users` table (1 row, untouched) |

The new inventory system is available at:

| Page | URL |
|---|---|
| Inventory dashboard (Lab 4 design) | `/dashboard` |
| Inventory tracking + CRUD | `/inventory` |
| Add product form | `/inventory/products/create` |
| Product details | `/inventory/products/{id}` |
| Edit product | `/inventory/products/{id}/edit` |
| Record transaction | `/inventory/transactions/create` |
| JSON API | `/inventory/api/items` |
| CSV export | `/inventory/export-csv` |

---

## 2. MVC Architecture

| Layer | Files | Responsibility |
|---|---|---|
| **Model** | `app/Models/InventoryProduct.php`, `app/Models/InventoryTransaction.php` | Data, relationships, casts, low-stock helpers/scopes |
| **View** | `resources/views/dashboard-inventory.blade.php`, `resources/views/inventory/*.blade.php` | Lab 4 layout, forms, tables, charts |
| **Controller** | `app/Http/Controllers/InventoryController.php`, `InventoryDashboardController.php` | CRUD, validation, filtering, JSON API, CSV export |
| **Observer** | `app/Observers/InventoryTransactionObserver.php` | Automatically adjusts stock when a transaction is created |
| **Routes** | `routes/web.php` | All URLs, protected by `auth` middleware |
| **Seeders** | `database/seeders/InventoryProductSeeder.php`, `InventoryTransactionSeeder.php` | Sample hardware products and stock movements |
| **Assets** | `public/css/lab4-style.css`, `public/js/lab4/*.js` | Original Lab 4 design and modular JavaScript |

The original `Product` model and `ProductController` were **not changed** — they still serve the Student Portal.

---

## 3. Database

Tables used (nothing was dropped or wiped):

| Table | Rows | Purpose |
|---|---:|---|
| `users` | 1 | Authentication (preserved) |
| `products` | 4 | Student Portal activities (preserved) |
| `inventory_products` | 20 | Inventory items |
| `inventory_transactions` | 9 | Stock movements |
| `migrations` | 7 | Migration history |

`inventory_products` columns:
`id`, `name`, `sku` (UNIQUE), `category`, `description`, `quantity`, `reorder_level`, `unit_price`, `supplier`, `created_at`, `updated_at`.

`inventory_transactions` columns:
`id`, `inventory_product_id`, `user_id`, `type`, `quantity`, `reference_number`, `notes`, `created_at`, `updated_at`.

### Migration note
The obsolete file `2026_10_12_022603_add_fields_to_inventory_transactions_table.php` was renamed to `.php.bak` because its columns already exist in the later create-table migration. Laravel ignores `.bak` files, so `migrate` now runs cleanly. This was reversible and touched no data.

A new additive migration added `description` and `supplier` to `inventory_products`.

---

## 4. Authentication

- Laravel Breeze session login at `/login`.
- All inventory routes are inside `Route::middleware(['auth'])`.
- The Lab 4 hardcoded `admin/password123` check and `localStorage` login were **removed**.
- Logout uses a CSRF-protected POST form, not JavaScript.

---

## 5. CRUD Operations

| Operation | Route | Controller method |
|---|---|---|
| Create product | `POST /inventory/products` | `storeProduct()` |
| Read all | `GET /inventory` | `index()` |
| Read one | `GET /inventory/products/{id}` | `show()` |
| Update | `PUT /inventory/products/{id}` | `updateProduct()` |
| Delete | `DELETE /inventory/products/{id}` | `destroyProduct()` |

Validation includes required name/SKU, unique SKU, valid category, non-negative quantity/reorder level/price, and custom friendly error messages.

Safety rule: a product with existing transactions **cannot be deleted**, so transaction history is never orphaned.

---

## 6. Dashboard & Charts

`/dashboard` uses the original Lab 4 layout and exact palette:

`#800776`, `#895737`, `#DAB49D`, `#C08552`, `#5E3023`, `#F3E3D7`.

It displays:
- stats cards: total items, inventory value, low stock, out of stock
- 3 Chart.js charts: value by category, stock status distribution, top 5 products by value
- search/filter toolbar and inventory table
- recent transactions and recently added products

Charts receive **all products** from the API (`all_items`), so filtering the table does not distort the charts.

---

## 7. Transactions

- Record Stock In or Stock Out through `/inventory/transactions/create` or the inline form on `/inventory`.
- `InventoryTransactionObserver` updates `quantity` automatically using `lockForUpdate()` inside a DB transaction.
- Stock Out is rejected if it would make quantity negative.
- The controller does **not** duplicate the stock update — this prevents double-counting.
- 9 sample transactions were seeded; running the seeder twice does not duplicate them or change stock twice.

---

## 8. Filtering & Export

- Search by product name or SKU
- Filter by category
- Filter by status (In Stock / Low Stock / Out of Stock)
- Filter by minimum and maximum price
- Quick buttons for Low Stock and Out of Stock
- CSV export respects the current filters

Filtering happens in **Laravel/MySQL**, not in the browser.

---

## 9. Seeders

```bat
php artisan db:seed --class=InventoryProductSeeder --force
php artisan db:seed --class=InventoryTransactionSeeder --force
```

Both use `firstOrCreate()`, so they are safe to run more than once.  
The original `ProductSeeder` was left untouched.

---

## 10. Questionnaire Answers

> Adapt the wording to match the exact questions on your worksheet.

**Q1. What is MVC and how is it applied in this project?**  
MVC (Model–View–Controller) separates data, presentation, and logic. In this project `InventoryProduct`/`InventoryTransaction` are Models, the Blade files under `resources/views/inventory` are Views, and `InventoryController`/`InventoryDashboardController` are Controllers. This makes the code easier to maintain and test.

**Q2. Why use a database instead of hardcoded/localStorage data?**  
A database stores real, shared, persistent data. Multiple users see the same stock levels, data survives browser clears, and operations can be protected by transactions and validation. `localStorage` is per-browser and was only suitable for the static Lab 4 prototype.

**Q3. How does Laravel handle authentication?**  
Laravel Breeze provides login/registration controllers and session management. Passwords are hashed with bcrypt. Routes inside the `auth` middleware redirect guests to `/login`. Logout uses a POST request with a CSRF token.

**Q4. What is Eloquent and what relationships are used?**  
Eloquent is Laravel's ORM. `InventoryProduct` has many `InventoryTransaction` records (`hasMany`), and each `InventoryTransaction` belongs to one `InventoryProduct` and one `User` (`belongsTo`).

**Q5. How is stock accuracy maintained?**  
Stock changes only through `InventoryTransactionObserver`, which runs inside a database transaction with row locking. Stock Out is blocked if there is not enough stock. Seeder and controller both create transactions instead of editing quantities directly.

**Q6. What is a migration?**  
A migration is a version-controlled database schema change. We used one additive migration to add `description` and `supplier`. No destructive migration (`migrate:fresh`, `db:wipe`, `DROP`) was used.

**Q7. What is a seeder and why use `firstOrCreate()`?**  
A seeder inserts sample data. `firstOrCreate()` checks by a unique key (SKU or reference number) first, so re-running the seeder never creates duplicates and never changes stock twice.

**Q8. How do filtering and export work?**  
Filters are added to the Eloquent query in the controller based on GET parameters. The CSV export applies the same query and streams the matching rows with headers.

**Q9. How are the charts populated?**  
The dashboard calls `/inventory/api/items`, which returns JSON containing all products and statistics. `charts.js` aggregates that data by category, status, and value to draw the Chart.js graphs.

**Q10. What design choices were preserved from Lab 4?**  
The sidebar, topbar, dashboard cards, chart canvases, table, search/filter controls, modals/forms, typography, spacing, responsiveness, and the exact six-color palette were preserved. Only the fake data and fake login were replaced.

---

## 11. Conclusion

The Lab 4 inventory dashboard was successfully integrated into the existing Laravel Student Portal. The system now uses MySQL as its single source of truth, Laravel MVC for structure, Breeze for authentication, Eloquent for data access, and a DB-transaction observer for reliable stock movements. CRUD, search/filtering, CSV export, charts, and seeders all work with live data. At the same time, the original Student Portal, its users, its products, and its routes were kept intact. This demonstrates how a static frontend prototype can be converted into a secure, data-driven web application without discarding prior work.

---

## 12. Screenshot Checklist

| # | Screenshot | How to capture |
|---|---|---|
| 1 | `InventoryProduct.php` model in VS Code | Open `app/Models/InventoryProduct.php` |
| 2 | Migrations list | Run `php artisan migrate:status` in terminal |
| 3 | Login page | Visit `http://127.0.0.1:8000/login` |
| 4 | Dashboard with stats cards | Login, visit `/dashboard` |
| 5 | Chart.js charts | Scroll to the Analytics section of `/dashboard` |
| 6 | Inventory table + filters | Visit `/inventory` |
| 7 | Low-stock filter result | Click "⚠️ Low Stock Only" on `/inventory` |
| 8 | Add Product form | Visit `/inventory/products/create` |
| 9 | Edit Product form | Click Edit on any product |
| 10 | Product details page | Click View on any product |
| 11 | Record Transaction form | Visit `/inventory/transactions/create` |
| 12 | Recent transactions table | Bottom of `/dashboard` or `/inventory` |
| 13 | CSV export | Click Export CSV and show the downloaded file |
| 14 | phpMyAdmin `inventory_products` | Show the 20 hardware products |
| 15 | Original Student Portal preserved | Visit `/student-portal` |

---

## 13. File Map

### Added
- `app/Http/Controllers/InventoryDashboardController.php`
- `resources/views/dashboard-inventory.blade.php`
- `resources/views/inventory/create.blade.php`
- `resources/views/inventory/show.blade.php`
- `resources/views/inventory/transaction-create.blade.php`
- `database/migrations/2026_11_18_000001_add_description_and_supplier_to_inventory_products_table.php`
- `database/seeders/InventoryProductSeeder.php`
- `database/seeders/InventoryTransactionSeeder.php`
- `public/css/lab4-style.css`
- `public/js/lab4/{login,Dashboard,dataManager,inventory,charts}.js`

### Modified (additively)
- `app/Models/InventoryProduct.php`
- `app/Http/Controllers/InventoryController.php`
- `routes/web.php`
- `resources/views/inventory/index.blade.php`
- `resources/views/inventory/edit.blade.php`

### Preserved untouched
- `app/Models/Product.php`
- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/DashboardController.php`
- `resources/views/dashboard.blade.php`
- `resources/views/products/*`
- `database/seeders/ProductSeeder.php`
- all existing `users` and `products` rows
