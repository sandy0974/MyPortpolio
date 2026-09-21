CREATE DATABASE IF NOT EXISTS portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  category VARCHAR(80) NOT NULL DEFAULT 'Project',
  description TEXT,
  thumbnail VARCHAR(500),
  project_url VARCHAR(500),
  github_url VARCHAR(500),
  model_url VARCHAR(500),
  model_format VARCHAR(20) DEFAULT 'glb',
  featured TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  category VARCHAR(80) NOT NULL DEFAULT 'Asset',
  description TEXT,
  thumbnail VARCHAR(500),
  file_url VARCHAR(500),
  external_url VARCHAR(500),
  published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
  id TINYINT UNSIGNED PRIMARY KEY,
  site_title VARCHAR(180) NOT NULL,
  tagline VARCHAR(255),
  about TEXT,
  github_url VARCHAR(500),
  itch_url VARCHAR(500),
  email VARCHAR(180)
);

INSERT INTO settings (id, site_title, tagline, about)
SELECT 1, 'My Game Portfolio', 'Game Developer • 3D Artist • Programmer',
       'Welcome to my portfolio.'
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE id = 1);

-- Generate your own admin password hash with:
-- php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
