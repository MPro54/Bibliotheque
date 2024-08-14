CREATE DATABASE IF NOT EXISTS Database_PortCartier;

USE Database_PortCartier;

CREATE TABLE members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    province VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    province VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE genres (
    genre_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE audience_ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL
);

CREATE TABLE documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publication_year INT,
    category_id INT,
    audience_rating_id INT,
    genre_id INT,
    description TEXT,
    isbn VARCHAR(20),
    image_path VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (audience_rating_id) REFERENCES audience_ratings(rating_id),
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id)
);

CREATE TABLE reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    document_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (document_id) REFERENCES documents(document_id)
);

CREATE TABLE loans (
    loan_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    document_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (document_id) REFERENCES documents(document_id)
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('member', 'employee', 'admin') NOT NULL,
    member_id INT,
    employee_id INT,
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    document_id INT NOT NULL,
    transaction_type ENUM('loan', 'reservation') NOT NULL,
    transaction_date DATE NOT NULL,
    due_date DATE,
    return_date DATE,
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (document_id) REFERENCES documents(document_id)
);

CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (document_id) REFERENCES documents(document_id)
);

INSERT INTO categories (name) VALUES
('Livre'),
('Film'),
('Jeu'),
('Console'),
('CD');

INSERT INTO genres (name) VALUES
('Comédie'),
('Drame'),
('Horreur'),
('Science-fiction'),
('Documentaire'),
('Romance'),
('Fantasy');

INSERT INTO audience_ratings (name) VALUES
('Enfant'),
('Adolescent'),
('Adulte');

INSERT INTO members (last_name, first_name, address, city, province, phone, email, password)
VALUES
('Dit', 'Jean', '098 Rue Principale', 'Port-Cartier', 'QC', '514-555-1234', 'jeandit@example.com', 'aaaa1111'),
('Darc', 'Jeanne', '456 Rue Secondaire', 'Port-Cartier', 'QC', '418-555-5678', 'jeannedarc@example.com', 'abcd1234');

INSERT INTO employees (last_name, first_name, address, city, province, phone, email, password, is_admin)
VALUES
('Doe', 'John', '123 Boulevard Inconnu', 'Port-Cartier', 'QC', '418-555-9012', 'john.doe@example.com', 'mdp123', TRUE),
('Doe', 'Jane', '321 Rue Imposteurs', 'Port-Cartier', 'QC', '418-555-3456', 'jane.doe@example.com', 'securepwd789', FALSE);
