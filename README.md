# Billiard Management System

A simple web-based billiard management system that allows admins and cashiers to manage tables, bookings, and tournaments. The system also integrates with payment gateways such as Paymongo, Gcash, and PayPal sandbox.

## Features

### Admin Role
- Create and manage billiard tables.
- Manage booking system for users.
- Manage tournament brackets.
- Access to payment integrations for Paymongo, Gcash, and PayPal (sandbox).

### Cashier Role
- View and manage booking transactions.

## Setup Instructions

### Prerequisites
- XAMPP (with PHP and MySQL) installed on your machine.
- phpMyAdmin to manage your MySQL database.
- Paymongo, Gcash, and PayPal sandbox accounts for payment integrations.

### Installation

#### 1. Clone the Repository:

```bash
git clone https://github.com/your-username/billiard-management.git
cd billiard-management
```

#### 2. Set up the Database:
- Navigate to the `database` folder in your project.
- Import the SQL file into phpMyAdmin:
  1. Go to `phpMyAdmin` -> `Databases` -> `Import`.
  2. Select the SQL file from the `database` folder.

#### 3. Configure Database Connection:
- Open the `conn.php` file located in the `includes` directory.
- Update the following database configuration to match your local phpMyAdmin credentials:

```php
$servername = "localhost";
$username = "root"; // default username
$password = ""; // default password is empty
$dbname = "billiard_management"; // or your database name
```

### 4. Start the PHP Server
- In XAMPP, start the **Apache** server to run your PHP files.
- Access the application in your browser: `http://localhost/billiard-management/`

## Payment Gateway Setup

### Paymongo
1. Sign up for a [Paymongo account](https://paymongo.com/).
2. Obtain your API keys from the Paymongo dashboard.
3. Integrate the API keys into the payment handling sections of your application.

### Gcash
1. Sign up for a [Gcash merchant account](https://www.gcash.com/business).
2. Obtain your API keys from the Gcash merchant dashboard.
3. Integrate the API keys into the payment processing area of your application.

### PayPal Sandbox
1. Create a [PayPal developer account](https://developer.paypal.com/).
2. Obtain your sandbox API credentials.
3. Add the credentials to your payment processing configuration.

## Roles and Permissions

### Admin
- Admins have full control over the system, including the ability to:
  - Create tables
  - Manage bookings
  - Handle tournament brackets

### Cashier
- Cashiers can:
  - View and manage booking transactions

## Screenshots

![report_analytics](https://github.com/user-attachments/assets/8a02dba0-2754-4c4e-95c3-1795318bc0b3)
![manage_tournament](https://github.com/user-attachments/assets/216e02fe-3695-472d-a9a4-807d67a1c51f)
![manage_booking](https://github.com/user-attachments/assets/f1ad8fb9-2abc-4e79-bd8a-ac0a93267702)
![login](https://github.com/user-attachments/assets/6efde29e-d744-4c24-bfb1-691dbe1e42f2)
![chatwithuser](https://github.com/user-attachments/assets/fcac918e-d85a-4801-a139-b7dce82ab46c)
![cashier_reports](https://github.com/user-attachments/assets/d64e411a-81a7-4173-b103-f4b3e5ba773b)
![booking](https://github.com/user-attachments/assets/560d7d50-3e23-43fc-9ae8-9b1cddb2bb5c)
![booking calendar](https://github.com/user-attachments/assets/75246c04-8908-4943-abbf-c5a81293f933)
![admin_dashboard](https://github.com/user-attachments/assets/79d13055-b42c-4339-b17f-a61c21e4314e)
![startingpage](https://github.com/user-attachments/assets/7f0fd0e6-06cc-4e37-bf72-d9bd4b5ed7a8)

