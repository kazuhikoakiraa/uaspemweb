<?php
session_start();
include 'koneksi.php';

function validateInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function loginUser($data, $db) {
    try {
        $username = validateInput($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            throw new Exception("Username dan password harus diisi.");
        }

        $query = $db->prepare("SELECT id, password FROM users WHERE username = ?");
        $query->execute([$username]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Username tidak ditemukan.");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Password salah.");
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header("Location: cms.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['login_error'] = $e->getMessage();
        header("Location: login.php");
        exit;
    }
}

function registerUser($data, $db) {
    try {
        $username = validateInput($data['username'] ?? '');
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';
        $full_name = validateInput($data['full_name'] ?? '');
        $phone = validateInput($data['phone'] ?? '');
        $dob = $data['dob'] ?? '';
        $address = validateInput($data['address'] ?? '');
        $gender = $data['gender'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || 
            empty($full_name) || empty($phone) || empty($dob) || empty($address) || empty($gender)) {
            throw new Exception("Semua field harus diisi.");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Password dan Konfirmasi Password tidak cocok.");
        }

        // Cek apakah username sudah ada
        $query = $db->prepare("SELECT id FROM users WHERE username = ?");
        $query->execute([$username]);
        if ($query->fetch()) {
            throw new Exception("Username sudah digunakan.");
        }

        // Cek apakah email sudah ada
        $query = $db->prepare("SELECT id FROM users WHERE email = ?");
        $query->execute([$email]);
        if ($query->fetch()) {
            throw new Exception("Email sudah digunakan.");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, dob, address, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $query->execute([$username, $email, $hashed_password, $full_name, $phone, $dob, $address, $gender]);

        $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['register_error'] = $e->getMessage();
        header("Location: register.php");
        exit;
    }
}

class AnimeManagement {
    private $db;

    public function __construct() {
        $conn = new Koneksi();
        $this->db = $conn->db;
    }

    private function validateInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function getAnimeData($id = null) {
        try {
            if ($id) {
                $query = $this->db->prepare("SELECT * FROM anime WHERE id = ?");
                $query->execute([$id]);
                $anime = $query->fetch(PDO::FETCH_ASSOC);

                if (!$anime) {
                    throw new Exception("Anime dengan ID $id tidak ditemukan.");
                }

                // Pastikan response dalam format JSON
                header('Content-Type: application/json');
                echo json_encode($anime);
                exit;
            } else {
                $query = $this->db->prepare("SELECT * FROM anime");
                $query->execute();
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }


    public function addAnime($data, $file) {
        $title = $this->validateInput($data['title'] ?? '');
        $genre = $this->validateInput($data['genre'] ?? '');
        $release_year = filter_var($data['release_year'] ?? 0, FILTER_VALIDATE_INT);
        $episodes = filter_var($data['episodes'] ?? 0, FILTER_VALIDATE_INT);
        $type = $this->validateInput($data['type'] ?? '');

        if (empty($title) || empty($genre) || !$release_year || !$episodes || empty($type)) {
            throw new Exception("Semua field harus diisi dengan benar.");
        }

        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $image_url = "";
        if (isset($file['image']) && $file['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = mime_content_type($file['image']['tmp_name']);

            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Format file gambar tidak valid.");
            }

            $target_file = $target_dir . uniqid() . '_' . basename($file['image']['name']);
            if (move_uploaded_file($file['image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            } else {
                throw new Exception("Gagal mengunggah gambar.");
            }
        } else {
            $image_url = "uploads/default.jpg"; // Placeholder default
        }

        $query = $this->db->prepare("INSERT INTO anime (title, genre, release_year, episodes, type, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $query->execute([$title, $genre, $release_year, $episodes, $type, $image_url]);

        $_SESSION['notification'] = [
            'message' => 'Data anime berhasil ditambahkan!',
            'type' => 'success'
        ];
    }

    public function editAnime($data, $file) {
        $id = filter_var($data['id'] ?? 0, FILTER_VALIDATE_INT);
        $title = $this->validateInput($data['title'] ?? '');
        $genre = $this->validateInput($data['genre'] ?? '');
        $release_year = filter_var($data['release_year'] ?? 0, FILTER_VALIDATE_INT);
        $episodes = filter_var($data['episodes'] ?? 0, FILTER_VALIDATE_INT);
        $type = $this->validateInput($data['type'] ?? '');

        if (!$id || empty($title) || empty($genre) || !$release_year || !$episodes || empty($type)) {
            throw new Exception("Semua field harus diisi dengan benar.");
        }

        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $image_url = "";
        if (isset($file['image']) && $file['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = mime_content_type($file['image']['tmp_name']);

            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Format file gambar tidak valid.");
            }

            $target_file = $target_dir . uniqid() . '_' . basename($file['image']['name']);
            if (move_uploaded_file($file['image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            } else {
                throw new Exception("Gagal mengunggah gambar.");
            }
        }

        $query = $this->db->prepare("UPDATE anime SET title = ?, genre = ?, release_year = ?, episodes = ?, type = ?, image_url = IF(? = '', image_url, ?) WHERE id = ?");
        $query->execute([$title, $genre, $release_year, $episodes, $type, $image_url, $image_url, $id]);

        $_SESSION['notification'] = [
            'message' => 'Data anime berhasil diperbarui!',
            'type' => 'success'
        ];
    }

    public function deleteAnime($id) {
        if (empty($id)) {
            throw new Exception("ID anime tidak boleh kosong.");
        }

        // Hapus data dari database
        $query = $this->db->prepare("DELETE FROM anime WHERE id = ?");
        $query->execute([$id]);

        $_SESSION['notification'] = [
            'message' => 'Data anime berhasil dihapus!',
            'type' => 'success'
        ];
    }
}

// Handle semua request
try {
    $conn = new Koneksi();
    $db = $conn->db;
    $animeManager = new AnimeManagement();

    // Handle GET request untuk mengambil data anime
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        if ($_GET['action'] === 'get_anime' && isset($_GET['id'])) {
            $animeManager->getAnimeData($_GET['id']);
            exit;
        }
    }

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'login':
                    loginUser($_POST, $db);
                    break;

                case 'register':
                    registerUser($_POST, $db);
                    break;

                case 'add_anime':
                    $animeManager->addAnime($_POST, $_FILES);
                    header("Location: cms.php");
                    exit;

                case 'edit_anime':
                    $animeManager->editAnime($_POST, $_FILES);
                    header("Location: cms.php");
                    exit;

                case 'delete_anime':
                    $animeManager->deleteAnime($_POST['id']);
                    header("Location: cms.php");
                    exit;
            }
        }
    }
} catch (Exception $e) {
    $_SESSION['cms_error'] = $e->getMessage();
    header("Location: cms.php");
    exit;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    } else {
        $_SESSION['notification'] = [
            'message' => $e->getMessage(),
            'type' => 'error'
        ];
        header("Location: cms.php");
        exit;
    }
}
?>