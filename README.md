# HRMS (Human Resource Management System)

A comprehensive HRMS built with Laravel 11, featuring role-based access control, dynamic menu management, and AJAX form handling.

## Features

- **Role-Based Access Control (RBAC)**: Admin, HR, Accounts, and Employee roles with granular permissions
- **Dynamic Sidebar Menu**: Menu items managed through database with role-based visibility
- **AJAX Form Submissions**: All forms use jQuery AJAX with client-side and server-side validation
- **Laravel Notifications**: Integrated notification system
- **Caching**: Menu items and dashboard statistics are cached for performance
- **Try-Catch Error Handling**: Comprehensive error handling throughout the application

## Requirements

- PHP >= 8.2
- MySQL >= 5.7
- Composer
- Node.js & NPM (for assets)

## Installation

1. **Clone the repository** (if applicable) or navigate to the project directory:
   ```bash
   cd laravel-hrms
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Copy environment file**:
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Configure database** in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hrms
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

7. **Create storage link**:
   ```bash
   php artisan storage:link
   ```

8. **Start the development server**:
   ```bash
   php artisan serve
   ```

## Default Login Credentials

After running seeders, you can login with:

- **Admin**: 
  - Username: `admin`
  - Password: `password`

- **HR Admin**: 
  - Username: `hr`
  - Password: `password`

- **Accounts Officer**: 
  - Username: `accounts`
  - Password: `password`

- **Employee**: 
  - Username: `employee`
  - Password: `password`

## Project Structure

```
laravel-hrms/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   └── Auth/           # Authentication controllers
│   │   └── Middleware/         # Custom middleware
│   └── Models/                 # Eloquent models
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── public/
│   ├── assets/                 # CSS, JS, images (from HTML templates)
│   └── js/
│       └── ajax-form.js        # AJAX form handler
├── resources/
│   └── views/
│       ├── layouts/            # Blade layouts
│       ├── components/         # Blade components
│       ├── auth/               # Authentication views
│       ├── admin/              # Admin views
│       ├── hr/                 # HR views
│       ├── accounts/           # Accounts views
│       └── employee/           # Employee views
└── routes/
    └── web.php                 # Web routes
```

## Key Features Implementation

### Role-Based Access Control

Roles are defined in the database and can be managed through the admin panel. Each role can have:
- Multiple permissions
- Multiple menu items
- Multiple users

### Dynamic Menu System

Menu items are stored in the database and are filtered based on:
- User's role
- Menu item type (admin, hr, accounts, employee, all)
- Active status
- Role-menu item relationships

Menu items are cached per user for performance.

### AJAX Form Handling

All forms use the custom `ajax-form.js` plugin which provides:
- Automatic form submission via AJAX
- Client-side and server-side validation
- Success/error message display
- Form reset on success
- Redirect on success
- Loading indicators

Usage:
```html
<form data-ajax-form action="/route" method="POST">
    <!-- form fields -->
</form>
```

### Caching

The application uses Laravel's cache system for:
- User menu items (cached per user)
- Dashboard statistics
- Role permissions

Cache is automatically cleared when:
- Roles are updated
- Permissions are updated
- Menu items are updated

## Database Structure

### Main Tables

- `users`: User accounts
- `roles`: User roles (admin, hr, accounts, employee)
- `permissions`: System permissions
- `menu_items`: Sidebar menu items
- `role_permission`: Pivot table for role-permission relationships
- `role_menu_item`: Pivot table for role-menu item relationships

## Development

### Creating New Controllers

```bash
php artisan make:controller Admin/YourController
```

### Creating New Migrations

```bash
php artisan make:migration create_your_table
```

### Creating New Seeders

```bash
php artisan make:seeder YourSeeder
```

## Security

- All routes are protected with authentication middleware
- Role-based access control on all admin routes
- CSRF protection on all forms
- Password hashing using bcrypt
- Input validation on all forms

## License

This project is proprietary software.

## Support

For support, please contact the development team.
# hrms
