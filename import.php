<?php
require_once 'core.php';

$errors = "";
$success = "";
$image_path = "";

// Проверка типа и размера файла
function isLoadingFile($file_name, $file_type, $file_size, $file_tmp_name)
{
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];

    // Проверяем тип файла, разрешен только PNG, JPEG, JPG
    if (!in_array($file_type, $allowed_types)) {
        return "Некорректный формат файла. Разрешены только PNG, JPEG, JPG";
    }

    // Проверяем размер файла
    if ($file_size > 40960000) {
        return "Ваш файл слишком большой";
    }

    return ""; // Возвращаем пустую строку, если ошибок нет
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Получаем данные о файле из $_FILES
    $file_name = $_FILES['file-1']['name'];
    $file_type = $_FILES['file-1']['type'];
    $file_size = $_FILES['file-1']['size'];
    $file_tmp_name = $_FILES['file-1']['tmp_name'];

    // Проверяем корректность файла
    $errors = isLoadingFile($file_name, $file_type, $file_size, $file_tmp_name);
    if (empty($errors)) {
        // Путь для сохранения загруженных файлов
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Генерация уникального имени файла и перемещение его в папку uploads
        $file_dest = $upload_dir . 'uploaded_image.' . pathinfo($file_name, PATHINFO_EXTENSION);
        if (move_uploaded_file($file_tmp_name, $file_dest)) {
            $image_path = $file_dest;
            $success = "Файл успешно загружен.";
        } else {
            $errors = "Ошибка при перемещении загруженного файла.";
        }
    }
}
?>

<main class="container-bg bg-white my-5 p-5">
<div class="container">
    <?php 
        if (!empty($success)) {
            echo "<div class='d-flex justify-content-center'>";
            echo "<img id='uploaded-image' src='$image_path' alt='Uploaded Image' style='max-width: 100%; height: auto;'>";
            echo "</div>";
        } else {
            echo "<p class='text-danger'>$errors</p>";
        }
    ?>
    <form class="form-export d-flex flex-column align-items-center gap-5" method="POST" enctype="multipart/form-data">
        <label for="upload-file"> <?php empty($success) ? "Загрузите ваше изображение (PNG, JPEG, JPG)" : "" ?></label>
        <input type="file" name="file-1" class="form-control w-25" id="upload-file">
        <button type="submit" name="submit" class="btn btn-info text-white mt-2">Загрузить</button>
    </form>
    <?php 
        if (!empty($success)) {
            echo "<div class='d-flex justify-content-center'>";
            echo "<button id='run-python' class='btn btn-info text-white mt-2'>Определить количество колоний</button>";
            echo "</div>";
        }
    ?>
</div>
</main>

<script>
    $(document).ready(function() {
    $('#run-python').on('click', function() {
        $.ajax({
            url: 'run_python.php',
            method: 'POST',
            data: {image_path: '<?php echo $image_path; ?>'},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#uploaded-image').attr('src', response.processed_image_path);
                    $('#run-python').text('Скачать обработанное изображение')
                        .attr('id', 'download-image')
                        .off('click')
                        .on('click', function() {
                            // Create a temporary anchor element for the download
                            var link = document.createElement('a');
                            link.href = response.processed_image_path;
                            link.download = 'processed_image.png'; // Set the desired file name
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                } else {
                    alert(response.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Ошибка при выполнении Python скрипта');
            }
        });
    });
});
</script>

<?php
require_once 'partials/footer.php';
?>
