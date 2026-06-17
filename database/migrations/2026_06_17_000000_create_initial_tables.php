<?php

return [
    'up' => function (\PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS login (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username TEXT NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                is_admin TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS folders (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                folder_name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY user_id (user_id),
                CONSTRAINT folders_ibfk_1 FOREIGN KEY (user_id) REFERENCES login (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS passwords (
                id INT(11) NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                login_data TEXT NOT NULL,
                password VARCHAR(255) NOT NULL,
                userID INT(11) DEFAULT NULL,
                folder_id INT(11) DEFAULT NULL,
                attachment VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (id),
                KEY userID (userID),
                KEY folder_id (folder_id),
                CONSTRAINT passwords_ibfk_1 FOREIGN KEY (userID) REFERENCES login (id),
                CONSTRAINT passwords_ibfk_2 FOREIGN KEY (folder_id) REFERENCES folders (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    },
    'down' => function (\PDO $pdo) {
        $pdo->exec('DROP TABLE IF EXISTS passwords');
        $pdo->exec('DROP TABLE IF EXISTS folders');
        $pdo->exec('DROP TABLE IF EXISTS login');
    }
];
