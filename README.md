<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Forum Application
This project is a Single Page Application (SPA), a full-fledged forum built with **MySQL**, **Laravel 11**, **Inertia SSR**, and **Vue 3**. It uses the **Breeze** starter kit with **Tailwind CSS** for a responsive, modern design. The application comes pre-seeded with categories, subcategories, threads, and posts, providing a fully functional community forum. It features a robust admin panel, extensive user management, and supports real-time messaging, user permissions, content creation, and more.

## Features

### **User Management & Authentication**
- **Authentication with Breeze**: Simple and secure user authentication using Laravel Breeze.
- **User Profiles**: Customizable user profiles, allowing users to update their details, display names, and more.
- **Avatars**: Users can upload custom avatars that appear alongside their posts and profile.
- **User Roles**: 
  - **Super Admin**: Full access to all functionalities, including managing users, content, and settings.
  - **Admin**: Manage content and users (except Super Admin settings).
  - **Moderator**: Moderate threads, posts, and reports; ban users if necessary.
  - **User**: Regular users who can create threads, post content, and interact.
  - **New User**: Automatically assigned to new users with limited access until they are verified.
  
### **Permissions & Admin Panel**
- **Permissions**: Role-based permissions to determine what each user can or cannot do within the application.
- **Admin Panel**: A powerful backend interface for admins to manage users, threads, posts, and reports.
- **Reports**: Users can report inappropriate content, and moderators can take action based on reports.
- **Ban**: Admins and moderators can ban users from posting, creating threads, or accessing the forum entirely.

### **Real-Time Features**
- **Private Messaging with Broadcasting**: Real-time private messaging, allowing users to send and receive messages instantly.
- **Notifications**: Users receive notifications for private messages.
- **Followed Content**: Users can follow threads, receiving notifications for new content and updates.
  
### **Content Management & Search**
- **User Content**: Users can search their threads, and replies.
- **Post with Images & YouTube**: Posts can contain images and embedded YouTube videos, enriching the content shared on the forum.
- **Search in Posts**: Powerful search functionality allows users to search for specific content within threads and posts across the forum.

### **Additional Features**
- **Captcha Protection**: CAPTCHA is enabled to prevent spammy activity on forms like registration, post creation, and comments.
- **SEO Friendly**: Properly structured URLs, meta tags, and clean markup for better search engine indexing.
- **Responsive Design**: Fully responsive UI.

---

## Tools Used
This forum application leverages several powerful tools and libraries to enhance its functionality and performance:

- **[Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)**: Used for managing roles and permissions, allowing you to easily assign and control access for users.
- **[Filament](https://filamentphp.com/)**: A beautiful, powerful admin panel built for Laravel, making it simple to manage content and users.
- **[Quill](https://quilljs.com/)**: A rich-text editor for posts, allowing users to create formatted content with ease.
- **[Pusher](https://pusher.com/)**: Used for real-time broadcasting of events, enabling live notifications, private messaging, and other real-time features.
- **[HTMLPurifier](https://github.com/xemlock/htmlpurifier-html5)**: Ensures the sanitization of user-generated content, preventing XSS and other malicious attacks.
- **[Intervention Image](https://image.intervention.io/)**: Provides easy image handling for resizing, cropping, and editing images uploaded by users.

These tools work together seamlessly to provide a robust, feature-rich, and secure forum platform.

## Installation
1. Clone the repository: Clone the repository to your local machine using Git.
    ```
    git clone https://github.com/FlorinM/forum-project
    ```
    
2. Navigate to the project directory: After cloning the repository, navigate to the project folder.
    ```
    cd forum-project
    ```
    
3. Install dependencies: Make sure you have Composer and Node.js installed. Run the following commands to install the PHP and JavaScript dependencies:
    ```
    composer install
    npm install
    ```
    
4. Create the .env file: Copy the .env.example file to a new .env file for configuration.
    ```
    cp .env.example .env
    ```
    
5. Generate the application key: Laravel requires an application key, which you can generate by running:
    ```
    php artisan key:generate
    ```
    
6. Create a new MySQL database for the project.

7. Make a Pusher account. This application requires Pusher for broadcasting.

8. Update the .env file with your database credentials and pusher credentials.
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your-database-name
    DB_USERNAME=your-username
    DB_PASSWORD=your-password
    ...
    BROADCAST_CONNECTION=pusher
    ...
    PUSHER_APP_ID=your-app-id
    PUSHER_APP_KEY=your-app-key
    PUSHER_APP_SECRET=your-app-secret
    PUSHER_HOST=your-pusher-host
    PUSHER_PORT=443
    PUSHER_SCHEME="https"
    PUSHER_APP_CLUSTER=your-app-cluster
    ...
    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    VITE_PUSHER_HOST="${PUSHER_HOST}"
    VITE_PUSHER_PORT="${PUSHER_PORT}"
    VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    ```
    
9. Run migrations and seed the database: This command will set up the database structure and seed it with the required data:
    ```
    php artisan migrate --seed
    ```
    
10. Run the development server: To start the development server, use the following commands:
    ```
    php artisan serve
    npm run dev
    php artisan queue:work
    ```
Use different terminals if needed. Your application will now be accessible at http://127.0.0.1:8000
    
Now, your local environment should be ready to use. Enjoy working with the X forum application!

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

Copyright © [2025] [Manolache Florin]. All rights reserved.
