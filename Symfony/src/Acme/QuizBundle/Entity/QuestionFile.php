<?php

namespace Acme\QuizBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QuestionFile {

    private static $fileName = 'QuizQuestions';

    private $extension;

    /**
     * @Assert\File(maxSize="1M")
     */
    private $file;

    function getFile() {
        return $this->file;
    }

    function setFile($file) {
        $this->file = $file;
        //Rossz mime type-ot határoz meg a MimeTypeGuesser; msword-öt mond application/vnd.openxmlformats-officedocument.spreadsheetml.sheet helyett
        //$this->extension = $this->file->guessExtension();

        if ($file->getClientMimeType() === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $this->extension = 'xlsx';
        } elseif ($file->getClientMimeType() === 'application/vnd.ms-excel') {
            $this->extension = 'xls';
        } else {
            $this->extension = $this->file->guessExtension();
        }
    }

    public function isValid() {
        if (null === $this->file) {
            return false;
        }

        return ($this->extension === 'xlsx' || $this->extension === 'xls');
    }

    public function upload() {
        if (null === $this->file) {
            return;
        }

        $prevFiles = glob($this->getUploadRootDir() . '/*'); // get all file names
        foreach($prevFiles as $prevFile){ // iterate files
            if(is_file($prevFile)) {
                unlink($prevFile); // delete file
            }
        }

        $this->file->move($this->getUploadRootDir(), self::$fileName . '.' . $this->extension);
        unset($this->file);
    }

    private function getStoredQuestionFileName($dir) {
        // Open a known directory, and proceed to read its contents
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
                }
                closedir($dh);
            }
        }
    }

    public function getAbsolutePath() {
        return null === $this->extension ? null : $this->getUploadRootDir() . '/' . self::$fileName . '.' . $this->extension;
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getWebPathForStoredFileIfExists() {
        $webPathForStoredFileIfExists = null;
        $dir = $this->getUploadRootDir() . '/';

        // Open a known directory, and proceed to read its contents
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_file($dir . $file)) {
                        $webPathForStoredFileIfExists = $this->getUploadDir() . '/' . $file;
                        break;
                    }
                }
                closedir($dh);
            }
        }

        return $webPathForStoredFileIfExists;
    }

    public function getWebPath() {
        return null === $this->extension ? null : $this->getUploadDir() . '/' . self::$fileName . '.' . $this->extension;
    }

    public function getExtension() {
        return $this->extension;
    }

    protected function getUploadDir() {
        return 'uploads/QuizQuestions';
    }
}