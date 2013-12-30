<?php

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('discuss2.core_path');
            if (empty($modelPath)) {
                $modelPath = '[[++core_path]]components/discuss2/model/';
            }
            if ($modx instanceof modX) {
                $modx->addExtensionPackage('discuss2',$modelPath, array(
                    'tablePrefix' => 'discuss2_',
                    'serviceName' => 'discuss2',
                    'serviceClass' => 'Discuss2'
                ));
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/').'model/';
            if ($modx instanceof modX) {
                $modx->removeExtensionPackage('discuss2');
            }
            break;
    }
}
return true;