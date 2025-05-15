<?php
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Initialize variables
$form_data = [
    'title' => '',
    'content' => '',
    'author_name' => '',
    'image_path' => ''
];
$error_message = '';
$upload_error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'title' => isset($_POST['title']) ? sanitize_input($_POST['title']) : '',
        'content' => isset($_POST['content']) ? $_POST['content'] : '', // Allow HTML formatting for content
        'author_name' => isset($_POST['author_name']) ? sanitize_input($_POST['author_name']) : '',
        'image_path' => isset($_POST['image_path']) ? sanitize_input($_POST['image_path']) : ''
    ];
    
    // File upload handling
    $uploaded_image = false;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../assets/images/news/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $unique_filename = 'news_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;
        
        // Check if image is valid
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES['image']['size'] <= 2000000) {
                // Allow only certain file formats
                if ($file_extension == "jpg" || $file_extension == "png" || $file_extension == "jpeg" || $file_extension == "gif") {
                    // Try to upload
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $form_data['image_path'] = '/lotterylk/assets/images/news/' . $unique_filename;
                        $uploaded_image = true;
                    } else {
                        $upload_error = "සමාවන්න, ගොනුව උඩුගත කිරීමේදී දෝෂයක් ඇති විය.";
                    }
                } else {
                    $upload_error = "සමාවන්න, JPG, JPEG, PNG & GIF ගොනු පමණක් අවසර ඇත.";
                }
            } else {
                $upload_error = "සමාවන්න, ඔබගේ ගොනුව විශාල වැඩිය. උපරිම ප්‍රමාණය 2MB වේ.";
            }
        } else {
            $upload_error = "සමාවන්න, ඔබගේ ගොනුව පින්තූරයක් නොවේ.";
        }
    }
    
    // Basic validation
    if (empty($form_data['title'])) {
        $error_message = 'කරුණාකර මාතෘකාව ඇතුළත් කරන්න.';
    } elseif (empty($form_data['content'])) {
        $error_message = 'කරුණාකර අන්තර්ගතය ඇතුළත් කරන්න.';
    } elseif (!empty($upload_error)) {
        $error_message = $upload_error;
    } else {
        // All validation passed, insert into database
        try {
            $sql = "
                INSERT INTO news_articles (title, content, author_name, image_path)
                VALUES (:title, :content, :author_name, :image_path)
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':title', $form_data['title']);
            $stmt->bindParam(':content', $form_data['content']);
            $stmt->bindParam(':author_name', $form_data['author_name']);
            $stmt->bindParam(':image_path', $form_data['image_path']);
            
            if ($stmt->execute()) {
                // Success
                $_SESSION['flash_message'] = 'පුවත සාර්ථකව එකතු කරන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
                header('Location: manage_news.php');
                exit;
            } else {
                $error_message = 'පුවත එකතු කිරීමේදී දෝෂයක් ඇති විය.';
            }
        } catch (PDOException $e) {
            error_log("Database error in add_news.php (insert): " . $e->getMessage(), 0);
            $error_message = 'පුවත එකතු කිරීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        }
    }
}

// Include admin header
include 'includes_admin/admin_header.php';
?>

<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">නව පුවතක් එකතු කරන්න</h2>
    <a href="manage_news.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> ආපසු යන්න
    </a>
</div>

<!-- Error Message -->
<?php if (!empty($error_message)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    <p><?php echo $error_message; ?></p>
</div>
<?php endif; ?>

<!-- Add News Form -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <form action="add_news.php" method="post" enctype="multipart/form-data" class="p-6">
        <!-- Title -->
        <div class="mb-6">
            <label for="title" class="block text-gray-700 font-bold mb-2">මාතෘකාව *</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($form_data['title']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryGreen" required>
        </div>
        
        <!-- Content -->
        <div class="mb-6">
            <label for="content" class="block text-gray-700 font-bold mb-2">අන්තර්ගතය *</label>
            <textarea id="content" name="content" rows="10" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryGreen" required><?php echo htmlspecialchars($form_data['content']); ?></textarea>
            <p class="text-sm text-gray-500 mt-1">පේළි බෙදීම් සඳහා නව පේළි භාවිතා කරන්න.</p>
        </div>
        
        <!-- Author Name -->
        <div class="mb-6">
            <label for="author_name" class="block text-gray-700 font-bold mb-2">ලියන්නා (ඇතුලත් කිරීම අවශ්‍ය නැත)</label>
            <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($form_data['author_name']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryGreen">
        </div>
        
        <!-- Image Upload -->
        <div class="mb-6">
            <label for="image" class="block text-gray-700 font-bold mb-2">පින්තූරය (ඇතුලත් කිරීම අවශ්‍ය නැත)</label>
            <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-primaryGreen file:text-white
                hover:file:bg-green-700">
            <p class="text-sm text-gray-500 mt-1">JPG, PNG හෝ GIF පින්තූර පමණක් අවසර ඇත. උපරිම ප්‍රමාණය 2MB.</p>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                <i class="fas fa-save mr-2"></i> සුරකින්න
            </button>
        </div>
    </form>
</div>

<!-- Simple WYSIWYG Editor -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add more WYSIWYG functionality if needed
        const contentTextarea = document.getElementById('content');
        
        // Auto resize textarea as content grows
        contentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
?>
