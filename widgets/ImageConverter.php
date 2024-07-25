<?php
namespace app\widgets;
use yii\base\Component;
use Intervention\Image\ImageManagerStatic as Image;
use yii\helpers\FileHelper;
class ImageConverter extends Component
{
    public function convertImagesToAvif($sourceDir, $targetDir, $quality = 90)
    {
        // Ensure the target directory exists
        if (!is_dir($targetDir)) {
            FileHelper::createDirectory($targetDir);
        }
        // Get all image files in the source directory
        $imageFiles = FileHelper::findFiles($sourceDir, ['only' => ['*.jpg', '*.jpeg', '*.png', '*.gif']]);
        foreach ($imageFiles as $imageFile) {
            $image = Image::make($imageFile);
            // Get the file name without extension
            $fileName = pathinfo($imageFile, PATHINFO_FILENAME);
            // Define the target file path
            $targetFilePath = $targetDir . DIRECTORY_SEPARATOR . $fileName . '.avif';
            // Save the image in AVIF format
            $image->encode('avif', $quality)->save($targetFilePath);
            // add detection mime/type
            // add detection filesize
        }
    }
}
