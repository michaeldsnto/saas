# Smart Inventory SaaS

A Laravel 12 multi-tenant inventory SaaS starter with:

- Company-based tenant isolation
- Owner / Staff RBAC
- Product, category, supplier, warehouse, stock, and stock movement management
- POS transaction flow with stock deduction
- Audit logs
- REST API with Sanctum
- Subscription plans and billing records
- Real-time event broadcasting with Reverb-compatible channels
- Basic analytics and AI-style stock run-out prediction
- Excel import / export and PDF reports

## Architecture

### Multi-tenant model

The app uses a `company_id` strategy for tenant isolation.

- `App\Support\Tenant\TenantManager` stores the active tenant for the current request.
- `App\Http\Middleware\EnsureTenantContext` populates the tenant manager from the authenticated user.
- `App\Models\Concerns\BelongsToCompany` automatically:
  - injects `company_id` on create
  - applies a global scope so tenant data is isolated by default

### Application layers

- `app/Models`: domain entities and relationships
- `app/Http/Controllers`: Blade and API entry points
- `app/Http/Requests`: input validation
- `app/Services`: business logic for inventory, transactions, analytics, subscriptions, and auditing
- `app/Events`: real-time events for stock and transaction broadcasts
- `app/Imports` / `app/Exports`: Excel and report adapters

### Key business flows

#### Registration
1. Register form creates a `company`
2. The first user is assigned the `owner` role
3. A free trial subscription is created automatically
4. The user is logged in and redirected to the dashboard

#### POS transaction
1. Cashier submits transaction items
2. `TransactionService` validates stock via `InventoryService`
3. Transaction and transaction details are stored inside a DB transaction
4. Stock is reduced and a stock movement record is created
5. Audit log is written
6. Broadcast events are fired for dashboard / stock listeners

#### Plan enforcement
`SubscriptionService` checks plan limits before creating products, warehouses, and staff users.

#### Prediction
`StockPredictionService` calculates a moving-average style estimate from recent transaction detail rows and returns:

- average daily usage
- remaining stock
- estimated run-out date
- recommended restock quantity

## Database schema

Core tables:

- `companies`
- `users`
- `roles`
- `plans`
- `subscriptions`
- `payments`
- `categories`
- `suppliers`
- `warehouses`
- `products`
- `stocks`
- `stock_movements`
- `transactions`
- `transaction_details`
- `audit_logs`

Tenant-aware tables include a `company_id` column so isolation works consistently.

## API

Protected by Sanctum and tenant middleware.

### Endpoints

- `GET /api/dashboard`
- `GET /api/products`
- `POST /api/products`
- `GET /api/products/{product}`
- `PUT /api/products/{product}`
- `DELETE /api/products/{product}`
- `GET /api/transactions`
- `POST /api/transactions`

### Authentication
Create a Sanctum token from a user, then call the API with:

`Authorization: Bearer {token}`

## Real-time

Broadcast channels use:

- `private-company.{companyId}` conceptually, implemented as `company.{companyId}` private channels in `routes/channels.php`

Broadcast events:

- `stock.updated`
- `stock.low`
- `transaction.created`

## Billing

The billing layer is intentionally abstracted:

- `MidtransGateway`
- `XenditGateway`
- `PaymentGatewayFactory`

These are stubbed with clear placeholders so you can replace them with live HTTP integrations later without rewriting subscription logic.

## Local setup

1. `composer install`
2. `npm install`
3. Copy `.env.example` to `.env`
4. Configure MySQL / Redis if you want production-like local infra
5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. `php artisan storage:link`
8. `php artisan serve`
9. `php artisan queue:work`
10. `php artisan reverb:start`

## Deployment notes

Recommended production stack:

- Nginx
- PHP-FPM
- MySQL
- Redis
- Supervisor for queue workers and Reverb
- TLS via Let's Encrypt

Operational checklist:

- set `APP_ENV=production`
- disable debug
- configure real MySQL and Redis credentials
- switch `BROADCAST_CONNECTION=reverb`
- run `php artisan config:cache`
- run `php artisan route:cache`
- run `php artisan queue:work --queue=default --tries=3`
- run `php artisan reverb:start`

## Review guide

Files worth reading first:

- `app/Models/Concerns/BelongsToCompany.php`
- `app/Support/Tenant/TenantManager.php`
- `app/Http/Middleware/EnsureTenantContext.php`
- `app/Services/TransactionService.php`
- `app/Services/InventoryService.php`
- `app/Services/AnalyticsService.php`
- `app/Services/Prediction/StockPredictionService.php`
- `app/Services/SubscriptionService.php`

The code includes short comments in the pieces where the intent is easiest to miss, especially around tenant scoping and transaction flow.
