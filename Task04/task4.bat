#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo "1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они ценили. В списке оставить первые 100 записей."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT DISTINCT
    u1.name AS user1_name,
    u2.name AS user2_name,
    m.title AS movie_title
FROM ratings r1
JOIN ratings r2 ON r1.movie_id = r2.movie_id AND r1.user_id < r2.user_id
JOIN users u1 ON r1.user_id = u1.id
JOIN users u2 ON r2.user_id = u2.id
JOIN movies m ON r1.movie_id = m.id
LIMIT 100"
echo " "

echo "2. Найти 10 самых свежих оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT 
    m.title AS movie_title,
    u.name AS user_name,
    r.rating,
    DATE(r.timestamp, 'unixepoch') AS review_date
FROM ratings r
JOIN users u ON r.user_id = u.id
JOIN movies m ON r.movie_id = m.id
ORDER BY r.timestamp DESC
LIMIT 10"
echo " "

echo "3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке Рекомендуем для фильмов должно быть написано Да или Нет".
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH avg_ratings AS (
    SELECT 
        movie_id,
        AVG(rating) AS avg_rating
    FROM ratings
    GROUP BY movie_id
),
max_min_ratings AS (
    SELECT 
        MAX(avg_rating) AS max_rating,
        MIN(avg_rating) AS min_rating
    FROM avg_ratings
)
SELECT 
    m.title,
    SUBSTR(m.title, INSTR(m.title, '(') + 1, 4) AS year,
    ar.avg_rating,
    CASE 
        WHEN ar.avg_rating = (SELECT max_rating FROM max_min_ratings) THEN 'Да'
        ELSE 'Нет'
    END AS Рекомендуем
FROM movies m
JOIN avg_ratings ar ON m.id = ar.movie_id
CROSS JOIN max_min_ratings mmr
WHERE ar.avg_rating = mmr.max_rating OR ar.avg_rating = mmr.min_rating
ORDER BY year, m.title"
echo " "

echo "4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-женщины в период с 2010 по 2012 год."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT 
    COUNT(r.rating) AS total_ratings,
    AVG(r.rating) AS average_rating
FROM ratings r
JOIN users u ON r.user_id = u.id
WHERE u.gender = 'female'
    AND DATE(r.timestamp, 'unixepoch') BETWEEN '2010-01-01' AND '2012-12-31'"
echo " "

echo "5. Составить список фильмов с указанием их средней оценки и места в рейтинге по средней оценке. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH movie_ratings AS (
    SELECT 
        m.id,
        m.title,
        SUBSTR(m.title, INSTR(m.title, '(') + 1, 4) AS year,
        AVG(r.rating) AS avg_rating
    FROM movies m
    LEFT JOIN ratings r ON m.id = r.movie_id
    GROUP BY m.id, m.title
)
SELECT 
    title,
    avg_rating,
    RANK() OVER (ORDER BY avg_rating DESC) AS rating_rank
FROM movie_ratings
ORDER BY year, title
LIMIT 20"
echo " "

echo "6. Вывести список из 10 последних зарегистрированных пользователей в формате Фамилия Имя Дата регистрации (сначала фамилия, потом имя)".
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT 
    SUBSTR(name, INSTR(name, ' ') + 1) || ' ' || SUBSTR(name, 1, INSTR(name, ' ') - 1) AS full_name,
    register_date
FROM users
ORDER BY register_date DESC
LIMIT 10"
echo " "

echo "7. С помощью рекурсивного CTE составить таблицу умножения для чисел от 1 до 10."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE multiplication AS (
    SELECT 1 AS i, 1 AS j
    UNION ALL
    SELECT 
        CASE WHEN j = 10 THEN i + 1 ELSE i END,
        CASE WHEN j = 10 THEN 1 ELSE j + 1 END
    FROM multiplication
    WHERE i < 10 OR (i = 10 AND j < 10)
)
SELECT i || 'x' || j || '=' || (i * j) AS result
FROM multiplication
ORDER BY i, j"
echo " "

echo "8. С помощью рекурсивного CTE выделить все жанры фильмов, имеющиеся в таблице movies (каждый жанр в отдельной строке)."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE genre_split AS (
    SELECT 
        genres,
        1 AS pos,
        SUBSTR(genres, 1, INSTR(genres || '|', '|') - 1) AS genre
    FROM movies
    WHERE genres IS NOT NULL
    
    UNION ALL
    
    SELECT 
        genres,
        pos + 1,
        SUBSTR(genres, 
            INSTR(genres, '|', 1, pos) + 1,
            INSTR(genres || '|', '|', 1, pos + 1) - INSTR(genres, '|', 1, pos) - 1
        )
    FROM genre_split
    WHERE pos < LENGTH(genres) - LENGTH(REPLACE(genres, '|', '')) + 1
)
SELECT DISTINCT genre
FROM genre_split
WHERE genre IS NOT NULL AND genre != ''
ORDER BY genre"