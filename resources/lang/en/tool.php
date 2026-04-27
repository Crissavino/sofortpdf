<?php

return [
    // Upload zone
    'drop_or_click' => 'Drop file here or click to select',
    'formats_label' => 'Formats:',
    'up_to_files' => 'Up to :n files',

    // Processing
    'processing' => 'Processing…',
    'js_starting_conversion' => 'Starting conversion…',
    'please_wait' => 'Please wait, we are preparing your document.',
    'loading_step_1' => 'Loading your document.',
    'loading_step_2' => 'Converting your document.',
    'loading_step_3' => 'Preparing your download.',
    'loading_converting' => ':tool — processing.',
    'loading_signing' => 'Processing your signature.',

    // Fake conversion loading modal (paywall flow)
    'fake_loading_title'  => 'Conversion in progress, please wait a moment',
    'fake_loading_step_1' => 'Uploading your document',
    'fake_loading_step_2' => 'Converting your document',
    'fake_loading_step_3' => 'Securing your document',

    // Done / download
    'done' => 'Done!',
    'ready_for_download' => 'Your file is ready for download.',
    'download' => 'Download',
    'process_another' => 'Process another file',

    // Error
    'error_generic' => 'An error occurred. Please try again.',
    'try_again' => 'Try again',

    // How it works
    'how_heading' => 'How it works',
    'step_label' => 'Step :n',
    'step1_title' => 'Upload file',
    'step1_desc' => 'Drag your file into the upload area or click to select.',
    'step2_title' => 'Process automatically',
    'step2_desc' => 'Our servers process your file in seconds — securely and reliably.',
    'step3_title' => 'Download',
    'step3_desc' => 'Download your finished file instantly. No waiting.',

    // FAQ
    'faq_heading' => 'Frequently Asked Questions',
    'faq_secure_q' => 'Is it secure to use?',
    'faq_secure_a' => 'Yes. All files are transferred via an SSL-encrypted connection and automatically deleted after 1 hour.',
    'faq_formats_q' => 'Which file formats are supported?',
    'faq_formats_a' => 'This tool supports the following formats: :formats.',
    'faq_size_q' => 'Is there a maximum file size?',
    'faq_size_a' => 'The maximum file size is :size MB per file.',
    'faq_mobile_q' => 'Does it work on mobile?',
    'faq_mobile_a' => 'Yes, sofortpdf.com works on all devices — desktop, tablet, and smartphone.',

    // Tool-specific params (rendered above the action button)
    'watermark_text_label' => 'Watermark text',
    'watermark_text_placeholder' => 'e.g. CONFIDENTIAL',
    'watermark_text_hint' => 'This text will be printed as a watermark on every page.',
    'param_required_suffix' => '*',
    'param_required_error' => 'Please fill in all required fields.',

    'rotate_angle_label' => 'Rotation angle',

    'protect_password_label' => 'Password',
    'protect_password_placeholder' => 'Choose a strong password',
    'protect_password_hint' => 'This password will be required to open the PDF.',

    'unlock_password_label' => 'Current password',
    'unlock_password_placeholder' => 'Enter the PDF password',
    'unlock_password_hint' => 'The password the PDF is currently protected with.',

    'pages_placeholder' => 'e.g. 1-3, 5, 7-9',
    'pages_hint' => 'Page numbers or ranges separated by commas.',
    'pages_remove_label' => 'Pages to remove',
    'pages_extract_label' => 'Pages to extract',

    // Page picker UI
    'picker_remove_heading' => 'Pick pages to remove',
    'picker_extract_heading' => 'Pick pages to extract',
    'picker_remove_hint' => 'Click the pages you want to remove.',
    'picker_extract_hint' => 'Click the pages you want to keep.',
    'picker_page_label' => 'Page :n',
    'picker_loading' => 'Loading pages …',
    'picker_selected_count_remove' => ':n page(s) marked to remove',
    'picker_selected_count_extract' => ':n page(s) marked to extract',
    'picker_need_selection_remove' => 'Pick at least one page to remove.',
    'picker_need_selection_extract' => 'Pick at least one page to extract.',
    'picker_select_all' => 'Select all',
    'picker_select_none' => 'Clear selection',

    // Rotate-mode picker
    'picker_rotate_heading' => 'Click pages to rotate',
    'picker_rotate_hint' => 'Each click rotates the page 90° clockwise.',
    'picker_rotate_count' => ':n page(s) rotated',
    'picker_need_rotation' => 'Rotate at least one page by clicking it.',
    'picker_reset_rotations' => 'Reset rotations',

    // Split-mode picker
    'picker_split_heading' => 'Place cut points between pages',
    'picker_split_hint' => 'Click between two pages to insert a cut point. Each resulting group becomes its own PDF.',
    'picker_split_count' => ':n group(s) — :groups',
    'picker_need_split' => 'Place at least one cut point.',
    'picker_reset_splits' => 'Reset cuts',

    'ocr_language_label' => 'Language',
    'ocr_language_hint' => 'Pick the language of the text in the document.',
    'ocr_lang_deu' => 'German',
    'ocr_lang_eng' => 'English',
    'ocr_lang_deu_eng' => 'German + English',
    'ocr_lang_spa' => 'Spanish',
    'ocr_lang_fra' => 'French',
    'ocr_lang_ita' => 'Italian',
    'ocr_lang_por' => 'Portuguese',
    'ocr_lang_nld' => 'Dutch',

    // Related
    'related_heading' => 'More PDF Tools',

    // Meta / page-title
    'title_suffix' => ' — Instant & Online',
    'default_action_label' => 'Convert now',
    'maintenance_suffix' => ' — Maintenance',
    'maintenance_heading' => 'Maintenance',
    'maintenance_body' => 'This tool is temporarily unavailable. Please try again later.',

    // Benefits (left column on desktop)
    'benefit_fast_title'   => 'Lightning-fast processing',
    'benefit_fast_desc'    => 'Your documents are processed in seconds. No waiting, no delays.',
    'benefit_secure_title' => 'Maximum security',
    'benefit_secure_desc'  => 'Files are encrypted in transit and automatically deleted after 1 hour.',
    'benefit_quality_title'=> 'Perfect quality',
    'benefit_quality_desc' => 'Formatting and layout are fully preserved — professional results guaranteed.',
    'benefit_free_title'   => 'Get started instantly',
    'benefit_free_desc'    => 'No installation, no signup required. Works directly in your browser on any device.',

    // Trust badges (under upload zone)
    'trust_fast'       => 'Instant results',
    'trust_secure'     => 'GDPR compliant',
    'trust_quality'    => '100% quality',
    'trust_delete'     => 'Files deleted after 1h',

    // Social proof stats
    'stat_docs'        => 'Documents converted',
    'stat_users'       => 'Happy users',
    'stat_quality'     => 'Quality guaranteed',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'Only one file allowed.',
    'js_max_files' => 'Maximum {n} files allowed at once.',
    'js_file_too_large' => 'The file "{name}" is too large. Maximum file size: {size} MB',
    'js_add_another' => 'Add another file',
    'js_upload_failed' => 'Upload failed.',
    'js_conversion_failed' => 'Conversion failed.',
];
