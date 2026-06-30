create schema if not exists lbaw2486;

-----------------------------------------
-- Drop old schema
-----------------------------------------

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS post CASCADE;
DROP TABLE IF EXISTS challenge CASCADE;
DROP TABLE IF EXISTS publications CASCADE;
DROP TABLE IF EXISTS commentaires CASCADE;
DROP TABLE IF EXISTS vote CASCADE;
DROP TABLE IF EXISTS group_users CASCADE;
DROP TABLE IF EXISTS group_owner CASCADE;
DROP TABLE IF EXISTS group_member CASCADE;
DROP TABLE IF EXISTS administrator CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS user_notification CASCADE;
DROP TABLE IF EXISTS group_notification CASCADE;
DROP TABLE IF EXISTS publication_notification CASCADE;
DROP TABLE IF EXISTS challenge_notification CASCADE;
DROP TABLE IF EXISTS follow_request CASCADE;
DROP TABLE IF EXISTS group_join_request CASCADE;
DROP TABLE IF EXISTS group_challenge CASCADE;
DROP TABLE IF EXISTS challenge_participants CASCADE;

DROP TYPE IF EXISTS comment_notification_types;
DROP TYPE IF EXISTS user_notification_types;
DROP TYPE IF EXISTS group_notification_types;
DROP TYPE IF EXISTS publication_notification_types;
DROP TYPE IF EXISTS challenge_notification_types;

DROP FUNCTION IF EXISTS group_users_search_update CASCADE;
DROP FUNCTION IF EXISTS users_search_update CASCADE;
DROP FUNCTION IF EXISTS publications_search_update CASCADE;
DROP FUNCTION IF EXISTS commentaires_search_update CASCADE;
DROP FUNCTION IF EXISTS challenge_search_update CASCADE;
DROP FUNCTION IF EXISTS update_publication_ranking CASCADE;
DROP FUNCTION IF EXISTS update_user_points_on_new_publication CASCADE;
DROP FUNCTION IF EXISTS update_user_ranking CASCADE;
DROP FUNCTION IF EXISTS verify_self_follow CASCADE;
DROP FUNCTION IF EXISTS check_follow CASCADE;
DROP FUNCTION IF EXISTS verify_challenge_participation CASCADE;
DROP FUNCTION IF EXISTS challenge_group_owner CASCADE;
DROP FUNCTION IF EXISTS group_owner_func CASCADE;
DROP FUNCTION IF EXISTS check_group_join_req CASCADE;
DROP FUNCTION IF EXISTS verify_publication_vote CASCADE;
DROP FUNCTION IF EXISTS verify_publication_comment CASCADE;
DROP FUNCTION IF EXISTS verify_challenge_group_participation CASCADE;
DROP FUNCTION IF EXISTS delete_mainnotification_action CASCADE;
    Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30);
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('birthdate');
            $table->string('description', 200)->default('');
            $table->boolean('public')->default(true);
            $table->integer('points')->default(0);
            $table->integer('ranking')->default(0);
            $table->string('profile_picture')->nullable();
            $table->timestamps();
        });
-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE comment_notification_types AS ENUM ('reply_comment', 'comment_publication');
CREATE TYPE user_notification_types AS ENUM ('request_follow', 'started_following', 'accepted_follow');
CREATE TYPE group_notification_types AS ENUM ('join_group', 'leave_group', 'request_group', 'invite', 'accepted_join', 'created_challenge');
CREATE TYPE publication_notification_types AS ENUM ('publication_post', 'vote_post', 'ranking_position');
CREATE TYPE challenge_notification_types AS ENUM ('deadline_time', 'dsgst_itf');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL CHECK (length(name) <= 30), 
  username TEXT NOT NULL CONSTRAINT username_uk UNIQUE,
  email TEXT NOT NULL CONSTRAINT user_email_uk UNIQUE,
  password TEXT NOT NULL CHECK (length(password) >= 8),
  birthdate TIMESTAMP NOT NULL CHECK (birthdate <= now()),
  description TEXT NOT NULL CHECK (length(description) <= 200),
  public BOOL NOT NULL DEFAULT TRUE,
  points INT NOT NULL DEFAULT 0,
  ranking INT NOT NULL,
  profile_picture TEXT
);

