-- Create the database
CREATE DATABASE IF NOT EXISTS lotterylk_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE lotterylk_db;

-- ලොතරැයි වර්ග (Lottery Types)
CREATE TABLE IF NOT EXISTS lotteries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50), -- NLB, DLB
    draw_schedule VARCHAR(255),
    description TEXT,
    image_path VARCHAR(255) NULL -- ලොතරැයි පතේ පින්තූරය
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ලොතරැයි ප්‍රතිඵල (Lottery Results)
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery_id INT,
    draw_date DATE NOT NULL,
    winning_numbers VARCHAR(255) NOT NULL, -- Comma-separated or JSON
    jackpot_amount DECIMAL(15,2),
    other_prize_details TEXT, -- JSON or structured text
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lottery_id) REFERENCES lotteries(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- පුවත් ලිපි (News Articles)
CREATE TABLE IF NOT EXISTS news_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255), -- Optional image for the article
    published_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    author_name VARCHAR(100) NULL -- Optional
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- පරිපාලකවරු (Admin Users)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- Store hashed passwords
    email VARCHAR(255) NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some default data for testing

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password_hash) 
VALUES ('admin', '$2y$10$8MIS8hCQCG0xV6N1kqwZ2ehwk5O2h1iUV/iDq5Xvc6G5NzuKbJz1a');

-- Insert some example lotteries
INSERT INTO lotteries (name, type, draw_schedule, description) VALUES
('මහජන සම්පත', 'NLB', 'Every Tuesday', 'National Lotteries Board\'s popular Mahajana Sampatha lottery.'),
('ගොවිසෙත', 'DLB', 'Every Monday', 'Development Lotteries Board\'s Govi Setha lottery.'),
('ජාතික සම්පත', 'NLB', 'Every Saturday', 'NLB\'s Jathika Sampatha lottery with big jackpot prizes.'),
('අද කෝටිපති', 'DLB', 'Daily draws', 'DLB\'s Ada Kotipathi lottery with daily draws.'),
('ඩෙවලප්මන්ට් ෆෝර්චූන්', 'DLB', 'Every Friday', 'DLB\'s Development Fortune lottery.');

-- Insert some example results
INSERT INTO results (lottery_id, draw_date, winning_numbers, jackpot_amount, other_prize_details) VALUES
(1, '2025-05-14', '04-12-23-34-45-66', 100000000.00, '{"second": 1000000, "third": 500000}'),
(2, '2025-05-13', '11-22-33-44-55-66', 50000000.00, '{"second": 500000, "third": 250000}'),
(3, '2025-05-11', '07-14-21-28-35-42', 75000000.00, '{"second": 750000, "third": 350000}'),
(4, '2025-05-15', '01-09-17-25-33-41', 25000000.00, '{"second": 250000, "third": 100000}'),
(5, '2025-05-10', '05-15-25-35-45-55', 60000000.00, '{"second": 600000, "third": 300000}');

-- Insert some example news articles
INSERT INTO news_articles (title, content, author_name) VALUES
('නව ලොතරැයියක් හඳුන්වාදෙයි', 'ජාතික ලොතරැයි මණ්ඩලය විසින් නව "සුපිරි සම්පත" ලොතරැයිය හඳුන්වා දී ඇත. මෙම ලොතරැයිය සෑම බ්‍රහස්පතින්දා දිනකම ඇදීමට නියමිතය.', 'ප්‍රවෘත්ති අංශය'),
('මහජන සම්පත ජැක්පොට් ඉහළට', 'මහජන සම්පත ලොතරැයියේ ජැක්පොට් තෑග්ග රුපියල් මිලියන 150 දක්වා ඉහළ ගොස් ඇත. මෙය මෙම වසරේ වාර්තාගත ඉහළම ජැක්පොට් එකයි.', 'ප්‍රවෘත්ති අංශය'),
('ශ්‍රී ලංකාවේ ලොතරැයි ඉතිහාසය', 'ශ්‍රී ලංකාවේ ලොතරැයි මණ්ඩලය 1963 දී ස්ථාපිත කරන ලදී. එතැන් සිට ලොතරැයි මණ්ඩලය රටේ සංවර්ධන කටයුතු සඳහා විශාල දායකත්වයක් ලබා දී ඇත.', 'ඉතිහාස අංශය');
