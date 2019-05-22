<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html4"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="hu-HU">
<head>
	<title></title>
    <style type="text/css">
        * {
        }
        body, html {
            font-size: 13px;
            margin:0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
          background: #f4f8ff;
        }

        header{
          padding: 10px 0;
          background: white;
        }

        footer{
          background: #2c62c7;
        }

        a:link, a:visited {
            color:#02a0e3;
        }
        .width {
            width: 800px;
             margin: 0 auto;
        }
        .cdiv {
            height: 10px;
            background: #02a0e3;
            display: block;
            position: relative;
        }
        .wb {
            background: #d9d9d9;
            font-size: 10px;
            color: #333333;
        }
        .radius {
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
        }
        .content-holder {
            background: #fff;
            color: #404040;
            padding: 25px;
        }

        .content-holder h1{
          color: black;
          margin: 0 0 25px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px !important;
            color: #ffffff !important;
        }
        .footer a {
          color: white;
        }
        .footer .row {
            margin: 5px 0;
        }
        .footer tr td {
            text-align: center;
            border-right: 1px solid #ffffff;
            font-size: 12px !important;
            color: #ffffff !important;
        }
         .footer tr td:last-child {
            border-right: none;
        }

        .footer .info {
            font-size: 11px;
            color: #8c8c8c;
        }
        .pad{
          padding: 12px;
        }
        .relax {
            color: #000000;
            font-size: 1.2rem;
            margin: 10px 0 5px 0;
        }
         table.if {
            font-size: 12px;
            color: #8c8c8c;
        }

         table.if strong {
            color: #444444;
        }
        table.if td{
          padding: 5px;
        }
        table.if th{
          background: #fafafa;
          text-align: left;
          padding: 5px;
        }
        table.if,
        table.if td,
        table.if th {
            border: 1px solid #d7d7d7;
            border-collapse: collapse;
        }
        .contacts{
          font-size: 0.7rem !important;
        }
        .contacts,
        .contacts a,
        .contacts a:hover,
        .contacts a:visited{
          color: white !important;
        }

        table.if tbody td a {
            font-weight: bold;
        }

        @media all and (max-width: 800px){
          .width{
            width: 100%;
          }
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
</head>
<body>

<header>
  <div class="width">
      <table width="100%" border="0" style="border:none;">
          <tr>
              <tbody>
                  <tr>
                      <td style="text-align:center;">
                          <img src="<?=DOMAIN?><?=$settings['logo']?>" alt="<?=$settings['page_title']?>" style="width: 33%;">
                          <div class="relax"><?=$settings['page_description']?></div>
                      </td>
                  </tr>
              </tbody>
          </tr>
      </table>
  </div>
</header>

<div class="width">
<div class="in-content">
    <div class="content-holder">
