<?php
class ClubController extends BaseController {
    private $clubRepo;
    public function __construct() { $this->clubRepo = new ClubRepository(); }

    public function index() { return $this->json(['status'=>'success','data'=>$this->clubRepo->getAll()]); }

    public function show() {
        $c = $this->clubRepo->findById($_GET['id']??0);
        if (!$c) return $this->json(['status'=>'error','message'=>'Khong tim thay CLB'],404);
        return $this->json(['status'=>'success','data'=>$c]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $d = $this->getJsonInput();
        if (empty($d['name'])||empty($d['created_by'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin'],400);
        $d['status'] = $d['status'] ?? 'active';
        $d['description'] = $d['description'] ?? '';
        $id = $this->clubRepo->create($d);
        return $this->json(['status'=>'success','data'=>$this->clubRepo->findById($id)],201);
    }

    public function update() {
        $id = $_GET['id'] ?? 0;
        $d = $this->getJsonInput();
        $allowed = ['name','description','status'];
        $update = array_intersect_key($d, array_flip($allowed));
        $this->clubRepo->update($id, $update);
        return $this->json(['status'=>'success','data'=>$this->clubRepo->findById($id)]);
    }

    public function delete() {
        $this->clubRepo->delete($_GET['id'] ?? 0);
        return $this->json(['status'=>'success','message'=>'Da xoa CLB']);
    }
}
