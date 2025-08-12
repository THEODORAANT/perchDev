<?php

class PerchShopGateway_sagepay_server extends PerchShopGateway_default
{
	public function format_payment_options(array $opts)
	{
		if (!isset($opts['data'])) $opts['data'] = [];
		if (!isset($opts['data']['card'])) $opts['data']['card'] = '';

		if (!isset($opts['data']['NotificationURL'])) {
			if (strpos($opts['return_url'], '?')) {
				$opts['data']['NotificationURL'] = $opts['return_url'].'&notify=1';
			}else{
				$opts['data']['NotificationURL'] = $opts['return_url'].'?notify=1';
			}
		}
		return $opts;
	}

	public function produce_payment_response(array $args, array $gateway_opts)
	{
		echo 'Status=OK'."\n";
		echo 'RedirectURL='.$gateway_opts['RedirectURL']."\n";
		flush();
	}

	public function get_order_from_env($Orders, $get, $post)
	{
		return $Orders->find_with_gateway_ref('"VendorTxCode":"'.$post['VendorTxCode'].'"');
	}

	public function callback_looks_valid($get, $post)
	{
		if ($post['Status'] == 'OK') return true;
		return false;
	}

	public function get_callback_args($get, $post)
	{
		return $post;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
	{
		$ref = PerchUtil::json_safe_decode($Order->orderGatewayRef(), true);

		if ($ref['VPSTxId'] == $post['VPSTxId']) {
			$args = [
				'transactionReference' => $ref['VendorTxCode'],
			];
			$this->produce_payment_response($args, $gateway_opts);
			$Order->set_status('paid');
			return [
				'status' => 'success',
			];
		}

		return false;
	}

	public function finalize_as_paid($Order)
	{
		#$Order->complete_order(['order_status'=>'paid']);
		return true;
	}
}