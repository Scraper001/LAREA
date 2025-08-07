# LAREA Project

## Overview
The LAREA project is a web application designed for managing student information and behavior records. It provides a user-friendly interface for administrators to add, edit, and delete student data, as well as track student behavior.

## File Structure
The project consists of the following key files and directories:

```
LAREA
├── users
│   ├── student_management.php       # Main page for managing student information
│   ├── student_behavior.php          # Page for managing student behavior records
│   └── functions
│       ├── select_users.php          # Script for selecting user data from the database
│       ├── add_users.php             # Script for adding new users to the database
│       ├── edit_users.php            # Script for editing existing user data
│       └── delete_users.php          # Script for deleting users from the database
├── includes
│   ├── header.php                    # Header structure for the application
│   ├── navbar.php                    # Primary navigation bar
│   ├── navbar2.php                   # Secondary navigation bar
│   └── footer.php                    # Footer structure for the application
└── README.md                         # Documentation for the project
```

## Features
- **Student Management**: Add, edit, and delete student information.
- **Student Behavior Tracking**: Manage and record student behavior incidents.
- **User-Friendly Interface**: Intuitive design with modals for data entry and editing.
- **Search and Filter**: Easily search for students and filter records based on criteria.

## Setup Instructions
1. **Clone the Repository**: Clone this repository to your local machine.
2. **Install Dependencies**: Ensure you have a local server environment set up (e.g., XAMPP, WAMP).
3. **Database Configuration**: Set up the database and configure the connection in the relevant PHP files.
4. **Access the Application**: Open your web browser and navigate to `http://localhost/LAREA/users/student_management.php` to access the application.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.