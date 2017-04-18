<?php

namespace Models;

/**
* Thumbnail Generator class
*
* This class use command line tool 'wkhtmltopdf' (https://wkhtmltopdf.org/) for rendering thumbnail
* If you are using debian release you may need to install 'xvfb' on headless server
* http://unix.stackexchange.com/questions/192642/wkhtmltopdf-qxcbconnection-could-not-connect-to-display
*
* This class also need 'imagemagick' for compression tool
*/
class Thumbnail
{
    /**
     * Output filename
     * @var string
     */
    private $outputFilename = 'thumbnail.png';
    /**
     * Location where output file to be saved
     * @var string
     */
    private $outputDirectory;
    /**
     * Image output quality between 0-100
     * You may want to experiment to get optimal value
     * @var integer
     */
    private $quality = 90;
    /**
     * Height of cropped image in px
     * @var integer
     */
    private $height = 600;
    /**
     * Shell command to generate thumbnail
     * @var string
     */
    private $command = 'echo "Something wrong"';
    /**
     * Online resource to be generated
     * @var string
     */
    private $url = 'http://localhost/';

    /**
     * Initiate all the necessary parameters
     * @param string $url    online resource
     * @param string $output output filename
     */
    public function __construct($url, $output)
    {
        $this->outputDirectory = dirname(dirname(__DIR__)) . '/public/thumbnails/';

        // Create directory if not exists
        if (!is_dir($this->outputDirectory)) {
            mkdir($this->outputDirectory, 777, true);
        }

        // Define output filename
        $this->outputFilename = $output ? $this->outputDirectory . $output : $this->outputFilename;
        $this->url = $url;
    }

    /**
     * Run shell command to generate thumbnail
     * @return void return nothing
     */
    public function render()
    {
        if (file_exists($this->outputFilename) && filesize($this->outputFilename) < 100000) {
            return $this->outputFilename;
        }
        $command = 'xvfb-run wkhtmltoimage --crop-h ' . $this->height . ' ' . $this->url . ' ' . $this->outputFilename;
        $command .= ';convert -quality ' . $this->quality . ' ' . $this->outputFilename . ' ' . $this->outputFilename;
        shell_exec($command);
        return $this->outputFilename;
    }
}
