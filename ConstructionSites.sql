CREATE DATABASE constructionsites;

USE constructionsites;

CREATE TABLE users ( 
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
  );
  
CREATE TABLE places(
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL UNIQUE,
  country VARCHAR(50) NOT NULL
);
  
CREATE TABLE sites(
  id INT PRIMARY KEY AUTO_INCREMENT,
  address VARCHAR(100) NOT NULL,
  floorCount INT(3) NOT NULL,
  aptCount INT(4) NOT NULL,
  insidePlaster VARCHAR(2) NOT NULL,
  outsidePlaster VARCHAR(2) NOT NULL,
  investor VARCHAR(50) NOT NULL,
  placeName VARCHAR(50) NOT null,
  FOREIGN KEY(placeName) REFERENCES places(name) ON UPDATE CASCADE ON DELETE CASCADE
);

