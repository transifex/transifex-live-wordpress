//****************************************************************************************************************************************************
// Copyright (c) 2012 AbstractLabs
// Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), 
// to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
// and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
// The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
// IN THE SOFTWARE.
//****************************************************************************************************************************************************
(function (jQuery) {
	var hash = new RegExp(/log\:?(all|debug|info|warn|error)/gi).exec(document.location.hash)
	jQuery.log =
	{
		__window: null,
		__level: "none",
		__context: new Array(),
		__operation: new Array(),
		level: function (value) {
			if (value != this.__level && (value == 'all' || value == 'debug' || value == 'info' || value == 'warn' || value == 'error')) {
				this.__level = value;
				this.write('Logging level is ' + this.__level)
			}
			return this.__level;
		},
		enabled: function (level) {
			return (this.__level == 'all'
				|| this.__level == 'debug'
				|| (this.__level != 'none' && level == 'any')
				|| (this.__level == 'info' && (level != 'debug'))
				|| (this.__level == 'warn' && (level != 'debug' || level != 'info'))
				|| (this.__level == 'error' && (level != 'debug' || level != 'info' || level != 'warn')));
		},
		context: function (depth) {
			var result = null;
			if (this.context.length > 0 && (typeof (depth) == 'undefined' || depth < this.__context.length)) result = this.__context[this.__context.length - 1];
			return result;
		},
		enter: function (context) {
			this.__context.push(context);
			return this;
		},
		exit: function () {
			if (this.__context.length > 0) this.__context.pop();
			return this;
		},
		start: function (operation) {
			this.__operation.push({ name: operation, timestamp: new Date().getTime() });
			this.write('Start ' + operation, '->')
		},
		stop: function () {
			if (this.__operation.length > 0) {
				var operation = this.__operation.pop()
				var elapsed = new Date();
				elapsed.setTime(new Date().getTime() - operation.timestamp);
				this.write('Stop ' + operation.name + ' ' + elapsed.getMinutes() + ':' + elapsed.getSeconds() + ':' + elapsed.getMilliseconds() + ' elapsed', '<-')
			}
		},
		reset: function () {
			this.__context = new Array();
			this.__operations = new Array();
			return this;
		},
		write: function (message, type) {
			if (this.enabled('any')) {
				if (typeof (type) != 'undefined') message = '[' + type + '] ' + message;
				if (typeof (console) == 'object') console.log(message)
				else if (typeof (opara) == 'object') opera.postError(message);
				else {
					if (this.__window == null) this.__window = window.open();
					this.__window.document.write(message);
				}
			}
		},
		format: function () {
			var result = (this.__context.length > 0) ? this.__context.join(' ') + ':' : '';
			for (var index = 0; index < arguments.length; index++)
			{
				if(typeof(arguments[index]) == 'string') 
				{
					result += arguments[index]
				}
				else if(JSON && JSON.stringify) result += JSON.stringify(arguments[index]);
				else 
				{
					results += '{';
					for(p in arguments[index]) result += p +':'+ arguments[index][p]
					results += '}';					
				}
			}
			return result;
		},
		debug: function () {
			if (this.enabled('debug')) {
				var message = this.format.apply(this, arguments);
				if (typeof (console.debug) == 'function') console.debug(message)
				else this.write(message, '*');
			}
		},
		info: function () {
			if (this.enabled('info')) {
				var message = this.format.apply(this, arguments);
				if (typeof (console.info) == 'function') console.info(message)
				else this.write(message, '?');
			}
		},
		warn: function () {
			if (this.enabled('warn')) {
				var message = this.format.apply(this, arguments);
				if (typeof (console.warn) == 'function') console.warn(message)
				else this.write(message, '+');
			}
		},
		error: function () {
			if (this.enabled('error')) {
				var message = this.format.apply(this, arguments);
				if (typeof (console.error) == 'function') console.error(message)
				else this.write(message, '!');
			}
		},
		exception: function (e) {
			if (this.enabled('error')) {
				var message = '';
				if ('fileName' in e) message += e.fileName + ' ';
				if ('lineNumber' in e) message += e.lineNumber + ' ';
				if ('number' in e) message += e.number;
				if (message.length > 0) message += ':';
				message += e.message;
				if ('description' in e) message += '-' + e.description;
			}
		}
	},
	jQuery.fn.log = function (jQuery) {
		var $this = $(this);
		var context = $this.prop('tagName');
		if ($this.attr('id')) context += ' #' + $this.attr('id');
		if ($this.attr('class')) context += ' ' + $this.attr('class');
		return {
			write: function (message, type) {
				$.log.enter(context);
				try { $.log.write.call($._log, message, type); }
				finally { $.log.exit(); }
			}
			,
			debug: function () {
				$.log.enter(context);
				try { $.log.debug.apply($.log, arguments); }
				finally { $.log.exit(); }
			},
			info: function () {
				$.log.enter(context);
				try { $.log.info.apply($.log, arguments); }
				finally { $.log.exit(); }
			},
			warn: function () {
				$.log.enter(context);
				try { $.log.warn.apply($.log, arguments); }
				finally { $.log.exit(); }
			},
			error: function () {
				$.log.enter(context);
				try { $.log.error.apply($.log, arguments); }
				finally { $.log.exit(); }
			}
		}
	}
	if (hash != null && hash.length > 1) try { $.log.level(hash[1]) } finally { }
})(jQuery);