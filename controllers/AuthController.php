<?php
class AuthController extends BaseController {
    
    private function getEnv($key) { return getenv($key); }

    private function handleLogin($email, $name) {
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($email);
        
        if (!$user) {
            // User does not exist, auto-register them
            // We use a random password since they login via OAuth
            $randomPass = bin2hex(random_bytes(8));
            $data = [
                'full_name' => $name ?: 'Nguoi dung',
                'email' => $email,
                'password' => $randomPass, // UserRepository hashes it or it gets hashed depending on implementation
                'password_hash' => password_hash($randomPass, PASSWORD_BCRYPT), // Safe fallback
                'phone' => null,
                'role' => 'member'
            ];
            $id = $userRepo->create($data);
            $user = $userRepo->findById($id);
        }

        if ($user->getStatus() === 'locked') {
            die('Tài khoản của bạn đã bị khóa!');
        }

        // Generate script to save user to localStorage and redirect
        $json = json_encode([
            'id' => $user->getId(),
            'full_name' => $user->getFullName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ]);

        $redirect = '/views/member/events.html';
        if ($user->getRole() === 'admin') $redirect = '/views/admin/dashboard.html';
        if ($user->getRole() === 'organizer') $redirect = '/views/organizer/dashboard.html';

        echo "<!DOCTYPE html><html><body><script>
            localStorage.setItem('user', '$json');
            window.location.href = '$redirect';
        </script></body></html>";
        exit;
    }

    private function httpRequest($url, $postData = null, $headers = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
        }
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        // for local testing without valid SSL sometimes:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    // ================== GOOGLE ==================
    public function google() {
        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $this->getEnv('GOOGLE_CLIENT_ID'),
            'redirect_uri' => $this->getEnv('GOOGLE_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ]);
        header("Location: $url");
        exit;
    }

    public function googleCallback() {
        $code = $_GET['code'] ?? '';
        if (!$code) die("Google Login Failed (no code)");

        $res = $this->httpRequest("https://oauth2.googleapis.com/token", [
            'client_id' => $this->getEnv('GOOGLE_CLIENT_ID'),
            'client_secret' => $this->getEnv('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => $this->getEnv('GOOGLE_REDIRECT_URI'),
            'grant_type' => 'authorization_code',
            'code' => $code
        ]);
        
        $tokenData = json_decode($res, true);
        if (empty($tokenData['access_token'])) die("Failed to get Google Access Token");

        $userInfoRes = $this->httpRequest("https://www.googleapis.com/oauth2/v2/userinfo", null, [
            "Authorization: Bearer " . $tokenData['access_token']
        ]);
        $userInfo = json_decode($userInfoRes, true);
        
        if (empty($userInfo['email'])) die("Could not get Google email");
        
        $this->handleLogin($userInfo['email'], $userInfo['name'] ?? 'User');
    }

    // ================== MICROSOFT ==================
    public function microsoft() {
        $url = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $this->getEnv('MICROSOFT_CLIENT_ID'),
            'redirect_uri' => $this->getEnv('MICROSOFT_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'User.Read'
        ]);
        header("Location: $url");
        exit;
    }

    public function microsoftCallback() {
        $code = $_GET['code'] ?? '';
        if (!$code) die("Microsoft Login Failed");

        $res = $this->httpRequest("https://login.microsoftonline.com/common/oauth2/v2.0/token", [
            'client_id' => $this->getEnv('MICROSOFT_CLIENT_ID'),
            'client_secret' => $this->getEnv('MICROSOFT_CLIENT_SECRET'),
            'redirect_uri' => $this->getEnv('MICROSOFT_REDIRECT_URI'),
            'grant_type' => 'authorization_code',
            'code' => $code
        ]);
        
        $tokenData = json_decode($res, true);
        if (empty($tokenData['access_token'])) die("Failed to get Microsoft Token");

        $userInfoRes = $this->httpRequest("https://graph.microsoft.com/v1.0/me", null, [
            "Authorization: Bearer " . $tokenData['access_token']
        ]);
        $userInfo = json_decode($userInfoRes, true);
        
        $email = $userInfo['mail'] ?? $userInfo['userPrincipalName'] ?? '';
        if (!$email) die("Could not get Microsoft email");
        
        $this->handleLogin($email, $userInfo['displayName'] ?? 'User');
    }

    // ================== GITHUB ==================
    public function github() {
        $url = "https://github.com/login/oauth/authorize?" . http_build_query([
            'client_id' => $this->getEnv('GITHUB_CLIENT_ID'),
            'redirect_uri' => $this->getEnv('GITHUB_REDIRECT_URI'),
            'scope' => 'user:email'
        ]);
        header("Location: $url");
        exit;
    }

    public function githubCallback() {
        $code = $_GET['code'] ?? '';
        if (!$code) die("Github Login Failed");

        $res = $this->httpRequest("https://github.com/login/oauth/access_token", [
            'client_id' => $this->getEnv('GITHUB_CLIENT_ID'),
            'client_secret' => $this->getEnv('GITHUB_CLIENT_SECRET'),
            'redirect_uri' => $this->getEnv('GITHUB_REDIRECT_URI'),
            'code' => $code
        ], ["Accept: application/json"]);
        
        $tokenData = json_decode($res, true);
        if (empty($tokenData['access_token'])) die("Failed to get Github Token");

        $headers = [
            "Authorization: Bearer " . $tokenData['access_token'],
            "User-Agent: CLBEvent-App",
            "Accept: application/json"
        ];

        // Fetch User
        $userRes = $this->httpRequest("https://api.github.com/user", null, $headers);
        $userInfo = json_decode($userRes, true);
        $name = $userInfo['name'] ?? $userInfo['login'] ?? 'Github User';
        $email = $userInfo['email'] ?? '';

        // If email is private, fetch from emails endpoint
        if (!$email) {
            $emailsRes = $this->httpRequest("https://api.github.com/user/emails", null, $headers);
            $emails = json_decode($emailsRes, true);
            if (is_array($emails)) {
                foreach ($emails as $em) {
                    if ($em['primary']) { $email = $em['email']; break; }
                }
            }
        }

        if (!$email) die("Could not get Github email");
        
        $this->handleLogin($email, $name);
    }
}
