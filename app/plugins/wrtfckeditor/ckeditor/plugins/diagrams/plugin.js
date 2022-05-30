CKEDITOR.plugins.add( 'diagrams', {
	icons: 'diagrams',

	init: function (editor) {
		var cls = 'diagrams';

		editor.widgets.add('diagrams', {
			inline: true,
			mask: false,
			button: text('editor.button.diagrams'),
			allowedContent: 'span(!' + cls + ')',
			styleToAllowedContentRules: function (style) {
				var classes = style.getClassesArray();
				if (!classes)
					return null;
				classes.push('!' + cls);
				return 'span(' + classes.join(',') + ')';
			},

			template: '<span class="' + cls + '">',

			parts: {
				span: 'span'
			},

			defaults: {
				image: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHkAAAA9CAYAAACJM8YzAAAAAXNSR0IArs4c6QAAA2N0RVh0bXhmaWxlACUzQ214ZmlsZSUyMGhvc3QlM0QlMjJlbWJlZC5kaWFncmFtcy5uZXQlMjIlMjBtb2RpZmllZCUzRCUyMjIwMjItMDItMjhUMTMlM0EyOSUzQTIwLjY0NlolMjIlMjBhZ2VudCUzRCUyMjUuMCUyMChXaW5kb3dzJTIwTlQlMjAxMC4wJTNCJTIwV2luNjQlM0IlMjB4NjQpJTIwQXBwbGVXZWJLaXQlMkY1MzcuMzYlMjAoS0hUTUwlMkMlMjBsaWtlJTIwR2Vja28pJTIwQ2hyb21lJTJGOTYuMC40NjY0LjE3NCUyMFlhQnJvd3NlciUyRjIyLjEuMy44NDglMjBZb3dzZXIlMkYyLjUlMjBTYWZhcmklMkY1MzcuMzYlMjIlMjBldGFnJTNEJTIyUFhSbUVOSU1BY0o0QU1ZTnpTTG0lMjIlMjB2ZXJzaW9uJTNEJTIyMTYuNi40JTIyJTIwdHlwZSUzRCUyMmVtYmVkJTIyJTNFJTNDZGlhZ3JhbSUyMGlkJTNEJTIyZW1ZUlNERFJrRFAyWWQzRVp5M3IlMjIlMjBuYW1lJTNEJTIyUGFnZS0xJTIyJTNFalpKTlU0UXdESVolMkZUZThMSFZlOWl1dnV4Uk1IejVWRzJyRTBUQ2tMJTJCT3N0TmhVNk84NTRJbmtTM255VjhhcWJ6MDcwNmhVbEdGWWU1TXo0TXl2TG9yemo0Yk9TSlpJSGZoOUI2N1NNNkxDQlduOEIlMkZabm9xQ1VNeENMeWlNYnJQb2NOV2d1Tno1aHdEcWM4N1FOTlhyVVhMV1FaSzZnYllXN3BtNVplMFJUbGNlTVgwSzFLbFl2alk0eThpJTJCYXpkVGhhcW1mUlFveDBJc2xRRDRNU0VxY2Q0aWZHSzRmb285WE5GWmgxcmZuR1h2NklVc3VEWDlJUXFWY0gxdjlMSVExeEZXYk1OSGFpazlJZTZsNDBxeiUyQkY2elAlMkJwSHhuZ2xjRTg3WW05WFVGNTJIZUllcmhETmlCZDB0SW9XamF6NUs3MDNhSElqRzF1OEdSbUtEVHQ3JTJGQzI4akJvS21UdTYzN0o3Wjd6dnowRFElM0QlM0QlM0MlMkZkaWFncmFtJTNFJTNDJTJGbXhmaWxlJTNFRvsC8gAAAPVJREFUeF7t04EJwEAMw8Bk/6G/FEqHeJ03kIR3Zs7Y1Qb2jXyOzrdW3t0R+da6H5fIlwd+8UQWOWAggOjJIgcMBBA9WeSAgQCiJ4scMBBA9GSRAwYCiJ4scsBAANGTRQ4YCCB6ssgBAwFETxY5YCCA6MkiBwwEED1Z5ICBAKInixwwEED0ZJEDBgKInixywEAA0ZNFDhgIIHqyyAEDAURPFjlgIIDoySIHDAQQPVnkgIEAoieLHDAQQPRkkQMGAoieLHLAQADRk0UOGAggerLIAQMBRE8WOWAggOjJIgcMBBA9WeSAgQCiJ4scMBBA/J8cYE0jPh5C7gE8XAF0AAAAAElFTkSuQmCC'
			},

			init: function () {
				var self = this;
				var span = self.parts.span;
				if ( !span.findOne('img') ) {
					span.setHtml('<img src="'+this.data.image+'">');
					setTimeout(function() {
						self.openEditor(span.findOne('img').$);
					}, 500);
				}
				self.parts.span.findOne('img').on('dblclick', function(e) {
					self.openEditor(this.$);
				});
			},

			data: function () {
			},

			openEditor: function(img) {
				var self = this;
				DiagramEditor.editElement(img, null, null, function(data, draft, el) {
					if ( draft ) return;
					self.parts.span.removeAttribute('data-cke-widget-data');
					self.parts.span.findOne('img').removeAttribute('data-cke-saved-src');
					self.editor.resetDirty();
					setTimeout(function() {
						self.setData({
							image: self.parts.span.findOne('img').getAttribute('src')
						});
					}, 500);
				});
			},

			upcast: function (el, data) {
				if ( !( el.name == 'span' && el.hasClass( cls ) ) ) return;
				if ( el.children.length != 1 ) return;

				this.data.image = el.children[0].attributes.src;
				return el;
			},

			downcast: function (el) {
				return el;
			}
		});
	}
});
