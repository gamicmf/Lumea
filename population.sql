INSERT INTO users (name, username, email, password, birthdate, description, public, points, ranking, profile_picture)
VALUES
('Ricardo Silva', 'ricardo45', 'ricardo45@gmail.com', '$2y$10$Gb64mZHqkUdZCKmBG.WkbuGFDH85.65nxzuCyzJ6Ek2pXajAPsmeK', '2003-04-29', 'A creative photographer with a passion for landscapes.', TRUE, 250, 1, 'ricardo.jpg'),
('Ana Costa', 'anacosta', 'ana_costa@gmail.com', '$2y$10$8qIsnuWgDDUWUBaz1SOFbOViCSTAw..xqmOqr/0zRhHh014QwUVB.', '1999-08-15', 'Loves photography and nature exploration.', TRUE, 320, 2, 'ana.jpg'),
('João Pereira', 'joao123', 'joao123@gmail.com', '$2y$10$TVOTKMOHaDsRvNrPm9ocsuI3bL2Zi0hT0wsz47HoYpE8T5tFo/pNO', '2000-01-10', 'Photography enthusiast with a keen eye for details.', TRUE, 150, 3, 'joao.jpg'),
('Sofia Gomes', 'sofiag', 'sofia_gomes@gmail.com', '$2y$10$nw96Gwt6BADEpc28OH8w8Ojql6tsl/VeWnKViZr4WwNhn7y8WQx5y', '1998-12-01', 'Passionate about portrait photography.', TRUE, 180, 4, 'sofia.jpg'),
('Carlos Mendes', 'carlosm', 'carlos_mendes@gmail.com', '$2y$10$FhrCnjN2mZGPGGBvhXei8.XK5GJ1UKejWKQgbSYTQpM9.a9BgCHN2', '1997-06-21', 'Urban photography lover.', TRUE, 210, 5, 'carlos.jpg'),
('Mariana Lopes', 'marianaL', 'mariana_lopes@gmail.com', '$2y$10$YEUrPuELRTNUP7CM3YYmHOv4hvpKo5WZ9.0l1fnXrMNdeUIDjdCL6', '2000-09-18', 'Loves nature and wildlife photography.', TRUE, 190, 6, 'mariana.jpg'),
('Pedro Nogueira', 'pedron', 'pedro_nogueira@gmail.com', '$2y$10$aNWaaV6CbPwdHCqWIXjgUOzTE7WjXIuL6eUX4JEwveWNJNCymdJP.', '1995-04-10', 'Creative in capturing candid moments.', TRUE, 200, 7, 'pedro.jpg'),
('Clara Moreira', 'claraM', 'clara_moreira@gmail.com', '$2y$10$4YNFAO0E139gccb3mrAia.zVJtByPDAiCatmPrXE11W8Mvw02m3N2', '2002-11-25', 'Enjoys street and travel photography.', TRUE, 175, 8, 'clara.jpg'),
('André Dias', 'andred', 'andre_dias@gmail.com', '$2y$10$goc1392Xclg0DVZCpzhUAuDwiqHNSwPJDrFdmTEaEX37gB/cCwBnm', '2001-02-14', 'Nature photographer with a focus on landscapes.', TRUE, 230, 9, 'andre.jpg'),
('Laura Rodrigues', 'laurar', 'laura_rodrigues@gmail.com', '$2y$10$BNBYjxbSee3fVREzVTSy0OU8i0mecTfXpQ59kLuwfDMclxYxfBdo2', '1999-07-05', 'Wildlife and animal photography enthusiast.', TRUE, 195, 10, 'laura.jpg'),
('Tiago Martins', 'tiagom', 'tiago_martins@gmail.com', '$2y$10$HIBhzUt2hVp76jcc7M.bZu9Y73wstWxdhQghCFHeOtFYwvVQhl82y', '1996-10-17', 'Urban explorer and architecture photographer.', TRUE, 170, 11, 'tiago.jpg'),
('Beatriz Silva', 'beatrizs', 'beatriz_silva@gmail.com', '$2y$10$ZmWMg/pcCR1cL/4.GKoabu1d5hAOpsxgdhGsPDed6hh/yuTCHv06q', '1998-05-30', 'Loves capturing nature and animals.', TRUE, 220, 12, 'beatriz.jpg'),
('Hugo Fernandes', 'hugof', 'hugo_fernandes@gmail.com', '$2y$10$CSzBDw88Npc9R6yzuu5Z0OixXrz4uOyjrpzYFTKXe.JNFYpRM1JT2', '2003-03-03', 'Aspiring photographer focused on landscapes.', TRUE, 160, 13, 'hugo.jpg'),
('Inês Santos', 'iness', 'ines_santos@gmail.com', '$2y$10$VGgx5srJcg5krI48aJ.efeI9/r.lsGljHbEb2..G83/VWHMk7F8dO', '2001-08-19', 'Portrait photographer with a unique style.', TRUE, 240, 14, 'ines.jpg'),
('Luis Almeida', 'luisa', 'luis_almeida@gmail.com', '$2y$10$4x5eSLCwSrUeQvoZx0FzU.5OytXOX9R8/kGttgJCZm.nB9kGp71NW', '1997-12-12', 'Specializes in black and white photography.', TRUE, 210, 15, 'luis.jpg');


