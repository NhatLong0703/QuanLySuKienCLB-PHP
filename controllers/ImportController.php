<?php
class ImportController extends BaseController {
    // POST /api/import/users  (multipart/form-data, file=csv)
    public function users() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        if (empty($_FILES['file'])) return $this->json(['status'=>'error','message'=>'Vui long upload file CSV'],400);
        $userRepo = new UserRepository();
        $handle = fopen($_FILES['file']['tmp_name'],'r');
        $header = fgetcsv($handle); // bo qua dong tieu de
        $inserted = 0; $errors = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) continue;
            [$fullName,$email,$password] = $row;
            $role = $row[3] ?? 'member';
            if ($userRepo->findByEmail(trim($email))) { $errors[] = "Email da ton tai: $email"; continue; }
            try {
                $userRepo->create(['full_name'=>trim($fullName),'email'=>trim($email),'password'=>trim($password),'phone'=>$row[4]??'','role'=>trim($role)]);
                $inserted++;
            } catch (Exception $e) { $errors[] = $e->getMessage(); }
        }
        fclose($handle);
        return $this->json(['status'=>'success','message'=>"Da import $inserted tai khoan",'errors'=>$errors]);
    }
}
