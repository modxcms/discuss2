<?php
class disCategoryCreateManagerController extends ResourceCreateManagerController {

    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $assetsUrl = $this->modx->getOption('discuss2.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/discuss2/');
        $connectorUrl = $assetsUrl.'connector.php';
        $jsUrl = $assetsUrl.'js/mgr/';
        $this->addJavascript($managerUrl.'assets/modext/util/datetime.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.grid.resource.security.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($managerUrl.'assets/modext/sections/resource/create.js');
        $this->addJavascript($jsUrl.'discuss2.js');
        $this->addJavascript($jsUrl.'panels/category/properties.js');
        $this->addLastJavascript($jsUrl.'panels/category/create.js');

        $this->addHtml('
        <script type="text/javascript">
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->resource->get('context_key').'";
        Ext.onReady(function() {
            MODx.load({
                xtype: "discuss2-page-category"
                ,resource: "'.$this->resource->get('id').'"
                ,record: ' . $this->modx->toJSON($this->resourceArray) . '
                ,publish_document: "'.$this->canPublish.'"
                ,preview_url: "'.$this->previewUrl.'"
                ,locked: '.($this->locked ? 1 : 0).'
                ,lockedText: "'.$this->lockedText.'"
                ,canSave: '.($this->canSave ? 1 : 0).'
                ,canEdit: '.($this->canEdit ? 1 : 0).'
                ,canCreate: '.($this->canCreate ? 1 : 0).'
                ,canDuplicate: '.($this->canDuplicate ? 1 : 0).'
                ,canDelete: '.($this->canDelete ? 1 : 0).'
                ,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
                ,mode: "create"
            });
        });
        </script>');
        /* load RTE */
        $this->loadRichTextEditor();
    }

    public function getLanguageTopics() {
        return array('resource', 'discuss2:default');
    }
    public function process(array $scriptProperties = array()) {
        $placeholders = parent::process($scriptProperties);

        $c = $this->modx->newQuery('disForum');
        $c->select(array('properties'));
        $c->where(array('id' => $this->scriptProperties['parent']));
        $c->prepare();
        $c->stmt->execute();
        $properties = $c->stmt->fetchColumn(0);
        $this->resourceArray['properties'] = $properties;
        return $placeholders;
    }
}