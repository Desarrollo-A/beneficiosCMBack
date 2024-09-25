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
                        <img src="https://ff86db0c545e803e35420289b8f6328e529eae0fd5e0be3b4a44805-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/maderasConecta.svg?jk=AdvOr8tni3LVxWrVKIUDkbxW9P2QB4jub5iqyceSVIM6ztNoD25jy-LpH3lmcBnixLArSzeX76MRyzj7s90jyjyQQ2NkAMTr7rxeYciml7uEh39V0IAWiPgk7sZrGriEg1R4A2EfkjqjKUWXMlJTYozMfwVKjrwZhHEKBPq1KBlYwI_SxEsSk7fHzLbTlNxNuNja-7pMsJvBJt2Ld0-W6K3Pcu8Ch0TAXKCE16YgFjK8Vj-Z0p4iXPEqDm201PPJr-xNVOJUHcNUp8Djn05c4HLACD3ojv4thE_rvKGuEZ5DVOWGmqwP0L0O2YpB43X4ZByGIJAouOAfXCxrLASglypSFRGzP9Tufr1S9rNFZpHvlE_n3dy_LVJv7gS03CU4Hm6gw7IG303gWq_vw_lDVhbBBXmtpSPVhcIFXxnGbyg-PY3K54JWlW7MQ7lKKyttBmax1lV4I__kj30cEtUd9thKdA7qnlQ2xkuLCj7qT6twLTXY2Wf3leP2qjBpOShzLJtD5vpSHXzSSsPabZXpre1wi3p8OZj4dPGHp2WIaSb0EhrPThRjVnKhP89sbE1qVuNrwiJddT2oNGHHBl70q7q_LK5xhO97-Vy4WsVcRYGFgOczSSczzzX2YjfDo5qUVB4QcSmDy8WP0jnsdfNpkwThs5ZOmLO0LsazqmoA7ix_5N11l9-2ow-7w4RCQG05srVFcKhxqV01oDbSQZVWzWk7bYvtyE9ji1DBhYvmyNUS0e32hW73vzi5do5cst1RijFZVbojKwky6SqKJxXxtXq3Q1LX9pQ8lMlFkDVbPEsTp0UdvM7Zf2ryCTwmC7Xhi1XC63TzuHIplkcdyOjh2rG-5PfXEldi7L8_qCiB4M_2G_DEEg4Wb5tI90DHgb1uufS64fwX2M3WI3rObrvysl-f2qhS3RO34MdUwF2fSnKod42rRDdoIn8-611HvUUPgL1MtW_YEccHWxt7lmHBqrEUG8wapX1b9luWWmpno92Bmp_kEjujxg91lee_AyEC3rQqA18wyoqTHcQtJfsHhXjJBdKFyPwuVXPrXhov5-5yGVG4Z3KYHs86odAxRu4ZTXokPtYf-VKtkugmUEvuUfeY9n0-lVnX3zs9emYBJldqW1CGwE_ZXYm-i_hte-ohb57QxaN_qaKUy-zP4moak5i2hZbV08JA8i2871Qq&isca=1" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
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
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://ff371dc00d168f4abc2bfa9a6e13342b23a5cce39a114f831292133-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/map-marked-alt-solid.png?jk=AdvOr8tOkP0-aJe-NNpizPvRnCzxneA1s41y7ALqyuEZYiphMuMC7B44oCRbq61AVQsadLi5IjxvmbgjrOG6AhDgATEFvLuFqyCizy4Jwqa5w7pl3W760TDjaSBrtu8bAGYkerECj9wGgxfFN0AmGmYgaKlC2uWOPYWZoq9xPJVMUfzSBBas79zxBZ6Wcq7LNJ4T0z2b5DUL9LxVZQO5lMTuH1v4EusrOcKu_8DngVulWo4m7ZNOcw_cFH_34KwI2ck76j65-_XNNfcPkV0DsKXAM8HHzvuS0LI1Fp_wD-YIebUEFCvzJhvGPmmGXjtppYCfm5PoRkpzQKZinymtB-qkq7r9Ik2HjCSHuiO_pU6fY99_DJftPzWqta_yHiImtAhugrCopf4cor8ojYoi0iu-9lLMBDRmR-dOVrfpyWhPwdgWTh9zsewYMm3S-tct7xEv8OKcdpYtLaUBQC5ZDzcARFcuEaIKylJs7uZtInVEyH3_WVtrkqrbrPe1CShTan-Q0rvoovEW2eN9dzyCOy6ZV_62y2U9vMt0mv021wHhezrYvr4Bh9alj-jvkOHu708juve5qhKXj4cP3uEEY91oJnDLYLan4CfR3ghCUJqOgV79VKXCIKPI--Bp7thxlY0bKqeCk9cltk9vi4_HJ8__zx01WEaQ0qe_whGiD-RBwgjMfBW95owKkTYAL0vaoaDmzKmEK_yQQ8aLZ5RFjYl-Cxb90TMVx98ruTN_BSorEpz3sQppQ971VraKzPLwogTDzzjVzZB_o85PosYLU5xVlltLeX83mtMWOZZi53ja7t4b61AlMDJ8gmRffHqMze2m0CnkSvac6DdiJ7iGGdxqO6g-TIZdAaVXIGoDPhugJDbAyBw6E7iKUASlDWuiCU_aAOzvQ5h37Yhzy9BU3F83vmVrNubQcwqwF0fZeE3pWyWALsdOEiJcfU8KLyCi39n2u5p7HTMvuZuAtypxbJCzwOXMWYchilPVKcLnMDwvKawj-L5Vp3tdJYZuxEBN9Zn2-63mMhavtaWyPBdIxX8lsj9TBI3SwpIPOU6LTo_S9EzQcl1BZxuvYjx-EtNo6sObHkNwah4KdxdgSirbq5cEFQPzRg2nB0_qWfKTBmasQnwNE2N5gvDBhGD1mJIAxkGXYFhiliuQ2lt49-aa-exgtyTqaRv4aZeuiBftPz8S5kwkIg&isca=1" class="icon"> Ubicación: <?= $ubicacion ?></h5>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://ff01d39f6adc0deae696c98347730536dac6d238eccf2d087e13949-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/calendar-alt-solid.png?jk=AdvOr8umXs44Mh8LptG0kBRvqLKkNCjinHQZrSvBRR4KrHQUQ6ssWGY5woTkzpLIGihWulUslEVuqKNBULRh6aYQbBgi8_yRd3mnrZS-LaFwh8x4tBkRwOY5durjMGhhmCV1GBzFDJryXj15uLz9N7oXuMP3IjDax8IoNLsJ666tkTBY9Yq4y_30Khv3XsH90td9bNwnL643Ykb6ZvPYQhRucB65OW72jn9kajnG_xaXWz9rKmSvIRLO-CNwBKAYMC8DBiMeZCiydcm3Mm-wv_AVco4BcZ5fGNOhbpttqehhsjd8oAaDgLwv_4C6sIYA6zfLbvVBb61qHdDxt-zhv2ZNz9PsJss-NBMcOrWX-95Q2KCAPxDH7xrRvasTvbj0vzE12z6Odj99L8a9iGRSXpOM-8LJDFUTokS190P4liEUDlRYRNKg8nOOCldIVdU4GJqc4t9I7-AnvSPi2LWXih543bvpBSwxu2VVBYRtbg1K5BhWViLKArcwGB-OH3ix3a9wIWJkabXreEHADIReqwdoFnzOY4fRMeR2SI79XWeTOEXMBV8l1wSa6ido_Uz0gjo0ry9YLQngxBCoFTawEGoQpf2l7bN-91kLcuwLJPG07TlPz2fciNhVzfFmEGXPgw5fZWnH5GZWsODTcxCRUxsF2tqiH5FFCUTfw3sYOuBYKO4jT0tbpiOMPuO5tBNPGcqJ3FwlBbiJHw7ObKe1ZRZO7oRwYrICr_USvqkmqGKximgFJaJcASpOHAau2IPdTH8K2o90FYvlTpnrFyH7kM_DBFe2UYSWe3bD3YEMAg1Lk9gyc3PoTJPqf6s4GvTq8-OBBZ-070G1bYL90nRjnee_2O-myjf0JY4sZr32hQV3D1kfAlH1kyRBzZnZxffWAae2EdVl_8wJCiQoG3eYmSvZUgOSMlp7wbZmUQ8ymsy5bWPKjeVEa1j0jmVG26BGLMIHew-GsE6VpOoZ2xCaiH4Ys4fsMsWE-kuJoLfpPMwRxlOYGP6SJiG_-Z39HDpKygO9oOUfFIY0CgKEdeRIafpNme8fNIBzzveEN2G2nZG7_BQFsbe-BU-FwyMMMNGE5zljPLSyuCBSYOvsAGNuSO3lCv6iNVwH2rapKJoclP0ypGaaSKYUfO7N4FTIUl1v1uHVvbHWBZxPSQKCMccEnHfEeKv0lcS07VzSKAdhQwLUWQ&isca=1" class="icon"> Fecha del evento: <?= $fechaEvento ?></h5>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://ffe5eeb1e43b798cc0aaaaa35fd3b34467d2b2bcf46cb07701c2b69-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/clock-regular.png?jk=AdvOr8tAiItdW8W0tO9-94EaWc-NK5tfJyv_GF2tiYQWZwekeUcn7Q01IjIUcVOKi2SJeDmG-u5EyuYYQEP9xJX3yJxl8BH9Xwd_xEc15yDTvfUykoM2W2uFhz3yu6gGgW4DGVZpho-Pdde780HBUdVzBPLa6kRyizN-hZNhwPCSqYXItCW8RBGWlYIV5iwZjPT4WMTRpr0GiDGNkY7KeXwK3o9tiANGO5j1bBU_2gfocqXtX4pdDK7BpGpSKVOZNkr1WouSWuxdpFcD2_YgVCzKWn3oS3O_n626NkfzgUJ1Zp12vSw4SQc8PvsT3HHo2ruMzy9zDLhRoL3eqISuB1v6vXDKPuZHZ_QUNINJfcpWggIIhMkGKG1X9Ac_1BTBT5P43Dmc5_sCvIp2G8N06ZZfdMfNSx_BQDWdf95M6Cfl44p5sc1rPm1MCuzYvL-oeRBGzztjzllKcNCpJ2-MMunIVNU2YIH7WRWvviUhQ8_Ve0_qmObmfi_hwOWjbi_RyMC7krDhyhIriEG5WnYuXmwDtw5sG272vk6jxhJ6xStJ0xqPYPKTQ6yghxIbZemcc38bjBu6dRNqTQUZc1B6QFYQ6lMuJEUFYGSeZF1ws6GBEx29OjVDOdgQAL2Ox4GXBnJ1iitLGHp4oLMJf6e1qPwrUAKt5ipL3l68ppN6-e_TGL3iRobRpbxXVBnZSSN-Xv23y3KmXvveO1Gj6SYVEk0yEG816YFG0nI-HMQPthKVETbg-OOy-3h39Mr33qlmOBAj52yw4wOLev8vQMrLB2GcYYQTYGqKj6lLgkX5-cyNmk63CzSxstOZ8Ekl6cBC4rHHIlVLi-rM6-l0hm4ZTV_3PUDpIeoKXh6OleMx_nxQkVQIB5SWVFNtlSEcslIB57l1RPTziM8aHVfdaj_kMrNruIgr2dQznQchABaqdVLiEGhVZfOvzvsU7nxoZnHdNawuXQ2nkbKEllSgIcWBGCXy06mAnwlrLwDBH_xa4LVue9MJcEC8k0qQgm68C49wOWF6K1S0HNvXVmK_3qAwtRukaapXLfVXhJNc9zdqrmWspFtAguZIjHAaPcRH2NqJ6ZWK2kMgiOZ4JgQb9vzsay3v1iwYm6daLQTxBjZqnQ2QOJkhxvIj-HH8JQsl0vmvhBYDqxQU-Ib7gDq-w6tCJGdqIr_-uDVxBDiOI5w&isca=1" class="icon"> Hora del evento: <?= $horaEvento ?></h5>
                            <h5 class='no-margin custom-font2'><img style="margin-right: 0.5em" src="https://ffe5eeb1e43b798cc0aaaaa35fd3b34467d2b2bcf46cb07701c2b69-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/clock-regular.png?jk=AdvOr8tAiItdW8W0tO9-94EaWc-NK5tfJyv_GF2tiYQWZwekeUcn7Q01IjIUcVOKi2SJeDmG-u5EyuYYQEP9xJX3yJxl8BH9Xwd_xEc15yDTvfUykoM2W2uFhz3yu6gGgW4DGVZpho-Pdde780HBUdVzBPLa6kRyizN-hZNhwPCSqYXItCW8RBGWlYIV5iwZjPT4WMTRpr0GiDGNkY7KeXwK3o9tiANGO5j1bBU_2gfocqXtX4pdDK7BpGpSKVOZNkr1WouSWuxdpFcD2_YgVCzKWn3oS3O_n626NkfzgUJ1Zp12vSw4SQc8PvsT3HHo2ruMzy9zDLhRoL3eqISuB1v6vXDKPuZHZ_QUNINJfcpWggIIhMkGKG1X9Ac_1BTBT5P43Dmc5_sCvIp2G8N06ZZfdMfNSx_BQDWdf95M6Cfl44p5sc1rPm1MCuzYvL-oeRBGzztjzllKcNCpJ2-MMunIVNU2YIH7WRWvviUhQ8_Ve0_qmObmfi_hwOWjbi_RyMC7krDhyhIriEG5WnYuXmwDtw5sG272vk6jxhJ6xStJ0xqPYPKTQ6yghxIbZemcc38bjBu6dRNqTQUZc1B6QFYQ6lMuJEUFYGSeZF1ws6GBEx29OjVDOdgQAL2Ox4GXBnJ1iitLGHp4oLMJf6e1qPwrUAKt5ipL3l68ppN6-e_TGL3iRobRpbxXVBnZSSN-Xv23y3KmXvveO1Gj6SYVEk0yEG816YFG0nI-HMQPthKVETbg-OOy-3h39Mr33qlmOBAj52yw4wOLev8vQMrLB2GcYYQTYGqKj6lLgkX5-cyNmk63CzSxstOZ8Ekl6cBC4rHHIlVLi-rM6-l0hm4ZTV_3PUDpIeoKXh6OleMx_nxQkVQIB5SWVFNtlSEcslIB57l1RPTziM8aHVfdaj_kMrNruIgr2dQznQchABaqdVLiEGhVZfOvzvsU7nxoZnHdNawuXQ2nkbKEllSgIcWBGCXy06mAnwlrLwDBH_xa4LVue9MJcEC8k0qQgm68C49wOWF6K1S0HNvXVmK_3qAwtRukaapXLfVXhJNc9zdqrmWspFtAguZIjHAaPcRH2NqJ6ZWK2kMgiOZ4JgQb9vzsay3v1iwYm6daLQTxBjZqnQ2QOJkhxvIj-HH8JQsl0vmvhBYDqxQU-Ib7gDq-w6tCJGdqIr_-uDVxBDiOI5w&isca=1" class="icon"> Límite de recepción: <?= $limiteRecepcion ?></h5>
                        </div>
                    </td>
                </tr>
                <tr>    
                    <td class="logo" style="text-align: center; padding: 1em;">
                        <img src="https://ff67bb6a704e5708849f667fd5addef6736cd8622d623f653abf56e-apidata.googleusercontent.com/download/storage/v1/b/pruebas-conecta-maderas/o/Logo_CM2.png?jk=AdvOr8s7mAUSQ0mYnStMtbBpdounDASHvncw-D4eARP8ViHwxMm2yAi1AEjUWnLI56_Bzemx4sDUFZ0zPdU0WSUSv21atCKoulJgb77cSaMTrIWFAXoqzxNPj4XE7gKFKUgOpAyny3qlaLGF25L5N8KKajhc2Qb8a0WWNHUp09HxRlKk7D76PsL7v4M0B6hQ0HBD_My-rkfkUwMzSxYW-HjmHyGXPVQDF-8A_68mdPz3kji_8JomRew9bTLvxjWj_VwIf7kDsGk0dszsQkhzijKL91ZDo0GW7bFaV9X9ToKD9cYQI1efUy6g1BMMIFrTvQgKYtndpuoV7RxGAZotNkQasa43eUDLlL3SjARahFP2MWBaI-ZwnormCoEyV8jGOSdnR14TTbOGPma85uA30ON1eIikVFTI8uKX8Efmq3hxqbv_5GHQVFOhwUj32Vgq4aXg5gwDAEqh2CEU4fgbced09WZlirNOATMvKK1NsgaAxlYJUhlYMp3sGd21rDdrsW4XkYFCqsOs9LMx_nGq6ht6nG-fgaNHgn9J8XlAjL3nXKAJHTGu-3sv17brQSwFC1gngq0_QItPmlneMlppOywke5cZD8EHdS6sg69_sStc7byyiR1DR-WwuIj89RcPV_1i0GMKY3sCPIhD3wNqXiYYMCeyVgWg646-MMuYsFYwx0DlKkxcWtValaEH-TWWONw9qDhzXZcBPIAtsfHmfZsSbIhNttpxV7PvtBiSahaBoqlogmhiCM8mvWVpHlDpx_5cSbFsIU3nzu8AAE6D2zyN_u2gX93gYv8vk_qilG_DTpdnmo3iFERXy6uJBZP3bt0ivREzQfgyST4FS3ORkMJEqrzgjCZHaLX3D6eoCv1xAI2Qowx6n1XK0Fky9Zn5beD9pdKbocz-9yCoivGz79LOzQGPrk2nMJxPitnL5UZo-iUYG5mY4_VsAY_QS_epgbBBFAZWBfJulaI7yEUX8ZBQFMM__IOWDoAFA8-LUsWEtl6rWJQrDA44203lSYEw0GUMenDdbVgUjwi46FgYgSI8hmRE0w-Xq57YdrdtPiJMAk0qOATK1sjAhI_I2RcWLZTL8Pf550QGMStB9qmljm6WEQJzlT24gAaO2m5rm7CYJMRMflqZv9smeqsNBE-ZMgwAayV1FnDm92xx4MPZrTIA2KqeWFkN&isca=1" alt="" style="width: 110px; max-width: 110px; height: auto; margin: auto; display: block;">
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