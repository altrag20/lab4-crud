-- Таблиця вистав
CREATE TABLE performances (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    performance_date DATE,
    duration INTEGER
);

-- Таблиця акторів
CREATE TABLE actors (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    birth_date DATE,
    experience INTEGER
);

-- Таблиця ролей
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL,
    performance_id INTEGER NOT NULL,
    actor_id INTEGER NOT NULL,

    CONSTRAINT fk_role_performance
        FOREIGN KEY (performance_id)
        REFERENCES performances(id),

    CONSTRAINT fk_role_actor
        FOREIGN KEY (actor_id)
        REFERENCES actors(id)
);

-- Таблиця залів
CREATE TABLE halls (
    id SERIAL PRIMARY KEY,
    hall_name VARCHAR(100) NOT NULL,
    seats_count INTEGER NOT NULL
);

-- Таблиця місць
CREATE TABLE seats (
    id SERIAL PRIMARY KEY,
    row_number INTEGER NOT NULL,
    seat_number INTEGER NOT NULL,
    status VARCHAR(30),
    hall_id INTEGER NOT NULL,

    CONSTRAINT fk_seat_hall
        FOREIGN KEY (hall_id)
        REFERENCES halls(id)
);

-- Таблиця глядачів
CREATE TABLE viewers (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(100)
);

-- Таблиця квитків
CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    price NUMERIC(8,2),
    status VARCHAR(30),
    performance_id INTEGER NOT NULL,
    seat_id INTEGER NOT NULL,
    viewer_id INTEGER NOT NULL,

    CONSTRAINT fk_ticket_performance
        FOREIGN KEY (performance_id)
        REFERENCES performances(id),

    CONSTRAINT fk_ticket_seat
        FOREIGN KEY (seat_id)
        REFERENCES seats(id),

    CONSTRAINT fk_ticket_viewer
        FOREIGN KEY (viewer_id)
        REFERENCES viewers(id)
);

CREATE INDEX idx_ticket_viewer
ON tickets(viewer_id);

CREATE INDEX idx_ticket_performance
ON tickets(performance_id);

-- =====================================================================
-- Доповнення для ЛР №4 (обмеження унікальності для CRUD і перевірки дублів)
-- =====================================================================
ALTER TABLE halls   ADD CONSTRAINT uq_hall_name   UNIQUE (hall_name);
ALTER TABLE viewers ADD CONSTRAINT uq_viewer_email UNIQUE (email);
ALTER TABLE seats   ADD CONSTRAINT uq_seat_place   UNIQUE (hall_id, row_number, seat_number);
ALTER TABLE tickets ADD CONSTRAINT uq_ticket_place UNIQUE (performance_id, seat_id);
CREATE INDEX idx_seats_hall  ON seats(hall_id);
CREATE INDEX idx_roles_perf  ON roles(performance_id);
CREATE INDEX idx_roles_actor ON roles(actor_id);
CREATE INDEX idx_ticket_seat ON tickets(seat_id);
