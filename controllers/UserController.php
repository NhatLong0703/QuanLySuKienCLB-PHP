<?php
class UserController extends BaseController {
    private $userRepo;
    public function __construct() { $this->userRepo = new UserRepository(); }

    // POST /api/user/login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $input = $this->getJsonInput();
        if (empty($input['email']) || empty($input['password'])) return $this->json(['status'=>'error','message'=>'Thieu email hoac mat khau'],400);
        $user = $this->userRepo->findByEmail($input['email']);
        $valid = $user && password_verify($input['password'], $user->getPasswordHash());
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

    // PUT /api/user/update?id=X
    public function update() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin') return $this->json(['status'=>'error','message'=>'Chi admin moi duoc sua'],403);
        
        $id = $_GET['id'] ?? 0;
        $u = $this->userRepo->findById($id);
        if (!$u) return $this->json(['status'=>'error','message'=>'Khong tim thay user'],404);

        $d = $this->getInputData();
        $allowed = ['full_name','email','phone','role','status'];
        if (!empty($d['password'])) $allowed[] = 'password';
        
        $update = array_intersect_key($d, array_flip($allowed));
        if (empty($update)) return $this->json(['status'=>'error','message'=>'Khong co gi cap nhat'],400);
        
        $this->userRepo->update($id, $update);
        return $this->json(['status'=>'success','message'=>'Cap nhat thanh cong','data'=>$this->userRepo->findById($id)]);
    }

    // DELETE /api/user/delete?id=X
    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin') return $this->json(['status'=>'error','message'=>'Chi admin moi duoc xoa'],403);
        
        $id = $_GET['id'] ?? 0;
        if ($id == $user['id']) return $this->json(['status'=>'error','message'=>'Khong the tu xoa chinh minh'],400);
        
        $u = $this->userRepo->findById($id);
        if (!$u) return $this->json(['status'=>'error','message'=>'Khong tim thay user'],404);

        $this->userRepo->delete($id);
        return $this->json(['status'=>'success','message'=>'Da xoa user']);
    }
}
