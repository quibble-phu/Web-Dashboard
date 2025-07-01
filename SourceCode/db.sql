
CREATE TABLE user_sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    last_activity DATETIME NOT NULL
);

#1user 1session
CREATE TABLE user_sessions (
    user_id INT PRIMARY KEY,
    session_id VARCHAR(255),
    last_activity DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

#1user many session
CREATE TABLE user_sessions (
    user_id INT,
    session_id VARCHAR(255),
    last_activity DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, session_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



#1user 1session
CREATE TABLE users (
    id INT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    profile_image VARCHAR(255),
    role  VARCHAR(255)
);

#1user many session

