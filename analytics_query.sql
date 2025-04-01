SELECT 
    ra.appointment_id, 
    CONCAT(au_customer.first_name, ' ', au_customer.last_name) AS `Customer Name`, 
    c.preferred_contact_method AS `Contact Method`, 
    ra.date_time AS `Date and Time`, 
    sm.method_name AS `Service Method`, 
    dt.type_name AS `Device Type`, 
    b.brand_name AS `Brand`, 
    dm.model_name AS `Device Model`, 
    rs.service_name AS `Repair Service`, 
    ra.total_price AS `Total Price`, 
    ra.status AS `Appointment Status`, 
    CONCAT(au_employee.first_name, ' ', au_employee.last_name) AS `Managed By`, 
    e.job_title AS `Assigned Employee Role`
FROM repair_appointment ra
LEFT JOIN customer c ON ra.customer_id = c.user_id
LEFT JOIN employee e ON ra.employee_id = e.user_id
LEFT JOIN app_user au_customer ON c.user_id = au_customer.user_id
LEFT JOIN app_user au_employee ON e.user_id = au_employee.user_id
LEFT JOIN service_method sm ON ra.method_id = sm.method_id
LEFT JOIN repair_service_appointment rsa ON ra.appointment_id = rsa.appointment_id
LEFT JOIN repair_service rs ON rsa.service_id = rs.service_id
LEFT JOIN device_model dm ON rs.model_id = dm.model_id
LEFT JOIN brand b ON dm.brand_id = b.brand_id
LEFT JOIN device_type_brand dtb ON b.brand_id = dtb.brand_id
LEFT JOIN device_type dt ON dtb.device_type_id = dt.device_type_id
WHERE ra.status = 'Completed';