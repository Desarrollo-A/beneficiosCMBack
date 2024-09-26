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
									<img src="https://ff79fbb7302da0dfafd834eb381b7e18508b02d771d771a814f8187-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/maderasConecta.png?jk=AdvOr8ut_vmT9bSeaW9VmkNoK0i2PtKJkolGyRGoBtegiREAGHx4_5s-3m4u6BvZpeXLA5XOwyKKECeA2b0DfZfIV3EM0KE83_QS3uCvhzKJgq5FUjiztXo0FQGZJUFWih30ol7Bsk1xtkgcYnDhhZ5J0ccgUlWxCJvXtadHRXjeztWf_mPiSYg069sZ_0MfVOO_HYIiYTzJeqeuMW1YCm_RfP0dnmFD7GbrtKyIeQJe18pr9xnA9Kwo3KWzpkcDjogWitPwZCNVR_-8b-qIYgcpjJ73rmz14-M2CHqnqn254rcdfy6KDPaP28lsdRlw-2wXG3Qq6LflNEUaN_GZqmWKSbfX99UB_UcfFYDiF9qVuCf3542h8XqYX5zpMVRxvGj-wy6-K0rwtcv9YrKPIKH-_F2RVfsdCDFI3tZJyaBzvbysHAFLoEmC--MYT-vg_wehq3iPU4qqViROd6dPwQfCqYvyIJr5Mrh5Px0oq57LMV1tt72y2lBBBRTdrWJ9-1ccnqiv3bWHO_Ig3B-9wE7guSfDhlztzCmzT62eRiQjLsgZahoDIbEaPa-eruxfnMkwAZTeSUPCn2I2seRBI4Gv6cNlGy1VWMwpKUjUiEfMO2MrFAw4N8wtUFdS337u2SOPVI7nOZ2pWEirlYEpr-l5aEYfKd-2MU-8lhdA4THty3YapQSA174_ZkSx0nUtl5BYXzU9eAv417Wk5brGM7zMFeqG0uygyVX7SV3jurd3zLs883bucyTcYPCXMv_MP8-Uqv7swfQHTUTAYgJv1b6ZPcs__MjTuIiQwKDiD5-eOsqS_7nLBjembfP9lq8WkPDZWHyRjQKV_o_azFDWQSH1K_tpCbQldmFYhZKEQj8cq5b2NJkP9Y1RIJzgZRFYqTd7WZ_oOlp1-EhgIpYBkwYyBQNGi6rMqyyZahZvgLUfNQO4sFhP512HcFx-780gZzUSYdcbHmLKBJB7EsJX5nVGWhr086QaShw266pJBkO_Shd2vGY2aO84keF5r0T2hFr9cVdVhKI20jGNzqI-xtl4OxaOFUP935gUHA4DvQPEXNf7ko4yDiA0LP_TKxCWjA1099os3Tbkas0TEWlzz2hCe5-Cs4nbMTCSnx-GpeclniD5nVAqiylR_kTdy-G0T13iLHaF5ZF7bqsT7Cw8gpb8UFLwsnDhxusJnYMx&isca=1" alt="" style="width: 250px; max-width: 250px; height: auto; margin: auto; display: block;">
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white">
						<img src="https://ffd4bcdbe479e15feae0018eb20189e4a2b8cfd950e70b63bc98141-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/qr.png?jk=AdvOr8sp41L1_CqwL3LTRNQpB6yN0VZO23KgTyMLbBxkwoRxYJ535L8oC9dFY0uxsPHJOqJcMgJMmnxLdl91i2aJDqzcNdNc54Ray5SibA9OJBCdkBj7PPFzmnGU8A1JO9QU_8tQsePxgrgxvxu3tkj78IORPwgQbCotsJCbfGkQhpDSu6A0fH3zTsnhYTc39mMtG-P3D79JWSpjXjZzlcaMQIfynd2jR78KF3VD6Lmz2UdkwjL6IKzL8pwyyUgyLzmMeQ140Kuw3YTuhKswd318I39-AgzedCmSj-AtROYmfUmP4U3w7KpxFqe1N5RFD4yyDGzeN2e5g5g10IL4lH1IJNMZ7GeWJfglj5HvKtX5GhYS_Qnz578IChv0mrCZOYP85k26nSIMCE2NwtarruMqnL4cvRW29Ym7AOFKVMzuaJnabEfsVqefQNvIbz9DwUphGk2mwAzBp78FGrxCHpWS5QqRw4GXqneoiEsB9sf4a1zFmejXwCy41-59ZcfaD56MQs5IR4DjSaVRcmydf_DUqB9U011XOV-5OIyX7U7iwxw1d8dbGQYghMb8C15bURAyn814Ty0FxasDOJ3ld3Rwgiy0RVCNNI2KusBMhDoibO4rbkruSgks5hCzOjJrgTk-F9avDzQqibIYbLG_opkrNg84dI6DwtJXYWamS6jejr48o4_saAaB8FJRHiOrP4QZKKotsNeGriUZr0ZGSWSF4qrddq7L4EeJkNlNUHGhg78cCbYZHRv6Iw2uI6pRCP2Ss_NY4588CY92twXYM6PCQr10tv3LEWdAPQKiSxTahLlXQhkW-SuoZpWJNZIL25hExs6owrrLPBEsB0wn0brESjy2OBOR7cm2Heux_flioracuZ2qPmwJTepi6dPhktwCD8JGpe_qBODK0IRw17xPNbkcnUoUKWM5zG1Y85wA70r2_yHEBMZQLxoxSzj-4ujZfR2mGp7xqPkI_ivQejfHnlOYs5sHITY9oV9nXIrze6zUx-SGd4pz3Trdedo80IMl9tEOQf7uxGjq9eKot3cLBJAg-0zrl9LXFGnX1uZDcJufeO5Z1w8js4itNrv_nlnlFhzZhmzfktMyEEgDZvY2dorhdnxQ6GZumOI3x58-48EHh_-mglXZS5V9katK3v1j__gDOdAWzFGXl6R13A7Q&isca=1" alt="" style="width: 300px; max-width: 300px; height: 300px; max-height: 300px; margin: auto; display: block;">
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
										<h5 class='no-margin custom-font2'><img src="https://ffd269c8e513cf6a7fa1d3bd44a898e7536cb76a24c2e445478c6f8-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/user.png?jk=AdvOr8uKQVDhQxD_K_kRh7d9I9bxtVL5NqBSUIHd_YRb1t8fsRmOYMsbGiFcJlW2f1SSmpfkopzHAMwEjPhV0p6QnyV4cOj8xVf2YCn0e0i3oB_5xtOgNnOwp-d-8PtBZn6aZzMoA2pT7Q2N6ITwl2EqoY_X1IA0SKapbO8WvJT5Xs7OOd8wY7Dr7Z4Jl7tUxWhLzqG12x7SkVGpp3Oxqk4t-uwgP2zgXySPmhaMUsJ8jJ60pXzQ9Enn758JV1armt3CZpg09tZ5e5wdMdCPRP1O58290jyvFOS4q2B6gjqCph_MkpiG-3WVonDTBiXe9eJ3mvPmtLayONXeyg-SLV2OjuXAC3Zd4Nmlyuaga56nhSYwmS9riEeXq46rYWne9Vg6Rkaac7Vhq5n9sTsJA-rlLWLaGnKTwmAj6TpNZfFZLhETwnvLPizoaff6BMGPFes-oPdikiSLngaclE-kGYATwy6_QV2y6tytbx4JxFxwh8TDn08fd-MtIqKZ7bNMdQHlAMCUPsjiDxygLeccbhXZUB-TZEYPlznyzPjsU2QwNwIu1slfD_nPOaKa-J-OENRIl1Pymcy7pk39aiN5YS81cgdlpnIMG6Vspcx9MwrZ4Ao3OLPphUo90oO13wllnudZpAjrv6Us3VxblcZtVJDlSfnq7A6M4wmwsZC4fTEtepIGFJAabdnnWq2yzGRv664BOUwD7KX0nejWJF59Jp94iPesP1R9VQh1weSCX_cg2VOuuQZkgU1gjsOwYI1SqXZ-gX-WE-FoBgoBz7wpQoiMlzKMpgiwVFkDC0--3A-6y9qQ4bJ3RnjcIUtJT_HOKxOs2uZLv2djj5ifaG7YsuPBdlVJpUc78yRjczZS-Gp1AzCd695ocHvbwy9qtuow0shGX9T3ocsatVCY8wliCdC4BnAQnirkvoXxxNSywW9xXRkQFkh4KaGgiX3DdnPOq-TYlpN5t5VQkydxV1lrVL06xn5DfgkoUK2g6Yv9fYlSX6USV37iR6T44Hg3YdZg1FzoDemRwnA9Yl6fWYtMToDYJmDe6RRtAwhpO6AzUynOn0_EfPbpji6I3_tM9rVidQ_Zyrj87IR_xGwxgw3_bMwKNUDBUG1k9TIPfjw0rCqnNEf3n_VHOJIZAbZ6yAZ3wuzwH4TK-aJ7mhLgxYkIO0QeYWU&isca=1" class="icon"> Nombre: <?= $nombreCompleto ?></h5>
										<h5 class='no-margin custom-font2'><img src="https://ff9e54be5a410914bb09bda80500a83772f2a3a30bea2c78aae0a29-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/id-card.png?jk=AdvOr8vjxtlf3wmphhgjCXgwd0__JXqWLUNiXKcrB_w8XGv1qrEWItaSR5MUOPIx90jcvL5ZMAuLvfcHHhJaQ-_lQj7TDGa0YRtCv1CXKg1RwJceE1iauBLSoJ_UBGuY0vsuwLODjjIhJpRAeJ6l-M5S2nJtofOpS3xSz6l3TlNvd9QKjDXt5sb9IPImahNclmTb0QBpx72qMbvDbz-vvKEh6UuJAF-b5K-di-7ITh35xDHtDT-vLGx2BfS2Awax4IWecfc1TkJNXZ49QBpT-SkYJn3Bm7xXfCm1V1JOQZULvFEVdWLPBBBmVfZCO4NMonsTgsAPoPQ75YzQpcHOXgbxiG0fdUtfr0n7cuxwvxlP6uSHcS2LKyAZaYT7ZWpSqpKF_zJJ2cVVK5XkAho68oaUUc2gPifbnzj2fHWMkCDgR_FNWvT0Xj4oAaywrVzeVqH98tJgmoUG1QmASRXZaEOgTQ56Sd6zqRcoAr4nMTgjNHR9_-1g2h0zTNEq_aE9zV6Z97MnEzZQvA_INwZxMF9ICsG2LRploU-WUAHsRhvyps33OId-5NtnXkvTFXUFK-i9j6lWTc9y399zJBkkAq60jcKTib_HS-pn7O7IaK7HGAwAiPFGalURVZjaIM2YF0VOdevwSHyDXIwypvYXKnY9h3Ats5PouOT7BBOD8Su4UN733ZU_EwKX0WvC6gAAjCfi1P4NVttJuoceQtQQFbWRpH9vmrlAFPl6yRFVBAMYXGGcPeabIYsAtRlL6H0xz-b-oOn7q5Z13hrNHZxFz_0Fn4d-3mcqihVbIz3RRSOz2fmCrOQGjsvXWyv3vUhSmsJgz2DIEUlIJhYkhqENkspnHc4HLTdA2fXYylNgNzIQ60DyuAGESYhTn1UhPIWS7R2gOXqo_R4kQ-PMxH1-XXmU2QgMciTkL1HUqUJbncoLyTVZwo3djnyVlg_zu3Ntg129vbrFb6nKxgraCHda5sSxJb-TnBcDjVJ8k_35fy8CpIfOZ7uN85G50Uk0tFCcubXdz4fW1kr-a1n3yP0ym-Tr_ko3kNNyX9QVp23dbk30bZ6K9Cv-MIT2UGKxCOuk2te9wE9A0mHyxl6YX64l_TXtZCmVI7LCk3y9lZHJWIFNB22pNHCtEXY4iBjvdyWQTm0VbzxvAmX-asy5uwSO0OsUocYzhg&isca=1" class="icon"> Número de empleado: <?= $num_empleado ?> </h5>
										<h4 class="custom-font1"> Detalles del evento</h4>
										<h5 class='no-margin custom-font2'><img src="https://ffbad8acb04497451f963a72a8edba88e67d1715057cbc7a83c649a-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/map.png?jk=AdvOr8s2ktNMSzZHaVjn7cI6_1Rngys5i_2nRg6oYgrIrN3Wgqs68BysjiWKcfoR_8_MrJGrsjlRNmK8LZycl9B8sAEOoqO9x404FkfApBj5sjHtt36Lq-xq_HJNF3_WJkP2KOk7nv8F8eacpjRe5NPErBySF66C4ldFvrtcKrvLdQV5Oh9_QJ6ewsEe03iXU7QFaBb_3M7lpv82jnE8l3VP9tKuQC0lcNqj3VFV60rSz3BICBhWL7MLWeetT_Zo3zuN-DCCO8bl7t0wjiO9vY5AlNB2t6Y7gRWkZLh5OM8E5aHUof84vZ7eVjggT466sxV_URL4rnKmz4bKlgYyFM1w5KCwAhWX-iI7PaI4mk3mSQzHmh0yyaWcNzM4dSJ3tOfejatIJLa8_bY_a8omcDE4d1fKBA88PhBo6U5mj1ASMRw2IGvKfeLGPondCB2ZNK-59UfzlKtyNbWPZnutqQl5fG4lTms_SODfQARmX8rnpkf2oCQMjysfXgnMTgQ_kern-8SkBCq8rZguumIlmaQKOLrzgkg0fvv1ntRCuibMJ_RRJQ57o10ePiTp3m2ThJuZzQT8nBjCa_fgDAmP4_F-nw5G1NnntLMG1TQcrCk4cbv0NbEgSvR4z3s-rDD6r6rWStg90MeYiYvng7iaUxopjhw703UodtvUCtCokQlbdLOa1EPUW2pkafXjb-kuYZzLv2J2m6hs_Kk4DTkmqhaIwHd8NHDzkpusNAqqYmsa0L-JKS8M_Z6FLaSnWVyaNXuId_ItL0XpjwjNWHLtvKQBc5govJ7DbJ-2eK0u05_oXSiR7xr2Wpbjia5Jyf4Vj1gv5vkVanVfYyk4ScLuBKgUCGP1zLt1jnMWJsfTmfussi1EfWMAjE3OvsYd31w-LcNH2eIcTN9ljLFcY2K6GDrExvxY8kh20Qc_yzsKhXa1VoYubyOCLfcOrViAQFgX6J3Zl8sCylZkyy2_lWwBaxDsOkkkCyOs9zXBfYN7Xug-Z_wRQEaubn-6hfl9ApdrIcAYPzyOurZ2wgMPmJRJXBzF7Z-O6fbBYCTO5StHLC1DUQnmNvFnVmtU41bw4NQjLkydIsZ50ybTqyoWH1ZY5c-EmWaerwjomopTWOtb-bLLHOfG-Zq6lru1CwvoG9baLoVcYVXR7JO3rPL_LlivU0vPLA&isca=1" class="icon"> Ubicación: <?= $ubicacion ?></h5>
										<h5 class='no-margin custom-font2'><img src="https://ff149425ffdf8806053ba943326cc0db235da584a35b4a19ae1484d-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/calendar.png?jk=AdvOr8teljo3Mjad34z-wGODsrCTmb8PvcMtLcpui0yIf4_vqHb9oaGYSxK1nhXAapevRiJdtW694qORGZQwlL4mV9bHRU3C-l4nfx07HMqH2FJxuaM_fQtERJsTBdEtY1gXepMJbfgetYJg68gODkQhmAzFTbbaXKtXAINqlGbenIXt9gk1NT8nyojrelA_lL5Z9fza8fL6KSSdS0TvK2ydExlsG9PTH4fFNbN2IYNsYe7NOW5F1uHnrPj6AmtdL_YUp1qKXLt58lsZPFY9TKx3WAleeNHqM_aZDX3xRAiEjjpCTqSRBqCY9lRBenc-MhHP69DvZrotkZ7JewIy9KIcBf_7eocoBfUsvvuFD0rJZQXCe4R5fykXHFVbRuGMXIXF9t2pAZt5LCzY-vMfhXj7SWUYnSP_AWQ9LsUIO9cgt2hohItgXsu35jsP9qIP3lNxONpJ5jSOMUOGyviT4Kg3o2UmZci432vZ-E5P1ASwyyqMH-KKvn0DyCz-4gFR9J0OCajvwVaDmzsMZ1WsxHO-kJhUjCToPwG_fMzz3bwtPa5ZA0Qybp1p2u7XyAh0lpD0WvjGXIFI5zugtB5elmlp1dnAnfMNgsERt1icHKb0EYcCG0dyU_DKxepoi5-as3WCgljOthzMnecmSht29G998aZTbssiiOupwSVJLVs5OrbeXP_N5VocUlceSheIHX93kYVcB1ozXiefjplWXfJoeBzyeeCVmowQk3a_hDQ-LPMU-6PkqpLhJWQo7r-H54cVX85YEhroQyJjXZ2jvhfxS-OmrSvlp-hCZ3YddwRRyywJFF19d5XDrmu6Up4tXc0QorIZt475qhiKuC4PUy9iHkMQMjD5AqoJK5awAb1ko-HjzHIi8njSxDE5grsHyOGScOL7IwB5KtoyTUHaWXwktv6I4sFV3KHr6Y4iNONoYAlkGvE13YFqyaBHPIOMP1Rtw2umEYcrAmsehBVNASr4RTxmXRD0YXIehDn08rjQmhIc5NmGX1TUUwWjxEyRJbqNYX9bVB2KK6r8uVNJFDoS3b2-UM5MvKvQh1A1hEDwP0YEloO8tNoXj57daziEB8DDTdCUmQB_nF52C4rgabymsKQmakMycYi5ruTmHb6Gqjhi5neaxDA-cT6tXgD2kxqyEH5YRzK63t3dXu9iB61wtPpGOidd&isca=1" class="icon"> Fecha del evento: <?= $fechaEvento ?> </h5>
										<h5 class='no-margin custom-font2'><img src="https://ff649fea862abd68815fa210ac5ae07ab3307d9bc28ee028d417055-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/clock.png?jk=AdvOr8tR1f9nXQaq77ntaBCcKStuA-hfQHvcK_QguZgfTTpZyr7u-36FoMmsCw4fA8_6CJ595W53JSa1IxLr_R3LykBFfUi7bTpuyCeDvlrFBsH-MEC15Dq7rAOdAEJCLNs6uiOFSVh4UPm9aO2x1_TRkgw4UurOYim53p8lVYfUMDZ9tvAfYieri2TUbRiZGtOahW75LjrDW9I4h80Bv5N1yk72c2tM4xGFGLs6BePsKny3MgHiaxDMlgx0o7LPubmZFTeqO8XvU71wUhq4kE-h77ZEcKZgFqM55R9gelA_yN0z3pDkct1To-JXYrbAau_5Y-ekcUE0iLEtCbjUoi-Qy7r7uhz2TjwyD95nQEf__hwHWdNsg-1t3qazpHI_121Sl0HpTS9wHtRVKy7F__GCmv3gPKyYT7PpQRXIxwGMLqTrWZHC67qGI4pfkkRDw4rr5Leapo0QtYgcjxRphDpzrPk4QwfioMSdhS4u09hJaMNTsp_j-e3n0289fPcnfrYtFOjZ-Og6_-RQGZJlUIHPeBkWy86prtBjSfZAljDIbZ-IqWjSv4PldFVAnRENuAwK2REY9xt9CasQKj1sGN3GM2guOGA17-OXMoDfUw2Wz1M75H041w83X0vr-FD0ekr0al-XH5WqQ1jTPEh7GW8k5odMKgCYUlchuER2Bbb2rEHCeYfNfQnUxiJjH8dVj7G97qym81uOzD7iWyIgSnK7zKPWEUupzDuSo6dUR5Ue9tWw9MlSUKwxoGuTG0EChbRcDs0dPBTamikgEYSQgueKKe_Qvww3EDunWg8jLihz_I0OOI7gwgvlsOuIwdEwmVfAa2ojfE_ND0rIXYeZACpYOt5TL9dUcMozw26NZQFVpoRfln1nZmPB7KKiF8kNSpTsfThaWvaF2oeana7k46TzmvCdHoA9ChmuzXg48xs3Wdm9OHQaMiMTJclfhV_ZB-Dc7YlwmX5FX_EW6FlHlvBTei5AIzIyHRk42yZ43OH3JX9w2B-nDnzjzopK_lxTmGtmCmEhCErMi8awwSuvFajVa8uNU8ba3IxeziM7y7v7a80q--BM97fFYyA2oCoZmYY6QEImE73nqElIMeCYrRWAmcEyN4B5LhXxNYIukbCiRl60bjbqqU59O2e6FHzuZdyb1eHQ_xHuF-RaOjAiDX7_X6vN&isca=1" class="icon"> Hora del evento: <?= $horaEvento ?></h5>
                                        <h5 class='no-margin custom-font2'><img src="https://ffc934a75a0c119fb5082023dc4cc89e988972b4671718c6079b521-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/wall-clock.png?jk=AdvOr8uJvaSrNGZEX7ZquomiXu8JPrqR9mpbGFNDsYeAe9EhP5uPfyf_v10S_k0KcKpvKjvR_P6tVX31AbarzqpXGYGnnaqAHTfuphBPHeJovDgoM2XSmb_aijsAmeKrOGesYXhbHtGMInQfxdC34aDTPFGg1qEUtFXepbr_uPbU16nd-OscsnivaY87Zn5MK6YY7whlpBJacuOXJMGvS-pNO8yvVg7BmX4-kTjND3tvl-VbdgBHWDepjxwyROOvd1GAGjz1p52pVJG_vFn2NkI_jveefLDkm3Hcqp8pyNM2Bw0MYrAdb-UXixlXqyQhfvgp-TrwlyY9S7_VZB39zC7JO9YtJh4aF7TLzJW-q-M9pqID39Fn4687QB9_IniMpcGM7wK-PGn68a2OcqkV5pccwN4CStpjpOsRUQgVq3mkJffcEl1lZHV9Td842qwPr9og_akcxTpbIUT2y71kA0C6wQbPJP3-nDBQ2LAUCwp61nACR6oeKwJ72p0og7_btOdUx8yEdVzuDD5ug_wdkLXQDaz74G7zuVJofOrg641xkIX0a_DOinHOl0hfxoreqzsNtQWKsYr-gYObI1MmArbuDZ1fXzrPiQR0NI-0B6ub5WWX6nJVjFDNPsSIP3x1LrbGqtTdyusbSHiDKY9Mg1vtizVfiWCw5cDtcvf6SoOLKAp7vaRn2PirsK-C987whc1188MyuYP5jMUPc-nOlObvyx3a1HV3LnbDIrYfplhoODkyK-wRAKEC6cShV_mLUBkktRkQhvcc_2DbrqjC7ru1vlMS2_wSY9Xw_J92GWT1qcVPgD0ARrwY0bFHLZzELuADc3uc_WXpZVNADjP5cehfvpmfzxxhBRbcwhZKQ3cTZeGoObm-bCICGshla5Do50Q2ZUnEXpOh482EvLK9DJl469UQUEUV6cW5mKbLzBbBVL3Ylybj9pc-1ykn3OnbSoDLFlO83oFq-_IxHXJoeqiJvn9Qmm_2yxXS9HILMmOqY2qSKjlApNh-OvI-_aMUaNHGih1nxerhkhqVwu87Y1WrneQUGsjp2BALYyu2g53udTFp1EWQm3PTDvAJQlFdHEmmKLtEEm2IK70uriZ6lBQ0FyADkRJBJiO8qrx1oBfBWDfQ5DfWvIGyGcVBP2r5IllE5R6xX5TS-qQ8E_0d2jGg__AtFujfeWs&isca=1" class="icon"> Límite de recepción: <?= $limiteRecepcion ?></h5>
                                    </div>
								</td>
							</tr>
							<tr>
								<td class="logo" style="text-align: center; padding: 1em;">
									<img src="https://ffe4ddca1c940f4b54ebd494d7400a6d5f18daca68dec246f43cbe2-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/Logo_CM2.png?jk=AdvOr8sJOYmMg4LLJCnFwL5PqlAW6dul9zw4bmZ-bcf03ujSaIXMWdDdkDTRijMXrFrkDXX38uEGa8pgTwJf0FJlTe3Rjg-ro0RFB95lZ6LHPffz_-0cFig8wtoJseDuBl9jFIZYeVwlVCQNGYJ8Ua-UnZxim_M6q-eJizr8U_qVL7B23AVzvkEXki_rmo6fne0pUB7qarXnMW7JnoDu-JlowQxh6gWTer9O2UlIiB03nYn2mCkmOYiaczeE7WNmx-KS9D9F4CbZAL_2E8IbP5piaRwGmi5ul8Cm9WD9Xc60Oa9gl3qH_U4_F4aj04QGXCHCWC1n3VpCydzxmPOTTwG7ZBl6o-zc1SUZvwE0Wx7oCh0ptRXaCXu8Bs-3GaA3LvqcOtfgCyX1QUvIpaYhNfZqZQvTnxOfDzlQZzE9eZup4ECt0Xnag2UdnnYg1ob_fYPlInOqF5oyn6GXUlKgsG_l4TmlYF-3aVOScIee-_7qC14h-rdjX9FWwpJQCspo8b6ffHBbf8MHkUFumV6hloYd_uQKRIJHmgqRQbbkz3fV65WiQryW7n91RglPWzkdEZ-GYhZSl-hqoB9UojgILiSQxbYAhE56bj0Opuz0ADlfSdu4Gk1-jreNFqMO9Ybn6N8bY8acPbD503CtfZaLZb7bizyY8zjtQd_Mp8fAJBU-0vIcn08hitXI9X3ju_b7pttiG-7zOs5uNc9g0rEh9zryQb_IhThaeA0SRqHhffP6UvirpNg8k4Hbsi0KCfFMqCb8JqbJ6alc2y2XPVT1dAbpmgTGJx3rVIAd5jknoJNuGYBzmBVVmU0Pczc2z2koxMingmmBtoOtpwai9YU70-YS6XbjbC6NUyTGGTKO5mj-XutxkPdFQdZcB_W5Fa9eQQiLXNC4tpABQIg5Xj1SrQxgBYx8OoPNsgX1-n71qvBbU1qCIAK5gFAu4FAIt6Gdm-94uZKMnMCK2jsFtWrCbH5pncz_j_zxZdq4pqWGmtqDAm89uyDfs9QpXzJaEeQ9GMxnJNeuRTYx4ZpNaSK_sfutNlQgD9pgX8e7nJammP1sxkAKK8G3pdEE-wZU8F_Z_Pvyp5LZSAF25xV7Znz0pWyyEk9id8PY9E1kTTkLN1N4Brr-UCj9Xr_dUHnlF9CDBBYq-7XwhNkmHbxeui77uluHMVnA0mlR&isca=1" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
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