<?php
/** @var $bfdesign array Form design option */
/** @var $form_slug string Form slug */
/** @var $is_registration_form bool Determinate if the current form is a registration form */
/** @var $need_registration_form bool Determinate if the current form need a registration form include */
$css_form_id = 'buddyforms_form_' . $form_slug;
?>
<style type="text/css" <?php echo apply_filters( 'buddyforms_add_loop_style_attributes', '', $css_form_id ); ?>>
    /*
	 * This is the BuddyForms Front End CSS for the views:
	 *
	 * - My Posts (List View)
	 * - My Posts (Table View)
	 * - Form Submission Single View
	 *
	 */

    /* --- BuddyForms View My Posts - LIST View --- */

    ul.buddyforms-list {
        margin: 25px 0;
        padding: 0;
        list-style: none outside none;
    }

    ul.buddyforms-list li.bf-submission {
        height: auto;
        overflow: auto;
        min-height: 44px;
        position: relative;
        padding: 20px 0 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        margin-left: 0px;
        list-style: none outside none;
    }

    ul.buddyforms-list li .item {
        overflow: auto;
        padding: 0 0 10px 0;
        margin: 0;
    }

    ul.buddyforms-list li img.thumb {
        width: 420px;
        max-width: 100%;
        height: auto;
        float: left;
        margin: 3px 10px 10px 2px;
    }

    ul.buddyforms-list li .item-title {
        clear: both;
    }

    ul.buddyforms-list li .item-desc {
        font-size: 13px;
        margin: 5px 5px 5px 0;
        clear: right;
    }

    @media (min-width: 420px) {

        ul.buddyforms-list li img.thumb {
            width: 120px;
            height: 120px;
        }

        ul.buddyforms-list li .item-title {
            clear: none;
        }

    }

    @media (min-width: 768px) {

        ul.buddyforms-list li .item {
            overflow: auto;
            width: calc(100% - 180px);
            float: left;
        }

        ul.buddyforms-list li div.action {
            max-width: 50px;
            width: auto;
            margin-bottom: 10px;
            float: right;
            text-align: left;
            min-width: 175px;
            font-size: 12px;
            clear: none;
        }

    }


    /* The Actions Box */
    ul.buddyforms-list li div.action {
        clear: none;
        margin-bottom: 20px;
        font-size: 12px;
    }

    ul.buddyforms-list .item-status {
        display: inline-block;
        margin: 10px 10px 10px 0;
        font-weight: normal;
    }

    ul.buddyforms-list .item-status:before {
        background-color: #e3e3e3;
        content: "";
        padding: 0px 8px;
        border-radius: 50%;
        margin-right: 4px;
    }

    ul.buddyforms-list .publish .item-status:before {
        background-color: #70d986;
    }

    ul.buddyforms-list .draft .item-status:before,
    ul.buddyforms-list .publish .draft .item-status:before,
    ul.buddyforms-list .edit-draft .item-status:before,
    ul.buddyforms-list .publish .edit-draft .item-status:before {
        background-color: #e3e3e3;
    }

    ul.buddyforms-list .bf-pending .item-status:before,
    ul.buddyforms-list .publish .bf-pending .item-status:before {
        background-color: #f3a93c;
    }

    ul.buddyforms-list .publish-date {
        margin: 5px 0 5px 0;
        font-size: 12px;
        color: #888;
    }

    /* Action Links - Edit and Delete */
    .buddyforms-list .edit_links {
        display: inline-block;
        padding: 0;
        margin: 5px 0 5px;
    }

    .buddyforms-list .edit_links li {
        display: inline-block;
        padding: 0;
        margin: 0;
        border: none;
        min-height: 0;
    }

    .buddyforms-list .edit_links a {
        padding: 3px 8px;
        margin: 0 5px 5px 0;
        display: inline-block;
        text-decoration: none;
        background: #f9f9f9;
        border: 1px solid #aaa;
        border: 1px solid rgba(0, 0, 0, 0.2);
        text-shadow: none;
        box-shadow: none;
        border-radius: 3px;
        line-height: 14px;
        color: #888;
    }

    .buddyforms-list .edit_links a:hover,
    .buddyforms-list .edit_links a:focus {
        border: 1px solid #8f8f8f;
        border: 1px solid rgba(0, 0, 0, 0.4);
    }

    .buddyforms-list .edit_links .dashicons:before {
        line-height: 14px;
        font-size: 12px;
    }

    .buddyforms-list .dashicons.disabled {
        opacity: 0.5;
    }

    @media (min-width: 768px) {

        .buddyforms-list .edit_links {
            display: block;
            margin: 10px 0 5px;
        }

    }


    /* BuddyForms Sub List */
    .buddyforms-list-sub {
        clear: both;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .buddyforms-list .buddyforms-list-sub .item {
        width: auto;
        clear: none;
        padding: 0;
    }

    .bf-submission-sub {
        padding: 15px 0 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .buddyforms-list .buddyforms-list-sub img.thumb {
        width: 75px;
        height: auto;
        border-radius: 0;
        float: left;
        margin: 5px 10px 5px 0;
    }

    .buddyforms-list .buddyforms-list-sub .item-desc {
        display: none;
    }

    .buddyforms-list .buddyforms-list-sub .action {
        clear: none;
        margin-left: 85px;
    }

    @media (min-width: 768px) {

        .buddyforms-list-sub {
            margin: 0 0 0 46px;
            border-top: none;
        }

        .buddyforms-list .buddyforms-list-sub .item {
            clear: none;
            width: calc(100% - 260px);
        }

        .buddyforms-list .buddyforms-list-sub .item-desc {
            display: block;
        }

        .buddyforms-list .buddyforms-list-sub .action {
            margin-left: 0;
        }

    }

    /* --- BuddyForms View My Posts - TABLE View --- */

    .table-striped > tbody > tr:nth-of-type(2n) {
        background-color: #f0f0f0;
    }

    .buddyforms_posts_table {
        margin-bottom: 25px;
    }

    .buddyforms_posts_table .table {
        width: 100%;
        max-width: 100%;
        border: none;
        border-top: 0;
    }

    @media screen and (max-width: 767px) {

        .buddyforms_posts_table .table thead {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .buddyforms_posts_table table,
        .buddyforms_posts_table thead,
        .buddyforms_posts_table tbody,
        .buddyforms_posts_table th,
        .buddyforms_posts_table td,
        .buddyforms_posts_table tr {
            display: block;
        }
    }

    .buddyforms_posts_table .table .table-inner {
        border: 0;
        background-color: transparent;
        padding: 0;
    }

    .buddyforms_posts_table .table-inner tbody tr:first-child td {
        padding-top: 25px;
    }

    @media screen and (max-width: 767px) {
        .buddyforms_posts_table .table-inner tbody tr:first-child td {
            padding-top: 5px;
        }
    }

    .buddyforms_posts_table .table-inner thead th:first-child {
        min-width: 165px;
    }

    .buddyforms_posts_table .table-striped > tbody > tr:nth-of-type(odd) {
        border-top: none;
        background: transparent;
    }

    .buddyforms_posts_table .table-striped > tbody > tr:nth-of-type(2n) {
        border-top: none;
        background: rgba(0, 0, 0, 0.04);
    }

    .buddyforms_posts_table .table th, .buddyforms_posts_table .table td {
        border-top: 0;
        border: 0;
    }

    .buddyforms_posts_table .table th {
        padding: 0;
    }

    .buddyforms_posts_table .table th + th {
        padding: 0 0 0 3px;
    }

    .buddyforms_posts_table .table th span {
        color: #000;
        font-size: 16px;
        line-height: 24px;
        font-weight: 500;
        text-align: left;
        letter-spacing: -0.025em;
        padding: 7px 10px;
        display: block;
        background-color: transparent;
    }

    .buddyforms_posts_table .table tbody td {
        color: #000;
        font-size: 14px;
        line-height: 24px;
        letter-spacing: -0.025em;
        padding: 25px 9px;
        vertical-align: top;
    }

    @media screen and (max-width: 767px) {
        .buddyforms_posts_table .table tbody td {
            position: relative;
            padding: 5px 9px;
            padding-left: 50%;
        }
    }

    #buddyforms-table-view .table tbody .table-wrapper {
        padding: 0;
    }

    @media screen and (max-width: 767px) {
        .buddyforms_posts_table .table.table-inner .tr-sub {
            padding: 5px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
    }

    .buddyforms_posts_table .table.table-inner td {
        padding: 5px 9px;
        width: 40%;
    }

    @media screen and (max-width: 767px) {
        .buddyforms_posts_table .table.table-inner {
            padding-top: 0;
        }

        .buddyforms_posts_table .table.table-inner td {
            position: relative;
            padding: 10px 9px 0 50%;
            width: auto;
        }
    }

    .buddyforms_posts_table .table .mobile-th {
        display: none;
        width: 50%;
        position: absolute;
        top: 7px;
        left: 10px;
    }

    @media screen and (max-width: 767px) {
        .buddyforms_posts_table .table .mobile-th {
            display: block;
        }
    }

    .buddyforms_posts_table .table-item-status {
        font-size: 13px;
        line-height: 24px;
        display: inline-block;
        margin: 0 5px 0 0;
        font-weight: normal;
    }

    @media screen and (min-width: 768px) {
        .buddyforms_posts_table .table-item-status {
            margin: 0 0 0 3px;
        }
    }

    .buddyforms_posts_table .draft .table-item-status,
    .buddyforms_posts_table .edit-draft .table-item-status,
    .buddyforms_posts_table .publish .draft .table-item-status,
    .buddyforms_posts_table .publish .edit-draft .table-item-status {
        color: #888;
    }

    .buddyforms_posts_table .table-item-status:before {
        background-color: #e3e3e3;
        content: "";
        padding: 0px 7px;
        border-radius: 50%;
        margin-right: 4px;
    }

    .buddyforms_posts_table .publish .table-item-status:before {
        background-color: #70d986;
    }

    .buddyforms_posts_table .draft .table-item-status:before,
    .buddyforms_posts_table .publish .draft .table-item-status:before,
    .buddyforms_posts_table .edit-draft .table-item-status:before,
    .buddyforms_posts_table .publish .edit-draft .table-item-status:before {
        background-color: #e3e3e3;
    }

    .buddyforms_posts_table .bf-pending .table-item-status:before,
    .buddyforms_posts_table .publish .bf-pending .table-item-status:before {
        background-color: #f3a93c;
    }

    .buddyforms_posts_table .item-status-action {
        line-height: 1.5;
        padding: 5px 0;
        letter-spacing: 0;
        font-weight: inherit;
        opacity: 0.6;
        font-size: 12px;
    }

    @media screen and (min-width: 768px) {
        .buddyforms_posts_table .item-status-action {
            margin: 0 0 0 10px;
        }
    }

    .buddyforms_posts_table .edit_links {
        margin: 0;
        padding: 0;
        list-style: none;
        text-align: left;
    }

    @media screen and (max-width: 767px) {
        .buddyforms_posts_table .edit_links {
            text-align: left;
            min-height: 30px;
            min-width: 50px;
        }
    }

    .buddyforms_posts_table .edit_links li,
    .buddyforms_posts_table .edit_links a {
        float: left;
        margin-right: 5px;
    }

    .buddyforms_posts_table .edit_links a {
        margin: 0;
        text-decoration: none;
        box-shadow: none;
        text-shadow: none;
    }

    /* --- Submissions Frontend - SINGLE View --- */
    /* only applicable for Registration & Contact Forms, otherwise it's a post ;) --- */

    .bf-submission-single .settings-input {
        display: block;
    }

    .bf-submission-single .bf-input {
        margin-bottom: 20px;
    }

    .bf-submission-single textarea,
    .bf-submission-single .standard-form input[type=url],
    .bf-submission-single .standard-form input[type=link],
    .bf-submission-single .standard-form input[type=text],
    .bf-submission-single .standard-form input[type=email],
    .bf-submission-single .standard-form input[type=date],
    .bf-submission-single .standard-form input[type=password] {
        width: 100%;
        border: 1px solid #ccc;
        background: #f1f1f1;
        color: #999;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        font: inherit;
        font-size: 15px;
        padding: 10px;
    }

    .bf-submission-single .standard-form input[type=date] {
        width: auto;
    }

    .bf-submission-single .bf-input .radio {
        display: block;
    }

    .bf-submission-single .bf-input textarea,
    .bf-submission-single .bf-input .form-control {
        display: block;
        width: 100%;
        background: #f1f1f1;
    }

    /* ----------------------------------------- */
    /* ------------ Helper Classes ------------- */
    /* ----------------------------------------- */

    .bf-alignleft {
        text-align: left;
    }

    .bf-aligncenter {
        text-align: center;
    }

    .bf-alignright {
        text-align: right;
    }

    .bf-box {
        padding: 20px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
    }

    .buddyforms-posts-content .dashicons {
        line-height: inherit;
        font-size: inherit;
        width: auto;
        height: auto;
    }

    .buddyforms-posts-content .dashicons:before {
        font-size: 19px;
    }

    /* Quick Column Grid for BuddyForms Frontend */
    .bf-row {
        margin: 30px -15px;
        padding: 30px 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        overflow: auto;
        display: block;
    }

    .bf-col-50 {
        padding: 0 15px 15px;
    }

    @media (min-width: 768px) {
        .bf-col-50 {
            width: 50%;
            float: left;
            padding: 0 15px 15px;
        }
    }


    /* Modals */

    .ui-dialog {
        background: #fff;
        padding: 30px;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.5);
    }

    .ui-dialog-titlebar-close {
        top: 0;
        right: 0;
        position: absolute;
        background: red;
        color: white;
        border: none;
        font-size: 11px;
        text-transform: uppercase;
        padding: 5px;
    }

    .bf-popup-button {
        margin: 15px 0 30px;
    }
</style>