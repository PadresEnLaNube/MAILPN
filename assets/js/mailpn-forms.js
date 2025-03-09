(function($) {
  'use strict';

  $(document).ready(function() {
    if ($('.mailpn-password-checker').length) {
      var pass_view_state = false;

      function mailpn_pass_check_strength(pass) {
        var strength = 0;
        var password = $('.mailpn-password-strength');
        var low_upper_case = password.closest('.mailpn-password-checker').find('.low-upper-case i');
        var number = password.closest('.mailpn-password-checker').find('.one-number i');
        var special_char = password.closest('.mailpn-password-checker').find('.one-special-char i');
        var eight_chars = password.closest('.mailpn-password-checker').find('.eight-character i');

        //If pass contains both lower and uppercase characters
        if (pass.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
          strength += 1;
          low_upper_case.text('task_alt');
        } else {
          low_upper_case.text('radio_button_unchecked');
        }

        //If it has numbers and characters
        if (pass.match(/([0-9])/)) {
          strength += 1;
          number.text('task_alt');
        } else {
          number.text('radio_button_unchecked');
        }

        //If it has one special character
        if (pass.match(/([!,%,&,@,#,$,^,*,?,_,~,|,¬,+,ç,-,€])/)) {
          strength += 1;
          special_char.text('task_alt');
        } else {
          special_char.text('radio_button_unchecked');
        }

        //If pass is greater than 7
        if (pass.length > 7) {
          strength += 1;
          eight_chars.text('task_alt');
        } else {
          eight_chars.text('radio_button_unchecked');
        }

        // If value is less than 2
        if (strength < 2) {
          $('.mailpn-password-strength-bar').removeClass('mailpn-progress-bar-warning mailpn-progress-bar-success').addClass('mailpn-progress-bar-danger').css('width', '10%');
        } else if (strength == 3) {
          $('.mailpn-password-strength-bar').removeClass('mailpn-progress-bar-success mailpn-progress-bar-danger').addClass('mailpn-progress-bar-warning').css('width', '60%');
        } else if (strength == 4) {
          $('.mailpn-password-strength-bar').removeClass('mailpn-progress-bar-warning mailpn-progress-bar-danger').addClass('mailpn-progress-bar-success').css('width', '100%');
        }
      }

      $(document).on('click', ('.mailpn-show-pass'), function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var mailpn_btn = $('.mailpn-show-pass');

        if (pass_view_state) {
          mailpn_btn.siblings('#mailpn-password').attr('type', 'password');
          mailpn_btn.find('i').text('visibility_off');
          pass_view_state = false;
        }else{
          mailpn_btn.siblings('#mailpn-password').attr('type', 'text');
          mailpn_btn.find('i').text('visibility');
          pass_view_state = true;
        } 
      });

      $(document).on('keyup', ('.mailpn-password-strength'), function(e){
        mailpn_pass_check_strength($('.mailpn-password-strength').val());

        if (!$('#mailpn-popover-pass').is(':visible')) {
          $('#mailpn-popover-pass').fadeIn('slow');
        }

        if (!$('.mailpn-show-pass').is(':visible')) {
          $('.mailpn-show-pass').fadeIn('slow');
        }
      });
    }
    
    $(document).on('mouseover', '.mailpn-input-star', function(e){
      if (!$(this).closest('.mailpn-input-stars').hasClass('clicked')) {
        $(this).text('star');
        $(this).prevAll('.mailpn-input-star').text('star');
      }
    });

    $(document).on('mouseout', '.mailpn-input-stars', function(e){
      if (!$(this).hasClass('clicked')) {
        $(this).find('.mailpn-input-star').text('star_outlined');
      }
    });

    $(document).on('click', '.mailpn-input-star', function(e){
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      $(this).closest('.mailpn-input-stars').addClass('clicked');
      $(this).closest('.mailpn-input-stars').find('.mailpn-input-star').text('star_outlined');
      $(this).text('star');
      $(this).prevAll('.mailpn-input-star').text('star');
      $(this).closest('.mailpn-input-stars').siblings('.mailpn-input-hidden-stars').val($(this).prevAll('.mailpn-input-star').length + 1);
    });

    $(document).on('change', '.mailpn-input-hidden-stars', function(e){
      $(this).siblings('.mailpn-input-stars').find('.mailpn-input-star').text('star_outlined');
      $(this).siblings('.mailpn-input-stars').find('.mailpn-input-star').slice(0, $(this).val()).text('star');
    });

    if ($('.mailpn-field[data-mailpn-parent]').length) {
      mailpn_form_update();

      $(document).on('change', '.mailpn-field[data-mailpn-parent~="this"]', function(e) {
        mailpn_form_update();
      });
    }

    if ($('.mailpn-html-multi-group').length) {
      $(document).on('click', '.mailpn-html-multi-remove-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var mailpn_users_btn = $(this);

        if (mailpn_users_btn.closest('.mailpn-html-multi-wrapper').find('.mailpn-html-multi-group').length > 1) {
          $(this).closest('.mailpn-html-multi-group').remove();
        }else{
          $(this).closest('.mailpn-html-multi-group').find('input, select, textarea').val('');
        }
      });

      $(document).on('click', '.mailpn-html-multi-add-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        $(this).closest('.mailpn-html-multi-wrapper').find('.mailpn-html-multi-group:first').clone().insertAfter($(this).closest('.mailpn-html-multi-wrapper').find('.mailpn-html-multi-group:last'));
        $(this).closest('.mailpn-html-multi-wrapper').find('.mailpn-html-multi-group:last').find('input, select, textarea').val('');

        $(this).closest('.mailpn-html-multi-wrapper').find('.mailpn-input-range').each(function(index, element) {
          $(this).siblings('.mailpn-input-range-output').html($(this).val());
        });
      });

      $('.mailpn-html-multi-wrapper').sortable({handle: '.mailpn-multi-sorting'});

      $(document).on('sortstop', '.mailpn-html-multi-wrapper', function(event, ui){
        mailpn_get_main_message(mailpn_i18n.ordered_element);
      });
    }

    if ($('.mailpn-input-range').length) {
      $('.mailpn-input-range').each(function(index, element) {
        $(this).siblings('.mailpn-input-range-output').html($(this).val());
      });

      $(document).on('input', '.mailpn-input-range', function(e) {
        $(this).siblings('.mailpn-input-range-output').html($(this).val());
      });
    }

    if ($('.mailpn-image-btn').length) {
      var image_frame;

      $(document).on('click', '.mailpn-image-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (image_frame){
          image_frame.open();
          return;
        }

        var mailpn_input_btn = $(this);
        var mailpn_images_block = mailpn_input_btn.closest('.mailpn-images-block').find('.mailpn-images');
        var mailpn_images_input = mailpn_input_btn.closest('.mailpn-images-block').find('.mailpn-image-input');

        var image_frame = wp.media({
          title: (mailpn_images_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_images : mailpn_i18n.select_image,
          library: {
            type: 'image'
          },
          multiple: (mailpn_images_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
        });

        image_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (mailpn_images_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.edit_images : mailpn_i18n.edit_image,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(image_frame.options.library),
            multiple: (mailpn_images_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        image_frame.open();

        image_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = image_frame.state().get('selection').toJSON();
          mailpn_images_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            mailpn_images_block.append('<img src="' + $(this)[0].url + '" class="">');
          });

          mailpn_input_btn.text((mailpn_images_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_images : mailpn_i18n.select_image);
          mailpn_images_input.val(ids);
        });
      });
    }

    if ($('.mailpn-audio-btn').length) {
      var audio_frame;

      $(document).on('click', '.mailpn-audio-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (audio_frame){
          audio_frame.open();
          return;
        }

        var mailpn_input_btn = $(this);
        var mailpn_audios_block = mailpn_input_btn.closest('.mailpn-audios-block').find('.mailpn-audios');
        var mailpn_audios_input = mailpn_input_btn.closest('.mailpn-audios-block').find('.mailpn-audio-input');

        var audio_frame = wp.media({
          title: (mailpn_audios_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_audios : mailpn_i18n.select_audio,
          library : {
            type : 'audio'
          },
          multiple: (mailpn_audios_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
        });

        audio_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (mailpn_audios_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_audios : mailpn_i18n.select_audio,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(audio_frame.options.library),
            multiple: (mailpn_audios_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        audio_frame.open();

        audio_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = audio_frame.state().get('selection').toJSON();
          mailpn_audios_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            mailpn_audios_block.append('<div class="mailpn-audio mailpn-tooltip" title="' + $(this)[0].title + '"><i class="dashicons dashicons-media-audio"></i></div>');
          });

          $('.mailpn-tooltip').tooltipster({maxWidth: 300,delayTouch:[0, 4000], customClass: 'mailpn-tooltip'});
          mailpn_input_btn.text((mailpn_audios_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_audios : mailpn_i18n.select_audio);
          mailpn_audios_input.val(ids);
        });
      });
    }

    if ($('.mailpn-video-btn').length) {
      var video_frame;

      $(document).on('click', '.mailpn-video-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (video_frame){
          video_frame.open();
          return;
        }

        var mailpn_input_btn = $(this);
        var mailpn_videos_block = mailpn_input_btn.closest('.mailpn-videos-block').find('.mailpn-videos');
        var mailpn_videos_input = mailpn_input_btn.closest('.mailpn-videos-block').find('.mailpn-video-input');

        var video_frame = wp.media({
          title: (mailpn_videos_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_videos : mailpn_i18n.select_video,
          library : {
            type : 'video'
          },
          multiple: (mailpn_videos_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
        });

        video_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (mailpn_videos_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_videos : mailpn_i18n.select_video,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(video_frame.options.library),
            multiple: (mailpn_videos_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        video_frame.open();

        video_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = video_frame.state().get('selection').toJSON();
          mailpn_videos_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            mailpn_videos_block.append('<div class="mailpn-video mailpn-tooltip" title="' + $(this)[0].title + '"><i class="dashicons dashicons-media-video"></i></div>');
          });

          $('.mailpn-tooltip').tooltipster({maxWidth: 300,delayTouch:[0, 4000], customClass: 'mailpn-tooltip'});
          mailpn_input_btn.text((mailpn_videos_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_videos : mailpn_i18n.select_video);
          mailpn_videos_input.val(ids);
        });
      });
    }

    if ($('.mailpn-file-btn').length) {
      var file_frame;

      $(document).on('click', '.mailpn-file-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (file_frame){
          file_frame.open();
          return;
        }

        var mailpn_input_btn = $(this);
        var mailpn_files_block = mailpn_input_btn.closest('.mailpn-files-block').find('.mailpn-files');
        var mailpn_files_input = mailpn_input_btn.closest('.mailpn-files-block').find('.mailpn-file-input');

        var file_frame = wp.media({
          title: (mailpn_files_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_files : mailpn_i18n.select_file,
          multiple: (mailpn_files_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
        });

        file_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (mailpn_files_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.select_files : mailpn_i18n.select_file,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(file_frame.options.library),
            multiple: (mailpn_files_block.attr('data-mailpn-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        file_frame.open();

        file_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          var attachments_arr = file_frame.state().get('selection').toJSON();
          mailpn_files_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            mailpn_files_block.append('<embed src="' + $(this)[0].url + '" type="application/pdf" class="mailpn-embed-file"/>');
          });

          mailpn_input_btn.text((mailpn_files_block.attr('data-mailpn-multiple') == 'true') ? mailpn_i18n.edit_files : mailpn_i18n.edit_file);
          mailpn_files_input.val(ids);
        });
      });
    }
  });

  $(document).on('click', '.mailpn-toggle', function(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var mailpn_toggle = $(this);

    if (mailpn_toggle.find('i').length) {
      if (mailpn_toggle.siblings('.mailpn-toggle-content').is(':visible')) {
        mailpn_toggle.find('i').text('add');
      }else{
        mailpn_toggle.find('i').text('clear');
      }
    }

    mailpn_toggle.siblings('.mailpn-toggle-content').fadeToggle();
  });
})(jQuery);
