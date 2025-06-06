<?php
requireAuth();
require __DIR__ . '/data/productsDB.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $photo = $user['photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($ext), $allowed)) {
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
                $photo = 'uploads/' . $filename;
            } else {
                $error = 'Помилка збереження фото.';
            }
        } else {
            $error = 'Дозволені формати: jpg, jpeg, png, gif.';
        }
    }

    if ($first_name === '' || strlen($first_name) < 2) {
        $error = 'Ім' . chr(8217) . 'я повинно містити більше 1 символу.';
    } elseif ($last_name === '' || strlen($last_name) < 2) {
        $error = 'Прізвище повинно містити більше 1 символу.';
    } elseif (!$birth_date || (strtotime($birth_date) > strtotime('-16 years'))) {
        $error = 'Вам має бути не менше 16 років.';
    } elseif (strlen($description) < 50) {
        $error = 'Опис повинен містити більше 50 символів.';
    }
    if (!$error) {
        $stmt = $pdo->prepare('UPDATE users SET first_name=?, last_name=?, birth_date=?, description=?, photo=? WHERE id=?');
        $stmt->execute([$first_name, $last_name, $birth_date, $description, $photo, $user_id]);
        $success = 'Дані профілю оновлено!';
        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;
        $user['birth_date'] = $birth_date;
        $user['description'] = $description;
        $user['photo'] = $photo;
    }
}
?>
<div class="main-content">
    <h2>Профіль користувача</h2>
    <div class="profile-wrapper">
        <div class="profile-photo-block">
            <div class="profile-photo" id="profilePhotoPreview">
                <?php if ($user['photo']): ?>
                    <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Фото" id="profilePhotoImg" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
                <?php endif; ?>
            </div>
            <form id="photoUploadForm" enctype="multipart/form-data" method="post" style="margin-top:10px;">
                <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;">
                <label for="photoInput" class="profile-upload-btn">Завантажити</label>
            </form>
        </div>
        <form method="post" enctype="multipart/form-data" class="profile-form" id="profileForm">
            <div class="profile-form-row">
                <label>Ім'я:
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </label>
                <label>Прізвище:
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                </label>
                <label>Дата народження:
                    <input type="date" name="birth_date" value="<?= htmlspecialchars($user['birth_date']) ?>" required>
                </label>
            </div>
            <label>Опис:
                <textarea name="description" rows="4" required><?= htmlspecialchars($user['description']) ?></textarea>
            </label>
            <div class="profile-form-actions">
                <button type="submit">Зберегти</button>
            </div>
        </form>
    </div>
    <?php if ($error): ?>
        <div class="profile-error"> <?= htmlspecialchars($error) ?> </div>
    <?php elseif ($success): ?>
        <div class="profile-success"> <?= htmlspecialchars($success) ?> </div>
    <?php endif; ?>
</div>
<script>

const photoInput = document.getElementById('photoInput');
const photoPreview = document.getElementById('profilePhotoPreview');
photoInput.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            let img = photoPreview.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '10px';
                photoPreview.innerHTML = '';
                photoPreview.appendChild(img);
            }
            img.src = ev.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});

const profileForm = document.getElementById('profileForm');
const photoUploadForm = document.getElementById('photoUploadForm');
profileForm.addEventListener('submit', function(e) {
    if (photoInput.files.length > 0) {
        photoInput.setAttribute('form', 'profileForm');
    }
});
</script> 