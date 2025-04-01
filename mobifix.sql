-- Step 1: Create the database
CREATE DATABASE mobifix;

-- Step 2: Select the database to use
USE mobifix;

-- Step 3: Create the tables

CREATE TABLE "User" (
    user_id       UUID PRIMARY KEY, 
    first_name    VARCHAR(50) NOT NULL,
    last_name     VARCHAR(50) NOT NULL,
    email         VARCHAR(100) UNIQUE NOT NULL,
    phone         VARCHAR(15) UNIQUE NOT NULL,
    password      VARCHAR(255) NOT NULL,
    salt          VARCHAR(255) NOT NULL, -- Added 'salt' for password security
    user_type     VARCHAR(50) NOT NULL, 
    status        VARCHAR(20) NOT NULL, 
    street_name   VARCHAR(100),
    house_number  VARCHAR(10),
    city          VARCHAR(50),
    postal_code   VARCHAR(10),
    referred_by   UUID,
    FOREIGN KEY (referred_by) REFERENCES "User"(user_id) ON DELETE SET NULL
);

CREATE TABLE Customer (
    user_id                 UUID PRIMARY KEY REFERENCES "User"(user_id) ON DELETE CASCADE,
    preferred_contact_method VARCHAR(50),
    loyalty_points           INT DEFAULT 0
);

CREATE TABLE Employee (
    user_id      UUID PRIMARY KEY REFERENCES "User"(user_id) ON DELETE CASCADE,
    job_title    VARCHAR(50) NOT NULL,
    salary       DECIMAL(10,2) NOT NULL,
    hire_date    DATE NOT NULL,
    shift        VARCHAR(50) NOT NULL,
    role         VARCHAR(50) NOT NULL
);

CREATE TABLE Repair_Appointment (
    appointment_id  UUID PRIMARY KEY,
    customer_id     UUID NOT NULL,
    employee_id     UUID NOT NULL,
    method_id       UUID NOT NULL,
    date_time       TIMESTAMP NOT NULL,
    status          VARCHAR(20) NOT NULL,
    total_price     DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES Customer(user_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES Employee(user_id) ON DELETE SET NULL,
    FOREIGN KEY (method_id) REFERENCES Service_Method(method_id) ON DELETE SET NULL
);

CREATE TABLE Repair_Service (
    service_id     UUID PRIMARY KEY,
    service_name   VARCHAR(100) NOT NULL,
    description    TEXT,
    price          DECIMAL(10,2) NOT NULL,
    time_taken     INT NOT NULL
);

CREATE TABLE Repair_Service_Appointment (
    service_id     UUID NOT NULL,
    appointment_id UUID NOT NULL,
    PRIMARY KEY (service_id, appointment_id),
    FOREIGN KEY (service_id) REFERENCES Repair_Service(service_id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES Repair_Appointment(appointment_id) ON DELETE CASCADE
);

CREATE TABLE Service_Method (
    method_id      UUID PRIMARY KEY,
    method_name    VARCHAR(50) NOT NULL,
    estimated_time INT NOT NULL,
    cost           DECIMAL(10,2) NOT NULL,
    note           TEXT
);

CREATE TABLE Payment (
    appointment_id   UUID NOT NULL,
    payment_number   UUID NOT NULL,
    amount           DECIMAL(10,2) NOT NULL,
    payment_status   VARCHAR(10) NOT NULL,
    payment_method   VARCHAR(50) NOT NULL,
    payment_date_time TIMESTAMP NOT NULL,
    PRIMARY KEY (appointment_id, payment_number),
    FOREIGN KEY (appointment_id) REFERENCES Repair_Appointment(appointment_id) ON DELETE CASCADE
);

CREATE TABLE Device_Type (
    device_type_id UUID PRIMARY KEY,
    type_name      VARCHAR(100) NOT NULL,
    description    TEXT
);

CREATE TABLE Brand (
    brand_id       UUID PRIMARY KEY,
    brand_name     VARCHAR(100) NOT NULL,
    country        VARCHAR(50),
    founded_year   INT
);

CREATE TABLE Device_Type_Brand (
    device_type_id UUID NOT NULL,
    brand_id       UUID NOT NULL,
    PRIMARY KEY (device_type_id, brand_id),
    FOREIGN KEY (device_type_id) REFERENCES Device_Type(device_type_id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES Brand(brand_id) ON DELETE CASCADE
);
