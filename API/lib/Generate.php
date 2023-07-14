<?php
namespace Recipely\Lib;

class Generate {
    private $firstName;
    private $lastName;
    private $certificateName;
    private $obtainedDate;
    private $image;

    public function __construct($firstName, $lastName, $certificateName) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->certificateName = $certificateName;
        $this->obtainedDate = date("d-m-Y");
        $this->image = imagecreatefrompng('../assets/templates/Certificate.png');
    }

    public function generateCertificate() {
        // Choose text color: Deep blue
        $colorName = imagecolorallocate($this->image, 0, 0, 128);

        // color : blue sky
        $colorDate = imagecolorallocate($this->image, 0, 191, 255);

        // Add the text to your image

        // First line of text (firstname and lastname)
        $text = $this->firstName . ' ' . $this->lastName;
        $fontSize = 32;

        // Get the bounding box of the text
        $colorName = imagecolorallocate($this->image, 0, 0, 128);

        // color : blue sky
        $colorDate = imagecolorallocate($this->image, 0, 191, 255);
    
        // First line of text (firstname and lastname)
        $this->addTextToImage(32, 280, $colorName, $font_path_name, $this->firstName . ' ' . $this->lastName);
    
        // Second line of text (certificate name)
        $this->addTextToImage(12, 370, $color, $font_text, $this->certificateName);
    
        // Third line of text (obtained date)
        $this->addTextToImage(12, 435, $color, $font_text , $this->obtainedDate);

        // Tell the browser that this is a file to download
        header('Content-Description: File Transfer');

        // Tell the browser the file type
        header('Content-Type: image/png');

        // Suggest a filename to the browser
        header('Content-Disposition: attachment; filename=certificat.png');

        // Tell that the file should be downloaded rather than displayed
        header('Content-Transfer-Encoding: binary');

        // Tell the browser not to cache the results
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // Display the image directly in the browser
        imagepng($this->image);

        // Free up memory
        imagedestroy($this->image);
    }

    private function addTextToImage($fontSize, $y, $color, $font, $text) {
        $textBox = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth = abs(max($textBox[2], $textBox[4]));
        $x = (imagesx($this->image) - $textWidth) / 2;
        imagettftext($this->image, $fontSize, 0, $x, $y, $color, $font, $text);
    }
}
?>