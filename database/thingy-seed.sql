--
-- Use a specific schema and set it as default - thingy.
--
DROP SCHEMA IF EXISTS thingy
CASCADE;
CREATE SCHEMA
IF NOT EXISTS thingy;
SET search_path
TO thingy;

--
-- Drop any existing tables.
--
DROP TABLE IF EXISTS users
CASCADE;
DROP TABLE IF EXISTS cards
CASCADE;
DROP TABLE IF EXISTS items
CASCADE;

--
-- Create tables.
--
CREATE TABLE users
(
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  email VARCHAR UNIQUE NOT NULL,
  password VARCHAR NOT NULL,
  remember_token VARCHAR
);

CREATE TABLE cards
(
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  descriptIon TEXT NOT NULL,
  image TEXT,
  rating INT NOT NULL,
  user_id INTEGER REFERENCES users NOT NULL,
  date TIMESTAMP NOT NULL CHECK (date <= now())
);

CREATE TABLE items
(
  id SERIAL PRIMARY KEY,
  card_id INTEGER NOT NULL REFERENCES cards ON DELETE CASCADE,
  description VARCHAR NOT NULL,
  done BOOLEAN NOT NULL DEFAULT FALSE
);

--
-- Insert value.
--

INSERT INTO users
VALUES
  (
    DEFAULT,
    'John Doe',
    'admin@example.com',
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
);
-- Password is 1234. Generated using Hash::make('1234')


