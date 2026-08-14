<?php
class UserController extends BaseController {
    private $userRepo;
    public function __construct() { $this->userRepo = new UserRepository(); }

    // Hàm mã hóa mật khẩu
    private function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Hàm kiểm tra mật khẩu
    private function checkPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // POST /api/user/login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $input = $this->getJsonInput();
        if (empty($input['email']) || empty($input['password'])) return $this->json(['status'=>'error','message'=>'Thieu email hoac mat khau'],400);
        
        $user = $this->userRepo->findByEmail($input['email']);
        // Sử dụng hàm checkPassword
        $valid = $user && $this->checkPassword($input['password'], $user->getPasswordHash());
        
        if (!$valid) return $this->json(['status'=>'error','message'=>'Email hoac mat khau khong chinh xac'],401);
        if ($user->getStatus() === 'locked') return $this->json(['status'=>'error','message'=>'Tai khoan bi khoa'],403);
        return $this->json(['status'=>'success','message'=>'Dang nhap thanh cong','data'=>$user]);
    }

    // POST /api/user/register
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $input = $this->getJsonInput();
        if (empty($input['full_name'])||empty($input['email'])||empty($input['password'])) return $this->json(['status'=>'error','message'=>'Vui long dien day du thong tin'],400);
        if ($this->userRepo->findByEmail($input['email'])) return $this->json(['status'=>'error','message'=>'Email da duoc su dung'],400);
        
        try {
            // Sử dụng hàm hashPassword trước khi lưu
            $input['password_hash'] = $this->hashPassword($input['password']);
            $id = $this->userRepo->create($input);
            return $this->json(['status'=>'success','message'=>'Dang ky thanh cong','data'=>$this->userRepo->findById($id)]);
        } catch (Exception $e) { return $this->json(['status'=>'error','message'=>$e->getMessage()],500); }
    }

    // GET /api/user/list  (Admin only)
    public function list() {
        $page = (int)($_GET['page'] ?? 1);
        $users = $this->userRepo->getAll($page);
        return $this->json(['status'=>'success','data'=>$users]);
    }

    // PUT /api/user/lock?id=X
    public function lock() {
        $id = $_GET['id'] ?? null;
        if (!$id) return $this->json(['status'=>'error','message'=>'Thieu ID'],400);
        $this->userRepo->updateStatus($id, 'locked');
        return $this->json(['status'=>'success','message'=>'Da khoa tai khoan']);
    }

    // PUT /api/user/unlock?id=X
    public function unlock() {
        $id = $_GET['id'] ?? null;
        if (!$id) return $this->json(['status'=>'error','message'=>'Thieu ID'],400);
        $this->userRepo->updateStatus($id, 'active');
        return $this->json(['status'=>'success','message'=>'Da mo khoa tai khoan']);
    }
    // PUT /api/user/update
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') return $this->json(['message'=>'Method Not Allowed'],405);
        $input = $this->getJsonInput();
        if (empty($input['id']) || empty($input['full_name'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin ID hoac Ten'],400);
        try {
            $this->userRepo->updateProfile($input['id'], $input);
            $user = $this->userRepo->findById($input['id']);
            return $this->json(['status'=>'success','message'=>'Cap nhat thong tin thanh cong','data'=>$user]);
        } catch (Exception $e) { return $this->json(['status'=>'error','message'=>$e->getMessage()],500); }
    }
}
