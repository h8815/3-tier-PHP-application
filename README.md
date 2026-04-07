# 3-Tier DevSecOps Pipeline

Production-style 3-tier PHP application with a DevSecOps-oriented delivery workflow. The stack is containerized with Docker Compose, fronted by Nginx, analyzed by SonarQube in CI, and deployed through Jenkins.

## Project Intent

This repository demonstrates how to run and operate a simple business app with platform engineering concerns in mind:

- Reproducible local and server environments with containers
- Isolated service boundaries (frontend, backend API, database, gateway)
- CI automation with quality scanning and image publishing
- Secure-by-default runtime controls (headers, network isolation, secrets via credentials)

## Architecture

### Service Topology

- `nginx` (`3tier-nginx`): Public entrypoint on port `3000`, reverse-proxy to internal services, sets HTTP security headers
- `frontend` (`3tier-frontend`): PHP/Apache presentation layer, internal-only
- `backend` (`3tier-backend`): PHP/Apache API layer, internal-only
- `database` (`3tier-database`): MySQL 8 with healthcheck and persistent volume

Only Nginx is exposed externally. Frontend, backend, and database communicate through the internal Docker bridge network `3tier-network`.

### Data and Session Persistence

- `db-data`: MySQL persistent storage
- `backend-sessions`: PHP session persistence for backend auth sessions
- `backend-uploads`: Uploaded student profile photos

## Repository Layout

```text
.
|- docker-compose.yml
|- Jenkinsfile
|- nginx/default.conf
|- init/init.sql
|- backend/
|  |- Dockerfile
|  `- src/
|     |- api/
|     |- create_admin.php
|     |- db.php
|     `- cors-helper.php
|- frontend/
|  |- Dockerfile
|  `- src/
`- tools/
     `- docker-compose-tools.yml
```

## DevSecOps Controls Implemented

### Runtime Security

- Security headers at gateway: `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`
- Sensitive extension blocking in Nginx (`.env`, `.sql`, `.log`, `.bak`, etc.)
- API CORS handling in backend with configurable allowed origins
- Session hardening flags in auth endpoint (`httponly`, strict mode, SameSite)
- Password hashing with `PASSWORD_ARGON2ID`
- Prepared statements in auth/data endpoints to reduce SQL injection risk

### CI Security/Quality Gate Foundations

- Jenkins pipeline runs SonarQube analysis on `frontend/src` and `backend/src`
- Docker images are built in CI and pushed to Docker Hub via credential binding
- Deployment uses Jenkins file credential to inject runtime `.env` securely

## Prerequisites

- Docker Engine with Docker Compose plugin
- Git
- (For CI/CD) Jenkins with:
    - Docker CLI available to agent
    - SonarScanner tool configured as `SonarScanner`
    - SonarQube server configured as `SonarServer`
    - Credentials IDs:
        - `dockerhub-credentials-h8815` (username/password)
        - `3TIER-PHP` (secret file used as `.env`)

## Local Environment Setup

1. Create an `.env` in repository root:

```env
DB_USER=student_user
DB_PASSWORD=change_me
DB_NAME=student_management
DB_ROOT_PASSWORD=change_root_me
API_BASE_URL=http://localhost:3000
ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
```

2. Start the stack:

```bash
docker compose up -d --build
docker compose ps
```

3. Bootstrap admin account:

```bash
docker exec -it 3tier-backend php create_admin.php
```

4. Access app:

- URL: `http://localhost:3000`

## Operations Runbook

### Health and Status

```bash
docker compose ps
docker compose logs --tail=100 nginx backend frontend database
```

### Restart Services

```bash
docker compose restart
```

### Rebuild and Recreate

```bash
docker compose down
docker compose up -d --build --force-recreate
```

### Database Reset (Destructive)

```bash
docker compose down -v
docker compose up -d
```

## CI/CD Pipeline (Jenkinsfile)

The pipeline currently performs:

1. Workspace cleanup
2. SCM checkout
3. SonarQube analysis
4. Docker image build and push (`h8815/student-app-frontend:latest`, `h8815/student-app-backend:latest`)
5. Deployment with `docker compose up -d --force-recreate`
6. Basic post-deploy validation (`curl` against API)

## DevSecOps Hardening Backlog (Recommended)

To evolve this into a stronger production baseline, add:

1. SAST dependency scanning (`Trivy`, `Grype`, `OWASP Dependency-Check`) in CI
2. Container image signing and provenance (`cosign`, attestations)
3. Non-root containers and read-only filesystems where feasible
4. Secrets externalization (Vault, cloud secret manager) instead of static env files
5. TLS termination and secure cookies enabled (`session.cookie_secure=1`) in HTTPS environments
6. Automated integration tests and security regression tests before deploy
7. Release tagging strategy (avoid only `latest` tags)

## Tooling: SonarQube (Local)

Start SonarQube from the tools compose:

```bash
docker compose -f tools/docker-compose-tools.yml up -d
```

- SonarQube UI: `http://localhost:9000`

## API Endpoints

### Auth

- `POST /api/auth.php` (login/register action in payload)
- `GET /api/auth.php` (session check)
- `DELETE /api/auth.php` (logout)

### Students

- `GET /api/students.php`
- `GET /api/students.php?id=<id>`
- `POST /api/students.php`
- `PUT /api/students.php`
- `DELETE /api/students.php`

### File Handling

- `POST /api/upload-photo.php`
- `POST /api/remove-photo.php`

## Troubleshooting

- If containers are healthy but app is unavailable, check gateway routing first:
    - `docker compose logs nginx`
- If auth does not persist, verify `backend-sessions` volume is mounted and writable.
- If API CORS errors occur, confirm `ALLOWED_ORIGINS` in `.env` and restart backend.
- If DB init fails, inspect MySQL logs and ensure `init/init.sql` was mounted.

## License

Use your organization's standard license policy for this repository.