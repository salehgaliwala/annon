/**
 * countdown.js
 * A simple countdown script to save your sanity
 * @author Leonard Teo, Ballistiq Digital Inc.
 *
 * Copyright (C) 2012 Ballistiq Digital Inc.
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

function Countdown(target_date, current_date) {
    this.target_date = target_date;
    this.current_date = current_date;
}

Countdown.prototype.countdown = function(callback) {

    var difference = this.target_date.getTime() - this.current_date.getTime(),
        runNext = (difference > 0),
        differenceObject;

    if (runNext) {
        var days = Math.floor( difference / (1000 * 60 * 60 * 24) );
        difference = difference - (days * 1000 * 60 * 60 * 24);
        var hours = Math.floor( difference / (1000 * 60 * 60) ),
            hoursB = hours;
        hours = hours + (days * 24);
        difference = difference - (hoursB * 1000 * 60 * 60);
        var minutes = Math.floor( difference / (1000 * 60) );
        difference = difference - (minutes * 1000 * 60);
        var seconds = Math.floor( difference / 1000 );

        differenceObject = {
            hours: hours,
            minutes: minutes,
            seconds: seconds
        };
    } else {
        differenceObject = {
            hours: '00',
            minutes: '00',
            seconds: '00'
        }
    }

    callback(differenceObject);

    if (runNext) {
        this.current_date = new Date( this.current_date.getTime() + 1000 );
        var _this = this;
        setTimeout(function() { _this.countdown(callback) }, 1000);
    }
}