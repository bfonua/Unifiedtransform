# TCTIMS - Tupou College Toloa Information Management System

A comprehensive school management and finance record system for Tupou College Toloa, built on [Unifiedtransform](https://github.com/changeweb/Unifiedtransform).

## Features

- **User Roles**: Admin, Teacher, Student, Accountant
- **Student Management**: registration
- **Academic Management**: forms, courses
- **Financial Management**: Fee assignment, payment recording
- **Data Import/Export**: Excel support for bulk operations

## Technology Stack

- Laravel 5.5
- Bootstrap 3.3.7
- MySQL

## Requirements

- PHP >= 7.1.0
- MySQL/MariaDB
- OpenSSL, PDO, Mbstring, Tokenizer, XML PHP Extensions

## Installation

```sh
# Clone repository
git clone https://github.com/changeweb/Unifiedtransform
cd Unifiedtransform

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database (edit .env with your credentials)
php artisan migrate

# Serve application
php artisan serve
```

## Configuration

### Database (.env)

```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tctims
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Email (.env) - to be implemented

```sh
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

## License

GNU General Public License v3.0

---

_Built with [Unifiedtransform](https://github.com/changeweb/Unifiedtransform)_
