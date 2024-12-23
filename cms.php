<?php
session_start();

// Redirect ke login jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Tampilkan notifikasi jika ada
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    echo "<div class='notification {$notification['type']}'>";
    echo htmlspecialchars($notification['message']);
    echo "</div>";

    // Hapus notifikasi setelah ditampilkan
    unset($_SESSION['notification']);
}

include 'koneksi.php';

// Mengambil data user dari session
$username = $_SESSION['username'] ?? '';

// Mengambil data anime
$conn = new Koneksi();
$query = $conn->db->prepare("SELECT * FROM anime");
$query->execute();
$animeList = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime List</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
    /* Japanese-Inspired CMS Theme */
    body {
        font-family: 'Noto Sans JP', sans-serif;
        background: linear-gradient(135deg, #F7F6F3 0%, #E8E4E1 100%);
        margin: 0;
        padding: 0;
        color: #2A2C2B;
    }

    /* Header Styling */
    header {
        background: linear-gradient(135deg, #394B59 0%, #2A2C2B 100%);
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    header h1 {
        font-size: 1.8rem;
        margin: 0;
        font-weight: 500;
        letter-spacing: 0.05em;
        color: white;
    }

    header p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
    }

    header .btn {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 2px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    header .btn:hover {
        background-color: #D64D4D;
    }

    /* Main Content */
    main {
        background: white;
        border-radius: 2px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin: 2rem;
        padding: 2rem;
        position: relative;
    }

    main::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #D64D4D;
    }

    /* Table Styling */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }

    .table thead th {
        background-color: #394B59;
        color: white;
        padding: 1rem;
        font-weight: 500;
        text-align: left;
        border: none;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #E5E5E5;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #F7F6F3;
    }

    .table img {
        border-radius: 2px;
        width: 80px;
        height: 80px;
        object-fit: cover;
    }

    /* Button Styling */
    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: 2px;
        font-size: 0.9rem;
        font-weight: 500;
        letter-spacing: 0.03em;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-primary {
        background-color: #D64D4D;
        color: white;
    }

    .btn-primary:hover {
        background-color: #C43E3E;
    }

    .btn-warning {
        background-color: #C7A17A;
        color: white;
    }

    .btn-warning:hover {
        background-color: #B08E6A;
    }

    .btn-danger {
        background-color: #D64D4D;
        color: white;
    }

    .btn-danger:hover {
        background-color: #C43E3E;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 2px;
        border: none;
    }

    .modal-header {
        background-color: #394B59;
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-body {
        padding: 2rem;
    }

    /* Form Controls */
    .form-control,
    .form-select {
        border-radius: 2px;
        border: 1px solid #E5E5E5;
        padding: 0.8rem;
        transition: all 0.3s ease;
        background-color: #F7F6F3;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #D64D4D;
        box-shadow: 0 0 0 2px rgba(214, 77, 77, 0.1);
        outline: none;
    }

    /* Notification Styling */
    .notification {
        padding: 1rem;
        margin: 1rem 0;
        border: none;
        border-radius: 2px;
        font-size: 0.9rem;
        position: relative;
        padding-left: 3rem;
    }

    .notification.success {
        color: #4A9072;
        background-color: rgba(74, 144, 114, 0.1);
    }

    .notification.error {
        color: #D64D4D;
        background-color: rgba(214, 77, 77, 0.1);
    }
    </style>
</head>

<body>
    <header>
        <div>
            <h1>Anime Management</h1>
            <p>Selamat datang di sistem manajemen anime</p>
        </div>
        <a href="logout.php" class="btn btn-sm">Logout</a>
    </header>


    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Daftar Anime</h2>
            <button id="addAnimeBtn" class="btn btn-primary">Tambah Anime</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Judul</th>
                        <th>Genre</th>
                        <th>Tahun Rilis</th>
                        <th>Jumlah Episode</th>
                        <th>Jenis</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($animeList as $anime): ?>
                    <tr>
                        <td><?= htmlspecialchars($anime['title']); ?></td>
                        <td><?= htmlspecialchars($anime['genre']); ?></td>
                        <td><?= htmlspecialchars($anime['release_year']); ?></td>
                        <td><?= htmlspecialchars($anime['episodes']); ?></td>
                        <td><?= htmlspecialchars($anime['type']); ?></td>
                        <td><img src="<?= htmlspecialchars($anime['image_url']); ?>"
                                alt="<?= htmlspecialchars($anime['title']); ?>" width="100" class="img-thumbnail"></td>
                        <td>
                            <button class="btn btn-warning btn-sm editAnimeBtn"
                                data-id="<?= $anime['id']; ?>">Edit</button>
                            <button class="btn btn-danger btn-sm deleteAnimeBtn"
                                data-id="<?= $anime['id']; ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Tambah Anime -->
    <div id="addModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Anime</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAnimeForm" action="proses.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_anime">

                        <div class="mb-3">
                            <label for="addTitle" class="form-label">Judul:</label>
                            <input type="text" id="addTitle" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="addGenre" class="form-label">Genre:</label>
                            <select id="addGenre" name="genre" class="form-select" required>
                                <option value="Action">Action</option>
                                <option value="Adventure">Adventure</option>
                                <option value="Comedy">Comedy</option>
                                <option value="Drama">Drama</option>
                                <option value="Fantasy">Fantasy</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="addReleaseYear" class="form-label">Tahun Rilis:</label>
                            <input type="number" id="addReleaseYear" name="release_year" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="addEpisodes" class="form-label">Jumlah Episode:</label>
                            <input type="number" id="addEpisodes" name="episodes" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="addType" class="form-label">Jenis:</label>
                            <div>
                                <input type="radio" id="addSeries" name="type" value="series" required>
                                <label for="addSeries">Series</label>
                                <input type="radio" id="addMovie" name="type" value="movie" required>
                                <label for="addMovie">Movie</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="addImage" class="form-label">Gambar:</label>
                            <input type="file" id="addImage" name="image" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Anime -->
    <div id="editModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Anime</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAnimeForm" action="proses.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit_anime">
                        <input type="hidden" name="id" id="editAnimeId">

                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Judul:</label>
                            <input type="text" id="editTitle" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="editGenre" class="form-label">Genre:</label>
                            <select id="editGenre" name="genre" class="form-select" required>
                                <option value="Action">Action</option>
                                <option value="Adventure">Adventure</option>
                                <option value="Comedy">Comedy</option>
                                <option value="Drama">Drama</option>
                                <option value="Fantasy">Fantasy</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editReleaseYear" class="form-label">Tahun Rilis:</label>
                            <input type="number" id="editReleaseYear" name="release_year" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="editEpisodes" class="form-label">Jumlah Episode:</label>
                            <input type="number" id="editEpisodes" name="episodes" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="editType" class="form-label">Jenis:</label>
                            <div>
                                <input type="radio" id="editSeries" name="type" value="series" required>
                                <label for="editSeries">Series</label>
                                <input type="radio" id="editMovie" name="type" value="movie" required>
                                <label for="editMovie">Movie</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="editImage" class="form-label">Gambar:</label>
                            <input type="file" id="editImage" name="image" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Deklarasi modal untuk tambah dan edit
    const addModal = new bootstrap.Modal(document.getElementById('addModal'));
    const editModalElement = document.getElementById('editModal');
    const editModal = new bootstrap.Modal(editModalElement);

    const addAnimeBtn = document.getElementById("addAnimeBtn");
    const addForm = document.getElementById("addAnimeForm");

    // Fungsi membuka modal tambah anime
    addAnimeBtn.onclick = function() {
        addModal.show();
        addForm.reset();
    };

    // Fungsi untuk membuka modal edit
    document.querySelectorAll(".editAnimeBtn").forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.getAttribute("data-id");

            // Reset form sebelum menampilkan modal
            const editForm = document.getElementById("editAnimeForm");
            editForm.reset();

            // Tampilkan modal edit
            editModal.show();

            // Muat data anime berdasarkan ID
            fetch(`proses.php?action=get_anime&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    // Isi form di modal dengan data anime
                    document.getElementById("editAnimeId").value = data.id;
                    document.getElementById("editTitle").value = data.title;
                    document.getElementById("editGenre").value = data.genre;
                    document.getElementById("editReleaseYear").value = data.release_year;
                    document.getElementById("editEpisodes").value = data.episodes;

                    // Set radio button berdasarkan type
                    const radioType = document.querySelector(
                        `input[name="type"][value="${data.type}"]`);
                    if (radioType) {
                        radioType.checked = true;
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        title: "Error",
                        text: "Gagal memuat data anime. Silakan coba lagi.",
                        icon: "error"
                    });
                    editModal.hide();
                });
        });
    });

    // Fungsi untuk menghapus data anime
    document.querySelectorAll(".deleteAnimeBtn").forEach(btn => {
        btn.onclick = function() {
            const id = this.getAttribute("data-id");
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data ini akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then(result => {
                if (result.isConfirmed) {
                    fetch("proses.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `action=delete_anime&id=${id}`
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Gagal menghapus data anime");
                            }
                            return response.json();
                        })
                        .then(() => location.reload())
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire("Error", "Gagal menghapus data anime. Silakan coba lagi.",
                                "error");
                        });
                }
            });
        };
    });
    </script>

</body>

</html>