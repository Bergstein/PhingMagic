<?php
require_once 'phing/Task.php';
require_once 'phing/BuildException.php';

class PHPDebTask extends Task
{
    protected $Name;
    protected $Version;
    protected $Maintainer;
    protected $Depends;
    protected $Architecture = 'all';
    protected $Provides;
    protected $Section = 'web';
    protected $Priority = 'optional';
    protected $Homepage;
    protected $Description;
    protected $Private;
    protected $Recommends;

    protected $Filename;

    public function main()
    {
        $Contents  = ['Package: '.$this->Name];
        $Contents [] = 'Version: '.$this->Version;
        $Contents [] = 'Architecture: '.$this->Architecture;
        $Contents [] = 'Maintainer: '.$this->Maintainer;

        if (null !== $this->Depends)
            $Contents[] = 'Depends: '.$this->Depends;

        if (null !== $this->Provides)
            $Contents[] ='Provides: '.$this->Provides;

        if (null !== $this->Recommends)
            $Contents [] = 'Recommends: '.$this->Recommends;

        if (null !== $this->Private)
            $Contents [] = 'X-Private: '.$this->Private;

        $Contents [] = 'Section: '.$this->Section;
        $Contents [] = 'Priority: '.$this->Priority;

        if (null !== $this->Homepage)
            $Contents [] = 'Homepage: '.$this->Homepage;

        $Contents [] = 'Description: '.$this->Description.PHP_EOL;

        $Control = new PhingFile($this->Filename);
        return file_put_contents($Control->getAbsoluteFile(), implode(PHP_EOL, $Contents));
    }

    public function setName ($Name)
    {
        $this->Name = $Name;
    }

    public function getName ()
    {
        return $this->Name;
    }

    public function setVersion ($Version)
    {
        $this->Version = $Version;
    }

    public function getVersion ()
    {
        return $this->Version;
    }

    public function setArchitecture ($Architecture)
    {
        $this->Architecture = $Architecture;
    }

    public function getArchitecture ()
    {
        return $this->Architecture;
    }

    public function setDepends ($Depends)
    {
        $this->Depends = $Depends;
    }

    public function getDepends ()
    {
        return $this->Depends;
    }

    public function setDescription ($Description)
    {
        $this->Description = $Description;
    }

    public function getDescription ()
    {
        return $this->Description;
    }

    public function setHomepage ($Homepage)
    {
        $this->Homepage = $Homepage;
    }

    public function getHomepage ()
    {
        return $this->Homepage;
    }

    public function setMaintainer ($Maintainer)
    {
        $this->Maintainer = $Maintainer;
    }

    public function getMaintainer ()
    {
        return $this->Maintainer;
    }

    public function setPriority ($Priority)
    {
        $this->Priority = $Priority;
    }

    public function getPriority ()
    {
        return $this->Priority;
    }

    public function setProvides ($Provides)
    {
        $this->Provides = $Provides;
    }

    public function getProvides ()
    {
        return $this->Provides;
    }

    public function setSection ($Section)
    {
        $this->Section = $Section;
    }

    public function getSection ()
    {
        return $this->Section;
    }

    public function setFilename ($Filename)
    {
        $this->Filename = $Filename;
    }

    public function getFilename ()
    {
        return $this->Filename;
    }

    public function setRecommends ($Recommends)
    {
        $this->Recommends = $Recommends;
    }

    public function getRecommends ()
    {
        return $this->Recommends;
    }

    public function setPrivate($Private)
    {
        $this->Private = $Private;
    }

    public function getPrivate()
    {
        return $this->Private;
    }
}