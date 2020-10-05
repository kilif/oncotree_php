var moveInterval;

$.fn.textWidth = function(text, font) {
    if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
    $.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
    return $.fn.textWidth.fakeEl.width();
};

function timesheet_moveTimeline(container, direction, speed, maxLength)
{
  //$('.labori_timeline_bubble_details').hide();
  //$('.labori_timeline_bubble_details_noclick').hide();
  moveInterval = setInterval(function()
  {
      /*if(parseInt($('#' + container + ' .scale').css('margin-left').replace('px', '')) + direction > 0)
      {
        return;
      }
      else if(Math.abs(parseInt($('#' + container + ' .scale').css('margin-left').replace('px', '')) + direction) > ((maxLength + 100) - $('#' + container).width()))
      {
        return;
      }*/

      $('#' + container + ' .scale').css('margin-left', parseInt($('#' + container + ' .scale').css('margin-left').replace('px', '')) + direction); 
      $('#' + container + ' .data').css('margin-left', parseInt($('#' + container + ' .data').css('margin-left').replace('px', '')) + direction);

      var scaleShift = parseInt($('#' + container + ' .data').css('margin-left').replace('px', ''));
      var timelineRows = $('#' + container + ' li');

      if(scaleShift < 0)
      {
        scaleShift = Math.abs(scaleShift);
        for(var i = 0; i < timelineRows.length; i++)
        { 
          var rowElements = $(timelineRows[i]).children();
          var computedWidth = 0;
          for(var j = 0; j < rowElements.length; j++)
          {
            var tempObj = $(rowElements[j]);
            var tempMarginLeft = parseInt(tempObj.css('margin-left').replace('px', ''));

            if(computedWidth + tempMarginLeft < scaleShift)
            {
              var textObj = tempObj.find('span');
              if(5 + (scaleShift - tempMarginLeft - computedWidth) < (tempObj.innerWidth() - textObj.width() - 10))
              {
                textObj.css('margin-left', 5 + (scaleShift - tempMarginLeft - computedWidth));
              }
            }

            computedWidth += tempMarginLeft + tempObj.width() + 10;
          }
        }
      }
  }, speed);     
}

function timesheet_stopMovement()
{
  clearInterval(moveInterval);
}

function timesheet_getPixelAmount(cssText)
{
  if(cssText == '')
  {
    return 0;
  }
  else
  {
    return parseInt(cssText.replace('px', ''));
  }
}


