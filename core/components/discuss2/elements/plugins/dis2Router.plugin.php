<?php
if ($modx->getOption('friendly_urls') == false) {
    return ;
}

switch ($modx->event->name) {
    case 'OnPageNotFound' :
        $request = $modx->getRequest();

        if ($modx->request){
            $url = $modx->request->getResourceIdentifier($modx->request->getResourceMethod());
            $url = rtrim($url, $modx->getOption('container_suffix', null, '/'));
            $alias = null;

            $modPages = explode(",", $modx->getOption('discuss2.moderator_page'));
            $userPages = explode(",", $modx->getOption('discuss2.user_page'));
            $isUser = false;
            $controller = null;

            // TODO: Implement better controller behavior. This one literally smell bit of bubblegum
            // Check if user or moderator page
            foreach ($userPages as $uPage) {
                $uUrl = $modx->discuss2->makeUrl($uPage);
                if (strpos($url, $uUrl) === 0) {
                    $alias = rtrim(substr($url, 0, strlen($uUrl)), '/');
                    if (strpos($url, '/', strlen($uUrl)) !== false) {
                        $uname = trim(substr($url, strlen($alias) +1, strpos($url, "/", strlen($alias) + 1) - (strlen($alias) + 1)), '/');
                        $action = trim(substr($url, (strlen($alias) +1) + strlen($uname)), '/');
                    } else {
                        $uname = trim(substr($url, strlen($alias)), '/');
                        $action = 'view/profile';
                    }
                    $modx->request->parameters['GET']['username'] = $uname;
                    $controller = 'user';
                    break;
                }
            }
            foreach ($modPages as $mPage) {
                $mUrl = $modx->discuss2->makeUrl($mPage);
                if (strpos($url, $mUrl) === 0) {
                    $controller = 'moderator';
                }
            }
            if ($alias === null) {
                $parts = explode("/", $url);
                $action = implode("/", array_slice($parts, -2));
                $alias = implode("/", array_slice($parts, 0, -2));
            }

            $modx->request->parameters['GET']['action'] = $action;
            $modx->request->paremeters['GET'][$modx->request->getResourceMethod()] = rtrim($alias, "/");
            $modx->request->paremeters['REQUEST'][$modx->request->getResourceMethod()] = rtrim($alias, "/");
            if (!$id = $modx->findResource($alias.'/')) {
                break;
            }

            $obj = $modx->request->getResource('id', $id);
            if (is_object($obj) && $obj instanceof modResource) {
                $modx->resource = $obj;
                if (in_array($obj->class_key, array('disThread', 'disThreadDiscussion', 'disThreadQuestion', 'disBoard', 'disForum'))) {
                    $modx->discuss2->init();
                } else if ($controller !== null) {
                    $modx->discuss2->runFrontController($controller);
                }
                $modx->request->prepareResponse();
            }
            break;
        }
}