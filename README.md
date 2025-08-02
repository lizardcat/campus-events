# USIU Campus Events Management System

A PHP-MySQL web application for managing university events, user registrations, bookmarks, and comment submissions. Designed for educational demonstration with clear separation of concerns and essential security practices.

## Screenshots

### Front Page

![](/images/screenshots/screenshot_1.png)

### Event Submission Modal (Admin)

![](/images/screenshots/screenshot_2.png)

### Events Page

![](/images/screenshots/screenshot_3.png)

### Event Details Modal

![](/images/screenshots/screenshot_4.png)

### User Dashboard

![](/images/screenshots/screenshot_5.png)

## Features

- **User Registration and Login**

  - Password hashing for secure authentication
  - Role-Based Access Control (RBAC) for Admin and Regular Users

- **Event Management**

  - Admins can create, edit, and delete events
  - Event images with fallback support

- **Bookmark System**

  - Users can bookmark or unbookmark events from both the main list and modal view
  - Bookmarks are synchronized across dashboard and event listings

- **Commenting System**

  - Authenticated users can add, edit, and delete their own comments
  - Inline comment editing with preserved formatting
  - Real-time comment updates without page reloads

- **Responsive Layout and Styling**

  - Bootstrap-based UI with clean, mobile-friendly design

- **Secure Database Operations**

  - All database interactions use prepared statements
  - Event and comment ownership verified server-side

- **Session-based Authentication**

  - User actions restricted based on login state and role
  - Unauthorized actions return JSON error responses for AJAX requests

## RBAC Implementation

- **Admin** role:

  - Create, edit, and delete any event
  - View all events

- **User** role:

  - View events
  - Bookmark/unbookmark events
  - Add, edit, and delete their own comments

- All role checks are enforced server-side to prevent privilege escalation.

## Setup Instructions

1. **Clone the Repository**

   ```bash
   git clone https://github.com/lizardcat/campus-events.git
   cd campus-events
   ```

2. **Database Setup**

   - Create a new MySQL database:

     ```sql
     CREATE DATABASE usiu_events;
     ```

   - Import the provided schema (update with your SQL file path):

     ```bash
     mysql -u root -p usiu_events < database/schema.sql
     ```

3. **Configure Database Connection**

   - Open `includes/db.php` in your editor
   - Locate the `$password` variable:

     ```php
     $password = 'your_mysql_password';
     ```

   - Replace `'your_mysql_password'` with your actual MySQL password or leave empty `''` if you don't have a password set
   - Save the file

4. **Run the Application**

   - Place the project folder in your web server's document root (e.g., `htdocs` for XAMPP)
   - Start Apache and MySQL
   - Visit:

     ```
     http://localhost/campus-events
     ```

5. **Default Admin User**

   - Create an admin account directly in the database or via the registration form and manually set the role in the `users` table:

     ```sql
     UPDATE users SET role = 'admin' WHERE username = 'your_admin_username';
     ```
