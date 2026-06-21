# StockScan Phase 2 — Backend Specification

## Overview
Phase 2 adds Reports, Activity Logging, and CSV Import functionality to the Laravel API.

---

## 1. DATABASE MIGRATIONS

### Create Activity Logs Table

```php
// database/migrations/YYYY_MM_DD_create_activity_logs_table.php

Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('action'); // e.g., 'product.created', 'stock.in', 'user.login'
    $table->string('entity_type')->nullable(); // e.g., 'product', 'transaction'
    $table->unsignedBigInteger('entity_id')->nullable();
    $table->text('description');
    $table->string('ip_address')->nullable();
    $table->timestamps();

    $table->index(['tenant_id', 'user_id']);
    $table->index(['action', 'created_at']);
});
```

No schema changes needed for other tables.

---

## 2. API ENDPOINTS

All endpoints require authentication and are tenant-scoped.

### 2.1 Reports Endpoints (Owner Only)

Base path: `/api/reports`

Middleware: `auth:sanctum` + `role:owner` (implement via policy/middleware)

---

#### GET /api/reports/stock-movement

Stock in vs. out over a time period, grouped by day/week/month.

**Query Parameters:**
- `from` (required, format: Y-m-d)
- `to` (required, format: Y-m-d)
- `group_by` (optional, default: 'day', values: 'day', 'week', 'month')
- `product_id` (optional, filter to single product)

**Response:**
```json
{
  "data": [
    {
      "period": "2024-01-15",
      "stock_in": 120,
      "stock_out": 45,
      "net": 75
    }
  ],
  "summary": {
    "total_in": 540,
    "total_out": 210,
    "net": 330
  }
}
```

**Implementation Notes:**
- Query `stock_transactions` grouped by period
- `type = 'in'` → `stock_in`
- `type = 'out'` → `stock_out`
- `net = stock_in - stock_out`
- Filter by `tenant_id` and optional `product_id`

---

#### GET /api/reports/top-products

Most active products by transaction volume.

**Query Parameters:**
- `from` (required, Y-m-d)
- `to` (required, Y-m-d)
- `type` (optional, default: 'out', values: 'in', 'out', 'all')
- `limit` (optional, default: 10, max: 50)

**Response:**
```json
{
  "data": [
    {
      "product_id": 1,
      "name": "Blue T-Shirt",
      "sku": "TSH-001",
      "category": "Clothing",
      "total_quantity": 340,
      "transaction_count": 28
    }
  ]
}
```

**Implementation:**
- Join `stock_transactions` with `products`
- Group by product, sum quantities and count transactions
- Filter by type if specified, else return all
- Order by total_quantity descending
- Limit to requested count

---

#### GET /api/reports/inventory-value

Current snapshot of inventory value by category.

**Query Parameters:** None

**Response:**
```json
{
  "total_value": 48320.00,
  "total_units": 1240,
  "by_category": [
    {
      "category": "Electronics",
      "product_count": 12,
      "total_units": 340,
      "total_value": 28400.00,
      "percentage": 58.8
    }
  ]
}
```

**Implementation:**
- Query all products with quantity > 0, grouped by category
- Calculate: `value = quantity * price`
- Percentage: `(category_value / total_value) * 100`
- Filter by tenant_id

---

#### GET /api/reports/low-stock-history

Products that have been low stock, with frequency and last restock.

**Query Parameters:** None

**Response:**
```json
{
  "data": [
    {
      "product_id": 5,
      "name": "USB-C Cable",
      "sku": "CBL-002",
      "current_quantity": 2,
      "threshold": 5,
      "times_hit_low_stock": 4,
      "last_restocked_at": "2024-01-10"
    }
  ]
}
```

**Implementation:**
- Query products where `quantity <= low_stock_threshold`
- Count how many times each product has been at/below threshold:
  - Query stock_transactions, count times when resulting quantity <= threshold
  - Or maintain a counter on the products table (performance optimization)
- Find the last `stock_in` transaction for each product (last_restocked_at)
- Filter by tenant_id
- Order by times_hit_low_stock descending

---

#### GET /api/reports/export

Export transactions as CSV file (client-side trigger).

**Query Parameters:**
- `from` (required, Y-m-d)
- `to` (required, Y-m-d)
- `type` (optional, default: 'all', values: 'in', 'out', 'all')
- `product_id` (optional)

