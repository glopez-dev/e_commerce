# E-Commerce Platform

> Full-stack e-commerce application built as part of the **T-WEB-600** module at Epitech.

## About the project

The objective of this project is to build a **generic e-commerce API** with a complete frontend, capable of handling user management, product catalog, shopping cart, order processing, and payment via Stripe. The example use case is a computer components store.

The project was built by a team of 3 developers over the course of the module.

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.3 / Symfony (REST API) |
| **Frontend** | React + TypeScript |
| **Database** | PostgreSQL 14 |
| **Authentication** | JWT (LexikJWTAuthenticationBundle) |
| **Payment** | Stripe Checkout |
| **Web Server** | Nginx |
| **Containerization** | Docker + Docker Compose |
| **API Documentation** | NelmioApiDocBundle (Swagger/OpenAPI) |

## Architecture

```
.
├── backend/              # Symfony REST API (PHP-FPM)
│   ├── src/
│   │   ├── Controller/   # API endpoints (User, Product, Cart, Order, Stripe)
│   │   ├── Entity/       # Doctrine ORM entities (User, Product, Cart, Order)
│   │   ├── Repository/   # Database query layer
│   │   └── EventListener/ # JWT authentication listeners
│   ├── migrations/       # Doctrine database migrations
│   └── config/           # Symfony configuration (security, routing, packages)
├── frontend/             # React TypeScript SPA
│   └── src/
│       ├── Components/   # Reusable UI components (Header, Sidebar, Cards, etc.)
│       ├── Pages/        # Route pages (Home, Login, Register, Cart, Orders, etc.)
│       └── Styles/       # CSS Modules
├── docker/               # Nginx configuration
├── Dockerfile            # PHP-FPM custom image
├── compose.yml           # Docker Compose orchestration
└── bruno/                # API testing collections (Bruno)
```

## API Endpoints

All endpoints follow the **REST** standard and use **JSON** for data exchange. Authenticated routes require a JWT token in the `Authorization` header.

### Users

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/register` | - | Register a new user |
| `POST` | `/api/login` | - | Login and retrieve JWT token |
| `GET` | `/api/users` | JWT | Get current user profile |
| `PUT` | `/api/users` | JWT | Update current user profile |

**User model:**
```json
{
  "login": "foobar",
  "email": "my@email.com",
  "firstname": "Foo",
  "lastname": "Bar"
}
```

### Products (Catalog)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/api/products` | - | List all products |
| `GET` | `/api/products/{id}` | - | Get a specific product |
| `POST` | `/api/products` | JWT | Create a product |
| `PUT` | `/api/products/{id}` | JWT | Update a product |
| `DELETE` | `/api/products/{id}` | JWT | Delete a product |

**Product model:**
```json
{
  "id": 1,
  "name": "Item 3000",
  "description": "Best item in the shop!",
  "photo": "https://path/to/image.png",
  "price": 13.37
}
```

### Shopping Cart

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/api/carts` | JWT | View cart contents |
| `POST` | `/api/carts/{productId}` | JWT | Add a product to the cart |
| `DELETE` | `/api/carts/{productId}` | JWT | Remove a product from the cart |
| `PUT` | `/api/carts/validate` | JWT | Convert cart into an order |

### Orders

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/api/orders` | JWT | List all orders for current user |
| `GET` | `/api/orders/{id}` | JWT | Get details of a specific order |

**Order model:**
```json
{
  "id": 1,
  "totalPrice": 42.01,
  "creationDate": "2021-04-01T08:32:00Z",
  "products": [
    {
      "id": 1,
      "name": "Item 3000",
      "description": "Best item in the shop!",
      "photo": "https://path/to/image.png",
      "price": 13.37
    }
  ]
}
```

### Payment

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/checkout` | JWT | Create a Stripe Checkout session |

Payment is handled via **Stripe Checkout**, redirecting the user to Stripe's hosted payment page and back to the application on success.

## Deployment

The application is fully containerized with **3 separate services**:

| Service | Image | Role |
|---------|-------|------|
| `database` | `postgres:14-alpine` | PostgreSQL database |
| `php_fpm` | Custom (see `Dockerfile`) | PHP-FPM application server |
| `nginx` | `nginx:1.25.1` | Web server / reverse proxy |

```bash
# Build and start all services
docker compose up --build

# The API is available at http://localhost:1030
```

## Development Setup

```bash
# Start the database
cd backend && docker compose -f docker-compose.dev.yml up -d

# Run database migrations
make migrate

# Generate JWT keypair for authentication
php bin/console lexik:jwt:generate-keypair

# Start the Symfony dev server
symfony server:start

# In a separate terminal, start the React frontend
cd frontend && npm install && npm start
```

## Error Handling

All API errors return a consistent JSON format:
```json
{
  "error": "The error message explaining what went wrong."
}
```

HTTP status codes are used precisely: `200` for success, `201` for resource creation, `404` for not found, `401`/`403` for authentication/authorization errors.