INSERT INTO post (id_poster, date, edited)
VALUES
(1, NOW(), FALSE), 
(2, NOW(), TRUE),  
(3, NOW(), FALSE),  
(4, NOW(), TRUE),   
(5, NOW(), FALSE),  
(6, NOW(), FALSE), 
(7, NOW(), FALSE),  
(8, NOW(), TRUE),  
(9, NOW(), FALSE), 
(10, NOW(), TRUE),   
(11, NOW(), FALSE),  
(12, NOW(), FALSE), 
(13, NOW(), FALSE), 
(14, NOW(), TRUE),  
(15, NOW(), FALSE),  
(1, NOW(), TRUE),   
(2, NOW(), FALSE),  
(3, NOW(), FALSE), 
(4, NOW(), FALSE),  
(5, NOW(), TRUE),  
(6, NOW(), FALSE), 
(7, NOW(), TRUE),   
(8, NOW(), FALSE),  
(9, NOW(), FALSE), 
(10, NOW(), TRUE),
(11, NOW(), TRUE),   
(12, NOW(), FALSE),  
(13, NOW(), FALSE), 
(14, NOW(), TRUE),
(15, NOW(), FALSE), 
(1, NOW(), FALSE),  
(2, NOW(), TRUE),  
(3, NOW(), FALSE), 
(4, NOW(), TRUE),   
(5, NOW(), FALSE),  
(6, NOW(), FALSE), 
(7, NOW(), TRUE),
(8, NOW(), TRUE),   
(9, NOW(), FALSE),  
(10, NOW(), FALSE), 
(12, NOW(), TRUE);

INSERT INTO challenge (name, description, begin_date, end_date, max_participants)
VALUES
('Wildlife Photography', 'Capture the essence of wildlife in its natural habitat. Focus on animals, birds, and other creatures in the wild.', '2024-01-01', '2024-03-01', 60),
('Street Photography', 'Explore the streets and capture candid moments of urban life, focusing on people and architecture.', '2024-02-01', '2024-04-01', 40),
('Portrait Challenge', 'Take the best portrait shots that showcase emotions and expressions. The focus is on people and their personalities.', '2024-03-01', '2024-05-01', 30),
('Macro Photography', 'Explore the world of small subjects through macro photography. Insects, plants, and textures are the focus.', '2024-04-01', '2024-06-01', 50),
('Abstract Photography', 'Showcase your creativity by capturing abstract images that leave room for interpretation. Use shapes, colors, and patterns.', '2024-05-01', '2024-07-01', 45),
('Landscape Photography', 'Capture stunning landscapes, from mountains to beaches, and everything in between.', '2024-06-01', '2024-08-01', 80),
('Night Photography', 'Capture the beauty of the night, including city lights, starry skies, and nighttime landscapes.', '2024-07-01', '2024-09-01', 35),
('Underwater Photography', 'Explore the underwater world through your lens. Capture marine life, corals, and underwater landscapes.', '2024-08-01', '2024-10-01', 25),
('Architectural Photography', 'Focus on capturing stunning architectural designs, whether modern skyscrapers or historic buildings.', '2024-09-01', '2024-11-01', 50),
('Action Sports Photography', 'Capture the energy and motion of action sports such as surfing, skating, and mountain biking.', '2024-10-01', '2024-12-01', 40);


