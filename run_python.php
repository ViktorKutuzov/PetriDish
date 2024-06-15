<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image_path = $_POST['image_path'];
    
    $processed_image_path = 'uploads/processed_image.png';
    
    // Execute the Python script and pass the image path
    $command = escapeshellcmd("python3 script.py " . escapeshellarg($image_path) . " " . escapeshellarg($processed_image_path));
    $output = shell_exec($command);

    if (file_exists($processed_image_path)) {
        echo json_encode([
            "success" => true,
            "processed_image_path" => $processed_image_path
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Ошибка при обработке изображения"
        ]);
    }
}
?>
