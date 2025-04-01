-- Create the new completed appointment
INSERT INTO repair_appointment (appointment_id, customer_id, employee_id, method_id, date_time, status, total_price)
VALUES 
    ('uuid_appointment6', 'uuid_customer2', 'uuid_employee2', 'uuid_method2', '2025-04-08 14:00:00', 'Completed', 250.00);

-- Link the newly created appointment to a service  
INSERT INTO repair_service_appointment (service_id, appointment_id)
VALUES 
    ('uuid_service4', 'uuid_appointment6');