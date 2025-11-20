# 🛠️ Development Guide - Unuha Showcase

## Getting Started

### Prerequisites
- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- Composer 2.x

### Installation

1. **Clone/Setup Project**
```bash
cd D:\laragon\www\proyek_ignitepad\unuha_showcase
```

2. **Install Dependencies**
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

3. **Environment Setup**
```bash
# Copy .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
DB_DATABASE=unuha_showcase
DB_USERNAME=root
DB_PASSWORD=
```

4. **Database Setup**
```bash
# Run migrations
php artisan migrate

# Seed test data
php artisan db:seed
```

5. **Build Assets**
```bash
# Development build with watch
npm run dev

# Production build (one-time)
npm run build
```

---

## Running the Application

### Development Server (All in one)
```bash
npm run dev
```

### Manual Start (Separate terminals)

**Terminal 1 - PHP Server:**
```bash
php artisan serve
```

**Terminal 2 - Frontend Build:**
```bash
npm run dev
```

---

## Database Commands

### Fresh Database Setup
```bash
php artisan migrate:fresh --seed
```

---

## Test Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@unuha.ac.id | password |
| Dosen | budi@unuha.ac.id | password |
| Mahasiswa | ahmad@student.unuha.ac.id | password |

---

**Ready to develop!** ✅
