<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8"> <!-- utf-8 works for most cases -->
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
	<meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->
	<link href="<?= base_url() ?>/dist/css/email.css" rel="stylesheet" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;500&display=swap" rel="stylesheet">

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
	<center style=" width: 100%; background-color: #f1f1f1;">
		<div
			style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
			&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
		</div>
		<div style="max-width: 600px; margin: 0 auto;" class="email-container">
			<!-- BEGIN BODY -->
			<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
				style="margin: auto;">
				<tr>
					<td valign="top" class="bg_white" style="padding: 5em 2.5em 0 2.5em;">
						<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="logo" style="text-align: center;">
									<img src="<?= base_url() ?>/dist/img/Logo_CM2.png" alt=""
										style="width: 100px; max-width: 100px; height: auto; margin: auto; display: block;">
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white" style="padding: 3em 0 2em 0;">
						<img src="<?= base_url() ?>/dist/img/<?= $data["imagen"]?>" alt=""
							style="width: 300px; max-width: 600px; height: auto; margin: auto; display: block;">
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white" style="padding: 2em 0 4em 0;">
						<table>
							<tr>
								<td>
									<div class="text" style="padding: 0 3.5em; text-align: center;">
										<h2 style="color:#003360"><?= $data["titulo"]?></h2>
										<h4>Tu reservarción de <strong><?= $data["beneficio"]?></strong> ha sido cancelada</h4>
										<h4>Especialista: <?= $data["especialista"]?></h4>
										<h4>Horario reservado</h4>
										<h5>Dia: <?= $data["fecha"] ?></h5>
										<h5>Hora: <?= $data["horaInicio"] ?> - <?= $data["horaFinal"] ?></h5>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<!-- 1 Column Text + Button : END -->
			</table>
			<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
				style="margin: auto;">
				<tr>
					<td class="bg_light" style="text-align: center;">
						<p>© Ciudad Maderas 2024<!-- <a href="#"
								style="color: rgba(0,0,0,.8);">Unsubscribe here</a> --></p>
					</td>
				</tr>
			</table>
		</div>
	</center>
</body>

</html>