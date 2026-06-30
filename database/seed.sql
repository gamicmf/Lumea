create schema if not exists lbaw2486;

-----------------------------------------
-- Drop old schema
-----------------------------------------

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS like_comments CASCADE;
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
DROP TABLE IF EXISTS admin_notification CASCADE;
DROP TABLE IF EXISTS follow_request CASCADE;
DROP TABLE IF EXISTS group_join_request CASCADE;
DROP TABLE IF EXISTS group_challenge CASCADE;
DROP TABLE IF EXISTS challenge_participants CASCADE;
DROP TABLE IF EXISTS reports CASCADE;
DROP TABLE IF EXISTS followers CASCADE;
DROP TABLE IF EXISTS messages CASCADE;
DROP TABLE IF EXISTS faq CASCADE;
DROP TABLE IF EXISTS password_resets CASCADE;
DROP TABLE IF EXISTS like_comments CASCADE;
DROP TABLE IF EXISTS invite_member CASCADE;


DROP TYPE IF EXISTS comment_notification_types;
DROP TYPE IF EXISTS user_notification_types;
DROP TYPE IF EXISTS group_notification_types;
DROP TYPE IF EXISTS publication_notification_types;
DROP TYPE IF EXISTS challenge_notification_types;
DROP TYPE IF EXISTS admin_notification_types;

DROP FUNCTION IF EXISTS group_users_search_update CASCADE;
DROP FUNCTION IF EXISTS users_search_update CASCADE;
DROP FUNCTION IF EXISTS publications_search_update CASCADE;
DROP FUNCTION IF EXISTS commentaires_search_update CASCADE;
DROP FUNCTION IF EXISTS challenge_search_update CASCADE;
DROP FUNCTION IF EXISTS challenge_search_update CASCADE;
DROP FUNCTION IF EXISTS update_publication_ranking CASCADE;
DROP FUNCTION IF EXISTS update_user_points_on_new_publication CASCADE;
DROP FUNCTION IF EXISTS update_user_ranking CASCADE;
DROP FUNCTION IF EXISTS verify_self_follow CASCADE;
DROP FUNCTION IF EXISTS check_follow CASCADE;
DROP FUNCTION IF EXISTS verify_challenge_participation CASCADE;
DROP FUNCTION IF EXISTS challenge_group_owner CASCADE;
DROP FUNCTION IF EXISTS verify_challenge_participation CASCADE;
DROP FUNCTION IF EXISTS challenge_group_owner CASCADE;
DROP FUNCTION IF EXISTS group_owner_func CASCADE;
DROP FUNCTION IF EXISTS check_group_join_req CASCADE;
DROP FUNCTION IF EXISTS verify_publication_vote CASCADE;
DROP FUNCTION IF EXISTS verify_publication_comment CASCADE;
DROP FUNCTION IF EXISTS verify_challenge_group_participation CASCADE;
DROP FUNCTION IF EXISTS verify_challenge_group_participation CASCADE;
DROP FUNCTION IF EXISTS delete_mainnotification_action CASCADE;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE comment_notification_types AS ENUM ('reply_comment', 'comment_publication','liked_comment', 'deleted_comment');
CREATE TYPE user_notification_types AS ENUM ('request_follow', 'started_following', 'accepted_follow','promoved_admin', 'blocked_account','unblocked_account');
CREATE TYPE group_notification_types AS ENUM ('request_join','join_group', 'leave_group', 'accepted_join', 'created_challenge','removed_f_group', 'added_t_group', 'invite_member', 'received_message','deleted_group','expelled_group');
CREATE TYPE publication_notification_types AS ENUM ('publication_post', 'vote_post', 'ranking_position', 'deleted_publication');
CREATE TYPE challenge_notification_types AS ENUM ('deadline_time', 'dsgst_itf','deleted_challenge','joined_challenge');
CREATE TYPE admin_notification_types AS ENUM ('report_user', 'report_post', 'report_group');
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
	ranking INT NOT NULL DEFAULT 0,
    profile_picture TEXT,
  remember_token VARCHAR,
  blocked BOOLEAN NOT NULL DEFAULT FALSE,
  google_id VARCHAR
);

