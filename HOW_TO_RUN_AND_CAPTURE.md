# How to Run the Project and Capture Screenshots (Windows CMD)

## 1. Start Apache and MySQL

Open **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**.

> If MySQL fails to start, run `C:\xampp01\mysql_start.bat` as administrator and wait until port 3306 is listening.

Verify MySQL is listening:

```bat
netstat -ano | findstr :3306
```

Expected output: a line containing `LISTENING`.

---

## 2. Open the correct project folder

Copy and paste this whole command into Command Prompt, then press Enter:

```bat
cd /d C:\xampp\htdocs\lab05_2411600350\lab05_2411600350
```

To confirm you are in the right folder:

```bat
cd
```

Expected output:
```
C:\xampp\htdocs\lab05_2411600350\lab05_2411600350
```

---

## 3. Start the Laravel server

```bat
php artisan serve
```

Expected output:
```
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

Leave this Command Prompt window open while you work.

---

## 4. Log in

Open your browser and go to:

```
http://127.0.0.1:8000/login
```

Enter:

| Field | Value |
|---|---|
| Email or Username | `admin` |
| Password | `password123` |

After login you land on the Lab 4 dashboard at `/dashboard`.

---

## 5. Screenshot checklist — exact steps

| # | What to capture | Exact steps |
|---|---|---|
| 1 | Model code | VS Code → `app/Models/InventoryProduct.php` |
| 2 | Migration status | CMD → `php artisan migrate:status` → capture terminal |
| 3 | Login page | Browser → `http://127.0.0.1:8000/login` (log out first) |
| 4 | Dashboard | After login → `/dashboard` → capture top with 4 stat cards |
| 5 | Charts | Same page → scroll to "Analytics & Charts" → capture 3 charts |
| 6 | Inventory table | Sidebar → **Manage Inventory** → `/inventory` |
| 7 | Low stock filter | On `/inventory` → click **⚠️ Low Stock Only** |
| 8 | Add Product form | `/inventory` → click **+ New Product Form** |
| 9 | Edit Product form | `/inventory` → **Edit** on any row |
| 10 | Product details | `/inventory` → **View** on any row |
| 11 | Transaction form | `/inventory` → **Open Full Form** next to Record Stock Transaction |
| 12 | Recent transactions | `/dashboard` → scroll to "Recent Inventory Transactions" |
| 13 | CSV export | `/inventory` → **Export CSV** → open downloaded file |
| 14 | Database data | phpMyAdmin → `lab05_inventory` → `inventory_products` |
| 15 | Original portal preserved | Browser → `http://127.0.0.1:8000/student-portal` |

---

## 6. Quick verification commands

Run these in the project folder:

```bat
php artisan migrate:status
php artisan route:list --except-vendor
php artisan view:cache
```

Expected:
- all migrations show `Ran`
- inventory routes include `inventory.api.items`, `inventory.export-csv`, `inventory.transactions.create`
- Blade templates cached successfully

To check live API data, open:

```
http://127.0.0.1:8000/inventory/api/items
```

Expected start of response:
```json
{"success":true,"items":[...],"stats":{"totalItems":20,...}}
```

---

## 7. Troubleshooting

| Problem | Fix |
|---|---|
| `No connection could be made ... 3306` | MySQL is off. Start it in XAMPP or run `C:\xampp01\mysql_start.bat`. |
| `SQLSTATE[42S02] ... performance_schema.session_status` | Harmless `db:show` issue on this XAMPP. Ignore it; the app still works. |
| Page shows 404 for JS/CSS | Run `php artisan serve` from the project root, not from `public`. |
| Login rejects `admin` | Make sure MySQL is running and the `users` table still has the admin row. Do **not** run `migrate:fresh`. |
| Charts do not appear | Check browser console. Ensure Chart.js CDN is reachable, then hard-refresh with `Ctrl+F5`. |
| Changes not visible | Run `php artisan view:cache` and `php artisan optimize:clear`. |
| `403` on git push | The GitHub account pushing must have Write access to `ainturjay123/lab05`. |

---

## 8. Never run these destructive commands

```bat
php artisan migrate:fresh
php artisan db:wipe
```

And never execute SQL like `DROP TABLE ...` unless you have a backup and explicit permission.
