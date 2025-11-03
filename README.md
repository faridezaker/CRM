# 🧩 CRM Lead Management System

This project is a **CRM-style Lead Management API**, built with **Laravel 12**, designed to handle user registration, contact management, and automated lead assignment through marketing codes or a fair (round-robin) distribution system.

---

## 🚀 Overview

This system manages customers (**Contacts**) and salespeople (**SalesPersons**) in a structured way.  
When a new customer registers or performs an action, a **Lead** is created and assigned to a salesperson based on one of two rules:

1. If a **marketing code** is provided, the lead is assigned to that salesperson.
2. Otherwise, it’s assigned **fairly using a round-robin algorithm**, ensuring balanced workloads.

In addition, a **background job** runs every 24 hours to:
- Check for customers who registered but made no purchase,
- Archive their previous leads,
- And create a new follow-up lead automatically in the `follow_up_24h` pipeline.

---

## ⚙️ Tech Stack

- **Language:** PHP 8.2+
- **Framework:** Laravel 12
- **Database:** MySQL
- **Cache/Queue:** Redis
- **Documentation:** Swagger (L5-Swagger)
- **Containerization:** Docker + Docker Compose
- **Testing:** PHPUnit + Laravel Test Suite

---

## 🏗️ Architecture Overview

The project uses the **Repository-Service pattern** for clean separation between business logic and data persistence:

- `Repositories` handle database interactions.
- `Services` manage application logic.
- `Jobs` handle background tasks such as lead follow-up checks.
- `Requests` handle validation for incoming data.
- `Resources` format API responses.

Key modules include:
- **AuthService** → Handles user registration and JWT authentication.
- **LeadService** → Manages lead creation, marketing code validation, and round-robin assignment.
- **Jobs/CheckFollowUpLeads** → Archives and recreates leads after 24 hours of inactivity.

---

## 🐳 Run with Docker

### To Run:

```bash
# Build and start containers
docker compose up -d --build

# Install dependencies and set up Laravel
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan jwt:secret

# Stop containers
docker compose down

# Run artisan commands
docker compose exec app php artisan <command>

# Start Docker containers
docker compose up -d --build