**Response:** CSV file with Content-Type: `text/csv`

**CSV Columns:**
```
Date, Product Name, SKU, Category, Type, Quantity, Note, Performed By
```

**Implementation:**
- Query stock_transactions in date range, with relationships loaded
- Generate CSV from query results
- Return as download response

---

### 2.2 Import Endpoint (Owner Only)

#### POST /api/products/import

Bulk upload products from CSV file. Creates new products, updates existing by SKU.

**Request:** `multipart/form-data`
- `file` (required, .csv file)

**CSV Format:**
```
name,sku,description,category,unit,quantity,price,low_stock_threshold
Blue T-Shirt,TSH-001,Cotton shirt,Clothing,pcs,100,9.99,10
```

**Response:**
```json
{
  "data": {
    "created": 8,
    "updated": 4,
    "failed": 1,
    "errors": [
      {
        "row": 3,
        "sku": "BAD-001",
        "message": "Quantity must be a positive number"
      }
    ]
  }
}
```

**Implementation:**
1. Parse CSV with headers: name, sku, description, category, unit, quantity, price, low_stock_threshold
2. For each row:
   - Validate: name required, quantity/price numeric, unit valid
   - Look up category by name (case-insensitive), create if not found
   - Check if product with this SKU exists in this tenant
   - **If exists:** Update name, description, category, unit, price, threshold; add quantity to current
   - **If not exists:** Create new product with quantity as initial stock
   - Create a `stock_transaction` of type `in` with note `"Bulk CSV import"` for any quantity changes
   - If validation fails, record error with row number and message, but continue processing
3. Return summary of created/updated/failed with error list

---

### 2.3 Labels Endpoint (All Authenticated Users)

#### POST /api/products/labels/generate

Get product data for label printing (no PDF generation, data only).

**Request:**
```json
{
  "product_ids": [1, 2, 3],
  "label_size": "small|medium|large"
}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Blue T-Shirt",
      "sku": "TSH-001",
      "barcode_value": "TSH-001",
      "price": 9.99,
      "category_name": "Clothing",
      "unit": "pcs"
    }
  ]
}
```

**Implementation:**
- Fetch requested products
- Return only fields needed for label rendering
- Filter by tenant_id

---

### 2.4 Activity Log Endpoint (Owner Only)

#### GET /api/activity-logs

Paginated activity log (30 per page).

**Query Parameters:**
- `page` (optional, default: 1)
- `user_id` (optional, filter by user)
- `action` (optional, filter by action type)
- `from` (optional, Y-m-d)
- `to` (optional, Y-m-d)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "user_name": "John Doe",
      "action": "product.created",
      "description": "Created product: Blue T-Shirt",
      "entity_type": "product",
      "entity_id": 5,
      "ip_address": "192.168.1.100",
      "created_at": "2024-01-15T10:30:00"
    }
  ],
  "pagination": {
    "total": 150,
    "per_page": 30,
    "current_page": 1,
    "total_pages": 5
  }
}
```

**Implementation:**
- Query activity_logs, join with users
- Filter by user_id, action, date range
- Paginate results
- Filter by tenant_id

---

## 3. ACTIVITY LOGGING

### Auto-Logging Setup

Use Laravel Observers and manual logging in controllers to record activities.

#### Product Model Observer

```php
// app/Observers/ProductObserver.php

public function created(Product $product)
{
    ActivityLog::create([
        'tenant_id' => $product->tenant_id,
        'user_id' => auth()->id(),
        'action' => 'product.created',
        'entity_type' => 'product',
        'entity_id' => $product->id,
        'description' => "Created product: {$product->name}",
        'ip_address' => request()->ip(),
    ]);
}

public function updated(Product $product)
{
    ActivityLog::create([
        'tenant_id' => $product->tenant_id,
        'user_id' => auth()->id(),
        'action' => 'product.updated',
        'entity_type' => 'product',
        'entity_id' => $product->id,
        'description' => "Updated product: {$product->name}",
        'ip_address' => request()->ip(),
    ]);
}

