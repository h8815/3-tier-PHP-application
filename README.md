# 🎓 Student Management System (3-Tier Architecture)

A modern, containerized PHP application built with true 3-tier microservices architecture. This project demonstrates DevOps best practices including Docker containerization, frontend/backend separation, secure authentication, and a complete CI/CD pipeline using Jenkins and AWS.

![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue)
![Architecture](https://img.shields.io/badge/Architecture-3--Tier-green)
![CICD](https://img.shields.io/badge/CI%2FCD-Jenkins-red)
![Security](https://img.shields.io/badge/Security-Enhanced-purple)

## 🏗️ Architecture Overview

This application is decoupled into three isolated layers running in separate Docker containers:

### 1. **Presentation Tier (Frontend)**
- **Container**: `3tier-frontend`
- **Technology**: Apache/PHP 8.2
- **Features**: Responsive HTML/CSS/JS interface with Bootstrap 5
- **Capabilities**: Dark/Light mode, mobile-responsive, toast notifications
- **Communication**: REST API calls to backend

### 2. **Logic Tier (Backend API)**
- **Container**: `3tier-backend` 
- **Technology**: Apache/PHP 8.2
- **Features**: RESTful JSON endpoints (GET, POST, PUT, DELETE)
- **Security**: Session-based authentication, CORS protection, input validation
- **File Handling**: Image upload/management with validation

### 3. **Data Tier (Database)**
- **Container**: `3tier-database`
- **Technology**: MySQL 8.0
- **Features**: Persistent storage with Docker volumes
- **Security**: Environment-based credentials, health checks

### 4. **Gateway Layer (Nginx)**
- **Container**: `3tier-nginx`
- **Technology**: Nginx reverse proxy
- **Purpose**: Single entry point, load balancing, security headers
- **Port**: 3000 (only exposed port)

## 🚀 Features

### Core Functionality
- ✅ **Full CRUD Operations**: Create, Read, Update, Delete student records
- 👤 **User Management**: Secure admin authentication with strong password requirements
- 📸 **Photo Management**: Upload, edit, and remove student profile photos
- 🔍 **Advanced Search**: Filter by name, email, status with pagination
- 📊 **Real-time Statistics**: Live student count and status monitoring

### User Experience
- 🎨 **Modern UI**: Neo-brutalist design with Bootstrap 5
- 🌓 **Dark/Light Mode**: Toggle between themes with persistence
- 📱 **Mobile Responsive**: Optimized for all device sizes
- 🔔 **Toast Notifications**: Real-time feedback with dismissible alerts
- ⚡ **Bulk Actions**: Select and manage multiple students
- 🔄 **Loading States**: Smooth animations and progress indicators

### Technical Features
- 🐳 **Fully Dockerized**: Complete containerization with docker-compose
- 🔒 **Security Enhanced**: Strong password policies, CORS protection, input validation
- 🔄 **CI/CD Ready**: Jenkins pipeline configuration included
- 📈 **Development Tools**: SonarQube integration for code quality
- 🌐 **API-First Design**: Separate backend that can serve multiple frontends

## 📂 Project Structure

```
student-management-system/
├── docker-compose.yml          # Main orchestration file
├── .env                        # Environment configuration
├── .gitignore                  # Comprehensive ignore patterns
├── Jenkinsfile                 # CI/CD pipeline definition
├── 
├── frontend/                   # Presentation Layer
│   ├── Dockerfile             # Frontend container config
│   └── src/                   # Frontend source code
│       ├── login.php          # Authentication interface
│       ├── view.php           # Main dashboard with advanced features
│       ├── add-student.php    # Student creation form
│       ├── edit.php           # Student editing with photo management
│       └── images/            # Static assets
│
├── backend/                    # Logic Layer
│   ├── Dockerfile             # Backend container config
│   └── src/                   # Backend source code
│       ├── api/               # RESTful API endpoints
│       │   ├── students.php   # Student CRUD operations
│       │   ├── auth.php       # Authentication API
│       │   ├── upload-photo.php # Photo upload handling
│       │   └── remove-photo.php # Photo removal
│       ├── create_admin.php   # CLI admin creation tool
│       ├── db.php             # Database connection
│       └── cors-helper.php    # CORS management
│
├── nginx/                      # Gateway Layer
│   └── default.conf           # Nginx configuration
│
├── init/                       # Database Initialization
│   └── init.sql               # Database schema
│
└── tools/                      # Development Tools
    ├── docker-compose-tools.yml # SonarQube & Jenkins setup
    └── .env.tools              # Tools configuration
```

## 🛠️ Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### 1. Clone & Setup
```bash
git clone <repository-url>
cd student-management-system
cp .env.example .env
```

### 2. Configure Environment
Edit `.env` file with your settings:
```env
# Database Configuration
DB_USER=student_user
DB_PASSWORD=secure_password_123
DB_NAME=student_management
DB_ROOT_PASSWORD=root_password_123

# Application Configuration
API_BASE_URL=http://localhost:3000

# Security Configuration
ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
```

### 3. Launch Application
```bash
# Start all services
docker-compose up -d

# Check service status
docker-compose ps
```

### 4. Create Admin User
```bash
# Create admin account with strong password requirements
docker exec -it 3tier-backend php create_admin.php
```

**Password Requirements:**
- Minimum 8 characters
- At least 1 uppercase letter (A-Z)
- At least 1 lowercase letter (a-z)
- At least 1 number (0-9)
- At least 1 special character (!@#$%^&*()_+-=[]{}|;:,.<>?)

### 5. Access Application
- **Main Application**: http://localhost:3000
- **Login**: Use the admin credentials you created

## 🔧 Development Tools

### Start Development Tools
```bash
cd tools/
docker-compose -f docker-compose-tools.yml up -d
```

### Available Tools
- **SonarQube**: http://localhost:9000 (Code quality analysis)
- **Jenkins Agent**: Configured for CI/CD pipeline

## 🔒 Security Features

### Authentication & Authorization
- Session-based authentication
- Strong password enforcement
- Secure password hashing (ARGON2ID)
- Session timeout and management

### API Security
- CORS protection with configurable origins
- Input validation and sanitization
- SQL injection prevention (prepared statements)
- File upload validation and restrictions

### Infrastructure Security
- Container isolation
- No direct database access
- Nginx security headers
- Environment-based configuration

## 📱 User Interface Features

### Dashboard (view.php)
- **Student Grid**: Card-based layout with photos
- **Search & Filter**: Real-time search with status filtering
- **Bulk Operations**: Multi-select with bulk delete
- **Pagination**: Efficient data loading
- **Statistics**: Live counts by status
- **Responsive Design**: Mobile-optimized layout

### Student Management
- **Add Student**: Drag-drop photo upload, form validation
- **Edit Student**: In-place photo management, field validation
- **Photo Management**: Upload, preview, remove with size limits
- **Status Management**: Active, Inactive, Graduated states

### User Experience
- **Toast Notifications**: Success/error feedback with close buttons
- **Loading States**: Smooth transitions and progress indicators
- **Dark/Light Mode**: Theme persistence across sessions
- **Mobile Responsive**: Touch-friendly interface

## 🚀 CI/CD Pipeline

### Jenkins Configuration
The included `Jenkinsfile` provides:
- Automated testing
- Code quality analysis with SonarQube
- Docker image building
- Deployment automation

### Pipeline Stages
1. **Checkout**: Source code retrieval
2. **Test**: Automated testing suite
3. **Quality Gate**: SonarQube analysis
4. **Build**: Docker image creation
5. **Deploy**: Automated deployment

## 🔧 Configuration

### Environment Variables

#### Database Settings
```env
DB_HOST=database
DB_USER=student_user
DB_PASSWORD=your_secure_password
DB_NAME=student_management
DB_ROOT_PASSWORD=your_root_password
```

#### Application Settings
```env
API_BASE_URL=http://localhost:3000
ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
```

#### Tools Configuration (tools/.env)
```env
JENKINS_URL=http://your-jenkins-server:8080
JENKINS_AGENT_NAME=your-agent-name
JENKINS_SECRET=your-jenkins-secret
SONAR_PORT=9000
```

## 🐛 Troubleshooting

### Common Issues

#### Application won't start
```bash
# Check service logs
docker-compose logs

# Restart services
docker-compose restart
```

#### Database connection issues
```bash
# Check database health
docker-compose exec database mysqladmin ping

# Reset database
docker-compose down -v
docker-compose up -d
```

#### Permission issues
```bash
# Fix upload directory permissions
docker-compose exec backend chmod 755 /var/www/html/uploads
```

### Service Health Checks
```bash
# Check all services
docker-compose ps

# View specific service logs
docker-compose logs frontend
docker-compose logs backend
docker-compose logs database
docker-compose logs nginx
```

## 📊 API Documentation

### Authentication Endpoints
- `POST /api/auth.php` - Login
- `GET /api/auth.php` - Check session
- `DELETE /api/auth.php` - Logout

### Student Management Endpoints
- `GET /api/students.php` - List students (with pagination/filtering)
- `GET /api/students.php?id={id}` - Get specific student
- `POST /api/students.php` - Create student
- `PUT /api/students.php` - Update student
- `DELETE /api/students.php` - Delete student

### File Management Endpoints
- `POST /api/upload-photo.php` - Upload student photo
- `POST /api/remove-photo.php` - Remove student photo

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Bootstrap 5 for the responsive UI framework
- Docker for containerization
- MySQL for reliable data storage
- Nginx for efficient reverse proxy
- SonarQube for code quality analysis
- Jenkins for CI/CD automation

---

**Built with ❤️ using modern DevOps practices and 3-tier architecture principles.**