CREATE TABLE post (
	id SERIAL PRIMARY KEY,
	id_poster INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	date TIMESTAMP NOT NULL CHECK (date <= now()),
	edited BOOL NOT NULL DEFAULT FALSE
);

CREATE TABLE challenge (
	id SERIAL PRIMARY KEY,
	name TEXT NOT NULL CHECK (length(name) <= 30),
	description TEXT NOT NULL CHECK (length(description) <= 200),
	begin_date TIMESTAMP NOT NULL CHECK (begin_date <= now()),
	end_date TIMESTAMP NOT NULL CHECK (end_date > begin_date),
	max_participants INT NOT NULL CHECK ((max_participants > 1) AND (max_participants <= 100))
);

CREATE TABLE publications (
	id SERIAL PRIMARY KEY,
	id_post INT NOT NULL REFERENCES post(id_poster) ON DELETE CASCADE,
	id_challenge INT REFERENCES challenge(id) ON DELETE CASCADE,
	pub_image TEXT NOT NULL,
	ranking DECIMAL NOT NULL CHECK ((ranking >= 0.0) AND (ranking <= 5.0)),
	description TEXT NOT NULL CHECK (length(description) <= 200)
);

CREATE TABLE commentaires (
	id SERIAL PRIMARY KEY,
	id_post INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
	id_publication INT NOT NULL REFERENCES publications(id) ON DELETE CASCADE,
	previous INTEGER DEFAULT NULL REFERENCES commentaires(id) ON UPDATE CASCADE,
	comment_text TEXT NOT NULL CHECK (length(comment_text) > 0 AND length(comment_text) < 200)
);

CREATE TABLE vote (
	id SERIAL PRIMARY KEY,
	id_post INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
	id_publication INT NOT NULL REFERENCES publications(id) ON DELETE CASCADE,
	aesthetic INT NOT NULL CHECK ((aesthetic >= 0) AND (aesthetic <= 100)),
	technique INT NOT NULL CHECK ((technique >= 0) AND (technique <= 100)),
	creativity INT NOT NULL CHECK ((creativity >= 0) AND (creativity <= 100)),
	rate DECIMAL NOT NULL CHECK ((rate >= 0.0) AND (rate <= 5.0))
);

CREATE TABLE group_users (
	id SERIAL PRIMARY KEY,
	name TEXT NOT NULL CHECK (length(name) <= 30),
	creation_date TIMESTAMP NOT NULL CHECK (creation_date <= now()),
	description TEXT NOT NULL CHECK (length(description) <= 200),
	max_participants INT NOT NULL CHECK ((max_participants > 1) AND (max_participants <= 100))
);

CREATE TABLE group_owner (
	id_user INT REFERENCES users(id) ON DELETE CASCADE,
	id_group INT REFERENCES group_users(id) ON DELETE CASCADE,
	PRIMARY KEY (id_user, id_group)
);

CREATE TABLE group_member (
	id_user INT REFERENCES users(id) ON DELETE CASCADE,
	id_group INT REFERENCES group_users(id) ON DELETE CASCADE,
	PRIMARY KEY (id_user, id_group)
);

