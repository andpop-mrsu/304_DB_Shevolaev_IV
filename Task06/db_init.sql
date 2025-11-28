PRAGMA foreign_keys = ON;

CREATE TABLE specializations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    specialization_id INTEGER NOT NULL,
    salary_percent REAL NOT NULL CHECK(salary_percent > 0 AND salary_percent <= 100),
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0, 1)),
    hired_date TEXT NOT NULL,
    fired_date TEXT,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CHECK(fired_date IS NULL OR fired_date >= hired_date)
);

CREATE TABLE service_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price > 0),
    FOREIGN KEY (category_id) REFERENCES service_categories(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    appointment_date TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'scheduled' 
        CHECK(status IN ('scheduled', 'completed', 'cancelled')),
    created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
    FOREIGN KEY (employee_id) REFERENCES employees(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

CREATE TABLE completed_procedures (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL UNIQUE,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    completed_at TEXT NOT NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

INSERT INTO specializations (name) VALUES 
    ('Терапевт'),
    ('Хирург'),
    ('Ортодонт'),
    ('Имплантолог');

INSERT INTO employees (full_name, specialization_id, salary_percent, is_active, hired_date, fired_date) VALUES 
    ('Иванова Мария Петровна', 1, 40.00, 1, '2022-03-15', NULL),
    ('Смирнов Алексей Иванович', 2, 45.50, 1, '2021-06-01', NULL),
    ('Петров Дмитрий Сергеевич', 3, 42.00, 1, '2023-01-10', NULL),
    ('Сидорова Елена Викторовна', 4, 50.00, 0, '2020-05-20', '2024-11-01');

INSERT INTO service_categories (name) VALUES 
    ('Терапевтическая стоматология'),
    ('Хирургическая стоматология'),
    ('Ортодонтия'),
    ('Имплантация');

INSERT INTO services (category_id, name, duration_minutes, price) VALUES 
    (1, 'Лечение кариеса', 60, 3500.00),
    (1, 'Чистка зубов', 45, 2500.00),
    (1, 'Пломбирование канала', 90, 5000.00),
    (2, 'Удаление зуба простое', 30, 2000.00),
    (2, 'Удаление зуба сложное', 60, 4500.00),
    (3, 'Установка брекетов', 120, 35000.00),
    (3, 'Коррекция брекетов', 30, 1500.00),
    (4, 'Установка импланта', 180, 45000.00);

INSERT INTO appointments (employee_id, service_id, appointment_date, status) VALUES 
    (1, 1, '2025-11-29 10:00:00', 'scheduled'),
    (1, 2, '2025-11-29 14:00:00', 'scheduled'),
    (2, 4, '2025-11-28 09:00:00', 'completed'),
    (2, 5, '2025-11-28 11:00:00', 'completed'),
    (3, 6, '2025-11-27 15:00:00', 'completed'),
    (1, 3, '2025-11-26 10:00:00', 'completed'),
    (2, 4, '2025-11-30 16:00:00', 'scheduled'),
    (3, 7, '2025-11-25 13:00:00', 'cancelled');

INSERT INTO completed_procedures (appointment_id, employee_id, service_id, completed_at) VALUES 
    (3, 2, 4, '2025-11-28 09:25:00'),
    (4, 2, 5, '2025-11-28 11:50:00'),
    (5, 3, 6, '2025-11-27 17:30:00'),
    (6, 1, 3, '2025-11-26 11:20:00');
