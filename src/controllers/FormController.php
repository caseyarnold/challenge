<?php
// include model
require_once('../src/models/Form.php');

class FormController {
    public function show() {
        HtmlResponse('../src/views/form.html');
    }

    public function save() {
        // here we should validate the fake passed csrf token
        $form = new Form();
        $form->project_name = $_POST['project_name'] ?? null;
        $form->script = $_POST['script'] ?? null;
        $form->country = $_POST['country'] ?? null;
        $form->province = $_POST['province'] ?? null;
        $form->file = $_FILES['file'] ?? null;
        $form->budget =  $_POST['budget'] ?? null;

        // validate form
        $errors = $form->validate();
        if($errors['status'] == "error") {
            return JsonResponse($errors);
        }
    
        // save to db
        $success = $form->save();
        if(!$success) {
            return JsonResponse($success);
        }

        // send email stage
        // ideally this portion could be implemented with saas like AWS SES 
        // or sendgrid
        $message = "A new project called {$form->project_name} has been submitted into the database for review";
        $message = wordwrap($message, 100);
        mail("jobform@voices.com", "New Form Submission", $message);
        
        return JsonResponse([
            "status" => "success",
            "message" => "The response has been saved. Thank you!"
        ]);
    }
}