INSERT INTO publications (id_post, id_challenge, pub_image, ranking, description)
VALUES
(1, 1, 'wildlife1.jpg', 4.5, 'Captured a bird in flight at sunrise.'),
(2, 1, 'wildlife2.jpg', 4.6, 'Close-up of a deer in the forest.'),
(3, 1, 'street1.jpg', 4.7, 'A candid street moment during rush hour.'),
(4, NULL, 'street2.jpg', 4.8, 'Street performers adding life to the city.'),
(5, 3, 'portrait1.jpg', 4.9, 'Portrait of a young woman with strong emotions.'),
(6, 4, 'portrait2.jpg', 4.7, 'Street portrait of an elderly man.'),
(7, 5, 'macro1.jpg', 4.4, 'Macro shot of a dew drop on a flower petal.'),
(8, NULL, 'macro2.jpg', 4.5, 'Close-up of a dragonfly on a leaf.'),
(9, 6, 'abstract1.jpg', 4.6, 'Abstract patterns created by city lights.'),
(10, 7, 'abstract2.jpg', 4.3, 'Shadows and reflections on a rainy day.'),
(11, 8, 'landscape1.jpg', 4.8, 'A sweeping mountain view during sunset.'),
(12, 9, 'landscape2.jpg', 4.9, 'The calm of a beach at dawn.');


INSERT INTO commentaires (id_post, id_publication, previous, comment_text)
VALUES
(13, 2, 'Impressive photo, Ricardo!'),  
(14, 3, 'Great job!'),  
(15, 4, 'Nice work on the edit, Ricardo!'),  
(16, 5, 'Ana, your work is fantastic!'),  
(17, 6, 'Amazing composition, Ana!'), 
(18, 7, 'João, love the detail!'), 
(19, 8, NULL, 'Beautiful shot, Sofia!'),  
(20, 9, NULL, 'Great colors, Carlos!'),  
(21, 10, NULL, 'The second post is even better!'), 
(22, 11, NULL, 'Nice work, Mariana!'),  
(23, 12, NULL, 'Pedro, that was really well done!'),  
(24, 2, NULL, 'Clara, excellent shot!'),  
(25, 3, NULL, 'André, this is awesome!'), 
(26, 4, NULL, 'Laura, great job!'), 
(27, 5, NULL, 'Love your second post, Laura!'), 
(28, 6, NULL, 'Great work, Mariana!'), 
(29, 7, NULL, 'The landscape is stunning, Ricardo!'); 


INSERT INTO vote (id_post, id_publication, aesthetic, technique, creativity, rate)
VALUES
(30, 1, 85, 88, 90, 4.5),
(31, 2, 90, 85, 87, 4.6),
(32, 3, 80, 78, 82, 4.7),
(33, 4, 83, 80, 85, 4.8),
(34, 5, 92, 90, 95, 4.9),
(35, 6, 88, 85, 87, 4.7),
(36, 7, 75, 80, 77, 4.4),
(37, 8, 78, 76, 80, 4.5),
(38, 9, 80, 85, 83, 4.6),
(39, 10, 70, 72, 68, 4.3),
(40, 11, 95, 92, 90, 4.8),
(41, 12, 98, 94, 96, 4.9);


INSERT INTO group_users (name, creation_date, description, max_participants)
VALUES
('Wildlife Enthusiasts', '2023-01-12', 'Grupo para amantes de fotografia de vida selvagem.', 10),
('Street Photographers', '2023-02-18', 'Focado em capturar momentos urbanos únicos.', 8),
('Portrait Artists', '2023-03-22', 'Especialistas em fotografia de retratos.', 12),
('Macro Masters', '2023-04-05', 'Explorando o mundo através da fotografia macro.', 6),
('Abstract Visionaries', '2023-05-15', 'Criadores de imagens abstratas e criativas.', 9),
('Landscape Dreamers', '2023-06-10', 'Grupo para capturar a beleza das paisagens.', 7),
('Nature and Wildlife', '2023-07-01', 'Amantes da natureza e da vida selvagem.', 11),
('Creative Explorers', '2023-08-22', 'Explorando novas técnicas fotográficas.', 5);


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
(3), 
(5);