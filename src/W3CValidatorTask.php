<?php
require_once 'phing/Task.php';
require_once 'phing/BuildException.php';
require_once 'Services/W3C/HTMLValidator.php';

class W3CValidatorTask extends Task
{
    protected $suffixesToCheck = null;
    protected $acceptedReportTypes = null;
    protected $reportDirectory = null;
    protected $reportType = null;
    protected $countTests = null;
    protected $fileToCheck = null;
    protected $filesToCheck = null;
    protected $reportFileName = null;
    protected $fileSets = null;
    
    public function init() {
        $this->suffixesToCheck = array('html');
        $this->acceptedReportTypes = array('cli', 'txt', 'xml', 'csv');
        $this->reportType = 'cli';
        $this->reportFileName = 'validator-report';
        $this->fileSets = array();
        $this->filesToCheck = array();
        $this->countTests = false;
    }
    public function setSuffixes($suffixListOrSingleSuffix) {
        if (stripos($suffixListOrSingleSuffix, ',')) {
            $suffixes = explode(',', $suffixListOrSingleSuffix);
            $this->suffixesToCheck = array_map('trim', $suffixes);
        } else {
            array_push($this->suffixesToCheck, trim($suffixListOrSingleSuffix));
        }
    }
    public function setFile(PhingFile $file) {
        $this->fileToCheck = trim($file);
    }

    public function setCountTests($countTests) {
        $this->countTests = (bool) $countTests;
    }

    public function createFileSet() {
        $num = array_push($this->fileSets, new FileSet());
        return $this->fileSets[$num - 1];
    }
    public function setReportType($type) {
        $this->reportType = trim($type);
    }
    public function setReportName($name) {
        $this->reportFileName = trim($name);
    }
    public function setReportDirectory($directory) {
        $this->reportDirectory = trim($directory);
    }
    public function main() {

        $this->_validateProperties();
        if (!is_null($this->reportDirectory) && !is_dir($this->reportDirectory)) {
            $reportOutputDir = new PhingFile($this->reportDirectory);
            $logMessage = "Report output directory doesn't exist, creating: " 
                . $reportOutputDir->getAbsolutePath() . '.';
            $this->log($logMessage);
            $reportOutputDir->mkdirs();
        }
        if ($this->reportType !== 'cli') {
            $this->reportFileName.= '.' . trim($this->reportType);
        }
        if (count($this->fileSets) > 0)
        {
            $project = $this->getProject();
            foreach ($this->fileSets as $fileSet) {
                $directoryScanner = $fileSet->getDirectoryScanner($project);
                $files = $directoryScanner->getIncludedFiles();
                $directory = $fileSet->getDir($this->project)->getPath();
                foreach ($files as $file)
                {
                    if ($this->isFileSuffixSet($file)) {
                        $this->filesToCheck[] = $directory . DIRECTORY_SEPARATOR 
                            . $file;
                    }
                }
            }
            $this->filesToCheck = array_unique($this->filesToCheck);
        }
        $this->runW3CValidator();
    }
    private function _validateProperties() {
        if (!isset($this->fileToCheck) && count($this->fileSets) === 0) {
            $exceptionMessage = "Missing either a nested fileset or the "
                . "attribute 'file' set.";
            throw new BuildException($exceptionMessage);
        }
        if (count($this->suffixesToCheck) === 0) {
            throw new BuildException("No file suffix defined.");
        }
        if (is_null($this->reportType)) {
            throw new BuildException("No report type defined.");
        }
        if (!is_null($this->reportType) && 
            !in_array($this->reportType, $this->acceptedReportTypes)) {
            throw new BuildException("Unaccepted report type defined.");
        }
        if (!is_null($this->fileToCheck) && !file_exists($this->fileToCheck)) {
            throw new BuildException("File to check doesn't exist.");
        }
        if ($this->reportType !== 'cli' && is_null($this->reportDirectory)) {
            throw new BuildException("No report output directory defined.");
        }
        if (count($this->fileSets) > 0 && !is_null($this->fileToCheck)) {
            $exceptionMessage = "Either use a nested fileset or 'file' " 
                . "attribute; not both.";
            throw new BuildException($exceptionMessage);
        }
        if (!is_bool($this->countTests)) {
            $exceptionMessage = "'countTests' attribute has no boolean value";
            throw new BuildException($exceptionMessage);
        }
        if (!is_null($this->fileToCheck)) {
            if (!$this->isFileSuffixSet($file)) {
                $exceptionMessage = "Suffix of file to check is not defined in"
                    . " 'suffixes' attribute.";
                throw new BuildException($exceptionMessage);
            }
        }
    }
    protected function isFileSuffixSet($filename) {
        $pathinfo = pathinfo($filename);
        $fileSuffix = $pathinfo['extension'];
        return in_array($fileSuffix, $this->suffixesToCheck);
    }
    protected function runW3CValidator() {
        $files = $this->getFilesToCheck();
        $Validator = new Services_W3C_HTMLValidator();

        echo 'Files';
        foreach ($files as $file)
            var_dump($Validator->validateFile($file->getRealPath ()));

        // $Result    = $Validator->validateFragmentFile ($Call['File']);
        return true;
    }
    protected function getFilesToCheck() {
        if (count($this->filesToCheck) > 0) {
            $files = array();
            foreach ($this->filesToCheck as $file) {
                $files[] = new SPLFileInfo($file);
            }
        } elseif (!is_null($this->fileToCheck)) {
            $files = array(new SPLFileInfo($this->fileToCheck));
        }
        return $files;
    }

}