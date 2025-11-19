# 🎓 Student Management System (3-Tier Architecture)

A modern, containerized PHP application re-architected into a true 3-tier microservices solution. This project demonstrates DevOps best practices including Docker containerization, frontend/backend separation, and a complete CI/CD pipeline using Jenkins and AWS.

![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue)
![Architecture](https://img.shields.io/badge/Architecture-3--Tier-green)
![CICD](https://img.shields.io/badge/CI%2FCD-Jenkins-red)

## 🏗️ Architecture

This application is decoupled into three isolated layers (Containers):

1.  **Presentation Tier (Frontend)**:
    * Running on: `Apache/PHP 8.2` (Port `80`)
    * Pure HTML/JS/CSS interface.
    * Communicates with the Backend via REST API.
2.  **Logic Tier (Backend API)**:
    * Running on: `Apache/PHP 8.2` (Port `8081`)
    * Handles all business logic and data validation.
    * Exposes JSON endpoints (`GET`, `POST`, `PUT`, `DELETE`).
3.  **Data Tier (Database)**:
    * Running on: `MySQL 8.0` (Port `3306`)
    * Persistent storage using Docker Volumes.

## 🚀 Features

- ✅ **Full CRUD**: Create, Read, Update, and Delete student records.
- 🎨 **Modern UI**: Responsive design using Bootstrap 5 with Dark/Light mode.
- 🔌 **REST API**: Separate backend that can be consumed by any frontend.
- 🐳 **Dockerized**: Fully automated setup with `docker-compose`.
- 🔄 **CI/CD Ready**: Configured for automated deployment via Jenkins.
- 📊 **Live Statistics**: Real-time student count and API status monitoring.

## 📂 Project Structure

```text
student-management-system/
├── docker-compose.yml      # Orchestrates Frontend, Backend, and DB services
├── Jenkinsfile             # CI/CD Pipeline script
├── .env                    # Environment variables (API URL, DB Creds)
├── frontend/
│   ├── Dockerfile          # Frontend container config
│   └── src/                # UI source code (index.php, view.php, etc.)
├── backend/
│   ├── Dockerfile          # Backend container config
│   └── src/                # API source code (api/students.php, db.php)
└── init/
    └── init.sql            # Database initialization script