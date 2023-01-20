<?php
namespace Opencart\Admin\Controller\Extension\WasaKredit\Payment;

class WasaLeasing extends \Opencart\System\Engine\Controller
{
    private string $extension_path = 'extension/wasa_kredit/payment/wasa_leasing';
    private string $extension_code = 'payment_wasa_leasing';

    public function index(): void
    {
        $this->load->language($this->extension_path);

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->extension_path, 'user_token=' . $this->session->data['user_token'])
        ];

        $data['save'] = $this->url->link($this->extension_path . '|save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');
        $this->load->model('extension/wasa_kredit/helper/system');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['current_version'] = $this->model_extension_wasa_kredit_helper_system->getCurrentVersion();
        $data['latest_version'] = $this->model_extension_wasa_kredit_helper_system->getLatestVersion();
        $data['opencart_version'] = $this->model_extension_wasa_kredit_helper_system->getOpenCartVersion();

        $settings = [
            'client_id'                 => null,
            'secret_key'                => null,
            'test_mode'                 => true,
            'test_mode_client_id'       => null,
            'test_mode_secret_key'      => null,
            'default_order_status_id'   => 2,
            'confirmed_order_status_id' => null,
            'cancelled_order_status_id' => null,
            'geo_zone_id'               => null,
            'status'                    => false,
            'sort_order'                => 1,
        ];

        foreach ($settings as $key => $value) {
            $data[$this->extension_code . '_' . $key] = $this->config->has($this->extension_code . '_' . $key)
                ? $this->config->get($this->extension_code . '_' . $key)
                : $value;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->extension_path, $data));
    }

    public function save(): void
    {
        $this->load->language($this->extension_path);

        $json = [];

        if (!$this->user->hasPermission('modify', $this->extension_path)) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting($this->extension_code, $this->request->post);

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(): void
    {
        if ($this->user->hasPermission('modify', 'extension/payment')) {
            //
        }
    }

    public function uninstall(): void
    {
        if ($this->user->hasPermission('modify', 'extension/payment')) {
            //
        }
    }
}