import csv

def create_tables(file):
    file.write("""DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS users;\n""")

    file.write("""CREATE TABLE movies (
        id INTEGER PRIMARY KEY,
        title TEXT,
        genres TEXT
    );\n""")

    file.write("""CREATE TABLE ratings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        movie_id INTEGER,
        rating REAL,
        timestamp INTEGER
    );\n""")

    file.write("""CREATE TABLE tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        movie_id INTEGER,
        tag TEXT,
        timestamp INTEGER
    );\n""")

    file.write("""CREATE TABLE users (
        id INTEGER PRIMARY KEY,
        name TEXT,
        email TEXT,
        gender TEXT,
        register_date TEXT,
        occupation TEXT
    );\n""")

def insert_movies(file):
    path = "dataset/movies.csv"
    rows = []
    with open(path, encoding="utf-8") as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            movie_id = row["movieId"]
            title = row["title"].replace("'", "''")
            genres = row["genres"].replace("'", "''")
            rows.append(f"({movie_id}, '{title}', '{genres}')")
    if rows:
        file.write(f"INSERT INTO movies (id, title, genres) VALUES {', '.join(rows)};\n\n")

def insert_ratings(file):
    path = "dataset/ratings.csv"
    rows = []
    with open(path, encoding="utf-8") as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            rows.append(f"({row['userId']}, {row['movieId']}, {row['rating']}, {row['timestamp']})")
    if rows:
        file.write(f"INSERT INTO ratings (user_id, movie_id, rating, timestamp) VALUES {', '.join(rows)};\n\n")

def insert_tags(file):
    path = "dataset/tags.csv"
    rows = []
    with open(path, encoding="utf-8") as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            tag = row["tag"].replace("'", "''")
            rows.append(f"({row['userId']}, {row['movieId']}, '{tag}', {row['timestamp']})")
    if rows:
        file.write(f"INSERT INTO tags (user_id, movie_id, tag, timestamp) VALUES {', '.join(rows)};\n\n")

def insert_users(file):
    path = "dataset/users.txt"
    rows = []
    with open(path, encoding="utf-8") as file_data:
        for line in file_data:
            line = line.strip()
            if not line:
                continue
            index, name, email, gender, reg_date, occupation = line.split("|")
            name = name.replace("'", "''")
            email = email.replace("'", "''")
            occupation = occupation.replace("'", "''")
            rows.append(f"({index}, '{name}', '{email}', '{gender}', '{reg_date}', '{occupation}')")
    if rows:
        file.write(f"INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES {', '.join(rows)};\n\n")

with open("db_init.sql", "w", encoding="utf-8") as f:
    create_tables(f)
    insert_movies(f)
    insert_ratings(f)
    insert_tags(f)
    insert_users(f)
