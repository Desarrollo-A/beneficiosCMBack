<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->
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

        .icon {
            width: 5%;
            filter: contrast(0);
        }

        .no-margin {
            margin: 0%;
            padding: 0%;
            justify-content: center;
            display: flex;
            align-items: center;
            margin-bottom: 1em
        }

        .custom-margin1 {
            margin: 1%;
            padding: 1%;
        }

        .custom-font1 {
            font-weight: 800;
            color: #003360;
        }

        .custom-font2 {
            font-size: 14px;
        }

        @media screen and (max-width: 500px) {}
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;500&display=swap" rel="stylesheet">
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
    <center style=" width: 100%; background-color: #f1f1f1;">
        <div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>
        <div style="max-width: 600px; margin: 0 auto;" class="email-container">
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td class="logo" style="text-align: center; padding: 1em;">
                        <img src="<?= base_url() ?>dist/img/maderasConecta.svg" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero">
                        <div class="text" style="padding: 0 3.5em; text-align: center; padding-bottom: 10%">
                            <h3 style="color:#003360">Listo para asistir a <?= $titulo ?></h3>
                            <h4 class="custom-font2">¡HOLA, ESTIMADO COLABORADOR <?= $nombreCompleto ?>!</h4>
                            <h4 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://ff906c58c03bc1e1fe0e79d3e23028e98d4904defd4b2c90c0cb8a1-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/map-marked-alt-solid.png?jk=AdvOr8uZ3PBstiBXlRDX57kcENylPmMuXTPtA0bFAtt1lC-OkjIOPDEiHsJioUv0Hp9Cv6n1jjAD6lfg9HT6UarMENFnd-yKJc65CRHxC1VGmLzFWv0MPXtZJTKB98yjLCUX7KfmTVtrquIX25QOYiAIeaT0jp0QVde6axQKpItrhM75Ia85CgfFKibot4Q_4bi6LqrTJPx8dXjY169JO2rrOm9iUZoK0sdfk852A724cXbvL4fCik3URO_-N-Qwm8DT1Qi8h17lOYSgi_lhLh6fuxB_3iU0RLr--7tMd_fPu9S9zOvYflxIL3webtrNApbCPH2rfd5Qw4AUrKyprXZpclabsP8rm557O8DaWAgCksV9C4-HstjjipwEZCoY76A4EC9ZgkRyLtBlPzx3qSUEwyUJkhNPweljy0chIoExZXCmfDCYGsTZNbfP_M6krbi8c7yxAzsKb848IimVeA4oK4WXynp0Eq5ugi_2NYlKPjCIgdIhWrZuXdXK1dUcAHSw22FeOFXCxJNGjMe7Dj_t1t5NUWqiKTXOnJemy_aInpNSOZCjqB-T-rn6gKBVvd6k7yXIi9QbewGo3wm6uszNIYbcSSaUOJpOd3tdu7Ou0SFjhF9jcyFPcY7lnYYjhodRcmazXYcvQObVv5oWprAk_KPyQc-GS-OhyS667MPr7B5kp5aXS2xzwBMy1hwD1pf-QaaryTHjjZRo7vo9v-b-FekeAGLgjL-OM8WoS_x2et1mR6bwnenLHra2qP9KBj0H-pbbnygdjRQppq7kByfpUNonGDgqAfgQVUxsnNXyZA2z-ARkjj6hDwgRX5KB1JJ9bkEb91CZx8wHAscVCTK9oK5U01XWLKfTnN6sjJdmfoiV384GXba9nIXbqWq02stGq3YMBj7cpjX07w4y1g_1HjK64kn3CJroVx9ozyhU_aTzmaCJiwZZgydeVkq-27Qi7DMqu-200krew_aZsUmpgljht0iyAUkkxhA2-0lXkdORD1uaMzomXzTqr-tTASNLASPoewCbRCGEPUYCofQoK2d1N_VmWXdIoYOYE67Fa_nbLtlaIyjGU1Kt9tCQ02x5h2FCNz7ZvPSgevkaIe4OUakX0RvJLKnb4EbhfjaCIVcr87O6_DRrgtFbsvIbHC2vFHPD03FBAjlI0tOWYcQsl8P_dmXBsObGYfMhPbm8TCVfag&isca=1" class="icon"> Número de empleado: <?= $num_empleado ?></h4>
                            <div class="qr-container">
                                <h4 class="custom-font1">QR de asistencia:</h4>
                                <img src="cid:qrFilePath" alt="QR Code" />
                                <img src="<?= $qrFilePath ?>" alt="Código QR" class="qr-image">
                            </div>
                            <h4 class="custom-font1">Detalles del evento</h4>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://storage.cloud.google.com/pruebas-conecta-maderas/user-solid.png?authuser=1" class="icon"> Ubicación: <?= $ubicacion ?></h5>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://storage.cloud.google.com/pruebas-conecta-maderas/calendar-alt-solid.png?authuser=1" class="icon"> Fecha del evento: <?= $fechaEvento ?></h5>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://storage.cloud.google.com/pruebas-conecta-maderas/clock-regular.png?authuser=1" class="icon"> Hora del evento: <?= $horaEvento ?></h5>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://storage.cloud.google.com/pruebas-conecta-maderas/clock-regular.png?authuser=1" class="icon"> Límite de recepción: <?= $limiteRecepcion ?></h5>
                        </div>
                    </td>
                </tr>
                <tr>    
                    <td class="logo" style="text-align: center; padding: 1em;">
                        <img src="https://storage.cloud.google.com/pruebas-conecta-maderas/Logo_CM2.png?authuser=1" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
                    </td>
                </tr>
            </table>
            <div class="footer">
                <p>© Departamento TI <?php echo date("Y"); ?></p>
            </div>
        </div>
    </center>
</body>

</html>