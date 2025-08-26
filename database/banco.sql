-- banco.sql (criação simplificada)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  points INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE habits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  points_base INT NOT NULL DEFAULT 10, -- pontos por execução
  frequency ENUM('daily','weekly','custom') DEFAULT 'daily',
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE habit_executions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  habit_id INT NOT NULL,
  executed_at DATE NOT NULL, -- registra dia de execução
  points_awarded INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE,
  UNIQUE(user_id, habit_id, executed_at)
);

CREATE TABLE badges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL, -- ex: STREAK_7
  title VARCHAR(100) NOT NULL,
  description TEXT,
  icon VARCHAR(255), -- nome de classe ou URL
  criteria JSON NOT NULL, -- regras (ex: {"type":"streak","days":7})
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_badges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  badge_id INT NOT NULL,
  awarded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
  UNIQUE(user_id, badge_id)
);

INSERT INTO users (name,email,password_hash,points) VALUES
('Usuario Padrao','padrao@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',100);

INSERT INTO habits (title,description,points_base,frequency,created_by) VALUES
('Beber 2L de água','Mantenha-se hidratado durante o dia',10,'daily',1),
('Meditar 10 min','Pratique mindfulness e relaxamento',15,'daily',1),
('Caminhar 30 min','Exercício cardiovascular leve',20,'daily',1),
('Ler 20 páginas','Desenvolva o hábito da leitura',12,'daily',1);

