<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8"> <!-- utf-8 works for most cases -->
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
	<meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->
	<link src="https://prueba.gphsis.com/beneficiosmaderasback/dist/css/email.css" rel="stylesheet" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;500&display=swap" rel="stylesheet">
	<script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
	
	<style type="text/css">
		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		/* What it does: Centers email on Android 4.4 */
		div[style*="margin: 16px 0"] {
			margin: 0 !important;
		}

		/* What it does: Stops Outlook from adding extra spacing to tables. */
		table,
		td {
			mso-table-lspace: 0pt !important;
			mso-table-rspace: 0pt !important;
		}

		/* What it does: Fixes webkit padding issue. */
		table {
			border-spacing: 0 !important;
			border-collapse: collapse !important;
			table-layout: fixed !important;
			margin: 0 auto !important;
		}

		/* What it does: Uses a better rendering method when resizing images in IE. */
		img {
			-ms-interpolation-mode: bicubic;
		}

		/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
		a {
			text-decoration: none;
		}

		/* What it does: A work-around for email clients meddling in triggered links. */
		*[x-apple-data-detectors],
		/* iOS */
		.unstyle-auto-detected-links *,
		.aBn {
			border-bottom: 0 !important;
			cursor: default !important;
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
		.a6S {
			display: none !important;
			opacity: 0.01 !important;
		}

		/* What it does: Prevents Gmail from changing the text color in conversation threads. */
		.im {
			color: inherit !important;
		}

		/* If the above doesn't work, add a .g-img class to any image in question. */
		img.g-img+div {
			display: none !important;
		}

		/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
		/* Create one of these media queries for each additional viewport size you'd like to fix */

		/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
		@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
			u~div .email-container {
				min-width: 320px !important;
			}
		}

		/* iPhone 6, 6S, 7, 8, and X */
		@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
			u~div .email-container {
				min-width: 375px !important;
			}
		}

		/* iPhone 6+, 7+, and 8+ */
		@media only screen and (min-device-width: 414px) {
			u~div .email-container {
				min-width: 414px !important;
			}
		}

		.primary {
			background: #30e3ca;
		}

		.bg_white {
			background: #ffffff;
		}

		.bg_light {
			background: #fafafa;
		}

		.bg_black {
			background: #000000;
		}

		.bg_dark {
			background: rgba(0, 0, 0, .8);
		}

		.email-section {
			padding: 2.5em;
		}

		/*BUTTON*/
		.btn {
			padding: 10px 15px;
			display: inline-block;
		}

		.btn.btn-primary {
			border-radius: 5px;
			background: #30e3ca;
			color: #ffffff;
		}

		.btn.btn-white {
			border-radius: 5px;
			background: #ffffff;
			color: #000000;
		}

		.btn.btn-white-outline {
			border-radius: 5px;
			background: transparent;
			border: 1px solid #fff;
			color: #fff;
		}

		.btn.btn-black-outline {
			border-radius: 0px;
			background: transparent;
			border: 2px solid #000;
			color: #000;
			font-weight: 700;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			font-family: 'Lato', sans-serif;
			color: #000000;
			margin-top: 0;
			font-weight: 400;
		}

		body {
			font-family: 'Lato', sans-serif;
			font-weight: 400;
			font-size: 15px;
			line-height: 1.8;
			color: rgba(0, 0, 0, .4);
		}

		a {
			color: #30e3ca;
		}

		table {}

		/*LOGO*/

		.logo h1 {
			margin: 0;
		}

		.logo h1 a {
			color: #30e3ca;
			font-size: 24px;
			font-weight: 700;
			font-family: 'Lato', sans-serif;
		}

		/*HERO*/
		.hero {
			position: relative;
			z-index: 0;
		}

		.hero .text {
			color: rgba(0, 0, 0, .3);
		}

		.hero .text h2 {
			color: #000;
			font-size: 40px;
			margin-bottom: 0;
			font-weight: 400;
			line-height: 1.4;
		}

		.hero .text h3 {
			font-size: 24px;
			font-weight: 300;
		}

		.hero .text h2 span {
			font-weight: 600;
			color: #30e3ca;
		}


		/*HEADING SECTION*/
		.heading-section {}

		.heading-section h2 {
			color: #000000;
			font-size: 28px;
			margin-top: 0;
			line-height: 1.4;
			font-weight: 400;
		}

		.heading-section .subheading {
			margin-bottom: 20px !important;
			display: inline-block;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 2px;
			color: rgba(0, 0, 0, .4);
			position: relative;
		}

		.heading-section .subheading::after {
			position: absolute;
			left: 0;
			right: 0;
			bottom: -10px;
			content: '';
			width: 100%;
			height: 2px;
			background: #30e3ca;
			margin: 0 auto;
		}

		.heading-section-white {
			color: rgba(255, 255, 255, .8);
		}

		.heading-section-white h2 {
			font-family: 'Arial';
			line-height: 1;
			padding-bottom: 0;
		}

		.heading-section-white h2 {
			color: #ffffff;
		}

		.heading-section-white .subheading {
			margin-bottom: 0;
			display: inline-block;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 2px;
			color: rgba(255, 255, 255, .4);
		}


		ul.social {
			padding: 0;
		}

		ul.social li {
			display: inline-block;
			margin-right: 10px;
		}

		/*FOOTER*/

		.footer {
			border-top: 1px solid rgba(0, 0, 0, .05);
			color: rgba(0, 0, 0, .5);
		}

		.footer .heading {
			color: #000;
			font-size: 20px;
		}

		.footer ul {
			margin: 0;
			padding: 0;
		}

		.footer ul li {
			list-style: none;
			margin-bottom: 10px;
		}

		.footer ul li a {
			color: rgba(0, 0, 0, 1);
		}

		.icon{
			width: 4%;
			filter: contrast(0);
		}

		.no-margin{
			margin: 0%;
			padding: 0%;
		}
		.custom-margin1{
			margin: 1%;
			padding: 1%;
		}
		.custom-font1{
			font-weight: 800; 
			color: #003360;
		}
		.custom-font2{
			font-size: 14px;
		}

		@media screen and (max-width: 500px) {}
	</style>
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
	<center style=" width: 100%; background-color: #f1f1f1;">
		<div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
			&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
		</div>
		<div style="max-width: 600px; margin: 0 auto;" class="email-container">
			<!-- BEGIN BODY -->
			<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
				<tr>
					<td valign="top" class="bg_white">
						<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="logo" style="text-align: center; padding: 5em 2.5em 0 2.5em;">
									<img src="https://ff9e43e01aa3178b0bf8b90e8171f5be920990ba9c571bd9afc7d65-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/maderasConecta.png?jk=AdvOr8tRUGQjT-odSbekFRNk1OOoywSydBYvxJx7C1-9_j2af3DYHCJsgvyBa363kyTIAxWrdNSbqlydd0xF1aGtba5dn0iLrM77eG1FW5F_PoBISr-alN3_GaECQJnxLmaTnG6CG6NIT0HUJXX8sPGRu5GX2hsArofEj3yIz3i8FcnVAq-DzK8FY-50w0o-C25j4TH1NqRbgfLI0Km4rnDZUyZBebTO9_DBoImZTIn_E1mQ9-matFKC1lVhQrIw8x-vz9lY8_KTrVnX_ZtCAWvCglZeLjMollY9QMeTd7AQPaKtfUZKTOspRhEQ_AOvfL4jeKDXnw1gz3Uz0Zfoau9jXNpUl83va_qxPi8MMVgBKb7GX7nuoJkUydnZIGY0DfYWTqQdf7OF-fYNiZD-qSJW-_P4hvz993uznxZI8oiPtU6L7ilsiql0qLtmeHc4QECqloxQresKgZmGWGi3aJDJs3sCgsxZtdYcEeOvr2H-LbIf3tpHDubP19VQnJzo6-y4yvywGCwKHGZwm8MdQz8t91FIcUP2FETuQrhaeOXs898TqCtI9PujlmlCxg1qlCQBGHDOUvqjsc3CYCuoOlzPzosIogWCAxogAVteoCdhxtKZ60FCGAaeZke1OvKmNAcz8y8Rb3mxI6Dmu8_HVpUAiIP-9si3kfOHius3DWEQ6V6CXWexxYTtZ6WZYHU3Z_x8re2jLtTMWQGoMEFd0IfVIAXz4eMNXk5ed7hYETOZLl984W5lQjYcMCYeh45yK6_d8h67er8EGHQDKKJbAI66-_MaGPxsyQaPQSIEt-GagneMCmwRn4Nh_1dYhZ8MqNQNHxDyxnTADlQ9eRN6aPbZN2ZslsVS6AyeFZUu7KPWtA1d5VIw0sCa1VwN82ABvfuNdo1KgoOrB0_DzWPaU3rWp3YTwRrDWt-Cwxy5Ip3RvDeKn8mC8Jnojre36NB_9Q9mqYpIDPUvgyxsST3ahvFCg_wS1T1k5plsWikwap6zwIPKzY8MSZyCRZRdRyD2hcfAKlCu2AlsYSdW9zzSSYlYGdjI1x4oHOi0Rvl3TzlsizYaAnG3TWe2mABYjY3y-P3c7YfzwOqr6g84wA97vBcgYILp6VFCfFgVvp6MUike8Bt44idtZDegT0-Xr1Ve6uJEPB0aUknlI5Q29wO0MJKneuE-9Yyu9xb4Y7AT&isca=1" alt="" style="width: 250px; max-width: 250px; height: auto; margin: auto; display: block;">
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white">
						<img src="https://ffa7c3b217eceaf0c6d34d7a1b1f7eaa97367c083b7ecf8c96ad78c-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/qr.png?jk=AdvOr8uTiAf2eurXtJ-3fXKlYUhDgEZ6Mb98AJJtWDWV5a87F32AyBJIHVnVmZ9ASjVgNlLJDCa5u-ctQTjdh7C3pV_19p2Ugj_qfPrMOvH4Bx0biLvfFC-aYHVscEEgInjUNX0xPdXNmRtigmoGCx4mdmlfAtkJjiB5h7tsMR0guDTKOwdY4C4GuFliKiV3Ka7tykQU5JmWfcmmmqZEMJxo2DhCK-7Hz433dtAkczGa3DN8ep4uwRrnoHK6InRaNA9bCVCLn4p5WMnVAHVqedaPnBq67Z2VF-lziXJzgM_qm6TEizpkVGVtFJ5orxdGWAn1y4nL3j5XawFmwvZxCBIERhR4BurDTNUwT1b19JdjWHe9vDB2Nr6Hmdpxd9IDtPAebGlVWk2ZGYyzVM3nK2FnGtCb5dKlvMWgxEc2_9woR7OwGwIRe5ZXlG2krrafBnJ0aAWp8dlwSjqOmlexuejbK_NDd2oSu3KqscjaJoT1ZMrdIh-0bIYuaETIAh_0jtZU1sFremBuXg00vtPBkcU9MigamdPDloC1RVoZ6tI1YRSrmATamidfjB6F-FW4R_6NOiQfL11RYj4erWYagqZy9Ps89ohxkkIoTeN76ICAe8jWm0KrwEm8NSkQ1xGrvVcbXSxm2vfeaGsWFJe09bUU2RRcY6pS4PASddLffEZQBr0y-Y-Y4CrdZDZaHRHn6tEJp8ThvYefSxIL5vmhlWqExMzx1FLLRHBDJmNIdUtn4qFNPJXlPQMtzCyLrTouub0PF67cNksCxvKuNMdY_cACCy8TQOARU2nlKtPKcCexSWM89ayzpjX-J4uXv4tzYeDRPZOpEjMsaez3opF0RTWumQLWMtD_kuXmznJYMzWesljE7q6hhyKKkaC2Ycn4nM2Nxj8F8Zx6rnkIOtT1TZGEV_okQB_tz0Vi9L6WmMkQcEXoi1pX_fhkBEODgKSIGEDg3I6LtrWZ_d8afGNhk3hRThzNUWi7uPt4b3d5t3XV2smK_8g9PoCywMqbY1DDbp6VPoYTgYoiCNCsFUzHzM0zAbXwp97J79Uj_EjFKvo9s4GX9Os2CL0dTLIM0q-5rQTn4gcBZm_k5KejqoQe4I4eHzhSij8zHWqM8EQ7D9qUxUl8FlnGid_nt3pGxNoIZVlHjw3m1ih2K1hmoF2lIOc&isca=1" alt="" style="width: 400px; max-width: 400px; height: 400px; max-height: 400px; margin: auto; display: block;">
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white" style="padding: 2em 0 4em 0;">
						<table>
							<tr>
								<td>
									<div class="text" style="padding: 0 3.5em; text-align: center; padding-bottom: 10%">
										<h3 style="color:#003360"><?= $titulo ?></h3>
										<h4 class="custom-font1"> Detalles del evento</h4>
										<h5 class='no-margin custom-font2'><img src="https://ffdacf86624bd282e7ea963ec48fae7b69b993bd763136e0800a53c-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/map-marked-alt-solid.png?jk=AdvOr8tL_hL7SvHeLO_JHu2ER8bWaaY6YJVQo4mTFraSxsjFXpyTtY_T0lFzx-CAQx3plW717OkEyuxl2_XRwMUYT3WgbCZnG-SBpUrWoxPT_z_kPz0s2QXkoFWuX2sB3WWTpw6pIgjz_lJ6JheYgwJfLH-lh3O3nMNIadl8DtzajBKuqoBqsDRyXJXzJf5oCKU15yUMl7X_RFC2iMkhN_f9yK0pOdmN6VNJyp_Qk0ypDZCS7U3DOHlQcmcEXQPvR0jTFASSBrHJC5ag5J-1_N2bqSnt1m0LEyPJUBbWd12isM8X0G2A7qBq1HUCDh73QHtmHs-u-MJB8koAB2IWsbSsMKUTp7Drj2rsjF9ASHkvBRzaOiWVYyylJFHQhIKxpG1VwK5kDHfTDDW9urX-8QkrObc5wGCb_Eu3KeFAcPaN98_F6Jl5O39g5BcdnvbMOWxGN-_DAREYSA4FzIQO0A1_6DqgdiWgzyTld45shWtEgGROJsTpXAQ8-fW0UdkPE-gSD3Fz6ZJ97ObpI5AzStpngshyPcDMKf3bWCtqXRgbCSMF4MleYdIuAOxz9Rb8JL4MDDGV5pLolA1VBYERszej9ZXj0PTpmgs06_6Y5Rqy_A7TRgj4vy89zlvQ3kH-Wq4C85xFIcjUTlpsOHgWc3eeuRLUPRlNhbEnPTCNwCtEaSx56ZzxQvRhvosCmYrIBoSmATecoLHF84rD8c0ySZ-7zHgRv_xAabToO7PlJk7B4tpkd4UvZ7-kUqtDeUsbJN4ATFojzfuFjlUNSNmYist4jjHqqWkxwRYRtfQnE2tvXEYYs6Z1HBESCFzNNjJQaRAmxRK7d7NIy-TAmsetR_pvVvBaM3kwxnK5vSceN3hrReNLci5pnH-dfTXdvWBLcvnRFpiclFMVPFQZoV76GMIw9oUFK4Q1Y4K8MOkdWTvCBoL4t-DZPqB6XGfrffNpG6G_C808c2vzlnUtlfxDfhbvFlYq36_U79HxTijwlASzhzuElMGiuDLWIgAkNMxSzEU8WzwBVkMihgwWEau2_eHktARoqUAPzH1YwzSkEXUgL4lX-zhzZ1_8xzgr6fBuUtiDuVkNpNAiatEXzPlHnzMBVJqQvro9hInL2p0vEoQDxjQpAGv0wFYsdZiFBKYz3wX5eDGRvuVTZLjE_88NmxyiuP8WG64zRwmBbzsLTmjqrlUVWA&isca=1" class="icon"> Ubicación: <?= $ubicacion ?></h5>
										<h5 class='no-margin custom-font2'><img src="https://ff7a3edadb3574b059f9862fa256ec8cba79ae9db8cb0e03ce34665-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/calendar-alt-solid.png?jk=AdvOr8t2kUY3LilwOdnV2fN7do5ubNtNKoeubvfT5wRJJvQAehvuTlv6ZvvPFbi2NRLjizUnDA1_4tGheWRmZvB4QNYrlOxQZfy42VMXv3dXbacDBXQoiJLLSIoEace-B9f-2-ElC_kCraDv9gH1BGOno7qCEaxi9ajkufOZzFup3vV6kRY3u8xol4aLsyPLOsdptQiUXGBSmavasMIhmzbbab1jUK0dUeGMRQfeIngZzO1znVSkYWB2PuwCjb6XuvGhxATDGm8WGJegy18rELU5J09YF3G0vAAWMWypajIgSzKU1AdXYokoELpXix3pohnPcwZs-5djPk9-a2ynaNOoTmeFArI5cHibAnD6TeOJ8lj7rAZtcBCOWF5QIQiOlvsUwQajkLxpsChLTmmvNWbwy261iDMNyPzVUQdi8-BUQnIu6p94h6f_LTg0vkBqTLESjlDIe7YAcZeTFZfXzgthlX27aJUHAp0D94wL5qQsAisnLSH0t1oUcsSxB8BD4p9Pcz1sRTxl22ljfHITxC_YnXu_bQuaP5_cNLbRXHGaTL1qfdEj9iOuykEeJISM6DNvkxvdx-kMsx49UydPFLSePRlZW7TrW71n4DYJUlEQlRcCW1eqvsHVgRvupg-sCw9doncPi1Tpweoz_WaMOhOViWbTUJVltvgCPQ9nmjKYwKtJ3pYJ3dgwDyThjwDS7OMo8gdT77Q-0QWsMt9xRwcHs4LUz7-F0LABt0KdcLt1xEH1TDTGXkysmiMOxFDUTZrZE2CocjwBXN_XVJjmfuIF56EBQQ9f5Dt7z_wuq2f4F8eex7agp5lFzZreoKrie9-_r-y0-NG9bNWRyjqkIbmFXVxM9EFu8mkCaevSVbplDBZY0fGD9--vC05fyvB_YsS3UIXvbrgA4IQQB6UU4bwVWywlz4aM6duLWq8IzHumbxj_df-aaei8XTLBDk6GnFSUYiwoLjH0VfCkDoK7aK_uo-j3lg9H8s34YBS3yTjzp0WlFN9x9NmdF5j-1VN4sY9_kpkHY-HXKDjXlw3wpJl2jgL35nSfaCw9pgHdUgIKNmk4r0WkFwiOLqsczbU57K6JyfDCWoCdyn34aGZmsjV7PsBFsvrsge7zR0ZWP23p-UJ09fa7evCL_ltiDNeno9Wr9B7HiecaQC_YEuI82tye2ZoX-BQYecztYqLrx9KY&isca=1" class="icon"> Fecha del evento: <?= $fechaEvento ?> </h5>
										<h5 class='no-margin custom-font2'><img src="https://ff2d6349c3e039de7d518da408421325f33f92ce95753747ba1795c-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/clock-regular.png?jk=AdvOr8srn61EEsrYCBl_yv500qcNofrgTkFUhbH3QbUAN-ebHq-npq6uP8dwM9yPbs7iMS63uqN9bqohe8YXIHzSNKq9KdpP_RMtwYbs2St2z7m9D47tB-3-dJXdeKlrWau1O9SUh6rgag3YChn2PTRF2PNTOJLHIgQfPSbuqy1uEoG185T9vpmsOuHNmRGXqybKmaHtJe0I0mZ-ugveTLUQBZT_v3y56bVGnDzfMc67vEwaC5KvaxabCJndUOq8wlrH5NBK26UvR4f-bso4sTxrqYHo5dmWCeuCs3buFNondwXvbwQuc-kTRxU26vN7EONVfStejUfRatK3nlLRcYy1Vzk0bl0BS4u-MhD_3VvpHI-CiGRm1SljliPznEEBPB-NHeEjAfPDIgkl5loWjP5DkNsuKBwvFus2lsDl_CphgPnmdwTZJZrN_N62AH4qVSKdQrADlBDqXadv_QgFpBFkrdAC_dhIvofYZvbDPcPmyHFkkG2Mk9IIWVn-_UGMr7yUxv43gV2-2Mjha2fGYI0bj3Yaq-miaOcYyKtd1U6RtLYPklIjiBxdr56joYo7Pyndz27bZ81QGXJlIqVMeEMizsxVnbIvmblCPcDGtrhfE3W4xduegmxXMz9BWD4uBeL-bLGHTSukJ8vHsj-7xQoXlCVoQ8B_DB-aFSqjPktTTjw0JDFsZXKzrUxiDaZ9GD9HEpTF25KZevkSSqBSRoMzGIp_5SbicHwtkS_K3pxm1A_ZooYMevKFdtt0Fou_gMWBPEQzzyuSgGJ2O_0tqDCyiRCXAv3AT-Qyixswq5wD3t6KUASwkPZ5_VOupgfDwPikTDvrL2mVy3CuNErYmbIP1NLXYcRjG9gOQ12u-yuOEjWsecKZDvnq2nK2irnrC7u44H4-HXqQ8HFOC-LmxS9eEz5l5-frrYYebZdJZoWM-CHymzg1jGQKuwQm1yWGn6BcN78u7Eo1zIAgVh6GvferoNgSTKXZSaCsm8UP3NyoGbIHSQhafitgU8IoAETnj6S0G0d1zT6qdNT_K78jwc6otiJjhGc0YRGuA02QFRYw58dw0Hbdh8qH-wy7GYFlqg9rbBLOqhbDffXp5fYXkvX_Ubov9a1LrA4e02V4SywIyqec7RF3D5Ww06pxG7mksi8FOb4wn-jX-SBnNx2YeVNhaHlJJ7ig46S_gV0&isca=1" class="icon"> Hora del evento: <?= $horaEvento ?></h5>
                                        <h5 class='no-margin custom-font2'><img src="https://ff2d6349c3e039de7d518da408421325f33f92ce95753747ba1795c-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/clock-regular.png?jk=AdvOr8srn61EEsrYCBl_yv500qcNofrgTkFUhbH3QbUAN-ebHq-npq6uP8dwM9yPbs7iMS63uqN9bqohe8YXIHzSNKq9KdpP_RMtwYbs2St2z7m9D47tB-3-dJXdeKlrWau1O9SUh6rgag3YChn2PTRF2PNTOJLHIgQfPSbuqy1uEoG185T9vpmsOuHNmRGXqybKmaHtJe0I0mZ-ugveTLUQBZT_v3y56bVGnDzfMc67vEwaC5KvaxabCJndUOq8wlrH5NBK26UvR4f-bso4sTxrqYHo5dmWCeuCs3buFNondwXvbwQuc-kTRxU26vN7EONVfStejUfRatK3nlLRcYy1Vzk0bl0BS4u-MhD_3VvpHI-CiGRm1SljliPznEEBPB-NHeEjAfPDIgkl5loWjP5DkNsuKBwvFus2lsDl_CphgPnmdwTZJZrN_N62AH4qVSKdQrADlBDqXadv_QgFpBFkrdAC_dhIvofYZvbDPcPmyHFkkG2Mk9IIWVn-_UGMr7yUxv43gV2-2Mjha2fGYI0bj3Yaq-miaOcYyKtd1U6RtLYPklIjiBxdr56joYo7Pyndz27bZ81QGXJlIqVMeEMizsxVnbIvmblCPcDGtrhfE3W4xduegmxXMz9BWD4uBeL-bLGHTSukJ8vHsj-7xQoXlCVoQ8B_DB-aFSqjPktTTjw0JDFsZXKzrUxiDaZ9GD9HEpTF25KZevkSSqBSRoMzGIp_5SbicHwtkS_K3pxm1A_ZooYMevKFdtt0Fou_gMWBPEQzzyuSgGJ2O_0tqDCyiRCXAv3AT-Qyixswq5wD3t6KUASwkPZ5_VOupgfDwPikTDvrL2mVy3CuNErYmbIP1NLXYcRjG9gOQ12u-yuOEjWsecKZDvnq2nK2irnrC7u44H4-HXqQ8HFOC-LmxS9eEz5l5-frrYYebZdJZoWM-CHymzg1jGQKuwQm1yWGn6BcN78u7Eo1zIAgVh6GvferoNgSTKXZSaCsm8UP3NyoGbIHSQhafitgU8IoAETnj6S0G0d1zT6qdNT_K78jwc6otiJjhGc0YRGuA02QFRYw58dw0Hbdh8qH-wy7GYFlqg9rbBLOqhbDffXp5fYXkvX_Ubov9a1LrA4e02V4SywIyqec7RF3D5Ww06pxG7mksi8FOb4wn-jX-SBnNx2YeVNhaHlJJ7ig46S_gV0&isca=1" class="icon"> Límite de recepción: <?= $limiteRecepcion ?></h5>
                                    </div>
								</td>
							</tr>   
							<tr>
								<td class="logo" style="text-align: center; padding: 1em;">
									<img src="https://ff395a923650a24b6a456fce74044cc66423d16fb223a16b94954fe-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/Logo_CM2.png?jk=AdvOr8uKJ1Yv2wDT8--YQyzjkgCcAsWf_q5EkV0Jj6v_gVpBRCLxPBdJuyfD15Z3YJgEfeu0fDHGhieUPsAHv42g_Cj8YnOVIGDLhL-SMxLow_0Rb7ZF9RKf6U-5aNlw3h9VVAN8mjZBHK57H_-dYuFAFQENiW52Y7La_2_by4GhuYEycBL0am95UPX5fPihhes9S3-MQ9vg2GyoCAt_Vtzdws946cmEfo31nDfzaF5RZZmaHYh2owJCAL21u01rTURgOFq9JOx3u4Aks0bjinssNDh1gSxAbBTDX0b_3kBMeFI15dzX2eRCipEZ3dMf9wF4mXEmRWKC_wH_f4eDVv4cKTNrEbxLqvTi5ff_D0mqMfoW67z5TBJZqgYP4_blV10qPIxTTqLJukuNAJlL7yLFHmYomScg2M5pCfYHJjNoK2djEbaH-86D3YePgsezxzfHkXLgZSgzHBAfd6E727xS3QojU274GcMS51qPvFCieKWxkdk2nryCufJbEp3W2-7p3X-ZCSR2kqMmGi8C4lz2A1uOnGhzJ7pzcakWzx-m6QaUM2ssFsugzCTagkqOpp6xRfyKJ122ibsE-WXF9V1S8UceccfX0FbDFA8pkipjGhbcJmZasr8Pc9ru0oZ0QOKoKgC9MVwbDqgsDLXPg03pRZKMa2SeW_fuR1CdYDXvM4YkyRUxR4TDb3ejwtwz_1BfBOCu-RL1tEVRKrT0iNNKc1RD97ahjFetbhwpFR10oJekuzhrhiftsYxsn-hmzmL-ndca-ynG4Z2Hnv_gNUW2REQW3-i2H40K__A--0_hn6GHD9TxXV_beUda1zlprU9wJddk2VgY3X7XpiHdPP74Un0uDfOVrOvVwdoFsNJHp9Es6NwBPt4CXC52_ZAB4QcILCRtNn2UxvkIfS0z_HsYsWiKy0V0PrRBSxOhkx_zX42Cmg7HS-wLM7P20bRFnM9_xvDMsznMXBO6MoWwtsRojxFWMVuJemwqRa2obmXQjuPNfAOcqNt4MV2DYuwrPfiOgEjSe1CHEJ2Fw9em_VtQYyjJ2YvHJY_lTyCfFWuUtMtTOqmz8VhwPX0ZRpF95WSuZpRS8WhndZxc3tWDcB4DSQdPNRxFAZROolCKDTU-HZ0Tb6Rg0BWPgGY4yEBXxWWeHAY5tTr0dC7o0iZ5eSo93qxOAQAD&isca=1" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
				<tr>
					<td class="bg_light" style="text-align: center;">
						<p>© Departamento TI <?php echo date("Y"); ?>
						</p>
					</td>
				</tr>
			</table>

		</div>
	</center>
</body>

</html>