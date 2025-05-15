<?php
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Initialize variables
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$form_data = [
    'id' => 0,
    'title' => '',
    'content' => '',
    'author_name' => '',
    'image_path' => '',
    'published_date' => ''
];
$error_message = '';
$upload_error = '';
$news_found = false;

// Get news data
if ($news_id > 0 && isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM news_articles WHERE id = :id');
        $stmt->bindParam(':id', $news_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $news = $stmt->fetch();
        
        if ($news) {
            $news_found = true;
            $form_data = [
                'id' => $news['id'],
                'title' => $news['title'],
                'content' => $news['content'],
                'author_name' => $news['author_name'],
                'image_path' => $news['image_path'],
                'published_date' => $news['published_date']
            ];
        } else {
            $error_message = 'පුවත හමු නොවීය.';
        }
    } catch (PDOException $e) {
        error_log("Database error in edit_news.php (fetch): " . $e->getMessage(), 0);
        $error_message = 'පුවත ලබා ගැනීමේදී දෝෂයක් ඇති විය.';
    }
} else {
    $error_message = 'වලංගු පුවත් ID එකක් සපයන්න.';
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $news_found) {
    // Get form data
    $form_data = [
        'id' => $news_id,
        'title' => isset($_POST['title']) ? sanitize_input($_POST['title']) : '',
        'content' => isset($_POST['content']) ? $_POST['content'] : '', // Allow HTML formatting for content
        'author_name' => isset($_POST['author_name']) ? sanitize_input($_POST['author_name']) : '',
        'image_path' => isset($_POST['current_image']) ? sanitize_input($_POST['current_image']) : '',
        'published_date' => isset($_POST['published_date']) ? sanitize_input($_POST['published_date']) : ''
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
                        // Delete old image if exists
                        if (!empty($form_data['image_path'])) {
                            $old_image_path = $_SERVER['DOCUMENT_ROOT'] . $form_data['image_path'];
                            if (file_exists($old_image_path)) {
                                @unlink($old_image_path);
                            }
                        }
                        
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
    
    // Handle "Remove Image" checkbox
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1' && !$uploaded_image) {
        // Delete existing image if exists
        if (!empty($form_data['image_path'])) {
            $old_image_path = $_SERVER['DOCUMENT_ROOT'] . $form_data['image_path'];
            if (file_exists($old_image_path)) {
                @unlink($old_image_path);
            }
        }
        $form_data['image_path'] = '';
    }
    
    // Basic validation
    if (empty($form_data['title'])) {
        $error_message = 'කරුණාකර මාතෘකාව ඇතුළත් කරන්න.';
    } elseif (empty($form_data['content'])) {
        $error_message = 'කරුණාකර අන්තර්ගතය ඇතුළත් කරන්න.';
    } elseif (!empty($upload_error)) {
        $error_message = $upload_error;
    } else {
        // All validation passed, update database
        try {
            $sql = "
                UPDATE news_articles 
                SET title = :title, 
                    content = :content, 
                    author_name = :author_name, 
                    image_path = :image_path
                WHERE id = :id
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $form_data['id'], PDO::PARAM_INT);
            $stmt->bindParam(':title', $form_data['title']);
            $stmt->bindParam(':content', $form_data['content']);
            $stmt->bindParam(':author_name', $form_data['author_name']);
            $stmt->bindParam(':image_path', $form_data['image_path']);
            
            if ($stmt->execute()) {
                // Success
                $_SESSION['flash_message'] = 'පුවත සාර්ථකව යාවත්කාලීන කරන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
                header('Location: manage_news.php');
                exit;
            } else {
                $error_message = 'පුවත යාවත්කාලීන කිරීමේදී දෝෂයක් ඇති විය.';
            }
        } catch (PDOException $e) {
            error_log("Database error in edit_news.php (update): " . $e->getMessage(), 0);
            $error_message = 'පුවත යාවත්කාලීන කිරීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        }
    }
}

// Include admin header
include 'includes_admin/admin_header.php';
?>

<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">පුවත සංස්කරණය කරන්න</h2>
    <a href="manage_news.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> ආපසු යන්න
    </a>
</div>

<!-- Error Message -->
<?php if (!empty($error_message)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    <p><?php echo $error_message; ?></p>
    <?php if (!$news_found): ?>
    <div class="mt-2">
        <a href="manage_news.php" class="text-red-700 underline">පුවත් ලැයිස්තුවට ආපසු යන්න</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($news_found): ?>
<!-- Edit News Form -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <form action="edit_news.php?id=<?php echo $news_id; ?>" method="post" enctype="multipart/form-data" class="p-6">
        <input type="hidden" name="id" value="<?php echo $news_id; ?>">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($form_data['image_path']); ?>">
        
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
        
        <!-- Current Image Preview (if exists) -->
        <?php if (!empty($form_data['image_path'])): ?>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">දැනට ඇති පින්තූරය</label>
            <div class="mt-2 flex items-start">
                <img src="<?php echo htmlspecialchars($form_data['image_path']); ?>" alt="Current Image" class="max-w-xs h-auto rounded border">
                <div class="ml-4 flex items-center">
                    <input type="checkbox" id="remove_image" name="remove_image" value="1" class="mr-2">
                    <label for="remove_image" class="text-red-600">පින්තූරය ඉවත් කරන්න</label>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Image Upload -->
        <div class="mb-6">
            <label for="image" class="block text-gray-700 font-bold mb-2">නව පින්තූරයක් උඩුගත කරන්න (ඇතුලත් කිරීම අවශ්‍ය නැත)</label>
            <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-primaryGreen file:text-white
                hover:file:bg-green-700">
            <p class="text-sm text-gray-500 mt-1">JPG, PNG හෝ GIF පින්තූර පමණක් අවසර ඇත. උපරිම ප්‍රමාණය 2MB.</p>
        </div>
        
        <!-- Published Date (Read Only) -->
        <div class="mb-6">
            <label for="published_date" class="block text-gray-700 font-bold mb-2">ප්‍රකාශිත දිනය (සංස්කරණය කළ නොහැක)</label>
            <input type="text" id="published_date" value="<?php echo date('Y-m-d H:i:s', strtotime($form_data['published_date'])); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                <i class="fas fa-save mr-2"></i> යාවත්කාලීන කරන්න
            </button>
        </div>
    </form>
</div>

<!-- Preview Button -->
<div class="mt-6 text-center">
    <a href="../news_article.php?id=<?php echo $news_id; ?>" target="_blank" class="inline-block bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
        <i class="fas fa-eye mr-2"></i> වෙබ් අඩවියේ පුවත බලන්න
    </a>
</div>
<?php endif; ?>

<!-- Simple WYSIWYG Editor -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add more WYSIWYG functionality if needed
        const contentTextarea = document.getElementById('content');
        
        if (contentTextarea) {
            // Auto resize textarea as content grows
            contentTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Auto resize on load
            contentTextarea.style.height = 'auto';
            contentTextarea.style.height = (contentTextarea.scrollHeight) + 'px';
        }
    });
</script>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
?>
