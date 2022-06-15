<?php

namespace MetForm_Pro\Core\Integrations\Crm\Zoho;

use MetForm_Pro\Traits\Singleton;
use MetForm_Pro\Utils\Render;

defined('ABSPATH') || exit;

class Integration
{
    use Singleton;

    private $parent_id;
    private $sub_tab_id;
    private $sub_tab_title;

    public function init()
    {
        /**
         *
         * Create a new tab in admin settings tab
         *
         */

        $this->parent_id = 'mf_crm';

        $this->sub_tab_id = 'zoho';
        $this->sub_tab_title = 'Zoho';

        add_action('metform_settings_subtab_' . $this->parent_id, [$this, 'sub_tab']);
        add_action('metform_settings_subtab_content_' . $this->parent_id, [$this, 'sub_tab_content']);
    }

    public function sub_tab()
    {
       Render::sub_tab($this->sub_tab_title, $this->sub_tab_id);
    }

    public function contents()
    {
        $data = [
            'lable' => 'API Authentication Token',
            'name' => 'mf_zoho_token',
            'description' => '',
            'placeholder' => 'Enter Zoho API token here',
        ];

        Render::textbox($data);
    }

    public function sub_tab_content()
    {

        Render::sub_tab_content($this->sub_tab_id, [$this, 'contents']);

    }

}

Integration::instance()->init();
