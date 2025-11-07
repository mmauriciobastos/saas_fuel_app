# SaaS Fuel App

A 

## 📁 Project Structure

```
saas_fuel_app/
├── backend/          # Symfony application
│   ├── src/          # Application source code
│   ├── config/       # Symfony configuration
│   ├── public/       # Web entry point
│   └── ...
├── infra/            # Infrastructure as Code
│   ├── docker-compose.yml
│   ├── Dockerfile
│   └── nginx.conf
└── scripts/          # Helper scripts
```

## 🚀 Getting Started


## Prerequisites

Make sure you have the following installed on your machine:

- **Docker**
- **Docker Compose**
- **Git**

---

## Clone the Repository

```bash
git clone https://github.com/mmauriciobastos/saas_fuel_app.git
```

Navigate into the project directory:

```bash
cd saas_fuel_app
```

---

## Start the Docker Environment

The Docker setup is located inside the `infra` folder:

```bash
cd infra
docker-compose up -d
```

This will build and start all required containers.

---

## Install Backend Dependencies

Navigate to the backend (Symfony API) folder:

```bash
cd ..
cd backend
```

Install PHP dependencies inside the container:

```bash
docker exec -it symfony-app composer install
```

---

## Database Setup

Generate and run migrations:

```bash
docker exec -it symfony-app php bin/console doctrine:migrations:diff
docker exec -it symfony-app php bin/console doctrine:migrations:migrate
```

Seed the database with fixtures:

```bash
docker exec -it symfony-app php bin/console doctrine:fixtures:load
```

---

## Accessing the Application

### API (Swagger UI)
Open in your browser:

```
http://localhost/api/docs
```

### pgAdmin
```
http://localhost:8081
```

**pgAdmin Credentials:**

| Field      | Value             |
|-----------|-------------------|
| Login     | admin@local.com   |
| Password  | admin             |


# Run tests
php bin/phpunit
```

#### Infrastructure Management

```bash
# Start all services
cd infra
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Rebuild containers
docker-compose up -d --build
```

## 🛠️ Available Scripts

See the `scripts/` directory for helper scripts that automate common tasks.

## 📝 Environment Configuration

Create `.env` files in the `backend/` directory as needed:
- `.env` - Base configuration
- `.env.local` - Local overrides (gitignored)

## 🗄️ Database

- **Type:** PostgreSQL 16
- **Default Database:** saas_fuel_db
- **Default User:** mauricio
- **Default Password:** secret
- **Port:** 5432

## 🔧 Services

### Services Running in Docker

1. **PostgreSQL Database** - Port 5432
2. **pgAdmin** - Port 8081
3. **Symfony PHP-FPM** - Internal
4. **Nginx** - Port 8000

## 📦 Monorepo Benefits

- **Unified version control** - Single repository for all related code
- **Atomic commits** - Changes to backend and infrastructure together
- **Shared tooling** - Common scripts and configurations
- **Easier collaboration** - All team members see the full stack

## 🤝 Contributing

1. Create a feature branch from `main`
2. Make your changes
3. Commit with clear messages
4. Push and create a pull request

## 📄 License

[Add your license information here]

