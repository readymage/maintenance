<?php
/**
 * @category ReadyMage
 * @package ReadyMage_Maintenance
 * @author Ricards Rikmanis <ricards@scandiweb.com>
 * @copyright Copyright (c) 2021 Scandiweb, Ltd (https://scandiweb.com)
 */

declare(strict_types=1);

namespace ReadyMage\Maintenance\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\MaintenanceMode as CoreMaintenanceMode;
use Magento\Framework\Filesystem;

class MaintenanceMode
{
    /**
     * @var  string
     */
    const FLAG_DIR = DirectoryList::VAR_DIR;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $flagDir;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->flagDir = $filesystem->getDirectoryWrite(self::FLAG_DIR);
    }

    /**
     * @param CoreMaintenanceMode $subject
     * @param callable $proceed
     * @param bool $isOn
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function aroundSet(CoreMaintenanceMode $subject, callable $proceed, bool $isOn)
    {
        $symlinkTarget = '';
        $flagFullPath = $this->flagDir->getAbsolutePath(CoreMaintenanceMode::FLAG_FILENAME);

        if (!$isOn && $this->flagDir->isExist(CoreMaintenanceMode::FLAG_FILENAME) && @is_link($flagFullPath)) {
            $symlinkTarget = @readlink($flagFullPath);
        }

        $proceed($isOn);

        if ($symlinkTarget && !$this->flagDir->isExist(CoreMaintenanceMode::FLAG_FILENAME)) {
            $this->flagDir->getDriver()->symlink($symlinkTarget, $flagFullPath);
            $this->flagDir->getDriver()->deleteFile($symlinkTarget);
        }
    }

    /**
     * @param CoreMaintenanceMode $subject
     * @param callable $proceed `
     * @param string $addresses
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function aroundSetAddresses(CoreMaintenanceMode $subject, callable $proceed, string $addresses)
    {
        $symlinkTarget = '';
        $flagPath = $this->flagDir->getAbsolutePath(CoreMaintenanceMode::IP_FILENAME);

        if (empty($addresses) && $this->flagDir->isExist(CoreMaintenanceMode::IP_FILENAME) && @is_link($flagPath)) {
            $symlinkTarget = @readlink($flagPath);

        }

        $proceed($addresses);

        if ($symlinkTarget && !$this->flagDir->isExist(CoreMaintenanceMode::IP_FILENAME)) {
            $this->flagDir->getDriver()->symlink($symlinkTarget, $flagPath);
            $this->flagDir->getDriver()->deleteFile($symlinkTarget);
        }
    }
}
