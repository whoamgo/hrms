# HRMS Setup Guide

## Quick Setup Instructions

### 1. Database Configuration

Update your `.env` file with your MySQL database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will:
- Create all necessary database tables
- Create default roles (admin, hr, accounts, employee)
- Create default permissions
- Create menu items for each role
- Create default users for each role

### 3. Create Storage Link

```bash
php artisan storage:link
```

### 4. Start the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Default Login Credentials

After running seeders, use these credentials to login:

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password |
| HR Admin | hr | password |
| Accounts Officer | accounts | password |
| Employee | employee | password |

## Important Notes

1. **Migration Order**: The migrations are ordered correctly - roles table is created before users table.

2. **Assets**: All CSS, JS, and image assets from the HTML templates have been copied to `public/assets/`.

3. **Menu System**: Menu items are dynamically loaded from the database based on user roles. Menu items are cached per user for performance.

4. **AJAX Forms**: All forms use the custom `ajax-form.js` plugin. Add `data-ajax-form` attribute to any form to enable AJAX submission.

5. **Notifications**: The notification system is set up and ready to use. Use `NotificationHelper::notify()` to send notifications.

6. **Cache**: Menu items and dashboard statistics are cached. Cache is automatically cleared when roles, permissions, or menu items are updated.

## Troubleshooting

### Migration Errors

If you encounter foreign key constraint errors:
1. Make sure the roles table is created first
2. Check that all migrations are in the correct order
3. Try running: `php artisan migrate:fresh --seed`

### Permission Denied Errors

If you see permission errors:
1. Make sure storage and cache directories are writable:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

### Menu Not Showing

If menu items are not showing:
1. Clear cache: `php artisan cache:clear`
2. Check that the user has a role assigned
3. Verify menu items are assigned to the role in the database

## Next Steps

1. **Customize Menu Items**: Add/edit menu items in the database or through the admin panel
2. **Add Permissions**: Create new permissions as needed for your modules
3. **Create Controllers**: Add controllers for your specific modules (Employee Management, Leave Management, etc.)
4. **Convert HTML Pages**: Convert remaining HTML pages to Blade templates following the existing structure

## Project Structure

- **Controllers**: `app/Http/Controllers/`
- **Models**: `app/Models/`
- **Views**: `resources/views/`
- **Routes**: `routes/web.php`
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`
- **Assets**: `public/assets/`

## Support

For issues or questions, refer to the main README.md file.

