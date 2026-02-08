# BookStore Token

![PHP](https://img.shields.io/badge/Language-PHP_8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Styling-Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![JavaScript](https://img.shields.io/badge/Scripting-JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Status](https://img.shields.io/badge/Status-Active-success?style=for-the-badge)

A premium, token-based digital bookstore platform engineered for seamless transactions and modern user experience. Built with native PHP and Tailwind CSS, featuring a robust admin dashboard and a secure wallet system.

---

## <img src="https://img.icons8.com/ios-glyphs/30/000000/rocket.png" width="20" height="20"/> Key Features

### **User Experience (Client-Side)**
*   **Authentication & Security**
    *   Secure Login & Registration flow.
    *   Session management & Role-based Access Control (RBAC).
    *   Profile management (Avatar, Password, Bio).

*   **Token Ecosystem**
    *   **Digital Wallet**: Integrated token system for all transactions.
    *   **Top Up Gateway**: Simulate payments via Virtual Accounts (BCA, Mandiri, BRI, BNI) & E-Wallets (Gopay, OVO, ShopeePay).
    *   **Transaction History**: Real-time log of all token purchases and usage.
    *   **Refund System**: Automated refund requests for failed/incorrect transactions.

*   **Catalog & Shopping**
    *   **Smart Catalog**: Search, filter, and sort functionality.
    *   **Wishlist**: Save favorite books for later.
    *   **Cart System**: Seamless checkout process with real-time stock validation.
    *   **Library**: Access purchased content instantly.

*   **Interface**
    *   **Responsive Design**: Mobile-first architecture using Tailwind CSS.
    *   **Glassmorphism UI**: Modern, clean aesthetic with blur effects and smooth animations.
    *   **Interactive Elements**: Hover states, modals, and dynamic content loading.

### **Administration (Admin-Side)**
*   **Dashboard Analytics**
    *   Real-time overview of Sales, Users, and Active Transactions.
    *   Visual charts and data summaries.

*   **Content Management**
    *   **Book Inventory**: CRUD operations for books (Cover upload, Stock, Price, synopsis).
    *   **Category Management**: Organize books into genres/categories.

*   **Finance Control**
    *   **Top Up Approval**: Review and approve/reject user token requests.
    *   **Refund Management**: Process refund claims with reason verification.
    *   **Sales Reports**: Detailed transaction logs.

*   **User Management**
    *   Monitor registered users.
    *   Manage user status and roles.

---

## <img src="https://img.icons8.com/ios-glyphs/30/000000/maintenance.png" width="20" height="20"/> Tech Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Backend** | PHP (Native) | Core application logic, routing, and session handling. |
| **Database** | MySQL | Relational database for Users, Books, Transactions, and Tokens. |
| **Frontend** | Tailwind CSS | Utility-first CSS framework for styling and responsiveness. |
| **Scripting** | Vanilla JS | Client-side interactivity, AJAX simulation, and DOM manipulation. |
| **Server** | Apache (XAMPP) | Local development server environment. |

---

## <img src="https://img.icons8.com/ios-glyphs/30/000000/installing-updates.png" width="20" height="20"/> Installation Guide

1.  **Clone Repository**
    ```bash
    git clone https://github.com/Start-Z/BookStore-Token.git
    ```

2.  **Setup Directory**
    *   Move the project folder to your local server directory (e.g., `c:\xampp\htdocs\autentikasi`).

3.  **Database Configuration**
    *   Open **PHPMyAdmin** (`http://localhost/phpmyadmin`).
    *   Create a new database named `autentikasi`.
    *   Import the `data.sql` file located in the root directory.

4.  **Configure Connection**
    *   Ensure `config/database.php` matches your local MySQL credentials (default: root, empty password).

5.  **Launch Application**
    *   Open your browser and navigate to:
        ```
        http://localhost/autentikasi
        ```

---

## <img src="https://img.icons8.com/ios-glyphs/30/000000/folder-invoices.png" width="20" height="20"/> Project Structure

```bash
autentikasi/
├── 📁 assets/          # Static assets (Images, CSS, JS)
├── 📁 config/          # Database configuration
├── 📁 includes/        # Reusable components (Header, Footer, Helpers)
├── 📁 pages/           # Core feature pages (Controllers & Views)
│   ├── admin_*.php     # Administrative modules
│   ├── api/            # API endpoints (if applicable)
│   └── ...             # User-facing pages
├── index.php           # Main Router
├── README.md           # Documentation
└── data.sql            # Database Schema
```

---

## <img src="https://img.icons8.com/ios-glyphs/30/000000/user-credentials.png" width="20" height="20"/> Default Credentials

| Role | Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@toko.com` | `password` | Full Control |
| **Standard User** | `user@toko.com` | `password` | Client Features |

---

## <img src="https://img.icons8.com/ios-glyphs/30/000000/copyright.png" width="20" height="20"/> License

Distributed under the MIT License. See `LICENSE` for more information.

> **Note**: This project is developed for educational purposes, demonstrating full-stack web development capabilities without relying on heavy frameworks for the backend logic.
