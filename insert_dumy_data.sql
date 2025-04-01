
-- Insert into app_user table
INSERT INTO app_user (user_id, first_name, last_name, email, phone, password, salt, user_type, status, street_name, house_number, city, postal_code, referred_by)
VALUES 
    ('uuid_customer1', 'John', 'Doe', 'john.doe@example.com', '1234567890', 'hashed_password1', 'salt1', 'Customer', 'Active', 'Main St', '123', 'Vienna', '1010', NULL),
    ('uuid_customer2', 'Jane', 'Smith', 'jane.smith@example.com', '0987654321', 'hashed_password2', 'salt2', 'Customer', 'Active', 'Park Lane', '456', 'Vienna', '1020', NULL),
    ('uuid_customer3', 'Alice', 'Brown', 'alice.brown@example.com', '2223334444', 'hashed_password3', 'salt3', 'Customer', 'Active', 'Oak St', '789', 'Vienna', '1040', NULL),
    ('uuid_employee1', 'Emily', 'Taylor', 'emily.taylor@example.com', '1112223333', 'hashed_password4', 'salt4', 'Employee', 'Active', 'Highway Ave', '888', 'Vienna', '1010', NULL),
    ('uuid_employee2', 'James', 'Bond', 'james.bond@example.com', '7778889999', 'hashed_password5', 'salt5', 'Employee', 'Active', 'Station Rd', '999', 'Vienna', '1030', NULL),
    ('uuid_employee3', 'Sarah', 'Connor', 'sarah.connor@example.com', '4445556666', 'hashed_password6', 'salt6', 'Employee', 'Active', 'Central Ave', '444', 'Vienna', '1050', NULL);

-- Insert into customer table
INSERT INTO customer (user_id, preferred_contact_method, loyalty_points)
VALUES 
    ('uuid_customer1', 'Email', 100),
    ('uuid_customer2', 'Phone', 200),
    ('uuid_customer3', 'Email', 300);

-- Insert into employee table
INSERT INTO employee (user_id, job_title, salary, hire_date, shift, role)
VALUES 
    ('uuid_employee1', 'Technician', 3000.00, '2022-01-01', 'Morning', 'Repair Specialist'),
    ('uuid_employee2', 'Technician', 3200.00, '2021-06-15', 'Evening', 'Repair Specialist'),
    ('uuid_employee3', 'Manager', 5000.00, '2020-10-01', 'Day', 'Operations Lead');

-- Insert into service_method table
INSERT INTO service_method (method_id, method_name, estimated_time, cost, note)
VALUES 
    ('uuid_method1', 'On-Site Repair', 120, 50.00, 'Repair done at customer location'),
    ('uuid_method2', 'In-Store Repair', 90, 30.00, 'Repair performed at store'),
    ('uuid_method3', 'Pickup & Return', 180, 70.00, 'Device picked up and returned post-repair');

-- Insert into device_type table
INSERT INTO device_type (device_type_id, type_name, description)
VALUES 
    ('uuid_device_type1', 'Smartphone', 'Mobile phone with advanced features'),
    ('uuid_device_type2', 'Tablet', 'Portable touchscreen device'),
    ('uuid_device_type3', 'Laptop', 'Portable computer with a keyboard');

-- Insert into brand table
INSERT INTO brand (brand_id, brand_name, country, founded_year)
VALUES 
    ('uuid_brand1', 'Apple', 'USA', 1976),
    ('uuid_brand2', 'Samsung', 'South Korea', 1938),
    ('uuid_brand3', 'Dell', 'USA', 1984);

-- Insert into device_type_brand table
INSERT INTO device_type_brand (device_type_id, brand_id)
VALUES 
    ('uuid_device_type1', 'uuid_brand1'),
    ('uuid_device_type2', 'uuid_brand2'),
    ('uuid_device_type3', 'uuid_brand3');
    
-- Insert into device_model table
INSERT INTO device_model (model_id, model_name, release_year, brand_id)
VALUES 
    ('uuid_model1', 'iPhone 13', 2021, 'uuid_brand1'),
    ('uuid_model2', 'Galaxy S21', 2021, 'uuid_brand2'),
    ('uuid_model3', 'XPS 13', 2020, 'uuid_brand3');

-- Insert into repair_appointment table
INSERT INTO repair_appointment (appointment_id, customer_id, employee_id, method_id, date_time, status, total_price)
VALUES 
    ('uuid_appointment1', 'uuid_customer1', 'uuid_employee1', 'uuid_method1', '2025-04-01 10:00:00', 'Completed', 150.00),
    ('uuid_appointment2', 'uuid_customer1', 'uuid_employee2', 'uuid_method2', '2025-04-01 12:00:00', 'Pending', 200.00),
    ('uuid_appointment3', 'uuid_customer2', 'uuid_employee3', 'uuid_method3', '2025-04-02 15:00:00', 'Completed', 175.00),
    ('uuid_appointment4', 'uuid_customer3', 'uuid_employee1', 'uuid_method1', '2025-04-03 09:00:00', 'Canceled', 100.00);

-- Insert into repair_service table with model_id reference
INSERT INTO repair_service (service_id, service_name, description, price, time_taken, model_id)
VALUES 
    ('uuid_service1', 'Screen Replacement', 'Replacing a broken screen', 100.00, 60, 'uuid_model1'),
    ('uuid_service2', 'Battery Replacement', 'Replacing the device battery', 50.00, 45, 'uuid_model2'),
    ('uuid_service3', 'Camera Repair', 'Fixing the camera issue', 75.00, 90, 'uuid_model3'),
    ('uuid_service4', 'Camera Repair', 'Fixing the camera issue', 250.00, 45, 'uuid_model2');

-- Insert into repair_service_appointment table
INSERT INTO repair_service_appointment (service_id, appointment_id)
VALUES 
    ('uuid_service1', 'uuid_appointment1'),
    ('uuid_service2', 'uuid_appointment2'),
    ('uuid_service3', 'uuid_appointment3');