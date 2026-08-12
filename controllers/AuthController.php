<?php
class AuthController extends BaseController {
    public function google() {
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query(['client_id'=>getenv('GOOGLE_CLIENT_ID'),'redirect_uri'=>getenv('GOOGLE_REDIRECT_URI'),'response_type'=>'code','scope'=>'email profile','access_type'=>'online']);
        header("Location: $url"); exit;
    }
    public function googleCallback() {
        if (!isset($_GET['code'])) { echo 'Error: no code'; exit; }
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_POSTFIELDS=>http_build_query(['client_id'=>getenv('GOOGLE_CLIENT_ID'),'client_secret'=>getenv('GOOGLE_CLIENT_SECRET'),'redirect_uri'=>getenv('GOOGLE_REDIRECT_URI'),'grant_type'=>'authorization_code','code'=>$_GET['code']])]);
        $td=json_decode(curl_exec($ch),true); curl_close($ch);
        if(!isset($td['access_token'])){ echo 'Error token: '.htmlspecialchars(json_encode($td)); exit; }
        $ch2=curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch2,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$td['access_token']]]);
        $p=json_decode(curl_exec($ch2),true); curl_close($ch2);
        if(!isset($p['email'])){ echo 'Error profile'; exit; }
        $this->processOAuthLogin($p['email'],$p['name']??'Google User');
    }
    public function microsoft() {
        $url='https://login.microsoftonline.com/common/oauth2/v2.0/authorize?'.http_build_query(['client_id'=>getenv('MICROSOFT_CLIENT_ID'),'response_type'=>'code','redirect_uri'=>getenv('MICROSOFT_REDIRECT_URI'),'response_mode'=>'query','scope'=>'openid email profile User.Read']);
        header("Location: $url"); exit;
    }
    public function microsoftCallback() {
        if(!isset($_GET['code'])){ echo 'Error: no code'; exit; }
        $ch=curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_POSTFIELDS=>http_build_query(['client_id'=>getenv('MICROSOFT_CLIENT_ID'),'client_secret'=>getenv('MICROSOFT_CLIENT_SECRET'),'redirect_uri'=>getenv('MICROSOFT_REDIRECT_URI'),'grant_type'=>'authorization_code','code'=>$_GET['code']])]);
        $td=json_decode(curl_exec($ch),true); curl_close($ch);
        if(!isset($td['access_token'])){ echo 'Error token MS: '.htmlspecialchars(json_encode($td)); exit; }
        $ch2=curl_init('https://graph.microsoft.com/v1.0/me');
        curl_setopt_array($ch2,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$td['access_token'],'Content-Type: application/json']]);
        $p=json_decode(curl_exec($ch2),true); curl_close($ch2);
        if(!isset($p['userPrincipalName'])&&!isset($p['mail'])){ echo 'Error profile MS: '.htmlspecialchars(json_encode($p)); exit; }
        $this->processOAuthLogin($p['mail']??$p['userPrincipalName'],$p['displayName']??'Microsoft User');
    }
    public function github() {
        $url='https://github.com/login/oauth/authorize?'.http_build_query(['client_id'=>getenv('GITHUB_CLIENT_ID'),'redirect_uri'=>getenv('GITHUB_REDIRECT_URI'),'scope'=>'user:email']);
        header("Location: $url"); exit;
    }
    public function githubCallback() {
        if(!isset($_GET['code'])){ echo 'Error: no code'; exit; }
        $ch=curl_init('https://github.com/login/oauth/access_token');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_HTTPHEADER=>['Accept: application/json'],CURLOPT_POSTFIELDS=>http_build_query(['client_id'=>getenv('GITHUB_CLIENT_ID'),'client_secret'=>getenv('GITHUB_CLIENT_SECRET'),'code'=>$_GET['code'],'redirect_uri'=>getenv('GITHUB_REDIRECT_URI')])]);
        $td=json_decode(curl_exec($ch),true); curl_close($ch);
        if(!isset($td['access_token'])){ echo 'Error token GH: '.htmlspecialchars(json_encode($td)); exit; }
        $ch2=curl_init('https://api.github.com/user');
        curl_setopt_array($ch2,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$td['access_token'],'User-Agent: QuanLySuKienCLB']]);
        $p=json_decode(curl_exec($ch2),true); curl_close($ch2);
        $email=$p['email']??null;
        if(!$email){
            $ch3=curl_init('https://api.github.com/user/emails');
            curl_setopt_array($ch3,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$td['access_token'],'User-Agent: QuanLySuKienCLB']]);
            $emails=json_decode(curl_exec($ch3),true); curl_close($ch3);
            foreach($emails as $e){ if($e['primary']&&$e['verified']){ $email=$e['email']; break; } }
        }
        if(!$email){ echo 'Cannot get email from GitHub'; exit; }
        $this->processOAuthLogin($email,$p['name']??$p['login']??'GitHub User');
    }
    private function processOAuthLogin($email,$name) {
        $userRepo=new UserRepository();
        $user=$userRepo->findByEmail($email);
        if(!$user){
            $userRepo->create(['full_name'=>$name,'email'=>$email,'password'=>bin2hex(random_bytes(10)),'phone'=>'','role'=>'member']);
            $user=$userRepo->findByEmail($email);
        }
        if($user->getStatus()==='locked'){ echo 'Tai khoan bi khoa'; exit; }
        $json=json_encode(['id'=>$user->getId(),'full_name'=>$user->getFullName(),'email'=>$user->getEmail(),'role'=>$user->getRole()]);
        header('Content-Type: text/html; charset=UTF-8');
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Redirecting...</title></head><body>
            <h3 style='text-align:center;margin-top:80px;font-family:sans-serif;'>Dang nhap thanh cong, dang chuyen huong...</h3>
            <script>
                const u=$json;
                localStorage.setItem('user',JSON.stringify(u));
                if(u.role==='admin') location.href='/views/admin/dashboard.html';
                else if(u.role==='organizer') location.href='/views/organizer/dashboard.html';
                else location.href='/views/member/events.html';
            </script></body></html>";
        exit;
    }
}
