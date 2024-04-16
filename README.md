# E-Commerce

## T-WEB-600

### Installation

#### Common

1. Install PHP >= 8.2 on your machine.

#### Development

Docker

```
docker compose -f docker-compose.dev.yml up -d
```

JWT

```
php bin/console lexik:jwt:generate-keypair
```
