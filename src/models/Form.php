<?php
require_once '../src/helpers/database.php';

class Form {
    public $project_name;
    public $script;
    public $country;
    public $province;
    public $file;
    private $file_path;
    public $budget;

    private function load_countries_provinces() {
        return json_decode(file_get_contents("./assets/data/provinces.json"), true);
    }

    public function validate() {
        $errors = [];
        $countries_province_data = $this->load_countries_provinces()['countries'];
        $countries = $countries_province_data;

        if(empty($this->project_name) || $this->project_name === "") {
            $errors["project_name"] = "We need to know your project name to proceed, please enter one.";
        }

        if(strlen($this->project_name) >= 300) {
            $errors["project_name"] = "Please enter a project name of less than 300 letters.";
        }

        if(strlen($this->script) >= 4000) {
            $errors["project_name"] = "Please enter a small script of less than 2,000 letters.";
        }

        if(!isset($countries[$this->country])) {
            $errors["country"] = "Please select a country from the dropdown list.";

        }
        if(!isset($countries[$this->country]) || !in_array($this->province, $countries[$this->country])) {
            $errors["province"] = "Please select a state/province from the dropdown list.";
        }

        if(empty($errors)) {
            return ["status" => "success"];
        }

        return ["status" => "error", "errors" => $errors];
    }

    public function handle_file() {
        // scan for viruses here

        if($this->file) {
            // upload file logic here
        }

        // returning dummy url for now
        $this->file_path = "/my/fake/path.jpg";
    }

    public function save() {
        if($this->file) {
            $this->handle_file();
        } else {
            $this->file_path = null;
        }

        try {
            $pdo = get_db();

            // Set PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the SQL query with placeholders
            $sql = "INSERT INTO form (
                `project_name`, `script`, `country`, `province`, `reference_file_path`, `budget`
            ) VALUES (
                :project_name, 
                :script, 
                :country, 
                :province, 
                :reference_file_path, 
                :budget
            )";

            // Prepare the statement
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':project_name', $this->project_name);
            $stmt->bindParam(':script', $this->script);
            $stmt->bindParam(':country', $this->country);
            $stmt->bindParam(':province', $this->province);
            $stmt->bindParam(':reference_file_path', $this->file_path);
            $stmt->bindParam(':budget', $this->budget);

            // Execute the query
            $stmt->execute();

            return ["status" => "success", "message" => "The form was successfully submitted!"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}