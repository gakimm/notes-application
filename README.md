# Notes Application by Muhammad Lukmanulhakim
A modern, feature-rich note-taking application built with Laravel 12 and Alpine.js. This application allows users to create, edit, and share notes with different visibility levels and collaborative features.

## Features
Core Features

- User Authentication - Secure login/register system with Laravel Breeze
- Create & Edit Notes - Create & edit notes easily
- Note Visibility Control - Private, Shared, or Public notes
- User Sharing System - Search and share notes with specific users
- Responsive Design - Mobile-friendly interface with Tailwind CSS
- Search Functionality - Find users to share notes with

## Technical Features

- Secure Authentication - Laravel Breeze implementation
- Modern UI/UX - Clean design with Tailwind CSS
- Interactive Frontend - Alpine.js for dynamic user interactions
- AJAX Search - Real-time user search without page refresh
- Mobile Responsive - Works seamlessly on all devices
- Input Validation - Server-side validation for data integrity

## Tech Stack

- Backend Framework: Laravel 12
- Authentication: Laravel Breeze
- Frontend Framework: Alpine.js (included with Breeze)
- CSS Framework: Tailwind CSS
- Database: PostgreSQL (configurable)
- Package Manager: Composer & NPM

## Requirements

PHP 8.2 or higher
Composer
Node.js & NPM
PostgreSQL 13+
Git

⚙️ Installation
1. Clone the Repository
bashgit clone https://github.com/gakimm/notes-application.git
cd notes-application
2. Install PHP Dependencies
bashcomposer install
3. Install JavaScript Dependencies
bashnpm install
4. Environment Setup
bash## Copy environment file
cp .env.example .env

## Generate application key
php artisan key:generate
5. Database Configuration
Edit your .env file with your database credentials:
envDB_CONNECTION=pgsql
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
6. Run Database Migrations
bashphp artisan migrate
7. Build Assets
bash## For development
npm run dev

## For production
npm run build
8. Start the Application
bash## Start the Laravel development server
php artisan serve

## In another terminal, run Vite for asset compilation (development)
npm run dev
Visit http://localhost:8000 to see your application.

## Key Features Explained
Note Visibility System

Private: Only the creator can view the note
Shared: Specific users can be granted access
Public: Anyone can view the note

## User Sharing Workflow

Select "Shared" visibility when creating/editing a note
Search for users by name or email
Add users to the sharing list
Users receive access to view and collaborate

## Real-time Features

Live Search: User search with debounced input
Dynamic UI: Show/hide sharing options based on visibility

## Security Features

Authentication Required: All note operations require login
Authorization Policies: Users can only edit their own notes
Input Validation: Server-side validation for all forms
CSRF Protection: Laravel's built-in CSRF protection
XSS Prevention: Proper output escaping

## API Endpoints
User Search API
GET /api/users/search?q={query}
Returns JSON array of users matching the search query.

## UI Components
Alpine.js Components

Note Editor: Text editing 
User Search: Dynamic user search and selection
Visibility Option: Private/Public/Share Specific sharing options

## Tailwind CSS Classes

Custom utility classes for consistent styling
Responsive design breakpoints
Interactive hover and focus states

## Deployment
Production Setup

Set APP_ENV=production in .env
Configure your production database
Run composer install --optimize-autoloader --no-dev
Run npm run build
Set up proper web server configuration

Recommended Server Configuration

Web Server: Nginx or Apache
PHP Version: 8.2+
Memory Limit: 256MB minimum
Upload Limits: Configure based on note size needs

## Contributing

Fork the repository
Create a feature branch (git checkout -b feature/amazing-feature)
Commit your changes (git commit -m 'Add some amazing feature')
Push to the branch (git push origin feature/amazing-feature)
Open a Pull Request

## Development Notes
Code Style

Follow PSR-12 coding standards
Use Laravel best practices
Keep Alpine.js components simple and focused
Maintain consistent Tailwind CSS patterns

## Testing
bash## Run PHP tests
php artisan test

## Run with coverage
php artisan test --coverage
## Troubleshooting
Common Issues
NPM Build Errors:
bashrm -rf node_modules package-lock.json
npm install
npm run dev
Database Connection Issues:

Check your .env database credentials
Ensure your database server is running
Verify database exists

Permission Errors:
bashchmod -R 775 storage
chmod -R 775 bootstrap/cache

Acknowledgments

Laravel Team for the amazing framework
Alpine.js Team for the lightweight frontend framework
Tailwind CSS for the utility-first CSS framework
Raja Gadai Recruitment Team for the opportunity to build this application

📞 Contact
For any questions or issues, please contact:

Developer: [Muhammad Lukmanulhakim]
Email: [ml.hakimm@gmail.com]
Website: [https://kimmycode.online]


Thank you to the Raja Gadai Recruitment Team for giving me the opportunity to take this test. I am very enthusiastic and excited about the possibility of joining the Raja Gadai team. Please enjoy reviewing this application, hope you like it! 🙏