CREATE TABLE post (
	id SERIAL PRIMARY KEY,
	id_poster INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	edited BOOL NOT NULL DEFAULT FALSE
);

CREATE TABLE challenge (
	id SERIAL PRIMARY KEY,
  id_creator INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	name TEXT NOT NULL CHECK (length(name) <= 30),
	description TEXT NOT NULL CHECK (length(description) <= 200),
	begin_date DATE NOT NULL CHECK (begin_date <= now()),
	end_date DATE NOT NULL CHECK (end_date > begin_date),
	max_participants INT NOT NULL CHECK ((max_participants > 1) AND (max_participants <= 100)),
  private BOOL NOT NULL DEFAULT FALSE
);

CREATE TABLE publications (
	id SERIAL PRIMARY KEY,
	id_post INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
  id_challenge INT REFERENCES challenge(id) ON DELETE CASCADE,
	pub_image TEXT NOT NULL,
	ranking DECIMAL NOT NULL CHECK ((ranking >= 0.0) AND (ranking <= 5.0)),
  created_date TIMESTAMP NOT NULL,
	description TEXT NOT NULL CHECK (length(description) <= 200)
);

CREATE TABLE commentaires (
	id SERIAL PRIMARY KEY,
	id_post INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
	id_publication INT NOT NULL REFERENCES publications(id) ON DELETE CASCADE,
	previous INTEGER DEFAULT NULL REFERENCES commentaires(id) ON UPDATE CASCADE,
  id_response INT DEFAULT NULL REFERENCES commentaires(id) ON DELETE CASCADE,
  created_date TIMESTAMP NOT NULL,
	comment_text TEXT NOT NULL CHECK (length(comment_text) > 0 AND length(comment_text) < 200)
);

CREATE TABLE vote (
	id SERIAL PRIMARY KEY,
	id_post INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
	id_publication INT NOT NULL REFERENCES publications(id) ON DELETE CASCADE,
	aesthetic INT NOT NULL CHECK ((aesthetic >= 0) AND (aesthetic <= 100)),
	technique INT NOT NULL CHECK ((technique >= 0) AND (technique <= 100)),
	creativity INT NOT NULL CHECK ((creativity >= 0) AND (creativity <= 100)),
	rate DECIMAL NOT NULL CHECK ((rate >= 0.0) AND (rate <= 5.0)),
  created_date TIMESTAMP NOT NULL 
);

