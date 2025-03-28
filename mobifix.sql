-- Step 1: Create the database
CREATE DATABASE mobifix;

-- Step 2: Select the database to use
USE mobifix;

-- Step 3: Create the tables

CREATE TABLE "User" (
    user_id          UUID PRIMARY KEY, 
    first_name       VARCHAR(50) NOT NULL,
    last_name        VARCHAR(50) NOT NULL,
    email            VARCHAR(100) UNIQUE NOT NULL,
    phone            VARCHAR(15) UNIQUE NOT NULL,
    password         VARCHAR(255) NOT NULL,
    street_name      VARCHAR(100),
    house_number     VARCHAR(10),
    city             VARCHAR(50),
    postal_code      VARCHAR(10),
    referred_by      UUID,
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
    salary       DECIMAL(10,2) NOT NULL,  -- Removed CHECK constraint, handled in application
    hire_date    DATE NOT NULL,
    shift        VARCHAR(50) NOT NULL
);

CREATE TABLE Repair_Appointment (
    appointment_id  UUID PRIMARY KEY,  -- UUID should be generated by the application
    customer_id     UUID NOT NULL,
    employee_id     UUID NOT NULL,
    method_id       UUID NOT NULL,
    date_time       TIMESTAMP NOT NULL,
    status          VARCHAR(20) NOT NULL,  -- Validation for status handled in application
    total_price     DECIMAL(10,2) NOT NULL,  -- Validation for total_price handled in application
    FOREIGN KEY (customer_id) REFERENCES Customer(user_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES Employee(user_id) ON DELETE SET NULL,
    FOREIGN KEY (method_id) REFERENCES Service_Method(method_id) ON DELETE SET NULL
);

CREATE TABLE Repair_Service (
    service_id     UUID PRIMARY KEY,  -- UUID should be generated by the application
    service_name   VARCHAR(100) NOT NULL,
    description    TEXT,
    price          DECIMAL(10,2) NOT NULL,  -- Validation for price handled in application
    time_taken     INT NOT NULL  -- Validation for time_taken handled in application
);

CREATE TABLE Repair_Service_Appointment (
    service_id     UUID NOT NULL,
    appointment_id UUID NOT NULL,
    PRIMARY KEY (service_id, appointment_id),
    FOREIGN KEY (service_id) REFERENCES Repair_Service(service_id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES Repair_Appointment(appointment_id) ON DELETE CASCADE
);

CREATE TABLE Service_Method (
    method_id      UUID PRIMARY KEY,  -- UUID should be generated by the application
    method_name    VARCHAR(50) NOT NULL,
    estimated_time INT NOT NULL,  -- Validation for estimated_time handled in application
    cost           DECIMAL(10,2) NOT NULL,  -- Validation for cost handled in application
    note           TEXT
);
CREATE TABLE Payment (
    appointment_id  UUID NOT NULL,
    payment_number  UUID NOT NULL,  -- Payment number should be part of the composite primary key
    amount          DECIMAL(10,2) NOT NULL,
    payment_status  VARCHAR(10) NOT NULL,
    payment_method  VARCHAR(50) NOT NULL,
    payment_date_time TIMESTAMP NOT NULL,
    PRIMARY KEY (appointment_id, payment_number),  -- Composite primary key
    FOREIGN KEY (appointment_id) REFERENCES Repair_Appointment(appointment_id) ON DELETE CASCADE
);


CREATE TABLE Device_Type (
    device_type_id UUID PRIMARY KEY,  -- UUID should be generated by the application
    type_name      VARCHAR(100) NOT NULL,
    description    TEXT
);

CREATE TABLE Brand (
    brand_id       UUID PRIMARY KEY,  -- UUID should be generated by the application
    brand_name     VARCHAR(100) NOT NULL,
    country        VARCHAR(50),
    founded_year   INT  -- Validation for founded_year handled in application
);

CREATE TABLE Device_Type_Brand (
    device_type_id UUID NOT NULL,
    brand_id       UUID NOT NULL,
    PRIMARY KEY (device_type_id, brand_id),
    FOREIGN KEY (device_type_id) REFERENCES Device_Type(device_type_id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES Brand(brand_id) ON DELETE CASCADE
);
