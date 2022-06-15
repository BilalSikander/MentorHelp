<?php

namespace MetForm_Pro\Core\Integrations\Crm\Hubspot;

use MetForm_Pro\Traits\Singleton;
use MetForm_Pro\Utils\Render;

defined('ABSPATH') || exit;

class Integration
{
    use Singleton;

    private $tab_id;
    private $tab_title;
    private $tab_sub_title;
    private $sub_tab_id;
    private $sub_tab_title;

    public function init()
    {
        /**
         *
         * Create a new tab in admin settings tab
         *
         */

        $this->tab_id = 'mf_crm';
        $this->tab_title = 'CRM';
        $this->tab_sub_title = 'All CRM info here';
        $this->sub_tab_id = 'hub';
        $this->sub_tab_title = 'Hubspot';

        add_action('metform_settings_tab', [$this, 'settings_tab']);

        add_action('metform_settings_content', [$this, 'settings_tab_content']);

        add_action('metform_settings_subtab_' . $this->tab_id, [$this, 'sub_tab']);

        add_action('metform_settings_subtab_content_' . $this->tab_id, [$this, 'sub_tab_content']);

    }

    public function settings_tab()
    {
        Render::tab($this->tab_id, $this->tab_title, $this->tab_sub_title);
    }

    public function settings_tab_content()
    {
        Render::tab_content($this->tab_id, $this->tab_title);
    }

    public function sub_tab()
    {
        Render::sub_tab($this->sub_tab_title, $this->sub_tab_id, 'active');
    }

    public function contents()
    {
        $data = [
            'lable' => 'Token',
            'name' => 'mf_hubsopt_token',
            'description' => '',
            'placeholder' => 'Enter Hubsopt token here',
        ];

        Render::textbox($data);
    }

    public function sub_tab_content()
    {
        Render::sub_tab_content($this->sub_tab_id, [$this, 'contents'],'active');
    }
}

Integration::instance()->init();
