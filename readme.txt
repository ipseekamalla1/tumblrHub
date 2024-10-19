TumblrHub - Ecommerce Site


Features:
1. User Authentication: Allows users to log in with their username and password.
2. CRUD Operations: 
   - Create a new Tumblr product.
   - Read and display a list of available Tumblrs.
   - Update an existing Tumblr product.
   - Delete a Tumblr product.
3. Admin Panel: Admin users can manage Tumblr products and view all existing entries.

---



1. **Prerequisites:**
   - Install **XAMPP**, **WAMP**, **LAMP**, or **MAMP** for local server setup (Apache, MySQL, PHP).
   - Ensure **MySQL** and **Apache** services are running in your local server tool (e.g., XAMPP).
   - Use **phpMyAdmin** to manage the MySQL database.

2. **Download and Place Files:**
   - Download or clone the project files.
   - Place the project folder in your server's `htdocs` directory (for XAMPP/WAMP/LAMP/MAMP).

3. **Create the Database:**
   - Open **phpMyAdmin** (usually via `localhost/phpmyadmin`).
   - Create a new database called `tumblrHub`.

4. **Import the Database:**
   - Inside **phpMyAdmin**, select the `tumblrHub` database.
   - Click on the **Import** tab.
   - Choose the provided `database.db` file and click **Go**. This will import the necessary database schema and tables.
   - The database will have the following structure:
     - `users`: Stores user credentials for login.
     - `tumblrs`: Stores Tumblr product details.

5. **Edit Database Connection (if needed):**
   - Open the `index.php` file.
   - If your MySQL database credentials are different from the default settings, modify the following line to match your database configuration:
     ```
     $conn = new mysqli('localhost', 'root', '', 'tumblrHub');
     ```
     - **localhost**: Database host (usually `localhost`).
     - **root**: Username (default for XAMPP).
     - **''**: Password (leave empty if using the default XAMPP configuration).
     - **tumblrHub**: Database name.

6. **Running the Project:**
   - Start **Apache** and **MySQL** services in XAMPP (or similar if using WAMP/LAMP/MAMP).
   - Open a browser and go to `localhost` or `localhost/[your project folder]`.
   - You should be able to view and interact with the site.

---

username:admin
password:admin