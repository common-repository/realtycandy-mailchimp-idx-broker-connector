<?php
/**
 *
 * Connect with Bridge
 * Return code status
 *
 */
if ( ! function_exists( 'idxaddons_connect_bridge_code' ) ) {
	function idxaddons_connect_bridge_code($api_url, $endpoint, $data)
	{
		$api_url = $api_url . "$endpoint";
		$ch = curl_init($api_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $httpcode;
	}
}
/**
 *
 * Connect with Bridge
 *
 */
if ( ! function_exists( 'idxaddons_connect_bridge' ) ) {
	function idxaddons_connect_bridge($api_url, $endpoint, $data)
	{
		$api_url = $api_url . "$endpoint";
		$ch = curl_init($api_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec($ch);
	}
}
if ( ! function_exists( 'idxaddons_api_idx' ) ) {
	function idxaddons_api_idx($level, $method, $type = "GET", $data = array())
	{
		$level_method = $level . "-" . $method;
		$data = array('apikey' => IDX_AM.'_API',
			'method' => $level_method,
			'type' => $type,
			'data' => $data,
		);
		return json_decode(idxaddons_connect_bridge(IDX_AM.'_DOMAIN', 'idx-api', $data));
		//return idxaddons_connect_bridge(IDX_DOMAIN,'idx-api', $data);
	}
}