-- Create the `app_user` table
CREATE TABLE app_user (
    user_id       CHAR(36) PRIMARY KEY, 
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
    referred_by   CHAR(36),
    FOREIGN KEY (referred_by) REFERENCES app_user(user_id) ON DELETE SET NULL
);

-- Create the `customer` table
CREATE TABLE customer (
    user_id CHAR(36) PRIMARY KEY REFERENCES app_user(user_id) ON DELETE CASCADE,
    preferred_contact_method VARCHAR(50),
    loyalty_points INT DEFAULT 0
);

-- Create the `employee` table
CREATE TABLE employee (
    user_id CHAR(36) PRIMARY KEY REFERENCES app_user(user_id) ON DELETE CASCADE,
    job_title VARCHAR(50) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    hire_date DATE NOT NULL,
    shift VARCHAR(50) NOT NULL,
    role VARCHAR(50) NOT NULL
);

-- Create the `service_method` table
CREATE TABLE service_method (
    method_id CHAR(36) PRIMARY KEY,
    method_name VARCHAR(50) NOT NULL,
    estimated_time INT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    note TEXT
);

-- Create the `repair_appointment` table
CREATE TABLE repair_appointment (
    appointment_id CHAR(36) PRIMARY KEY,
    customer_id CHAR(36) NULL,
    employee_id CHAR(36) NULL,  -- Make employee_id nullable
    method_id CHAR(36) NULL,     -- Make method_id nullable
    date_time TIMESTAMP NOT NULL,
    status VARCHAR(20) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customer(user_id) ON DELETE SET NULL,
    FOREIGN KEY (employee_id) REFERENCES employee(user_id) ON DELETE SET NULL,
    FOREIGN KEY (method_id) REFERENCES service_method(method_id) ON DELETE SET NULL
);

-- Create the `brand` table
CREATE TABLE brand (
    brand_id CHAR(36) PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    country VARCHAR(50),
    founded_year INT
);

-- Create the `device_type` table
CREATE TABLE device_type (
    device_type_id CHAR(36) PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Create the `device_type_brand` table
CREATE TABLE device_type_brand (
    device_type_id CHAR(36) NOT NULL,
    brand_id CHAR(36) NOT NULL,
    PRIMARY KEY (device_type_id, brand_id),
    FOREIGN KEY (device_type_id) REFERENCES device_type(device_type_id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brand(brand_id) ON DELETE CASCADE
);

CREATE TABLE device_model (
    model_id CHAR(36) PRIMARY KEY,
    model_name VARCHAR(100) NOT NULL,
    release_year INT NOT NULL,
    brand_id CHAR(36) NOT NULL,
    FOREIGN KEY (brand_id) REFERENCES brand(brand_id) ON DELETE CASCADE
);

CREATE TABLE repair_service (
    service_id CHAR(36) PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    time_taken INT NOT NULL,
    model_id CHAR(36) NOT NULL,
    FOREIGN KEY (model_id) REFERENCES device_model(model_id) ON DELETE CASCADE
);


-- Create the `repair_service_appointment` table
CREATE TABLE repair_service_appointment (
    service_id CHAR(36) NOT NULL,
    appointment_id CHAR(36) NOT NULL,
    PRIMARY KEY (service_id, appointment_id),
    FOREIGN KEY (service_id) REFERENCES repair_service(service_id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES repair_appointment(appointment_id) ON DELETE CASCADE
);

-- Create the `payment` table
CREATE TABLE payment (
    appointment_id CHAR(36) NOT NULL,
    payment_number CHAR(36) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status VARCHAR(10) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_date_time TIMESTAMP NOT NULL,
    PRIMARY KEY (appointment_id, payment_number),
    FOREIGN KEY (appointment_id) REFERENCES repair_appointment(appointment_id) ON DELETE CASCADE
);