CREATE TABLE group_users (
	id SERIAL PRIMARY KEY,
	name TEXT NOT NULL CHECK (length(name) <= 30),
	creation_date TIMESTAMP NOT NULL CHECK (creation_date <= now()),
	description TEXT NOT NULL CHECK (length(description) <= 200),
	max_participants INT NOT NULL CHECK ((max_participants > 1) AND (max_participants <= 100)),
  image TEXT,
  public BOOL NOT NULL DEFAULT TRUE

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
   id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE ON DELETE CASCADE,
   id_comment INT NOT NULL REFERENCES commentaires(id) ON UPDATE CASCADE ON DELETE CASCADE,
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
    id_group INT REFERENCES group_users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_challenge INT REFERENCES challenge(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (id_group, id_challenge)
);

CREATE TABLE challenge_participants (
	id_user INT REFERENCES users(id) ON UPDATE CASCADE,
	id_challenge INT REFERENCES challenge(id) ON UPDATE CASCADE,
	PRIMARY KEY (id_user, id_challenge)
);

CREATE TABLE reports (
  id SERIAL PRIMARY KEY,              
  id_user INTEGER NOT NULL,           
  reportable_id INTEGER NOT NULL,     
  reportable_type VARCHAR(255) NOT NULL CHECK (reportable_type IN ('user', 'post','group')),
  description TEXT NOT NULL,           
  created_at TIMESTAMP NOT NULL CHECK (created_at <= now()),  
  updated_at TIMESTAMP NOT NULL CHECK (updated_at <= now()),  
  FOREIGN KEY (id_user) REFERENCES users(id) 
);

CREATE TABLE admin_notification (
   id INT PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
   id_admin INT NOT NULL REFERENCES users(id) ON UPDATE CASCADE,
   id_report INT NOT NULL REFERENCES reports(id) ON UPDATE CASCADE,
   notification_type admin_notification_types NOT NULL
);


CREATE TABLE followers (
  id SERIAL PRIMARY KEY,
  user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  follower_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
    id SERIAL PRIMARY KEY,
    id_group INT REFERENCES group_users(id) ON DELETE CASCADE,
    id_user INT REFERENCES users(id) ON DELETE CASCADE,
    content TEXT NOT NULL CHECK (length(content) <= 500),
    created_at TIMESTAMP NOT NULL CHECK (created_at <= now())
);

CREATE TABLE faq (
  id SERIAL PRIMARY KEY,
  id_user INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  question TEXT NOT NULL CHECK (length(question) <= 200),
  answer TEXT DEFAULT NULL CHECK (length(answer) <= 500),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE like_comments (
  id SERIAL PRIMARY KEY,  
  id_post INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
  id_user INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  id_comment INT NOT NULL REFERENCES commentaires(id) ON DELETE CASCADE,
  UNIQUE(id_user, id_comment)  
);

CREATE TABLE invite_member (
  id SERIAL PRIMARY KEY,
  id_group INT NOT NULL REFERENCES group_users(id) ON DELETE CASCADE,
  id_user INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  invited_at TIMESTAMP NOT NULL CHECK (invited_at <= now()),
  UNIQUE(id_group, id_user)
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

IF EXISTS (SELECT * FROM challenge WHERE NEW.id_challenge = challenge.id)

AND EXISTS (SELECT * FROM group_challenge, challenge WHERE challenge.id = group_challenge.id_challenge)

AND NOT EXISTS (SELECT * FROM group_challenge, group_member WHERE NEW.id_challenge = group_challenge.id_challenge AND NEW.id_user = group_member.id_user AND group_challenge.id_group = group_member.id_group) THEN

RAISE EXCEPTION 'A user can only participate in group challenges if he belongs to that group.';

END IF;

RETURN NEW;

RETURN NEW;

END

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_challenge_group_participation
BEFORE INSERT OR UPDATE ON challenge_participants
FOR EACH ROW
EXECUTE PROCEDURE verify_challenge_group_participation();

CREATE OR REPLACE FUNCTION delete_mainnotification_action()
RETURNS TRIGGER AS
$BODY$
BEGIN
    -- Exclui a notificação correspondente na tabela 'notification'
    DELETE FROM notification WHERE id = OLD.id;

    -- Retorna o registro excluído (OLD) para confirmar a ação do trigger
    RETURN OLD;
END;
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


INSERT INTO users (name, username, email, password, birthdate, description, public, points, ranking, profile_picture)
VALUES
('Ricardo Cardoso', 'ricardo', 'up202108786@up.pt', 'Ricardo33!', '2003-04-29', 'A creative photographer with a passion for landscapes.', TRUE, 350, 16, 'ricardo.jpg'),
('Margarida Fonseca', 'margarida', 'up202207742@up.pt', 'Margarida33!', '2004-07-29', 'A creative photographer with a passion for parties.', TRUE, 350, 17, 'margarida.jpg'),
('Martim Campos', 'martim', 'up202108739@up.pt', 'Martim33!', '2003-01-29', 'A creative photographer with a passion for mountains.', TRUE, 350, 18, 'martim.jpg'),
('Hugo Cruz', 'hugo', 'up202205022@up.pt', 'Hugos33!', '2004-02-29', 'A creative photographer with a passion for beaches.', TRUE, 350, 19, 'hugo.jpg'),
('Ricardo Silva', 'ricardo45', 'ricardo45@gmail.com', 'Ricardo45!', '2003-04-29', 'A creative photographer', TRUE, 250, 1, 'ricardo45.jpg'),
('Ana Costa', 'anacosta', 'ana_costa@gmail.com', 'AnaCosta99!', '1999-08-15', 'Loves photography and nature exploration.', TRUE, 320, 2, 'ana.jpg'),
('João Pereira', 'joao123', 'joao123@gmail.com', 'Joao123@!', '2000-01-10', 'Photography enthusiast with a keen eye for details.', FALSE, 150, 3, 'joao.jpg'),
('Sofia Gomes', 'sofiag', 'sofia_gomes@gmail.com', 'SofiaG98!', '1998-12-01', 'Passionate about portrait photography.', TRUE, 180, 4, 'sofia.jpg'),
('Carlos Mendes', 'carlosm', 'carlos_mendes@gmail.com', 'CarlosM77!', '1997-06-21', 'Urban photography lover.', TRUE, 210, 5, 'carlos.jpg'),
('Mariana Lopes', 'marianaL', 'mariana_lopes@gmail.com', 'MarianaL00!', '2000-09-18', 'Loves nature and wildlife photography.', TRUE, 190, 6, 'mariana.jpg'),
('Pedro Nogueira', 'pedron', 'pedro_nogueira@gmail.com', 'PedroN22!', '1995-04-10', 'Creative in capturing candid moments.', TRUE, 200, 7, 'pedro.jpg'),
('Clara Moreira', 'claraM', 'clara_moreira@gmail.com', 'ClaraM88!', '2002-11-25', 'Enjoys street and travel photography.', TRUE, 175, 8, 'clara.jpg'),
('André Dias', 'andred', 'andre_dias@gmail.com', 'AndreD33!', '2001-02-14', 'Nature photographer with a focus on landscapes.', TRUE, 230, 9, 'andre.jpg'),
('Laura Rodrigues', 'laurar', 'laura_rodrigues@gmail.com', 'LauraR99!', '1999-07-05', 'Wildlife and animal photography enthusiast.', TRUE, 195, 10, 'laura.jpg'),
('Tiago Martins', 'tiagom', 'tiago_martins@gmail.com', 'TiagoM11!', '1996-10-17', 'Urban explorer and architecture photographer.', TRUE, 170, 11, 'tiago.jpg'),
('Beatriz Silva', 'beatrizs', 'beatriz_silva@gmail.com', 'BeatrizS44!', '1998-05-30', 'Loves capturing nature and animals.', TRUE, 220, 12, 'beatriz.jpg'),
('Hugo Fernandes', 'hugof', 'hugo_fernandes@gmail.com', 'HugoF66!', '2003-03-03', 'Aspiring photographer focused on landscapes.', TRUE, 160, 13, 'hugo.jpg'),
('Inês Santos', 'iness', 'ines_santos@gmail.com', 'InesS77!', '2001-08-19', 'Portrait photographer with a unique style.', TRUE, 240, 14, 'ines.jpg'),
('Luis Almeida', 'luisa', 'luis_almeida@gmail.com', 'LuisA55!', '1997-12-12', 'Specializes in black and white photography.', TRUE, 210, 15, 'luis.jpg');


INSERT INTO post (id_poster, edited)
VALUES
(1, FALSE), 
(2, TRUE),  
(3, FALSE),  
(4, TRUE),   
(5, FALSE),  
(6, FALSE), 
(7, FALSE),  
(8, TRUE),  
(9, FALSE), 
(10, TRUE),   
(11, FALSE),  
(12, FALSE), 
(13, FALSE), 
(14, TRUE),  
(15, FALSE),  
(1, TRUE),   
(2, FALSE),  
(3, FALSE), 
(4, FALSE),  
(5, TRUE),  
(6, FALSE), 
(7, TRUE),   
(8, FALSE),  
(9, FALSE), 
(10, TRUE),
(11, TRUE),   
(12, FALSE),  
(13, FALSE), 
(14, TRUE),
(15, FALSE), 
(1, FALSE),  
(2, TRUE),  
(3, FALSE), 
(4, TRUE),   
(5, FALSE),  
(6, FALSE), 
(7, TRUE),
(8, TRUE),   
(9, FALSE),  
(10, FALSE), 
(12, TRUE);

INSERT INTO challenge (id_creator, name, description, begin_date, end_date, max_participants, private)
VALUES
(1,'Portrait Challenge', 'Take the best portrait shots that showcase emotions and expressions. The focus is on people and their personalities.', '2024-03-01', '2025-03-01', 30, FALSE),
(1,'Macro Photography', 'Explore the world of small subjects through macro photography. Insects, plants, and textures are the focus.', '2024-04-01', '2025-04-01', 50, FALSE),
(1,'Abstract Photography', 'Showcase your creativity by capturing abstract images that leave room for interpretation. Use shapes, colors, and patterns.', '2024-05-01', '2025-05-01', 45, FALSE),
(1,'Landscape Photography', 'Capture stunning landscapes, from mountains to beaches, and everything in between.', '2024-06-01', '2025-06-01', 80, FALSE),
(1,'Night Photography', 'Capture the beauty of the night, including city lights, starry skies, and nighttime landscapes.', '2024-07-01', '2025-07-01', 35, FALSE),
(1,'Underwater Photography', 'Explore the underwater world through your lens. Capture marine life, corals, and underwater landscapes.', '2024-08-01', '2025-08-01', 25, FALSE),
(1,'Architectural Photography', 'Focus on capturing stunning architectural designs, whether modern skyscrapers or historic buildings.', '2024-09-01', '2025-09-01', 50, FALSE),
(1,'Action Sports Photography', 'Capture the energy and motion of action sports such as surfing, skating, and mountain biking.', '2024-10-01', '2025-10-01', 40, FALSE);


INSERT INTO publications (id_post, id_challenge, pub_image, ranking, created_date, description)
VALUES
(1, 1, 'bird.png', 4.5, '2023-01-12 20:01:20', 'Captured a bird in flight at sunrise.'),
(2, 1, 'deer.jpeg', 4.6, '2024-08-12 12:51:34', 'Close-up of a deer in the forest.'),
(3, 1, 'candid.jpg', 4.7, '2024-11-02 07:21:56', 'A candid street moment during rush hour.'),
(4, NULL, 'performer.jpg', 4.8, '2023-12-12 10:21:00', 'Street performers adding life to the city.'),
(5, 3, 'emotions.jpg', 4.9, '2024-10-30 18:31:09', 'Portrait of a young woman with strong emotions.'),
(6, 4, 'elderly.jpg', 4.7, '2024-09-14 23:41:22', 'Street portrait of an elderly man.'),
(7, 5, 'petal.jpg', 4.4, '2024-02-25 04:08:59', 'Macro shot of a dew drop on a flower petal.'),
(8, NULL, 'dragonfly.jpg', 4.5, '2024-06-07 16:27:20', 'Close-up of a dragonfly on a leaf.'),
(9, 6, 'lights.jpg', 4.6, '2024-05-24 22:39:45', 'Abstract patterns created by city lights.'),
(10, 7, 'shadow.jpeg', 4.3, '2023-07-02 17:11:49', 'Shadows and reflections on a rainy day.'),
(11, 8, 'sunset.jpg', 4.8, '2023-11-09 19:45:00', 'A sweeping mountain view during sunset.'),
(12, 6, 'beach.jpeg', 4.9, '2024-01-29 15:23:56', 'The calm of a beach at dawn.');


INSERT INTO commentaires (id_post, id_publication, previous, created_date, comment_text)
VALUES
(13, 2, NULL, NOW(), 'Impressive photo, Ricardo!'),  
(14, 3, NULL, NOW(), 'Great job!'),  
(15, 4, NULL, NOW(), 'Nice work on the edit, Ricardo!'),  
(16, 5, NULL, NOW(), 'Ana, your work is fantastic!'),  
(17, 6, NULL, NOW(), 'Amazing composition, Ana!'), 
(18, 7, NULL, NOW(), 'João, love the detail!'), 
(19, 8, NULL, NOW(), 'Beautiful shot, Sofia!'),  
(20, 9, NULL, NOW(), 'Great colors, Carlos!'),  
(21, 10, NULL, NOW(), 'The second post is even better!'), 
(22, 11, NULL, NOW(), 'Nice work, Mariana!'),  
(23, 12, NULL, NOW(), 'Pedro, that was really well done!'),  
(24, 2, NULL, NOW(), 'Clara, excellent shot!'),  
(25, 3, NULL, NOW(), 'André, this is awesome!'), 
(26, 4, NULL, NOW(), 'Laura, great job!'), 
(27, 5, NULL, NOW(), 'Love your second post, Laura!'), 
(28, 6, NULL, NOW(), 'Great work, Mariana!'), 
(29, 7, NULL, NOW(), 'The landscape is stunning, Ricardo!'); 


INSERT INTO vote (id_post, id_publication, aesthetic, technique, creativity, rate, created_date)
VALUES
(30, 1, 85, 88, 90, 4.5, NOW()),
(31, 2, 90, 85, 87, 4.6, NOW()),
(32, 3, 80, 78, 82, 4.7, NOW()),
(33, 4, 83, 80, 85, 4.8, NOW()),
(34, 5, 92, 90, 95, 4.9, NOW()),
(35, 6, 88, 85, 87, 4.7, NOW()),
(36, 7, 75, 80, 77, 4.4, NOW()),
(37, 8, 78, 76, 80, 4.5, NOW()),
(38, 9, 80, 85, 83, 4.6, NOW()),
(39, 10, 70, 72, 68, 4.3, NOW()),
(40, 11, 95, 92, 90, 4.8, NOW()),
(41, 12, 98, 94, 96, 4.9, NOW());


INSERT INTO group_users (name, creation_date, description, max_participants, public)
VALUES
('Wildlife Enthusiasts', '2023-01-12', 'Grupo para amantes de fotografia de vida selvagem.', 10, TRUE),
('Street Photographers', '2023-02-18', 'Focado em capturar momentos urbanos únicos.', 8, TRUE),
('Portrait Artists', '2023-03-22', 'Especialistas em fotografia de retratos.', 12, TRUE),
('Macro Masters', '2023-04-05', 'Explorando o mundo através da fotografia macro.', 6, FALSE),
('Abstract Visionaries', '2023-05-15', 'Criadores de imagens abstratas e criativas.', 9, FALSE),
('Landscape Dreamers', '2023-06-10', 'Grupo para capturar a beleza das paisagens.', 7, TRUE),
('Nature and Wildlife', '2023-07-01', 'Amantes da natureza e da vida selvagem.', 11, FALSE),
('Creative Explorers', '2023-08-22', 'Explorando novas técnicas fotográficas.', 5, FALSE);


INSERT INTO group_owner (id_user, id_group)
VALUES
(1, 1),  
(3, 2),  
(5, 3), 
(7, 4),  
(9, 5),  
(11, 6), 
(13, 7), 
(15, 8);


INSERT INTO group_member (id_user, id_group)
VALUES
(4, 1), 
(2, 1),  
(14, 2),  
(4, 2),  
(1, 3),  
(6, 3),
(5, 4),  
(8, 4),  
(7, 5),  
(10, 5), 
(9, 6), 
(12, 6), 
(11, 7), 
(14, 7),
(13, 8); 


INSERT INTO administrator (id_user)
VALUES
(1),  
(2),
(3),
(4);



DROP TRIGGER IF EXISTS challenge_group_owner ON group_challenge;
DROP FUNCTION IF EXISTS challenge_group_owner();
