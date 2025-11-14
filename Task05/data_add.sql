INSERT INTO users (surname, name, email, gender, register_date, occupation)
VALUES (
    'Шеволаев',
    'Илья',
    'ilya.shevolaev@mail.com',
    'Мужской',
    DATE('now'),
    'Студент'
);

INSERT INTO users (surname, name, email, gender, register_date, occupation)
VALUES (
    'Ямбаев',
    'Константин',
    'yambaev.kostya@mail.com',
    'Мужской',
    DATE('now'),
    'Студент'
);

INSERT INTO users (surname, name, email, gender, register_date, occupation)
VALUES (
    'Ястребцев',
    'Денис',
    'denis.yastrebtsey@mail.com',
    'Мужской',
    DATE('now'),
    'Студент'
);

INSERT INTO users (surname, name, email, gender, register_date, occupation)
VALUES (
    'Шагилов',
    'Кирилл',
    'kirill.shagilov@mail.com',
    'Мужской',
    DATE('now'),
    'Студент'
);

INSERT INTO users (surname, name, email, gender, register_date, occupation)
VALUES (
    'Тумайкина',
    'Дарья',
    'tymaikina.daria@mail.com',
    'Женский',
    DATE('now'),
    'Студент'
);

INSERT INTO movies (title, release_year)
VALUES (
    'Зеленая миля (1999)',
    1999
);

INSERT INTO movies (title, release_year)
VALUES (
    'Джон Уик (2014)',
    2014
);

INSERT INTO movies (title, release_year)
VALUES (
    'Один дома (1990)',
    1990
);


INSERT INTO movie_genres (movie_id, genre_id)
SELECT 
    (SELECT id FROM movies WHERE title = 'Зеленая миля (1999)'),
    (SELECT id FROM genres WHERE name = 'Drama')
WHERE NOT EXISTS (
    SELECT 1 FROM movie_genres 
    WHERE movie_id = (SELECT id FROM movies WHERE title = 'Зеленая миля (1999)')
    AND genre_id = (SELECT id FROM genres WHERE name = 'Drama')
);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT 
    (SELECT id FROM movies WHERE title = 'Джон Уик (2014)'),
    (SELECT id FROM genres WHERE name = 'Action')
WHERE NOT EXISTS (
    SELECT 1 FROM movie_genres 
    WHERE movie_id = (SELECT id FROM movies WHERE title = 'Джон Уик (2014)')
    AND genre_id = (SELECT id FROM genres WHERE name = 'Action')
);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT 
    (SELECT id FROM movies WHERE title = 'Джон Уик (2014)'),
    (SELECT id FROM genres WHERE name = 'Thriller')
WHERE NOT EXISTS (
    SELECT 1 FROM movie_genres 
    WHERE movie_id = (SELECT id FROM movies WHERE title = 'Джон Уик (2014)')
    AND genre_id = (SELECT id FROM genres WHERE name = 'Thriller')
);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT 
    (SELECT id FROM movies WHERE title = 'Один дома (1990)'),
    (SELECT id FROM genres WHERE name = 'Children')
WHERE NOT EXISTS (
    SELECT 1 FROM movie_genres 
    WHERE movie_id = (SELECT id FROM movies WHERE title = 'Один дома (1990)')
    AND genre_id = (SELECT id FROM genres WHERE name = 'Children')
);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT 
    (SELECT id FROM movies WHERE title = 'Один дома (1990)'),
    (SELECT id FROM genres WHERE name = 'Comedy')
WHERE NOT EXISTS (
    SELECT 1 FROM movie_genres 
    WHERE movie_id = (SELECT id FROM movies WHERE title = 'Один дома (1990)')
    AND genre_id = (SELECT id FROM genres WHERE name = 'Comedy')
);


INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'ilya.shevolaev@mail.com'),
    (SELECT id FROM movies WHERE title = 'Зеленая миля (1999)'),
    5.0,
    CAST(STRFTIME('%s', 'now') AS INTEGER)
WHERE NOT EXISTS (
    SELECT 1 FROM ratings 
    WHERE user_id = (SELECT id FROM users WHERE email = 'ilya.shevolaev@mail.com')
    AND movie_id = (SELECT id FROM movies WHERE title = 'Зеленая миля (1999)')
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'ilya.shevolaev@mail.com'),
    (SELECT id FROM movies WHERE title = 'Джон Уик (2014)'),
    4.0,
    CAST(STRFTIME('%s', 'now') AS INTEGER)
WHERE NOT EXISTS (
    SELECT 1 FROM ratings 
    WHERE user_id = (SELECT id FROM users WHERE email = 'ilya.shevolaev@mail.com')
    AND movie_id = (SELECT id FROM movies WHERE title = 'Джон Уик (2014)')
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'ilya.shevolaev@mail.com'),
    (SELECT id FROM movies WHERE title = 'Один дома (1990)'),
    3.0,
    CAST(STRFTIME('%s', 'now') AS INTEGER)
WHERE NOT EXISTS (
    SELECT 1 FROM ratings 
    WHERE user_id = (SELECT id FROM users WHERE email = 'ilya.shevolaev@mail.com')
    AND movie_id = (SELECT id FROM movies WHERE title = 'Один дома (1990)')
);