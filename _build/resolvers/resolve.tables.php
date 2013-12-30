<?php
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/').'model/';
            $modx->addPackage('discuss2',$modelPath, 'discuss2_');
            $m = $modx->getManager();

            $m->createObjectContainer('disForumActivity');
            $m->createObjectContainer('disPostAttachment');
            $m->createObjectContainer('disSubscription');
            $m->createObjectContainer('disThreadParticipant');
            $m->createObjectContainer('disThreadProperty');
            $m->createObjectContainer('disThreadRead');
            $m->createObjectContainer('disUserActivity');
            $m->createObjectContainer('disUserIgnoreBoard');
            $m->createObjectContainer('disUserMention');
            $m->createObjectContainer('disUserNetwork');
            $m->createObjectContainer('disUserProfile');
            $m->createObjectContainer('disUserThread');
            $m->createObjectContainer('disUserThreadParticipant');
            $m->createObjectContainer('disUserThreadPost');

            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;