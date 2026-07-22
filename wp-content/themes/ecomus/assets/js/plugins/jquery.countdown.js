/**
 * jQuery Tabs plugin 1.0.0
 *
 * @author Drfuri
 */
(function ($) {
    $.fn.ecomus_countdown = function () {
        return this.each(function () {
            var $this = $(this),
                diff = $this.data('expire'),
                icon = $this.data('icon');

            var updateClock = function (distance) {
                var days = Math.floor(distance / (60 * 60 * 24));
                var hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
                var minutes = Math.floor((distance % (60 * 60)) / (60));
                var seconds = Math.floor(distance % 60);
                var texts = $this.data('text');
                var icons = icon ? icon : '';
                var daySimple = texts.day ? texts.day : texts.days;
                var hourSimple = texts.hour ? texts.hour : texts.hours;
                var minuteSimple = texts.minute ? texts.minute : texts.minutes;
                var secondSimple = texts.second ? texts.second : texts.seconds;

                $this.html(
                    icons +
                    '<span class="days timer"><span class="digits">' + days + '</span><span class="text">' + (days == 1 ? daySimple : texts.days) + '</span><span class="divider">:</span></span>' +
                    '<span class="hours timer"><span class="digits">' + (hours < 10 ? '0' : '') + hours + '</span><span class="text">' + (hours == 1 ? hourSimple : texts.hours) + '</span><span class="divider">:</span></span>' +
                    '<span class="minutes timer"><span class="digits">' + (minutes < 10 ? '0' : '') + minutes + '</span><span class="text">' + (minutes == 1 ? minuteSimple : texts.minutes) + '</span><span class="divider">:</span></span>' +
                    '<span class="seconds timer"><span class="digits">' + (seconds < 10 ? '0' : '') + seconds + '</span><span class="text">' + (seconds == 1 ? secondSimple : texts.seconds) + '</span></span>'
                );
            };

            updateClock(diff);

            var countdown = setInterval(function () {
                diff = diff - 1;
                var new_diff = diff < 0 ? 0 : diff;
                updateClock(new_diff);

                if (diff < 0) {
                    clearInterval(countdown);
                }
            }, 1000);
        });
    };

    /* Init tabs */
    $(function () {
        $('.ecomus-countdown').ecomus_countdown();

        $(document.body).on('ecomus_countdown', function (e, $el) {
            $el.ecomus_countdown();
        });
    });
})(jQuery);