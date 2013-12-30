<?php

if (!$object->xpdo) return true;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $modx =& $object->xpdo;
        $modelPath = $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/').'model/';
        $modx->addPackage('discuss2',$modelPath, 'discuss2_');

        $modx->setLogLevel(modX::LOG_LEVEL_ERROR);

        /* assign policies to Discuss2Template ACL Policy Template */
        $policies = array(
            'Discuss2 Global Moderators',
            'Discuss2 Moderators',
            'Discuss2 Members',
        );
        $template = $modx->getObject('modAccessPolicyTemplate',array('name' => 'Discuss2Template'));
        if ($template) {
            foreach ($policies as $policyName) {
                $policy = $modx->getObject('modAccessPolicy',array(
                    'name' => $policyName,
                ));
                if ($policy) {
                    $policy->set('template',$template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not find "'.$policyName.'" Access Policy!');
                }
            }
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not find DiscussTemplate Access Policy Template!');
        }

        /* assign policy to forum members group */
        $policy = $modx->getObject('modAccessPolicy',array('name' => 'Discuss2 Global Moderator'));
        $adminGroup = $modx->getObject('modUserGroup',array('name' => 'Discuss2 Global Moderators'));
        if ($policy && $adminGroup) {
            $access = $modx->getObject('modAccessContext',array(
                'target' => 'web',
                'principal_class' => 'modUserGroup',
                'principal' => $adminGroup->get('id'),
                'authority' => 9000,
                'policy' => $policy->get('id'),
            ));
            if (!$access) {
                $access = $modx->newObject('modAccessContext');
                $access->fromArray(array(
                    'target' => 'web',
                    'principal_class' => 'modUserGroup',
                    'principal' => $adminGroup->get('id'),
                    'authority' => 900,
                    'policy' => $policy->get('id'),
                ));
                $access->save();
            }
        }

        /* assign policy to admin group */
        $policy = $modx->getObject('modAccessPolicy',array('name' => 'Discuss2 Member'));
        $adminGroup = $modx->getObject('modUserGroup',array('name' => 'Discuss2 Members'));
        if ($policy && $adminGroup) {
            $access = $modx->getObject('modAccessContext',array(
                'target' => 'web',
                'principal_class' => 'modUserGroup',
                'principal' => $adminGroup->get('id'),
                'authority' => 9999,
                'policy' => $policy->get('id'),
            ));
            if (!$access) {
                $access = $modx->newObject('modAccessContext');
                $access->fromArray(array(
                    'target' => 'web',
                    'principal_class' => 'modUserGroup',
                    'principal' => $adminGroup->get('id'),
                    'authority' => 9999,
                    'policy' => $policy->get('id'),
                ));
                $access->save();
            }
        }
        $modx->setLogLevel(modX::LOG_LEVEL_INFO);
        break;
    case xPDOTransport::ACTION_UPGRADE:
        break;
}
return true;