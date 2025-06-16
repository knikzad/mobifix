# Mobifix – Device Repair Management System 🛠

Mobifix is a full-stack device repair management platform that enables customers to request repair appointments, employees to process services, and admins to analyze payments and usage through SQL and NoSQL (MongoDB). It is implemented using **Laravel**, **MariaDB**, **MongoDB**, **Blade**, and **Docker Compose**.

---

## 🧱 Project Structure

```
mobifix/
├── backend/ # Laravel application
│ ├── app/
│ ├── database/
│ ├── routes/
│ ├── resources/views/
│ ├── Dockerfile
│ └── ...
├── nginx/
│ └── default.conf # NGINX config
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## 🚀 Tech Stack

| Layer         | Technology        |
| ------------- | ----------------- |
| Backend       | Laravel (PHP 8)   |
| Frontend      | Blade + Bootstrap |
| Relational DB | MariaDB (MySQL)   |
| NoSQL DB      | MongoDB           |
| Deployment    | Docker + Compose  |

---

## 🔧 Setup Instructions

### 1. Clone the Repository

```bash
git clone [project url]
cd mobifix

```

## Setup Environment

Copy the .env.example and edit if needed.
Update your .env file with:

```bash
    DB_HOST=mobifix-db
    DB_DATABASE=mobifix
    DB_USERNAME=mobifixuser
    DB_PASSWORD=mobifixpass
    MONGO_DB_HOST=mobifix-mongo
    MONGO_DB_PORT=27017

```

## Start Docker Environment

Ensure Docker is installed and running.

```bash
docker-compose up --build
```

This will:

- Spin up Laravel backend
- Start MariaDB
- Launch MongoDB
- Configure NGINX
- Install all Composer dependencies
- Run migrations automatically

- Once running:
  - Access the application in your browser via: http://localhost
  - MariaDB is available internally at mobifix-db:3306
  - MongoDB is available at mobifix-mongo:27017

## SQL Features

- Customer & Employee management
- Appointment scheduling
- Payment processing (card/cash)

## NoSQL Features

- MongoDB version of appointments
- Embedded payment subdocuments
- Aggregated analytics (NoSQL)
- Indexing for optimized queries

## SQL → MongoDB Migration

- To migrate existing relational data to MongoDB:
- Go to Admin Sidebar
- Click 🔁 Migrate SQL → MongoDB
- All SQL data will be converted to denormalized NoSQL format

## 🤝 Contributors

    Mustafa Fahimy
    Khalifa Nikzad
