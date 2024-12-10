<?php 
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_map";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $text_content = $_POST['text_content'];

    // Initialize $image_path as null in case no image is uploaded
    $image_path = null;

    // Check if an image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        // Ensure file is an image
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");

        if (!in_array($image_file_type, $allowed_types)) {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit;
        }

        // Check file size (example: limit to 5MB)
        if ($_FILES["image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit;
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file; // Save the path to the image
        } else {
            echo "Error during image upload!";
            exit;
        }
    }

    // Prepare the SQL query to insert the post into the database
    $stmt = $conn->prepare("INSERT INTO posts (username, email, text_content, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $text_content, $image_path);

    if ($stmt->execute()) {
        echo "Post submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch posts from the database
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result === false) {
    die('Error: ' . $conn->error);
}

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
} else {
    echo "No posts found.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Comment Section</title>
    <link rel="stylesheet" href="blog.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="Images/college-logo.png" alt="College Logo" class="logo">
            <div>
                <h1>Blog</h1>
                <h3>(Post your experience with our website!)</h3>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Introduction</a></li>
                <li><a href="campusmap.html">Campus Map</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="blog.html" class="active">Blog</a></li>
            </ul>
        </nav>
        <button id="register-btn">Register</button>
        <span id="username-display" style="display: none;"></span>
    </header>

    <div id="registration-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Register</h2>
            <form id="registration-form">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Register</button>
            </form>
        </div>
    </div>

    <main>
        <section class="blog-comments">
            <h2>Post Your Comments</h2>
            <form id="post-form" enctype="multipart/form-data" class="comment-form" method="POST" action="">
                <input type="hidden" name="username" id="username-input" />
                <input type="hidden" name="email" id="email-input" />
                <label for="text-content">Share your experience:</label>
                <textarea id="text-content" name="text_content" rows="4" required></textarea>

                <label for="image">Attach an image:</label>
                <input type="file" id="image" name="image" accept="image/*">

                <button type="submit">Post</button>
            </form>
            <div class="comments-list">
            <h3>Recent Comments:</h3>
            <ul>
                <?php
                if (!empty($posts)) {
                    foreach ($posts as $post): 
                ?>
                    <li>
                        <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                        (<?php echo htmlspecialchars($post['email']); ?>) - 
                        <?php echo (new DateTime($post['created_at']))->format('Y-m-d H:i:s'); ?>
                        <br>
                        <p><?php echo nl2br(htmlspecialchars($post['text_content'])); ?></p>
                        <?php if ($post['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image" style="max-width: 300px;">
                        <?php endif; ?>
                    </li>
                <?php 
                    endforeach;
                } else {
                    echo "<p>No posts available.</p>";
                }
                ?>
            </ul>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Here We R Team | All Rights Reserved</p>
    </footer>

    <script src="blog.js"></script>
</body>
</html>