CREATE TABLE administrator (
	id SERIAL PRIMARY KEY,
	id_user INT REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notification (
   id SERIAL PRIMARY KEY,
   date TIMESTAMP NOT NULL CHECK (date <= now()),
   received_user INT NOT NULL REFERENCES users(id) ON UPDATE CASCADE,
   emitter_user INT NOT NULL REFERENCES users(id) ON UPDATE CASCADE,
   viewed BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE comment_notification (
   id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
   id_comment INT NOT NULL REFERENCES commentaires(id) ON UPDATE CASCADE,
   notification_type comment_notification_types NOT NULL
);

CREATE TABLE user_notification (
   id INT PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
   notification_type user_notification_types NOT NULL
);

CREATE TABLE group_notification (
   id INT PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
   id_group INT NOT NULL REFERENCES group_users(id) ON UPDATE CASCADE,
   notification_type group_notification_types NOT NULL
);

CREATE TABLE publication_notification (
   id INT PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
   id_publication INT NOT NULL REFERENCES publications(id) ON UPDATE CASCADE,
   notification_type publication_notification_types NOT NULL
);

CREATE TABLE challenge_notification (
   id INT PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
   id_challenge INT NOT NULL REFERENCES challenge(id) ON UPDATE CASCADE,
   notification_type challenge_notification_types NOT NULL
);

CREATE TABLE follow_request (
	id_follower INT REFERENCES users(id) ON UPDATE CASCADE,
   	id_followed INT REFERENCES users(id) ON UPDATE CASCADE,
   	PRIMARY KEY (id_follower, id_followed)
);

CREATE TABLE group_join_request (
	id_user INT REFERENCES users(id) ON UPDATE CASCADE,
   	id_group INT REFERENCES group_users(id) ON UPDATE CASCADE,
   	PRIMARY KEY (id_user, id_group)
);

CREATE TABLE group_challenge (
	id_group INT REFERENCES group_users(id) ON UPDATE CASCADE,
	id_challenge INT REFERENCES challenge(id) ON UPDATE CASCADE,
	PRIMARY KEY (id_group, id_challenge)
);

CREATE TABLE challenge_participants (
	id_user INT REFERENCES users(id) ON UPDATE CASCADE,
	id_challenge INT REFERENCES challenge(id) ON UPDATE CASCADE,
	PRIMARY KEY (id_user, id_challenge)
);

-----------------------------------------
-- Indexes
-----------------------------------------

CREATE INDEX received_user_notification ON notification USING btree (received_user);
CLUSTER notification USING received_user_notification;

CREATE INDEX emmited_user_notification ON notification USING btree (emitter_user);
CLUSTER notification USING emmited_user_notification;

CREATE INDEX publications_ranking ON publications USING btree (ranking);

CREATE INDEX challenge_end_date ON challenge USING btree (end_date);

CREATE INDEX id_poster_post ON post USING hash (id_poster);


-----------------------------------------
-- Fts Indexes
-----------------------------------------

-- Add column to group_users to store computed ts_vectors.

ALTER TABLE group_users

ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.

CREATE FUNCTION group_users_search_update() RETURNS TRIGGER AS $$

BEGIN

IF TG_OP = 'INSERT' THEN

NEW.tsvectors = (

setweight(to_tsvector('portuguese', NEW.name), 'A') ||

setweight(to_tsvector('portuguese', NEW.description), 'B')

);

END IF;

IF TG_OP = 'UPDATE' THEN

IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN

NEW.tsvectors = (

setweight(to_tsvector('portuguese', NEW.name), 'A') ||

setweight(to_tsvector('portuguese', NEW.description), 'B')

);

END IF;

END IF;

RETURN NEW;

END $$

LANGUAGE plpgsql;

-- Create a trigger before insert or update on group_users.

CREATE TRIGGER group_users_search_update

BEFORE INSERT OR UPDATE ON group_users

FOR EACH ROW

EXECUTE PROCEDURE group_users_search_update();

-- Create a GIN index for ts_vectors.

CREATE INDEX search_group_users_idx ON group_users USING GIN (tsvectors);

-- Add column to users to store computed ts_vectors.

ALTER TABLE users

ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.

CREATE FUNCTION users_search_update() RETURNS TRIGGER AS $$

BEGIN

IF TG_OP = 'INSERT' THEN

NEW.tsvectors = (

setweight(to_tsvector('portuguese', NEW.name), 'A') ||

setweight(to_tsvector('portuguese', NEW.username), 'B')

);

END IF;

IF TG_OP = 'UPDATE' THEN

IF (NEW.name <> OLD.name OR NEW.username<> OLD.username) THEN

NEW.tsvectors = (

setweight(to_tsvector('portuguese', NEW.name), 'A') ||

setweight(to_tsvector('portuguese', NEW.username), 'B')

);

END IF;

END IF;

RETURN NEW;

END $$

LANGUAGE plpgsql;

-- Create a trigger before insert or update on users.

CREATE TRIGGER users_search_update

BEFORE INSERT OR UPDATE ON users

FOR EACH ROW

EXECUTE PROCEDURE users_search_update();

-- Create a GIN index for ts_vectors.

CREATE INDEX search_users_idx ON users USING GIN (tsvectors);

-- Add column to publications to store computed ts_vectors.

ALTER TABLE publications

ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.

CREATE FUNCTION publications_search_update() RETURNS TRIGGER AS $$

BEGIN

IF TG_OP = 'INSERT' THEN

NEW.tsvectors = to_tsvector('portuguese', NEW.description);

END IF;

IF TG_OP = 'UPDATE' THEN

IF (NEW.description <> OLD.description) THEN

NEW.tsvectors = to_tsvector('portuguese', NEW.description);

END IF;

END IF;

RETURN NEW;

END $$

LANGUAGE plpgsql;

-- Create a trigger before insert or update on publications.

CREATE TRIGGER publications_search_update

BEFORE INSERT OR UPDATE ON publications

FOR EACH ROW

EXECUTE PROCEDURE publications_search_update();

-- Create a GIN index for ts_vectors.

CREATE INDEX search_publications_idx ON publications USING GIN (tsvectors);

-- Add column to commentaires to store computed ts_vectors.

ALTER TABLE commentaires

ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.

CREATE FUNCTION commentaires_search_update() RETURNS TRIGGER AS $$

BEGIN

IF TG_OP = 'INSERT' THEN

NEW.tsvectors = to_tsvector('portuguese', NEW.comment_text);

END IF;

IF TG_OP = 'UPDATE' THEN

IF (NEW.comment_text<> OLD.comment_text) THEN

NEW.tsvectors = to_tsvector('portuguese', NEW.comment_text);

END IF;

END IF;

RETURN NEW;

END $$

LANGUAGE plpgsql;

-- Create a trigger before insert or update on commentaires.

CREATE TRIGGER commentaires_search_update

BEFORE INSERT OR UPDATE ON commentaires

FOR EACH ROW

EXECUTE PROCEDURE commentaires_search_update();

-- Create a GIN index for ts_vectors.

CREATE INDEX search_commentaires_idx ON commentaires USING GIN (tsvectors);

-- Add column to challenge to store computed ts_vectors.

ALTER TABLE challenge

ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.

CREATE FUNCTION challenge_search_update() RETURNS TRIGGER AS $$

BEGIN

IF TG_OP = 'INSERT' THEN

NEW.tsvectors = to_tsvector('portuguese', NEW.description);

END IF;

IF TG_OP = 'UPDATE' THEN

IF (NEW.description <> OLD.description) THEN

NEW.tsvectors = to_tsvector('portuguese', NEW.description);

END IF;

END IF;

RETURN NEW;

END $$

LANGUAGE plpgsql;

-- Create a trigger before insert or update on challenge.

CREATE TRIGGER challenge_search_update

BEFORE INSERT OR UPDATE ON challenge

FOR EACH ROW

EXECUTE PROCEDURE challenge_search_update();

-- Create a GIN index for ts_vectors.

CREATE INDEX search_challenge_idx ON challenge USING GIN (tsvectors);

CREATE OR REPLACE FUNCTION update_publication_ranking() RETURNS TRIGGER AS 

$BODY$

BEGIN

  UPDATE publications
  SET ranking = (
    CASE
      WHEN (SELECT COUNT(*) FROM vote WHERE             
    id_publication = NEW.id) = 0
      THEN 0  
      ELSE (SELECT SUM(rate) FROM vote WHERE     
         id_publication = NEW.id) /
      (SELECT COUNT(*) FROM vote WHERE    
      id_publication = NEW.id)
    END
  )
  WHERE id = NEW.id;

  RETURN NEW;
END;

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER update_publication_ranking
AFTER INSERT ON vote
FOR EACH ROW
EXECUTE FUNCTION update_publication_ranking();       

CREATE OR REPLACE FUNCTION update_user_points_on_new_publication() RETURNS TRIGGER AS 

$BODY$

BEGIN

UPDATE users
  SET points = points + 10
  WHERE id = (SELECT id_poster FROM post WHERE id = NEW.id_post);

RETURN NEW;
END;

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER add_points_after_publication
AFTER INSERT ON publications
FOR EACH ROW
EXECUTE FUNCTION update_user_points_on_new_publication();

CREATE OR REPLACE FUNCTION update_user_ranking() RETURNS TRIGGER AS 

$BODY$

BEGIN

WITH ranked_users AS (
  SELECT id,
  RANK() OVER (ORDER BY points DESC) AS rank
  FROM users)
    UPDATE users
    SET ranking = ranked_users.rank
    FROM ranked_users
    WHERE users.id = ranked_users.id;

  RETURN NEW;
END;

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER adjust_ranking_after_points_update
AFTER UPDATE OF points ON users
FOR EACH ROW
EXECUTE FUNCTION update_user_ranking();

CREATE FUNCTION verify_self_follow() RETURNS TRIGGER AS

$BODY$

BEGIN

  IF NEW.id_follower= NEW.id_followed THEN
    RAISE EXCEPTION 'A user can not follow itself';
  END IF;
  RETURN NEW;
END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_self_follow
BEFORE INSERT OR UPDATE ON follow_request
FOR EACH ROW
EXECUTE PROCEDURE verify_self_follow();

CREATE FUNCTION check_follow() RETURNS TRIGGER AS

$BODY$

BEGIN

IF EXISTS

(SELECT * FROM follow_request WHERE id_follower = NEW.id_follower AND id_followed = NEW.id_followed)

THEN RAISE EXCEPTION 'Can not send a follow request to a user you are already following';

END IF;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER check_follow
BEFORE INSERT OR UPDATE ON follow_request
FOR EACH ROW
EXECUTE PROCEDURE check_follow();

CREATE FUNCTION verify_challenge_participation() RETURNS TRIGGER AS

$BODY$

BEGIN

IF EXISTS

(SELECT * FROM challenge_participants WHERE id_user = NEW.id_user AND id_challenge = NEW.id_challenge)

THEN RAISE EXCEPTION 'A user can only participate once in each challenge';

END IF;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_challenge_participation
BEFORE INSERT OR UPDATE ON challenge_participants
FOR EACH ROW
EXECUTE PROCEDURE verify_challenge_participation();

CREATE FUNCTION challenge_group_owner() RETURNS TRIGGER AS

$BODY$

BEGIN

INSERT INTO challenge_participants (id_user, id_challenge)

VALUES (
(SELECT id_user FROM group_owner WHERE id_group = NEW.id_group), NEW.id_challenge
);

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER challenge_group_owner
BEFORE INSERT OR UPDATE ON group_challenge 
FOR EACH ROW
EXECUTE PROCEDURE challenge_group_owner();

CREATE FUNCTION group_owner_func() RETURNS TRIGGER AS

$BODY$

BEGIN

INSERT INTO group_member (id_user, id_group)

VALUES (NEW.id_user, NEW.id_group);

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER group_owner_func
AFTER INSERT ON group_owner
FOR EACH ROW
EXECUTE PROCEDURE group_owner_func();

CREATE FUNCTION check_group_join_req() RETURNS TRIGGER AS

$BODY$

BEGIN

IF EXISTS

(SELECT * FROM group_member WHERE NEW.id_user= id_user AND NEW.id_group = id_group)

THEN RAISE EXCEPTION 'Can not request to join a group in which you are already a member';

END IF;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER check_group_join_req
BEFORE INSERT OR UPDATE ON group_join_request
FOR EACH ROW
EXECUTE PROCEDURE check_group_join_req();

CREATE FUNCTION verify_publication_vote() RETURNS TRIGGER AS

$BODY$

BEGIN

IF EXISTS (SELECT 1 FROM vote WHERE id_post = NEW.id_post AND id_publication = NEW.id_publication)  THEN

RAISE EXCEPTION 'A user can only vote a publication once';

END IF;

IF EXISTS (SELECT * FROM publications, group_challenge WHERE NEW.id_post= publications.id_post AND publications.id_challenge = group_challenge.id_challenge)

AND NOT EXISTS (SELECT * FROM vote, post, publications, group_challenge, group_member WHERE vote.id_post = post.id AND post.id_poster = group_member.id_user AND vote.id_publication = publications.id AND publications.id_challenge = group_challenge.id_challenge AND group_challenge.id_group = group_member.id_group) THEN

RAISE EXCEPTION 'A user can only vote a publication from groups to which they belong';

END IF;

IF EXISTS (SELECT * FROM users,post,publications  WHERE NEW.id_post= publications.id_post AND publications.id_post = post.id AND post.id_poster= users.id AND NOT users.public)

AND NOT EXISTS (SELECT * FROM post,follow_request WHERE NEW.id_post= post.id AND post.id_poster= follow_request.id_follower  AND follow_request.id_followed = (SELECT id_poster FROM post WHERE post.id = (SELECT id_post FROM publications WHERE publications.id_post = NEW.id_post))) THEN

RAISE EXCEPTION 'A user can only vote publications from public users or users they follow';

END IF;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_publication_vote
BEFORE INSERT OR UPDATE ON vote
FOR EACH ROW
EXECUTE PROCEDURE verify_publication_vote();

CREATE FUNCTION verify_publication_comment() RETURNS TRIGGER AS

$BODY$

BEGIN

IF EXISTS (SELECT * FROM publications, group_challenge WHERE NEW.id_post= publications.id_post AND publications.id_challenge = group_challenge.id_challenge)

AND NOT EXISTS (SELECT * FROM commentaires, post, publications, group_challenge, group_member WHERE commentaires.id_post = post.id AND post.id_poster = group_member.id_user AND commentaires.id_publication = publications.id AND publications.id_challenge = group_challenge.id_challenge AND group_challenge.id_group = group_member.id_group) THEN

RAISE EXCEPTION 'A user can only comment publications from groups to which they belong';

END IF;

IF EXISTS (SELECT * FROM users,post,publications, commentaires WHERE NEW.id_post= publications.id_post AND publications.id_post = post.id AND publications.id = commentaires.id_publication AND post.id_poster= users.id AND NOT users.public)

AND NOT EXISTS (SELECT * FROM post,follow_request WHERE NEW.id_post= post.id AND post.id_poster= follow_request.id_follower AND follow_request.id_followed = (SELECT id_poster FROM post WHERE post.id = (SELECT id_post FROM publications WHERE publications.id_post = NEW.id_post))) THEN

RAISE EXCEPTION 'A user can only comment publications from public users or users they follow';

END IF;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_publication_comment
BEFORE INSERT OR UPDATE ON commentaires
FOR EACH ROW
EXECUTE PROCEDURE verify_publication_comment();

CREATE FUNCTION verify_challenge_group_participation() RETURNS TRIGGER AS

$BODY$

BEGIN

IF EXISTS (SELECT * FROM challenge WHERE NEW.id_challenge= challenge.id)

AND NOT EXISTS (SELECT * FROM challenge, group_challenge, group_member WHERE NEW.id_challenge = challenge.id AND NEW.id_user = group_member.id_user AND challenge.id = group_challenge.id_challenge AND group_challenge.id_group = group_member.id_group) THEN

RAISE EXCEPTION 'A user can only participate in group challenges if he’s belong to that group.';

END IF;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_challenge_group_participation
BEFORE INSERT OR UPDATE ON challenge_participants
FOR EACH ROW
EXECUTE PROCEDURE verify_challenge_group_participation();

CREATE FUNCTION delete_mainnotification_action() RETURNS TRIGGER AS

$BODY$

BEGIN

DELETE FROM notification WHERE OLD.id= notification.id;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER delete_main_publication_notification_action
AFTER DELETE ON publication_notification
FOR EACH ROW
EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_comment_notification_action
AFTER DELETE ON comment_notification
FOR EACH ROW
EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_group_notification_action
AFTER DELETE ON group_notification
FOR EACH ROW
EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_challenge_notification_action
AFTER DELETE ON challenge_notification
FOR EACH ROW
EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_user_notification_action
AFTER DELETE ON user_notification
FOR EACH ROW
EXECUTE PROCEDURE delete_mainnotification_action();