public function deleted(Product $product)
{
    ActivityLog::create([
        'tenant_id' => $product->tenant_id,
        'user_id' => auth()->id(),
        'action' => 'product.deleted',
        'entity_type' => 'product',
        'entity_id' => $product->id,
        'description' => "Deleted product: {$product->name}",
        'ip_address' => request()->ip(),
    ]);
}
```

Register observer in AppServiceProvider:
```php
public function boot()
{
    Product::observe(ProductObserver::class);
}
```

#### Transaction Controller Logging

In `StockTransactionController@store`:

```php
// After creating transaction
ActivityLog::create([
    'tenant_id' => auth()->user()->tenant_id,
    'user_id' => auth()->id(),
    'action' => $transaction->type === 'in' ? 'stock.in' : 'stock.out',
    'entity_type' => 'transaction',
    'entity_id' => $transaction->id,
    'description' => "Stocked {($transaction->type === 'in' ? 'in' : 'out')} {$transaction->quantity} units of {$transaction->product->name}",
    'ip_address' => request()->ip(),
]);
```

#### Auth Logging (Optional)

In your auth controller or middleware:

```php
// After successful login
ActivityLog::create([
    'tenant_id' => $user->tenant_id,
    'user_id' => $user->id,
    'action' => 'user.login',
    'description' => "User logged in",
    'ip_address' => request()->ip(),
]);

// After logout
ActivityLog::create([
    'tenant_id' => auth()->user()->tenant_id,
    'user_id' => auth()->id(),
    'action' => 'user.logout',
    'description' => "User logged out",
    'ip_address' => request()->ip(),
]);
```

---

## 4. AUTHORIZATION

### Policies / Middleware

Create a `role:owner` middleware or use policies:

```php
// app/Policies/ReportPolicy.php

public function viewReports(User $user)
{
    return $user->role === 'owner';
}
```

Protect routes:
```php
Route::middleware(['auth:sanctum', 'role:owner'])
    ->get('/reports/*', ...);
```

---

## 5. HELPER FUNCTIONS

Optional helpers for cleaner code:

```php
// app/Helpers/ActivityLogger.php

function logActivity($action, $description, $entityType = null, $entityId = null)
{
    ActivityLog::create([
        'tenant_id' => auth()->user()->tenant_id,
        'user_id' => auth()->id(),
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'description' => $description,
        'ip_address' => request()->ip(),
    ]);
}
```

---

## 6. TESTING

Test each endpoint:

```php
// tests/Feature/ReportsTest.php

public function test_owner_can_view_stock_movement()
{
    $owner = User::factory()->owner()->create();
    $product = Product::factory()->for($owner->tenant)->create();
    
    StockTransaction::factory()
        ->for($owner->tenant)
        ->for($product)
        ->create(['type' => 'in', 'quantity' => 100]);

    $response = $this->actingAs($owner)
        ->get('/api/reports/stock-movement?from=2024-01-01&to=2024-01-31');

    $response->assertOk();
    $response->assertJsonStructure(['data', 'summary']);
}

public function test_staff_cannot_view_reports()
{
    $staff = User::factory()->staff()->create();
    
    $response = $this->actingAs($staff)
        ->get('/api/reports/stock-movement?from=2024-01-01&to=2024-01-31');

    $response->assertForbidden();
}
```

---

## 7. SUMMARY OF CHANGES

| Item | Type | Notes |
|------|------|-------|
| `activity_logs` table | Migration | New table for audit trail |
| `POST /api/products/import` | Endpoint | CSV bulk upload (owner only) |
| `GET /api/reports/stock-movement` | Endpoint | Chart data (owner only) |
| `GET /api/reports/top-products` | Endpoint | Product stats (owner only) |
| `GET /api/reports/inventory-value` | Endpoint | Snapshot (owner only) |
| `GET /api/reports/low-stock-history` | Endpoint | Alert tracking (owner only) |
| `GET /api/reports/export` | Endpoint | CSV download (owner only) |
| `POST /api/products/labels/generate` | Endpoint | Label data (all users) |
| `GET /api/activity-logs` | Endpoint | Audit log (owner only) |
| ProductObserver | Observer | Auto-log product changes |
| ActivityLogger | Helper | Optional logging utility |
| `role:owner` middleware | Middleware | Authorization for reports |

---

## Frontend Integration Notes

React frontend expects responses in `{ data, pagination }` format (already implemented in API calls).

- Charts render with Recharts (client-side)
- CSV export triggers `window.download()` (frontend handles)
- Import preview uses PapaParse (client-side CSV parsing)
- Activity log and report filters are all client-side queries; backend handles pagination

All endpoints use tenant-scoped queries automatically via auth user's tenant.

