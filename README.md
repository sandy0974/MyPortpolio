# PHP Game Portfolio

Portfolio website for game projects, 3D models and game assets.

## Stack

- PHP 8.3
- MySQL/MariaDB
- PDO + prepared statements
- HTML/CSS
- `<model-viewer>` for GLB/GLTF previews
- GitHub + GitHub Actions + FTP deployment
- Designed for shared hosting such as InfinityFree

InfinityFree currently advertises PHP 8.3 and MySQL 8.0 / MariaDB 11.4 on its free hosting. GitHub Pages is NOT used because GitHub Pages does not support server-side PHP.

## 1. Local VS Code setup

1. Install PHP 8.3 and MySQL/MariaDB locally, or use XAMPP.
2. Copy `config.example.php` to `config.php`.
3. Put your local DB credentials in `config.php`.
4. Import `database.sql`.
5. Create the first admin user:

```sql
INSERT INTO admins (username, password_hash)
VALUES ('admin', 'PASTE_PASSWORD_HASH_HERE');
```

Generate the hash:

```bash
php -r "echo password_hash('YourStrongPassword', PASSWORD_DEFAULT), PHP_EOL;"
```

6. Start PHP:

```bash
php -S localhost:8000
```

Open `http://localhost:8000`.

Admin: `http://localhost:8000/admin/login.php`

## 2. InfinityFree setup

1. Create an InfinityFree hosting account.
2. Create a hosting account/domain.
3. Create a MySQL database in the InfinityFree control panel.
4. Import `database.sql` using phpMyAdmin.
5. On the server, create `config.php` from `config.example.php` and enter the database values supplied by InfinityFree.
6. Set `BASE_URL` to your real site URL.
7. Upload the project to the site's `htdocs` directory.
8. Make sure `uploads/images` and `uploads/models` are writable if uploads fail.

Do not commit the real `config.php` to GitHub.

## 3. GitHub

In VS Code terminal:

```bash
git init
git add .
git commit -m "Initial portfolio"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPO.git
git push -u origin main
```

## 4. Automatic deployment to InfinityFree

The included `.github/workflows/deploy.yml` deploys on every push to `main`.

Create these GitHub repository Actions secrets:

- `INFINITY_FTP_SERVER`
- `INFINITY_FTP_USER`
- `INFINITY_FTP_PASSWORD`
- `INFINITY_FTP_DIR`

Use the FTP server, username and destination directory shown by your InfinityFree account. Do not put the password directly in the workflow.

Important: the workflow intentionally excludes `config.php` so a server-side database configuration is not overwritten by GitHub.

## 5. Recommended workflow

VS Code
  ↓ git commit
GitHub main
  ↓ GitHub Actions
InfinityFree FTP
  ↓
Live PHP + MySQL site

The database itself is NOT stored in GitHub. GitHub stores the PHP/CSS/JS source; InfinityFree stores the live MySQL database and uploaded media.

## Security notes

- Use a strong admin password.
- Never commit `config.php`.
- Keep admin URLs protected by the login session.
- The CRUD uses PDO prepared statements.
- Change/delete the default admin account after setup.
