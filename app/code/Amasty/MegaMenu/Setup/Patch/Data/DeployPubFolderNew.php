<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenu\Setup\Patch\Data;

/**
 * We updated images. Create new patch to re-run image deploy patch
 */
class DeployPubFolderNew extends DeployPubFolder
{
    public static function getDependencies(): array
    {
        return [DeployPubFolder::class];
    }
}
