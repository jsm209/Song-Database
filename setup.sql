-- Joshua Maza
-- 12/17/2018
-- Rainy Dawg Radio Database System
-- This file sets up the database for the music database at Rainy Dawg Radio.
-- The database consists of three tables, intended to be joined for referencing.
-- Each of the three tables, "Artists", "Song", and "Album" each have an id
-- column for each entry, along with additional columns depending on purpose.
-- Ex: The columns for artist and album for "Songs", are intended to correspond
-- to primary keys (IDs) of the appropriate artist and album.

DROP DATABASE IF EXISTS rdr_song_db;
CREATE DATABASE rdr_song_db;
USE rdr_song_db;

-- Artists Table
DROP TABLE IF EXISTS Artists;
CREATE TABLE Artists (
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(100),
  PRIMARY KEY (id)
);

-- Song Table
DROP TABLE IF EXISTS Songs;
CREATE TABLE Songs (
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(100),
  artist INT,
  release_date DATE,
  album INT,
  genre VARCHAR(100),
  medium VARCHAR(100),
  PRIMARY KEY (id)
);

-- Album Table
DROP TABLE IF EXISTS Albums;
CREATE TABLE Albums (
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(100),
  artist INT,
  PRIMARY KEY (id)
);