(function() {
  'use strict';

  /**
   * Initialize a Timesheet
   */
  var Timesheet = function(container, min, max, data) {
    this.data = [];
    this.maxLength = 0;
    this.containerID = container;
    this.year = {
      min: min,
      max: max
    };

    this.completeParse(data || []);

    if (typeof document !== 'undefined') {
      this.container = (typeof container === 'string') ? document.querySelector('#'+container) : container;
      this.drawSections();
      this.insertDataCompact();
    }

    this.container.innerHTML += '<div class="timeline_button timeline_back_btn" onmousedown="timesheet_moveTimeline(\'' + 
                                 container + 
                                 '\', 2, 1, ' + this.maxLength + ')" onmouseup="timesheet_stopMovement()" onmouseout="timesheet_stopMovement()"><i class="fa fa-backward" aria-hidden="true"></i></div>';
    this.container.innerHTML += '<div class="timeline_button timeline_forward_btn" onmousedown="timesheet_moveTimeline(\'' + 
                                 container + 
                                 '\', -2, 1, ' + this.maxLength + ')" onmouseup="timesheet_stopMovement()" onmouseout="timesheet_stopMovement()"><i class="fa fa-forward" aria-hidden="true"></i></div>';
  };

  /**
   * Insert data into Timesheet
   */
  Timesheet.prototype.insertData = function() {
    var html = [];
    var widthMonth = this.container.querySelector('.scale section').offsetWidth;

    for (var n = 0, m = this.data.length; n < m; n++) {
      var cur = this.data[n];
      var bubble = this.createBubble(widthMonth, this.year.min, cur.start, cur.end, cur.label);

      var line = [
        '<span style="margin-left: ' + bubble.getStartOffset() + 'px; width: ' + 
                                       bubble.getWidth() + 'px;" class="bubble bubble-' + 
                                       (cur.type || 'default') + '" data-duration="' + 
                                       (cur.end ? Math.round((cur.end-cur.start)/1000/60/60/24/39) : '') + '">' + 
                                       '' + '</span>',
        '<span class="date">' + bubble.getDateLabel() + '</span>',
        '<span class="label">' + cur.label + '</span>'
      ].join('');

      html.push('<li>' + line + '</li>');
    }

    this.container.innerHTML += '<ul class="data">' + html.join('') + '</ul>';
  };

  Timesheet.prototype.insertDataCompact = function() {
    var html = [];
    var widthMonth = this.container.querySelector('.scale section').offsetWidth;
    $('#' + this.containerID).html($('#' + this.containerID).html() + '<ul class="data"></ul>');

    for (var n = 0, m = this.data.length; n < m; n++) {
      var cur = this.data[n];
      var bubble = this.createBubble(widthMonth, this.year.min, cur.start, cur.end, cur.label);

      var tempContent = cur.icon == 'default' ? '<i class="fa fa-info-circle" aria-hidden="true"></i>' : cur.icon;
      tempContent += ' ' + cur.label; 

      var tempOnClick = '""';

      if(cur.click_show != null)
      {
        tempOnClick = '"$(\'.labori_timeline_bubble_details\').hide(); $(\'.labori_timeline_bubble_details_noclick\').hide(); $(\'#' + cur.click_show + '\').show();"';
      }

      var line = [
        '<span onclick=' + tempOnClick + ' style="margin-left: ' + '---REPLACE_ME---' + 'px; width: ' + 
                                       bubble.getWidth() + 'px;" class="bubble bubble-' + 
                                       (cur.type || 'default') + '" data-duration="' + 
                                       (cur.end ? Math.round((cur.end-cur.start)/1000/60/60/24/39) : '') + '">' + 
                                       '<span>' + tempContent + '</span></span>'
      ].join('');

      var timelineRows = $('#' + this.containerID + ' li');
      var insertedIntoPrevRow = false;

      for(var i = 0; i < timelineRows.length; i++)
      { 
        var rowElements = $(timelineRows[i]).children();
        var computedWidth = 0;
        var takenSpaces = [];
        for(var j = 0; j < rowElements.length; j++)
        {
          var tempObj = $(rowElements[j]);

          takenSpaces.push({
            start: computedWidth + timesheet_getPixelAmount(tempObj.css('marginLeft')),
            end: (computedWidth + tempObj.width() + 
                 timesheet_getPixelAmount(tempObj.css('marginLeft')) +
                 timesheet_getPixelAmount(tempObj.css('paddingLeft')) +
                 timesheet_getPixelAmount(tempObj.css('marginRight')) +
                 timesheet_getPixelAmount(tempObj.css('paddingRight'))),
            html: tempObj.parent().html()
          });
          
          computedWidth += tempObj.width() + 
                           timesheet_getPixelAmount(tempObj.css('marginLeft')) +
                           timesheet_getPixelAmount(tempObj.css('paddingLeft')) +
                           timesheet_getPixelAmount(tempObj.css('marginRight')) +
                           timesheet_getPixelAmount(tempObj.css('paddingRight'));
        }

        var availableSpaces = null;

        for(var j = 0; j < takenSpaces.length; j++)
        {
          if(availableSpaces == null)
          {
            availableSpaces = [];
            availableSpaces.push({start: 0, 
                                  end: takenSpaces[j].start - 1,
                                  prior_html: ""});
          }
          else if(j+1 < takenSpaces.length)
          {
            availableSpaces.push({start: takenSpaces[j-1].end + 1, 
                                  end: takenSpaces[j].start - 1,
                                  prior_html: takenSpaces[j-1].html});
          }
        }
        
        if(computedWidth < bubble.getStartOffset())
        {
          line = line.replace('---REPLACE_ME---', Math.round(bubble.getStartOffset() - computedWidth));
          $(timelineRows[i]).html($(timelineRows[i]).html() + line);
          insertedIntoPrevRow = true;
          break;
        }
        
      }

      if(!insertedIntoPrevRow)
      {
         line = line.replace('---REPLACE_ME---', bubble.getStartOffset());
         $('#' + this.containerID + ' .data').html($('#' + this.containerID + ' .data').html() + '<li>' + line + '</li>');
      }
    }

    var timelineRows = $('#timeline li');
    var tempMaxLength = null;

    for(var i = 0; i < timelineRows.length; i++)
    { 
      var rowElements = $(timelineRows[i]).children();
      var computedWidth = 0;
      for(var j = 0; j < rowElements.length; j++)
      {
        var tempObj = $(rowElements[j]);

        computedWidth += tempObj.width() + 
                         timesheet_getPixelAmount(tempObj.css('marginLeft')) +
                         timesheet_getPixelAmount(tempObj.css('paddingLeft')) +
                         timesheet_getPixelAmount(tempObj.css('marginRight')) +
                         timesheet_getPixelAmount(tempObj.css('paddingRight'));
      }

      if(tempMaxLength == null || tempMaxLength < computedWidth)
      {
        tempMaxLength = computedWidth;
      }
    }

    this.maxLength = tempMaxLength;
  };

  /**
   * Draw section labels
   */
  Timesheet.prototype.drawSections = function() {
    var html = [];

    for (var c = this.year.min; c <= this.year.max; c++) {
      html.push('<section>' + c + '</section>');
    }

    this.container.className = 'timesheet color-scheme-default';
    this.container.innerHTML = '<div class="scale">' + html.join('') + '</div>';
  };

  /**
   * Parse data string
   */
  Timesheet.prototype.parseDate = function(date) {
    if (date.indexOf('-') === -1) {
      date = new Date(parseInt(date, 10), 0, 1);
      date.hasMonth = false;
    } else {
      date = date.split('-');
      date = new Date(parseInt(date[0], 10), parseInt(date[1], 10)-1, 1);
      date.hasMonth = true;
    }

    return date;
  };

  /**
   * Parse passed data
   */
  Timesheet.prototype.parse = function(data) {
    for (var n = 0, m = data.length; n<m; n++) {
      var beg = this.parseDate(data[n][0]);
      var end = data[n].length === 4 ? this.parseDate(data[n][1]) : null;
      var lbl = data[n].length === 4 ? data[n][2] : data[n][1];
      var cat = data[n].length === 4 ? data[n][3] : data[n].length === 3 ? data[n][2] : 'default';

      if (beg.getFullYear() < this.year.min) {
        this.year.min = beg.getFullYear();
      }

      if (end && end.getFullYear() > this.year.max) {
        this.year.max = end.getFullYear();
      } else if (beg.getFullYear() > this.year.max) {
        this.year.max = beg.getFullYear();
      }

      this.data.push({start: beg, end: end, label: lbl, type: cat});
    }
  };

  Timesheet.prototype.completeParse = function(data) {
    for (var n = 0, m = data.length; n<m; n++) {
      var beg = data[n].start != undefined ? this.parseDate(data[n].start) : null;
      var end = data[n].end != undefined ? this.parseDate(data[n].end) : null;
      var lbl = data[n].label != undefined ? data[n].label : '';
      var cat = data[n].category != undefined ? data[n].category : 'default';
      var temp_icon = data[n].icon != undefined ? data[n].icon : 'default';
      var temp_show = data[n].click_show != undefined ? data[n].click_show : null;

      if (beg.getFullYear() < this.year.min) {
        this.year.min = beg.getFullYear();
      }

      if (end && end.getFullYear() > this.year.max) {
        this.year.max = end.getFullYear();
      } else if (beg.getFullYear() > this.year.max) {
        this.year.max = beg.getFullYear();
      }

      this.data.push({start: beg, end: end, label: lbl, type: cat, icon: temp_icon, click_show: temp_show});
    }
  };

  /**
   * Wrapper for adding bubbles
   */
  Timesheet.prototype.createBubble = function(wMonth, min, start, end, label) {
    return new Bubble(wMonth, min, start, end, label);
  };

  /**
   * Timesheet Bubble
   */
  var Bubble = function(wMonth, min, start, end, label) {
    this.min = min;
    this.start = start;
    this.end = end;
    this.widthMonth = wMonth;
    this.label = label;
  };

  /**
   * Format month number
   */
  Bubble.prototype.formatMonth = function(num) {
    num = parseInt(num, 10);

    return num >= 10 ? num : '0' + num;
  };

  /**
   * Calculate starting offset for bubble
   */
  Bubble.prototype.getStartOffset = function() {
    return (this.widthMonth/12) * (12 * (this.start.getFullYear() - this.min) + this.start.getMonth());
  };

  /**
   * Get count of full years from start to end
   */
  Bubble.prototype.getFullYears = function() {
    return ((this.end && this.end.getFullYear()) || this.start.getFullYear()) - this.start.getFullYear();
  };

  /**
   * Get count of all months in Timesheet Bubble
   */
  Bubble.prototype.getMonths = function() {
    var fullYears = this.getFullYears();
    var months = 0;

    if (!this.end) {
      months += !this.start.hasMonth ? 12 : 1;
    } else {
      if (!this.end.hasMonth) {
        months += 12 - (this.start.hasMonth ? this.start.getMonth() : 0);
        months += 12 * (fullYears-1 > 0 ? fullYears-1 : 0);
      } else {
        months += this.end.getMonth() + 1;
        months += 12 - (this.start.hasMonth ? this.start.getMonth() : 0);
        months += 12 * (fullYears-1);
      }
    }

    return months;
  };

  /**
   * Get bubble's width in pixel
   */
  Bubble.prototype.getWidth = function() {
    return (this.widthMonth/12) * this.getMonths();
  };

  /**
   * Get the bubble's label
   */
  Bubble.prototype.getDateLabel = function() {
    return [
      this.start.getFullYear() + (this.start.hasMonth ? '-' + this.formatMonth(this.start.getMonth() + 1) : '' ),
      (this.end ? ' to ' + (this.end.getFullYear() + (this.end.hasMonth ? '-' + this.formatMonth(this.end.getMonth() + 1) : '' )) : '')
    ].join('');
  };

  Bubble.prototype.getBubbleLabel = function(){
    return '<span class="date">' + this.getDateLabel() + '</span><span class="label">' + this.label + '</span>';
  };

  window.Timesheet = Timesheet;
})();