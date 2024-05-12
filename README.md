# E-Commerce

## T-WEB-600

### Installation

#### Common

1. Install PHP >= 8.2 on your machine.

#### Development

Docker

```
cd app
docker compose -f docker-compose.dev.yml up -d
```

Build The Database

```
cd app
make migrate
```

JWT

```
cd app
php bin/console lexik:jwt:generate-keypair
```

### Production

```
cd front && npm run build
docker compose up
```

