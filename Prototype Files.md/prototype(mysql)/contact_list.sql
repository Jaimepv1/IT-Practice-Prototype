-- 1. Create the database
CREATE DATABASE IF NOT EXISTS contacts_list;
USE contacts_list;

-- 2. Create the contacts table
CREATE TABLE IF NOT EXISTS contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product VARCHAR(100) NOT NULL,
  supplier VARCHAR(100) NOT NULL,
  end_date DATE NOT NULL,
  email VARCHAR(100) NOT NULL
);

CREATE TABLE `sent_emails` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `recipient` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject`   VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `message`   TEXT            CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sent_at`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);



-- 3. Insert the sample contacts
INSERT INTO contacts (product, supplier, end_date, email) VALUES
  ('Blackboard',        'BlackboardInc',      '2025-05-20', 'example@gmail.com'),
  ('Microsoft 360',     'Microsoft',          '2026-03-13', 'microsoft@outlook.com'),
  ('O''Really learning','O''Really Learning', '2026-04-13', 'oreallylearning@gmail.com'),
  ('Monitor',           'Dell',               '2025-05-09', 'dell@gmail.com'),
  ('Apps Anywhere',     'Apps Anywhere',      '2027-07-01', 'appsanywhere@gmail.com'),
  ('Maintenance',       'Lancer Scott',       '2027-05-20', 'lancerscott@gmail.com');
