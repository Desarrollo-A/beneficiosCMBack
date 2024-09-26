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
									<img src="https://ff69d5722c1e9f58e3776df993c560794c1eccfacd745f771f856a1-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/maderasConecta.png?jk=AdvOr8tdb6CYW2bLvX3fM5Xivm4zqb8tZ6h1jrkuQil-seYd7Hwh2E4lTpBblZNsDkmKylaYsJ2o0Ami0YW1Pj-qYPKJfFpMCZdSPdpyBfLoK6JZZETqLUkHpsmi14Bb2t_Oi8G60lJGFDe0yuhRFR8IG8ZA1jaeed-meZWTfDf0V1SlH06z8bZX66r4vlDrey030ZNGLfYKn-ji0I2wGEJs25xDx6CkRkUfSLuoelnGAzBvlu2rleuveTEH0DXwgifwiDTqRn9Ystq9B2nTO9VtaimzxOeC0xrsGutK6mB8Ml7sCUbXQB5RCeQlFyI9hxC0o1GpWQiANxEhy8PV4V1FOaeKxb-IgpDZiXeDqb331Vc0fTbeWvymNL4UE4iE8NnGwE1cGgXvj1lwWOf-KcBcOB32bXIjtH-EhQ12RvcEsdokGvunb8c8SQ7mGkU_eL-jXfcqZKFmY8iSM9Xdn5ZByYNaM8OEjvN6k6MtmE5XGa7WrEcRZnAt5Y_HIfqmJhhZ6Qa9X645900YvFHzwOqunCsivGuU6FhYCxxGOyUO6EBwCF7_60j3y1RrjRnn6unDYx8bV_JbH_rscj1JPYvM_19O4unchdrPFqspgK8wKJqV7Iqm6Uw_jOOTkPFIkEVMZZGfJjaBn28RTSR3E_wb_9MUXfph6TGGHfcEhzLhmwDE6Z5eGR1RXwKPtIaV8NtzX66KKf84tUmpPYb634wPbtfKthtdONMkNx5Y-Px2cpwTV5wty0hXDU4iAaBhyAKW2-PWJB5Gfc8FFra5Ch55mVkPpPbjsZ7o0i9SpF6xM-LS4Yw6JSVkvbvGxouF7wDzCMfxz1gOY2Xv0_Af_UdrK2K1RMggSIEKJteO2mRxlReZm0OS59vIyFR1ntFreeVOszRHv2R7V7plUr7H9IfMx1bowmXU0gDHkSWd3iyc8nfBIgVWqEpo50c2e4FvYdAkYvhxyV9PtSH3LMF9r-Z7S2M51bk38rL6Zu2kasED0co8z__b-kuzd119TT7Fib_ufNlDp_Ul-E6nl-9WxUQoaXM7j2PneYXfNXgSq4t_qcd31lx3LVFcrUC01r2evKcKIPRZ4alGP9yNF2crLM185nSelnr3zle0uWNVl14xSqv9rZM1PJ3DrzLFaMdu9VBdz1M1bwf4rfsSRXq_qwAgPlL8wuqN42wf5yjP&isca=1" alt="" style="width: 250px; max-width: 250px; height: auto; margin: auto; display: block;">
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white">
						<img src="<?= $qrFilePath ?>" alt="" style="width: 300px; max-width: 300px; height: 300px; max-height: 300px; margin: auto; display: block;">
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white" style="padding: 2em 0 4em 0;">
						<table>
							<tr>
								<td>
									<div class="text" style="padding: 0 3.5em; text-align: center; padding-bottom: 10%">
										<h3 style="color:#003360"><?= $titulo ?></h3>
										<h4 class="custom-font1"> Detalles del empleado</h4>
										<h5 class='no-margin custom-font2'><img src="https://ffcef83676b3de110e36fad0eefe668289fbdd777806979d190a865-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/user.png?jk=AdvOr8vlCbLCxjLwPr6lA23qqKJ1MyP05mOjWd8zL3o0fF_yjHhdDBw3LAgrAo6WyMSumsIWh4C2ahDtPFX5awYhamS7OpqB2jxeLRsl3XVWg-1I8sv3H7bAsHLcKI6lTxpPSbV0Qq4bBJASqq5Jpv-pQ-h0N2GfCXPA-I-HSaJBT0tJ33Uyv3S_6vKr1ATxm3rChtxofjUTxM1vOjke3dbzpYXL8eWmQyA1ao9pjDF7mFfNcv4-W3lLBhmoOGXxdqC8VBZTbWR3kncyFCM5THJIm5I0mxjhXkYHf9bPXP43249foVN0Rpx43bYYihhFeW_Wg6nPZOs7lPqKBYKBQB-Pu7fy1y2CgVE7514-JiKQleLgBJdSOdu_BPVegA8WG1jM7dPwUB69XDzaEJn0Oh7X_wUxDr09fj0a-0hUa1rSgs0sf_uyJf9lnwD1Qk3R5-_EYDvdNzi4A6Xr8J3q3KKtJzo_Xl9ovwO8a-6o4jXhFgnH-CaOijqWvhZZuSSjUPiO47fbGC-90hod3FFbMUhEu9fzbZ9G16i9EpW71bNizMh7znOAe-JQjdLUW8AtJ94ol20Kj9LChu56FZth-PzsFFoq4vjuZTLXRA3sbbbAL13WG1rlvaM_G510z9m_PhCOWoNoDdDeJq22G7xsS0-Wree7k8mWOARpt5iMnp2TBC-w9LvThA7q44s6ddYqQZEmxL74KO86g-GEm8QkO9R89v9g3rYSg3kBcACJdm13oy1SwC1Ez0_-jnBlikR_MsfJ-MBRnZHEhG-FnV0V_vmoRBzLSaPMtOQ22adSzuSCrvSFxchR2IHon5krerucSnZamj9XVcbs44eUOo45YVdt7NUafOrZiKHSvpa1JTEwev8oTjWES_8kunGrH5b57aMQFs4BxR9vbdxWNx-R79CwIOs9CZiM5tJNkYOvfRg2Nu0EQxbb5a8nPO7njjocEx_PE68icG0EOzh87YyxHJM8WJuqWMksCxClSivfsPQpkCRgx8D6xXz_8z65InhmnhUjvpObrBnhx17V1VInvbTii3RB8Fr0mCdMSgGy-H0Q59pTZrxBAEvB0xbhiKlcnFPZXGWJu_LHjDyrzjMt57OBQLyYY9DHl8_6mjze8rMVM-lBqs6ttMvOO4etcSWcKWIEICymLsj4Uj6yaathZRxWhjk&isca=1" class="icon"> Nombre: <?= $nombreCompleto ?></h5>
										<h5 class='no-margin custom-font2'><img src="https://ff4779caa76378ec3895b2a1595abd104c96775b00b60b63a435c98-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/id-card.png?jk=AdvOr8ufHu-d91tTiyPOZaiy05ikOrp0Qjeb6lK590GIvY3G7Lb2pdTgJjCmT4opN2FkrziUiHJC10lPHpW5Sgi6qE3IjfYCNnpAh7-fC5Ky3i1lHhRL6h4wKP-ZjoVuoKniiBiXzMOVbjtWbPDfcD-4b1a2p8EoOS2v9768akwFh3hwRO7OjQkrONmxxBOq3d2hdowWFTjF2GreUJalzbTZj_1wumiVUmTOr9CRqHkpFk3n83YB1yWz5uoPkLJbQAPnT8Fnooct0engyPRx4aj5x4tsnUDrhgeC-ql0z7fzD4SlKSrx2V0PgpDwoJdGjGewHKMHlJzAcC6dh7n-e3-fjic3EWsbIaEPC0pP4a1kTO9j1Bht5urEGtMbjjLSYRqMIYT7IfzhS_dy0cVfVQZMs_Oh-8VspOdjcS8BTMP6ylqvTz7vfZvODBUA5vdY0UTWK9WQvbbdwWTdlSzJWF4QXHgXaXBx-C8xdqSspzQvSec3nub_l-UH_oVhp3eKKoCPR9_eRr67N02XfgDH19xWf-TmW8Ahp6KXtIrDDfhl3hQG6jfJIT8HHbTdc4sH5NORyQ9i_FgMsVec1JfhYVDYPoe1PmxRInuLIqnigkZigA27TbIM5baENn37g1-hx5ybFYXLTZLscuKych1-aceqoKbMCsPDKBHxH-jLk-4FNYfi22Ci6RrcZsHt0TRX5mCVn3OGXspH9NDs-fOncSSFzGima43uGjXrgLLwDBdKP5AsnyIiPbJDCm8pPmopcV-BpxNnN3xqhelIjFfbR1oNswCl5h0LaGPzi9Q91gebMgHo9Hrs_hRIhv863y-J8tIsfRD3_HelSwCLee1EJMc6ukPF6Ix_Gw2DPqlEATX-ZD-5559Xem1QNKFNgI8tsEWRuiFAYb1pdbDh9ODPticje2BOtfOqWbfwW9a6J27gwgIvGNrNB08Hxrr_a6jqwjROpQQkpvsYkvkFkp4HEED1-8GT-eQjO7CnbzzJhj-BT6GNpkwi98Qw4lWnV0kRmDKb_EziGX16T3-IvQ2C7-ZDTjXLxYeqSsxANxtzhjJLUMhnRpyPhCwPugu8VRcHPRJOxPVM9nOcbq0-cibP_N7ZP0JNS9-at-5bIZQg83fgumMF8_R-mJQfvy9dsOLnzV1TN2HaKkF7ErhLCCP7mjVGDl2NSg&isca=1" class="icon"> Número de empleado: <?= $num_empleado ?> </h5>
										<h4 class="custom-font1"> Detalles del evento</h4>
										<h5 class='no-margin custom-font2'><img src="https://ff9fcb6ebbba17cded8b04527912af492fbf01845d3c8875e11785d-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/map.png?jk=AdvOr8vbGUuMko1uveEkwOCCK11LVjLGNu2JEdavfJ9NJQtHwMJKmcENxbNlX4NfpZapebz6idudci4LezfvvuhgNoEBDIDIdz_Xa90ZyjnvSDKUCh1D-oop8vS0_Q8Ku_GW_kUwqbYh0yxBPIVMEO2EPtd0dsIWr-Q4HEzBxu6drhJHcZrYLoyq197drXn_hTKuD_TS1Hq_b_iyaB6nJtm0D1ow_YTE21yxYdnVqSu26fJjY4Bgdk_nnqVa-hNjcwEfmU55qQkZfG-dk_bJFqnKlFD6iWFBONKnqJ4rQzQK2Jc45IPyk0zmJoXUjVYWTqTE-e6-dDxkL89R_hSmfXHZ9lVuIV_NCr2FfCHWTKKLG-Yg6j4_2Znodb3KKLG1UDFo0V1GhOaDp6ZAusBBXHBaJ2_6huK_NS50-wMp_QJ3pKlyRYYnX83LhCMJphGDSL2rQryizgGhN1Fl6pzBSSLwTwAVG9BwS_kXwGARKjG8YHaq-7tZHLGBWILsxO-Hlc-44UTPRB5OuO26Qk4PxaDMHPAQbofQbwhOS7zfaTv2LZZlEWocVT6wgERN8paqym22E_Apq766XS1H-ZYXLDl3W_mC2zLHFZmUKOD_a-wyPpPgYkUe2pO9h38zYYDtXZxfwTx1xoJyZD1ZU2l_dNrwoeWzWuVX_uWr3wkavM5GAsWAd7ioGVGF_kur7LLK-in_uTUHSGECgYaqw03jl9CD1lt3kgb3uqXEX71S0GKrJB5HWm17VFdpDz1WEjvu4oVvStB7c4cBQn-Uce5z_1h0lRo1CQbTHxBembJDH1d9cOHSQoHn-3bEZ7jYl1HkzYSp-jEooOy1ZMVCXrKQiSRC7cC3g_0QApi3BaNKILRkeUSkWW9WQlXTQe1opBBjQlx6cZRll4v7biKOrnM-J5HcaMy1FvXsjRpYTygW9HFghyRl7MYZvyeii_PzmBPd-5hmbsHgeFRqco3kg1Z2zPwsEPdUNB8tK7LApB0OV-VPPL1XcA0mEDY0aj99JL_M27ao0UBoEhEnvXoVu_Ygmp4aWjFKh6PoN6ANMX53_COfBXZ3cOvjOv9tFYITwpKWxyP16gfpdozXN6JIBgYpX7iNMsCSaXZKyYio52pgmZgCYniqkslrv_p9s9jnbQWH7h0msrNY1PXO1Crk1DKjgSJxdg&isca=1" class="icon"> Ubicación: <?= $ubicacion ?></h5>
										<h5 class='no-margin custom-font2'><img src="https://fff77e68bd44c82d06174d84a5970dfb4bc7aee711504174da57091-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/calendar.png?jk=AdvOr8sOJ0_aiaizbNoBwnORIm7LLD23eirFBVKsxGxcyf0lr3IufDvIaWsaNz95-ULknF-0-TWOeeeMIDRBwzmrWlbTfAuqiwmlLWgJSmSJ00QN_vgizwco41k3PjymEy_8AlyV5uiHiitAwcFa5NfUylVG7CPtl_gqO0cbdhiGN9-7RaW2RlWmSfj8lY8m-5IS2o5l65Rt4e8GjI4rNxad-X6CN-LlNyQ77N9Lmsz7sJSZdQ6R5GUmdFFIAqmSHO-gKXg2cCZYmLz0REic7Ia9Mkx60-AW1qlYQYaNSP037tptQejtOm9yIfBDitatm_0Dp2hEP0TNWd-4rL6ZLHAaO2hoo6dwwnE5VKgAuVeFbULVaFemoQlXYeJ0bg6Cxtn3robrukpluhvIMCAU0nebMHR4PdNf5kYLdAgRxmYk_Tmr8VTMZIxHavZ0xHblXQxkDHPriHnxEi4P7eOpsGAfK7vMMMKiZEB-D_I97r5GQTDgqV5tKU4RmO52p2YVaSsuo7wMus2Fm8HqVlUhhCnpcEMk8H27fn7fhjxxXO9MI8gV_nFTw1e4ENpKrHXIA-a--hGLxUdu9pSUqhdy6hbkHDMkG8dozxRxrq36eSJo8yqC9naWIAlEmmNBC87wUih1DRkE6aYb-rVjf5SyELMjjjwdxx6tscA3ep_YiWcZIefLjfeR8jnIRy7HEveLWde0YVGHVXTWAurRvkt9uYG0EaUVLsy-C1SiqmuOVsWg14mIEAvfAOEjLmVo9-07l25KLwrAVmrbHNFvbVBG0V4lUWj9xZ937yUZOfzSG0rfqrFqxNd3FJj7muochy6FisTxyGqAZNcOkJSW8dA1TelCyV2v7eDOhB_mfL11-FZ_9x42oQ8Dh724Mzz44kp6ID3jk5zpgEL6f_inekfHdZ9MMWYQiYLXpyMseYWOjhPguMyFKM_bSsKyq-bLeHynl460mmKDtagwvSsNIyyZJxqJwmodkT4I1TGF-RJRdxftXaT2gRA6MrF1TQTNvDPhU_gS110vaSve8yoQ3YJna0V8zD3p4mYsA3H-0vWlymDYkaC5C6kPKKtAIh4N7QUYkhZ4abVCXlIPvlkfE_KHWink_PL0jdz7JaVhdeDWhPK0sXwNrh2Oh0N5ltaGK2aEATDe7pHcI2wCXCfQRMUWzWM6x9Zr4YnJ&isca=1" class="icon"> Fecha del evento: <?= $fechaEvento ?> </h5>
										<h5 class='no-margin custom-font2'><img src="https://fffd386298efbc45ff42d3a957ea8089a4258fc713e9a0588383810-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/clock.png?jk=AdvOr8uTVLZEBrcKod1KllsQSHUFLmMI0z5W8MVJDhgwK02HUKWt8RPRs-Ivs5kKyeYaLb9xL9HcU2ZDgiNy_GN14cdEoGvti8LmCvfaklnEcnR7MQbAlZUQIeVruO7TA1vF69NxIs2j20z7NkuYYU7BBqmzjPCZIfO-QVOIY3b47weZ8eDJkXUw6olH4E4WaMYL7SPlW1NwJWs6_jiYT-DWEcvM9EByNg2qY2NJcP8rXfxdL8J1yEYOWz3kBLDwXK5UjomO2BupNurrf2QMDk8ye4rptl4PK7ZssyhKmAtUDFf8ZY_9GaRwBXMltR0BhZReU9GfqnHYq8DJArb6OsPgttpT7yu4wN4aBacmy6xZnP186mXCY4Rv7BPFXh8aPnzb6iPLivL6Q1NiX0QAbaEQd3uBboamhiacUVOaPq56cy5DYXZ-iazsGjhtB6xwZbdWr-d-QBIj_1ZErQTNRb_9wuMPkU6Q6UtF5XA5U6t0RldxSfbj0ovUsU0hSiI9eBf2Sx11EryW4oGaHg1gdGyTGPJyvaFsnu_F1itq_IaFLjKdU81PCCOGU7exRh_UPmMosqgrW4MBW546mDSI17FTpChG3yIFRSpCE32-yFI3Qp9KrQq063hT4eD2tpo5d0EWpZkrBWVEmEvugNUJr0qTdXxiXKUOuzQzBLvAQo5muoj6rf3Wm0IO2UIPsZj69Rt4jxp_YuCn1uHuprk6UVdOV2vlOit32gb4yZ6Xzx5K99YnrA7fsoxZOdnbfQ_wN_U-rd07b9_nR3crPsdMfo5_uwhX0UPLg7_zE68LZ0JAa1n2ZtTz_Fsll1D6qV2m5_ngJeldVGA2s0DRF75LwsGMTXC_9ySbLBaeWNdeLvL5yPIjjIBSp_L3X7uAh-Q9NYYzlB4PX0qJsviCUwXlFH0acNYfHRIALo63ZkEfvHJT5HAE2wVrV9IfRzL03dtJU0fPuKPzqv7Sks0acGhs3QGkojUCaWiera55_Ga1pdYy0lwdXvTNUBbmmMuitpJhXALJLa9BRhK76DJCBOps6pO3btaNEuIbNb2qfMI9l1_RI7u4rss5jZJbgMcV7RVxmBTZ6tkjFAp9E8qpjjp_B-SyCaOoxRfHmbFNlfgIvXWNNmO4L-8hkRYqGg4Gja9WAtfLQq-Ld6AZfKoDAgANHuNYEcCN&isca=1g" class="icon"> Hora del evento: <?= $horaEvento ?></h5>
                                        <h5 class='no-margin custom-font2'><img src="https://ff253da45285a4e7f6b48f360ec4261501fc9b4ce2bcec7c7fa2c62-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/wall-clock.png?jk=AdvOr8vxn-Kz4WokbYyPmU4uVkbHNqDz75h8RB9hmzKIEK2FSgafIKNq2fL6TXIGVhML1O_4H_POKeU2LzYyrHnrv0IdxWbQuFCjZ5voIZcMZVg98lbFKXQTRG5ka9pQOb3RtYyHB6InmPLgAN9PperAIzuMDke_4frzF4nG7H30s857IXmHg_Vkn0OeNm0iQpJR8_SxU8kolZKT5H4LSbNBEV7Oqts-ifK-7Xwk9Sw8T5Sowfa_QVx4j-OJElnRzPl5QEe6iAtCTH2dlaOYANSMJau_wKF1ldOTONN8K7exdH9rqcbfNgWmvxBW7Cd5t-mBPPQsQ-MC4CKcMRk6LH-wUbkZMb-SvwaSh3kwCwlB9jOJCGUuMBmUzZliq_fkQXOCjPGSevFxTB5PXzRvJQ8KVmgBS_ocmFYY0soCylUJGyNjOMmEJET_f9dUqVgAQBhRUn0pwnhQPoeIPqOtxqu7aFL-s2PiiUtlY9oWzjpG4b8pWV8Wvj1sbOOliOZvyLfBLqwAd0v-afyI0g1cBfozEDNYK7-WIZRWU35Utv1R1GGk8zwAoCTWeA4ju47ht-VU64EB5TWPK159CpEkSYO2AVpUyxwd7Ziyng2VYaki2eV4RJ3YfKXKSmlAm6nTKXVtK_f73WL8bDZzoZk1qzAQ6zYRbq1jZZr5gFC54aq6569BtvTbi20v0C_CS5tgj6qem4nV3_PAfibuD4KNiGxBp0KwtHlFLkVcK6zUibNVG1e1sfY9mL13SMhtJgMgRkwqB1qVuguo-CMFtUql1t637_5MVjezHej0ANJhHBTtlo_o64bDuZoS5D5MN496B1kW2RBXxkiU6QMv0phctPMgoynPQC4jy7Qwwb3YHVEc1avGaLlwYtb-1QGx7m4EwRbNe_sRpVzzwZxTRtIBgM2b9UXeVd5oCgVYL-twI5iujE_cjBqHIQLnxAvOmLytpW7Vyg-ZbdtNhpGe3UUtOEsw_h6xxb-JWI64ZREmVhsu38pDBw2HoZe1pEhbTEvgi_aJvXbpnm6pV2UqMdTbyeJtUx_TZJqnwb_IhpbBzPc58jipDQNTqrnVV6OWf05A0KqUoyAS7twwSk6GBjWxszFfsHYltlMgmdI3H8aHtAHBuWLVVxYA0_blYJXMNFanpRdu7TBEPlszZplPGChN1qgRVhZMuHtBWQw&isca=1" class="icon"> Límite de recepción: <?= $limiteRecepcion ?></h5>
                                    </div>
								</td>
							</tr>
							<tr>
								<td class="logo" style="text-align: center; padding: 1em;">
									<img src="https://ff18fc5aefd86abd285c6b051755a4a009eae3bae6ef53b34f1eec8-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/Logo_CM2.png?jk=AdvOr8u8TRrjRdzy_VoStXfzt1ELmzv9vkS2us7Z4d7_2uSWuoaW0NmhK3dtFAqkUWZUOAonLaRlTiCvZvOnPLcplez9NjHbB-1tz2zr-T07OHVHelrR39qf0BXN69m0lRYP1ViHFXfrqnQtKxgRT2cBmF0eIY2vOvtFPvDSX7J8wNOsTta0AP5EmHdhXlhwKyrZVqLiRVCnE--DbXV-ufu9YYicDRmZXIehJIVCceaeOXmE2ubf4vwK2XpPYWNEvasWOsaEE6YODEtNYLBfl-iklM0DAd99HC_b-CB07EAeO_cdPPmqhSaSUi5JC0CJ8S31vfZzOd_vkV4Wji9XuYCvqooqbs8rDDhUz-sw0mzST547J7MTtOK1mvnHLURasUplwx2VQvTN3J4zHdA753Y0JTMdoguUsAYAcqM1bWNhkbiWDTcs-X6nito1jUsn2uBAOD5L0f5h8J8HzzGKfy5Vocm2Fsg7wHdQVcQbatL5_DAoQuCGdSkpYeyl3cDOc-8KLMMTswOudrwtrcnWCMTvu_YXFUf5ydXtbNg-vLDtV_MnlA7lVvANm1sIOGE83uxjkVPyfIx-K_7i9HlwcIb3FeSdiemTARUhctvnRbVKo9FL_ySTbpVF_eKSk9MfpxLTF__W8uWFivGYitO1JDdMNjo7IwGD_21xwoisN8lHW37uuFmFxNTsX69f95pzwMITrPzB5KEEqW5e1rL1Kr2_PBeX3JdH0C58TQHO1JyQ6nC4Fwi9WqpSxhSIKDBMv_Fued6xyQpOcVloEpy2ucfKST68qyJ5dC57hy82da-25Y4VmciAEdef4rSjTcqVNaLpK_DJ0Ch-i_LTPhKXRhMxombxTl7_XWzCUWMoo8gLwJgmovEV6x1Dms1uaB2P1OTbS7o3zmrtQMa-oj4NLVN5Hfp5nzbRb3UJ0jRf2oGcQVAjhVS_kMfF5NPHRGpOEjRAh42qrerBv3oTs6052rtlBOGiJXkwnvfJvwHg6MBZ8REOWfpN94kltXZAWIIJ6RxQu_x1Me5zzUbqbwF6m5BnXrb_qn2bVRuqw5VRBQnuzPu2CJtF8ntRonPNGykayAKd5BHEeagT9TzCy-7uDa6tvkDg9Cp4WuVS5RaoLw5grRNLwhWWHlBt7REyp5VzQgNuwMJUuVqKnNWmJaI5pStlg9YqOxY&isca=1" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
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