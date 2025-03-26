<?php
require_once '/var/www/src/helpers/database.php';
$pdo = get_db();

# this is an example of a migration for MySQL 
$up_query = <<<Q
use form;
CREATE TABLE form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    script TEXT,
    country ENUM('USA', 'Canada') NOT NULL,
    province VARCHAR(100) NOT NULL,
    reference_file_path VARCHAR(255),
    budget ENUM('5to99', '100to249', '250to499') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (
        (country = 'USA' AND province IN (
            'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 
            'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 
            'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 
            'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 
            'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 
            'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 
            'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
        )) OR
        (country = 'Canada' AND province IN (
            'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick', 'Newfoundland and Labrador', 
            'Northwest Territories', 'Nova Scotia', 'Ontario', 'Prince Edward Island', 'Quebec', 
            'Saskatchewan', 'Yukon'
        ))
    )
);
Q;

$down_query = <<<Q
use form;
DROP TABLE form;
Q;

if(count($argv) == 2 && $argv[1] === "up") {
    $pdo->exec($up_query);
}

if(count($argv) == 2 && $argv[1] === "down") {
    $pdo->exec($down_query);
}