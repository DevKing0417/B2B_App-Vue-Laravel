# ACME Corp Employee Donation Platform

A modern full-stack application for managing employee donations and fundraising campaigns.

## Tech Stack

- **Frontend**: Vue 3 + Vite, TypeScript, Pinia (State Management), Vue Router
- **Backend**: Laravel 10, PHP 8.2+
- **Database**: PostgreSQL
- **Testing**: Pest, PHPStan (Level 8)
- **Authentication**: Laravel Sanctum
- **Payment Integration**: Stripe (with abstraction layer for future changes)

## Architecture Overview

### Backend Architecture
- RESTful API with Laravel
- Repository Pattern for data access
- Service Layer for business logic
- DTOs for data transfer
- Event-driven architecture for notifications
- Queue system for background jobs

### Frontend Architecture
- Component-based architecture
- Pinia for state management
- Vue Router for navigation
- Composition API with TypeScript
- Responsive design with Tailwind CSS
- Form validation with VeeValidate
- Internationalization support

### Security Features
- JWT-based authentication
- Role-based access control (RBAC)
- CSRF protection
- Input validation
- Rate limiting
- Secure payment processing

## Setup Instructions

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL
- Git

### Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd acme-donation-platform
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

5. Set up database:
```bash
php artisan migrate
php artisan db:seed
```

6. Start development servers:
```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend
npm run dev
```

## Features

- Employee authentication and authorization
- Campaign creation and management
- Donation processing
- Admin dashboard
- Real-time notifications
- Reporting and analytics
- Multi-language support
- Responsive design

## Testing

```bash
# Run PHP tests
php artisan test

# Run PHPStan
./vendor/bin/phpstan analyse

# Run frontend tests
npm run test
```

## Deployment

The application is containerized using Docker for easy deployment. See `docker-compose.yml` for details.

## Contributing

Please read CONTRIBUTING.md for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the LICENSE.md file for details.